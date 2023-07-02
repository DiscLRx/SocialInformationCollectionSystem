<?php

namespace admin;

use dto\request\admin\EnableDto;
use framework\exception\JSONSerializeException;
use framework\RequestMapping;
use framework\response\Response;
use framework\response\ResponseModel;
use framework\util\JSON;
use security\RequireAuthority;
use service\questionnaire\QuestionnnaireManageService;

require_once 'dto/request/admin/EnableDto.php';
require_once 'service/questionnaire/QuestionnnaireManageService.php';


class QuestionnnaireController {

    private QuestionnnaireManageService $qm_service;

    public function __construct() {
        $this->qm_service = new QuestionnnaireManageService();
    }

    #[RequestMapping('GET', '/admin-api/questionnnaires')]
    #[RequireAuthority('Admin')]
    public function get_questionnaires($uri_arr, $uri_query_map, $body): ResponseModel{
        return $this->qm_service->get_questionnaire_list();
    }


    #[RequestMapping('PATCH', '/admin-api/questionnnaires/*')]
    #[RequireAuthority('Admin')]
    public function disable_questionnaire($uri_arr, $uri_query_map, $body): ResponseModel{
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