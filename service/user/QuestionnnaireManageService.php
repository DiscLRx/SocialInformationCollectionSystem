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
use entity\Questionnaire;
use entity\Question;
use entity\Option;
use framework\exception\DatabaseException;
use framework\RedisExecutor;
use framework\response\Response;
use framework\response\ResponseModel;

require_once 'dao/QuestionnaireDaoImpl.php';
require_once 'dao/QuestionDaoImpl.php';
require_once 'dao/OptionDaoImpl.php';
require_once 'dto/response/user/QuestionnaireListDto.php';

class QuestionnnaireManageService {
    private QuestionnaireDao $questionnaire_dao;
    private QuestionDao $question_dao;
    private OptionDao $option_dao;
    private RedisExecutor $redis;

    public function __construct() {
        $this->questionnaire_dao = new QuestionnaireDaoImpl();
        $this->question_dao = new QuestionDaoImpl();
        $this->option_dao = new OptionDaoImpl();
        $this->redis = new RedisExecutor();
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

}