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
use service\questionnaire\QuestionnnaireManageService;

require_once 'service/questionnaire/QuestionnnaireManageService.php';
require_once 'dto/request/user/QuestionnaireCreateDto.php';
require_once 'dto/common/QuestionInfoDto.php';
require_once 'dto/common/OptionInfoDto.php';

class QuestionnnaireManageController {

    private QuestionnnaireManageService $qm_service;

    public function __construct() {
        $this->qm_service = new QuestionnnaireManageService();
    }

    #[RequestMapping("GET", "/user-api/questionnnaires")]
    #[RequireAuthority('User')]
    public function get_questionnnaire_list($uri_arr, $uri_query_map, $body): ResponseModel {
        return $this->qm_service->get_questionnnaire_list_created_by_current_user();
    }

    #[RequestMapping("GET", "/user-api/questionnnaires/*")]
    #[RequireAuthority('User')]
    public function get_questionnnaire_detail($uri_arr, $uri_query_map, $body): ResponseModel {
        $qnid = $uri_arr[2];
        if (!is_numeric($qnid)){
            return Response::invalid_argument();
        }
        return $this->qm_service->user_get_questionnaire_details(intval($qnid));
    }

    #[RequestMapping("GET", "/user-api/questionnnaires/*/statistics")]
    #[RequireAuthority('User')]
    public function get_questionnnaire_statistics($uri_arr, $uri_query_map, $body): ResponseModel {
        $qnid = $uri_arr[2];
        if (!is_numeric($qnid)){
            return Response::invalid_argument();
        }

        return $this->qm_service->get_questionnnaire_statistics(intval($qnid));

    }

    #[RequestMapping("POST", "/user-api/questionnnaires")]
    #[RequireAuthority('User')]
    public function create_questionnnaire($uri_arr, $uri_query_map, $body): ResponseModel {
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
        return $this->qm_service->create_questionnnaire($qn);
    }

    #[RequestMapping("PUT", "/user-api/questionnnaires/*")]
    #[RequireAuthority('User')]
    public function update_questionnnaire($uri_arr, $uri_query_map, $body): ResponseModel {
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
        return $this->qm_service->update_questionnnaire(intval($qnid), $qn);
    }

}