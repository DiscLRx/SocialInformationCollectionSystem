<?php

use dao\QuestionnaireDao;
use dao\QuestionnaireDaoImpl;
use dao\QuestionDao;
use dao\QuestionDaoImpl;
use dao\OptionDao;
use dao\OptionDaoImpl;
use dto\response\user\QuestionnaireListDto;
use dto\request\user\QuestionnaireCreateDto;
use dto\universal\OptionInfoDto;
use dto\universal\QuestionInfoDto;
use dto\universal\QuestionnaireDetailDto;
use entity\Questionnaire;
use entity\Question;
use entity\Option;
use framework\exception\DatabaseException;
use framework\RedisExecutor;
use framework\response\Response;
use framework\response\ResponseModel;
use framework\util\JSON;

require_once 'dao/QuestionnaireDaoImpl.php';
require_once 'dao/QuestionDaoImpl.php';
require_once 'dao/OptionDaoImpl.php';
require_once 'dto/response/user/QuestionnaireListDto.php';
require_once 'dto/common/QuestionnaireDetailDto.php';
require_once 'dto/common/QuestionInfoDto.php';
require_once 'dto/common/OptionInfoDto.php';

class QuestionnnaireManageService {
    private QuestionnaireDao $questionnaire_dao;
    private QuestionDao $question_dao;
    private OptionDao $option_dao;
    private RedisExecutor $redis;

    public function __construct() {
        $this->questionnaire_dao = new QuestionnaireDaoImpl();
        $this->question_dao = new QuestionDaoImpl();
        $this->option_dao = new OptionDaoImpl();
        $this->redis = new RedisExecutor(1);
    }

    public function get_questionnnaire_list(): ResponseModel{
        $uid = $GLOBALS['USER']->get_id();
        $questionnaire_arr = $this->questionnaire_dao->select_by_userid($uid);
        return Response::success(new QuestionnaireListDto($questionnaire_arr));
    }

    public function create_questionnnaire(QuestionnaireCreateDto $qn_dto): ResponseModel {
        $uid = $GLOBALS['USER']->get_id();

        $qnid = $this->questionnaire_dao->insert(
            $uid,
            $qn_dto->get_title(),
            $qn_dto->get_begin_date(),
            $qn_dto->get_end_date()
        );
        if ($qnid === 0){
            throw new DatabaseException("问卷表插入失败");
        }
        $q_arr = $qn_dto->get_question();
        foreach ($q_arr as $q) {
            $qid = $this->question_dao->insert(
                $qnid,
                $q->get_order(),
                $q->get_type(),
                $q->get_content()
            );
            if ($qid === 0){
                throw new DatabaseException("问题表插入失败");
            }
            $o_arr = $q->get_option();
            foreach ($o_arr as $o) {
                $oid = $this->option_dao->insert(
                    $qid,
                    $o->get_order(),
                    $o->get_content()
                );
                if ($oid === 0){
                    throw new DatabaseException("选项表插入失败");
                }
            }
        }

        return Response::success();
    }

    public function get_questionnnaire_detail(int $qnid): ResponseModel {

        $qn_str = $this->redis->get("qnid_{$qnid}");
        if ($qn_str===false){
            $qn = $this->questionnaire_dao->select_by_id($qnid);
            if (!isset($qn)){
                return Response::invalid_argument();
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

        if ($qn->get_user_id()!==$GLOBALS['USER']->get_id()){
            Response::permission_denied();
        }

        $qn_detail_dto = new QuestionnaireDetailDto(
            $qn->get_id(),
            $qn->get_title(),
            $qn->get_begin_date(),
            $qn->get_end_date(),
            $qn->is_enable()
        );
        $q_dto_arr = array_map(function ($q) {
            $o_arr = $q->get_option_arr();
            $o_dto_arr = array_map(function ($o) {
                return new OptionInfoDto(
                    $o->get_order(),
                    $o->get_content()
                );
            }, $o_arr);
            return new QuestionInfoDto(
                $q->get_order(),
                $q->get_type(),
                $q->get_content(),
                $o_dto_arr
            );
        }, $qn->get_question_arr());
        $qn_detail_dto->set_question_($q_dto_arr);

        return Response::success($qn_detail_dto);
    }

    private function unserialize_questionnnaire($json_str): Questionnaire{
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