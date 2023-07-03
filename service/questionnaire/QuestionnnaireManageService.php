<?php

namespace service\questionnaire;

use common\QuestionnaireBasicService;
use dto\request\admin\EnableDto;
use dto\request\user\QuestionnaireCreateDto;
use dto\response\user\QuestionnaireListDto;
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
require_once 'service/common/QuestionnaireBasicService.php';

class QuestionnnaireManageService extends QuestionnaireBasicService {

    public function __construct() {
        parent::__construct();
    }

    public function get_questionnaire_list(): ResponseModel {
        $questionnaire_arr = $this->questionnaire_dao->select();
        return Response::success($questionnaire_arr);
    }

    public function get_questionnnaire_list_created_by_current_user(): ResponseModel {
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
        if ($qn === 21) {
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
        if ($qn === 21) {
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

    public function update_questionnnaire(int $qnid, QuestionnaireCreateDto $qn_dto): ResponseModel {

        $qn = $this->get_questionnaire($qnid);
        if ($qn instanceof ResponseModel) {
            return $qn;
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
        return $this->create_questionnnaire($qn_dto);
    }

    public function set_enable(int $qnid, EnableDto $enable_dto): ResponseModel{
        $this->questionnaire_dao->update_enable_by_id($qnid, $enable_dto->is_enable());
        $this->redis->del("qnid_{$qnid}");
        return Response::success();
    }

}