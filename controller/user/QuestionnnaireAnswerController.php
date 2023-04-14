<?php

namespace user;

use framework\RequestMapping;
use framework\response\Response;
use framework\response\ResponseModel;
use QuestionnnaireAnswerService;
use security\RequireAuthority;

require_once 'service/user/QuestionnnaireAnswerService.php';

class QuestionnnaireAnswerController {

    private QuestionnnaireAnswerService $qa_service;

    public function __construct() {
        $this->qa_service = new QuestionnnaireAnswerService();
    }

    #[RequestMapping('GET', '/user-api/questionnnaires/*/contents')]
    #[RequireAuthority('User')]
    public function get_content($uri_arr, $uri_query_map, $body) : ResponseModel{
        $qnid = $uri_arr[2];
        if (!is_numeric($qnid)){
            return Response::invalid_argument();
        }
        return $this->qa_service->get_content(intval($qnid));
    }

}