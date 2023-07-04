<?php

namespace service\questionnaire;

use common\QuestionnaireBasicService;
use dao\AnswerRecordDao;
use dao\AnswerRecordDaoImpl;
use dao\ChoiceAnswerDao;
use dao\ChoiceAnswerDaoImpl;
use dao\TextAnswerDao;
use dao\TextAnswerDaoImpl;
use dto\request\admin\EnableDto;
use dto\request\user\QuestionnaireCreateDto;
use dto\response\user\OptionStatisticsDto;
use dto\response\user\QuestionnaireListDto;
use dto\response\user\QuestionnaireStatisticsDto;
use dto\response\user\QuestionStatisticsDto;
use dto\universal\OptionInfoDto;
use dto\universal\QuestionInfoDto;
use dto\universal\QuestionnaireDetailDto;
use entity\Questionnaire;
use framework\exception\DatabaseException;
use framework\response\Response;
use framework\response\ResponseModel;
use framework\util\Time;

require_once 'dto/response/user/QuestionnaireListDto.php';
require_once 'dto/common/QuestionnaireDetailDto.php';
require_once 'dto/common/QuestionInfoDto.php';
require_once 'dto/common/OptionInfoDto.php';
require_once 'dto/response/user/QuestionnaireStatisticsDto.php';
require_once 'dto/response/user/QuestionStatisticsDto.php';
require_once 'dto/response/user/OptionStatisticsDto.php';
require_once 'service/common/QuestionnaireBasicService.php';
require_once 'dao/ChoiceAnswerDaoImpl.php';
require_once 'dao/TextAnswerDaoImpl.php';
require_once 'dao/AnswerRecordDaoImpl.php';

class QuestionnaireManageService extends QuestionnaireBasicService {

    private ChoiceAnswerDao $choice_answer_dao;
    private TextAnswerDao $text_answer_dao;
    private AnswerRecordDao $answer_record_dao;

    public function __construct() {
        parent::__construct();
        $this->choice_answer_dao = new ChoiceAnswerDaoImpl();
        $this->text_answer_dao = new TextAnswerDaoImpl();
        $this->answer_record_dao = new AnswerRecordDaoImpl();
    }


    public function get_questionnaire_list(): ResponseModel {
        $questionnaire_arr = $this->questionnaire_dao->select();
        return Response::success($questionnaire_arr);
    }

    public function get_questionnaire_list_created_by_current_user(): ResponseModel {
        $uid = $GLOBALS['USER']->get_id();
        $questionnaire_arr = $this->questionnaire_dao->select_by_userid($uid);
        return Response::success(new QuestionnaireListDto($questionnaire_arr));
    }

    public function create_questionnaire(QuestionnaireCreateDto $qn_dto): ResponseModel {
        $uid = $GLOBALS['USER']->get_id();

        $qnid = $this->questionnaire_dao->insert(
            $uid,
            $qn_dto->get_title(),
            $qn_dto->get_begin_date(),
            $qn_dto->get_end_date()
        );
        if ($qnid === 0) {
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
            if ($qid === 0) {
                throw new DatabaseException("问题表插入失败");
            }
            $o_arr = $q->get_option();
            foreach ($o_arr as $o) {
                $oid = $this->option_dao->insert(
                    $qid,
                    $o->get_order(),
                    $o->get_content()
                );
                if ($oid === 0) {
                    throw new DatabaseException("选项表插入失败");
                }
            }
        }

        return Response::success();
    }

    public function user_get_questionnaire_details(int $qnid): ResponseModel {

        $qn = $this->get_questionnaire($qnid);
        if (!isset($qn)) {
            return Response::invalid_argument();
        }

        if ($qn->get_user_id() !== $GLOBALS['USER']->get_id()) {
            return Response::permission_denied();
        }

        $dto = $this->generate_questionnaire_detail_dto($qn);
        return Response::success($dto);
    }

    public function admin_get_questionnaire_details(int $qnid): ResponseModel {

        $qn = $this->get_questionnaire($qnid);
        if (!isset($qn)) {
            return Response::invalid_argument();
        }

        $dto = $this->generate_questionnaire_detail_dto($qn);
        return Response::success($dto);
    }

    public function generate_questionnaire_detail_dto(Questionnaire $qn): QuestionnaireDetailDto {
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
        $qn_detail_dto->set_question($q_dto_arr);
        return $qn_detail_dto;
    }

    public function update_questionnaire(int $qnid, QuestionnaireCreateDto $qn_dto): ResponseModel {

        $qn = $this->get_questionnaire($qnid);
        if (!isset($qn)) {
            return Response::invalid_argument();
        }

        $uid = $GLOBALS['USER']->get_id();
        if ($uid !== $qn->get_user_id()) {
            return Response::permission_denied();
        }

        //问卷开始后不允许修改
        if (Time::ts_after($qn->get_begin_date(), second: 5) <= Time::current_ts()) {
            return Response::reject_request("不允许修改已开始的问卷");
        }

        //删除已有问卷,重新创建
        $line = $this->questionnaire_dao->delete_by_id($qnid);
        if ($line !== 1) {
            throw new DatabaseException("删除问卷失败, qnid:{$qnid}");
        }
        $this->redis->del("qnid_{$qnid}");
        return $this->create_questionnaire($qn_dto);
    }

    public function set_enable(int $qnid, EnableDto $enable_dto): ResponseModel{
        $this->questionnaire_dao->update_enable_by_id($qnid, $enable_dto->is_enable());
        $this->redis->del("qnid_{$qnid}");
        return Response::success();
    }

    public function get_questionnaire_statistics($qnid): ResponseModel{

        $auth_uid = $GLOBALS['USER']->get_id();
        $qn = $this->questionnaire_dao->select_by_id($qnid);
        if (!isset($qn) || $qn->get_user_id()!==$auth_uid){
            return Response::reject_request();
        }

        $qn_st_dto = new QuestionnaireStatisticsDto(
            $qn->get_title(),
            $this->answer_record_dao->count_by_questionnaireid($qnid)
        );

        $q_st_dto_arr = [];
        $question_arr = $this->question_dao->select_by_questionnaireid($qnid);
        foreach ($question_arr as $question){
            $q_st_dto = new QuestionStatisticsDto(
                $question->get_order(),
                $question->get_type(),
                $question->get_content()
            );

            $qid = $question->get_id();
            if ($question->get_type() === 'text') {
                $text_answer_arr = $this->text_answer_dao->select_by_questionid($qid);
                $text_arr = array_map(fn ($ta) => $ta->get_text(), $text_answer_arr);
                $q_st_dto->set_answers($text_arr);
            } else {
                $o_st_dto_arr = [];
                $option_arr = $this->option_dao->select_by_questionid($qid);
                foreach ($option_arr as $option){
                    $oid = $option->get_id();
                    $o_st_dto = new OptionStatisticsDto(
                        $option->get_order(),
                        $option->get_content(),
                        $this->choice_answer_dao->count_by_optionid($oid)
                    );
                    $o_st_dto_arr[] = $o_st_dto;
                }
                $q_st_dto->set_answers($o_st_dto_arr);
            }
            $q_st_dto_arr[] = $q_st_dto;
        }

        $qn_st_dto->set_question($q_st_dto_arr);

        return Response::success($qn_st_dto);

    }

}