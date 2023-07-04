<?php

namespace admin;

use dto\request\admin\EnableDto;
use framework\exception\JSONSerializeException;
use framework\RequestMapping;
use framework\response\Response;
use framework\response\ResponseModel;
use framework\util\JSON;
use security\RequireAuthority;
use service\questionnaire\QuestionnaireManageService;

require_once 'dto/request/admin/EnableDto.php';
require_once 'service/questionnaire/QuestionnaireManageService.php';


class QuestionnaireController {

    private QuestionnaireManageService $qm_service;

    public function __construct() {
        $this->qm_service = new QuestionnaireManageService();
    }

    #[RequestMapping('GET', '/admin-api/questionnaires')]
    #[RequireAuthority('Admin')]
    public function get_questionnaires($uri_arr, $uri_query_map, $body): ResponseModel{
        return $this->qm_service->get_questionnaire_list();
    }

    #[RequestMapping('GET', '/admin-api/questionnaires/*')]
    #[RequireAuthority('Admin')]
    public function get_questionnaire_details($uri_arr, $uri_query_map, $body): ResponseModel{
        $qnid = $uri_arr[2];
        if (!is_numeric($qnid)){
            return Response::invalid_argument();
        }
        return $this->qm_service->admin_get_questionnaire_details(intval($qnid));
    }

    #[RequestMapping('PATCH', '/admin-api/questionnaires/*')]
    #[RequireAuthority('Admin')]
    public function set_questionnaire_enable($uri_arr, $uri_query_map, $body): ResponseModel{
        $qnid = $uri_arr[2];
        if (!is_numeric($qnid)){
            return Response::invalid_argument();
        }
        try {
            $enable_dto = JSON::unserialize($body, EnableDto::class);
        } catch (JSONSerializeException) {
            return Response::invalid_argument();
        }
        return $this->qm_service->set_enable(intval($qnid), $enable_dto);
    }

}