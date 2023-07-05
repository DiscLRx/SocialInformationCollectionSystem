<?php

namespace user;

use dto\request\user\QuestionnaireCreateDto;
use dto\universal\OptionInfoDto;
use dto\universal\QuestionInfoDto;
use framework\exception\JSONSerializeException;
use framework\RequestMapping;
use framework\response\Response;
use framework\response\ResponseModel;
use framework\util\JSON;
use security\RequireAuthority;
use service\questionnaire\QuestionnaireManageService;

require_once 'service/questionnaire/QuestionnaireManageService.php';
require_once 'dto/request/user/QuestionnaireCreateDto.php';
require_once 'dto/common/QuestionInfoDto.php';
require_once 'dto/common/OptionInfoDto.php';

class QuestionnaireManageController {

    private QuestionnaireManageService $qm_service;

    public function __construct() {
        $this->qm_service = new QuestionnaireManageService();
    }

    #[RequestMapping("GET", "/user-api/questionnaires")]
    #[RequireAuthority('User')]
    public function get_questionnaire_list($uri_arr, $uri_query_map, $body): ResponseModel {
        return $this->qm_service->get_questionnaire_list_created_by_current_user();
    }

    #[RequestMapping("GET", "/user-api/questionnaires/*")]
    #[RequireAuthority('User')]
    public function get_questionnaire_detail($uri_arr, $uri_query_map, $body): ResponseModel {
        $qnid = $uri_arr[2];
        if (!is_numeric($qnid)){
            return Response::invalid_argument();
        }
        return $this->qm_service->user_get_questionnaire_details(intval($qnid));
    }

    #[RequestMapping("GET", "/user-api/questionnaires/*/statistics")]
    #[RequireAuthority('User')]
    public function get_questionnaire_statistics($uri_arr, $uri_query_map, $body): ResponseModel {
        $qnid = $uri_arr[2];
        if (!is_numeric($qnid)){
            return Response::invalid_argument();
        }

        return $this->qm_service->get_questionnaire_statistics(intval($qnid));

    }

    #[RequestMapping("POST", "/user-api/questionnaires")]
    #[RequireAuthority('User')]
    public function create_questionnaire($uri_arr, $uri_query_map, $body): ResponseModel {
        try {
            $qn = JSON::unserialize($body, QuestionnaireCreateDto::class);
            $q_arr = $qn->get_question();
            $qn->set_question(array_map(function ($q) {
                    $q = JSON::unserialize(JSON::serialize($q), QuestionInfoDto::class);
                    $o_arr = $q->get_option();
                    $q->set_option(array_map(function ($o) {
                            return JSON::unserialize(JSON::serialize($o), OptionInfoDto::class);
                        }, $o_arr));
                    return $q;
                }, $q_arr));
        } catch (JSONSerializeException $ex) {
            $ex->log();
            return Response::invalid_argument();
        }
        return $this->qm_service->create_questionnaire($qn);
    }

    #[RequestMapping("PUT", "/user-api/questionnaires/*")]
    #[RequireAuthority('User')]
    public function update_questionnaire($uri_arr, $uri_query_map, $body): ResponseModel {
        $qnid = $uri_arr[2];
        if (!is_numeric($qnid)){
            return Response::invalid_argument();
        }
        try {
            $qn = JSON::unserialize($body, QuestionnaireCreateDto::class);
            $q_arr = $qn->get_question();
            $qn->set_question(array_map(function ($q) {
                $q = JSON::unserialize(JSON::serialize($q), QuestionInfoDto::class);
                $o_arr = $q->get_option();
                $q->set_option(array_map(function ($o) {
                    return JSON::unserialize(JSON::serialize($o), OptionInfoDto::class);
                }, $o_arr));
                return $q;
            }, $q_arr));
        } catch (JSONSerializeException) {
            return Response::invalid_argument();
        }
        return $this->qm_service->update_questionnaire(intval($qnid), $qn);
    }

}