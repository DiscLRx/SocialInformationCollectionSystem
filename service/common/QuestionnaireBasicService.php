<?php

namespace common;

use dao\OptionDao;
use dao\OptionDaoImpl;
use dao\QuestionDao;
use dao\QuestionDaoImpl;
use dao\QuestionnaireDao;
use dao\QuestionnaireDaoImpl;
use entity\Option;
use entity\Question;
use entity\Questionnaire;
use framework\RedisExecutor;
use framework\response\Response;
use framework\response\ResponseModel;
use framework\util\JSON;

require_once 'dao/QuestionnaireDaoImpl.php';
require_once 'dao/QuestionDaoImpl.php';
require_once 'dao/OptionDaoImpl.php';

class QuestionnaireBasicService {
    protected QuestionnaireDao $questionnaire_dao;
    protected QuestionDao $question_dao;
    protected OptionDao $option_dao;
    protected RedisExecutor $redis;

    protected function __construct() {
        $this->questionnaire_dao = new QuestionnaireDaoImpl();
        $this->question_dao = new QuestionDaoImpl();
        $this->option_dao = new OptionDaoImpl();
        $this->redis = new RedisExecutor(1);
    }

    protected function get_questionnaire(int $qnid): Questionnaire|int {
        $qn_str = $this->redis->get("qnid_{$qnid}");
        if ($qn_str===false){
            $qn = $this->questionnaire_dao->select_by_id($qnid);
            if (!isset($qn)){
                return 21;
            }
            $q_arr = $this->question_dao->select_by_questionnaireid($qnid);

            $q_arr = array_map(
                function ($q) {
                    $o_arr = $this->option_dao->select_by_questionid($q->get_id());
                    $q->set_option_arr($o_arr);
                    return $q;
                }, $q_arr);
            $qn->set_question_arr($q_arr);

            $this->redis->set("qnid_{$qnid}", JSON::serialize($qn));
        }else{
            $qn = $this->unserialize_questionnnaire($qn_str);
        }
        return $qn;
    }

    protected function unserialize_questionnnaire($json_str): Questionnaire{
        $qn = JSON::unserialize($json_str, Questionnaire::class);
        $q_arr = $qn->get_question_arr();
        $qn->set_question_arr(array_map(function ($q) {
            $q = JSON::unserialize(JSON::serialize($q), Question::class);
            $o_arr = $q->get_option_arr();
            $q->set_option_arr(array_map(function ($o) {
                return JSON::unserialize(JSON::serialize($o), Option::class);
            }, $o_arr));
            return $q;
        }, $q_arr));
        return $qn;
    }

}