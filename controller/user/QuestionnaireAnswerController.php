<?php

namespace user;

use dto\request\user\QuestionAnswerDto;
use dto\request\user\QuestionnaireAnswerDto;
use framework\RequestMapping;
use framework\response\Response;
use framework\response\ResponseModel;
use framework\util\JSON;
use security\RequireAuthority;
use service\questionnaire\QuestionnaireAnswerService;

require_once 'service/questionnaire/QuestionnaireAnswerService.php';
require_once 'dto/request/user/QuestionnaireAnswerDto.php';
require_once 'dto/request/user/QuestionAnswerDto.php';

class QuestionnaireAnswerController {

    private QuestionnaireAnswerService $qa_service;

    public function __construct() {
        $this->qa_service = new QuestionnaireAnswerService();
    }

    #[RequestMapping('GET', '/user-api/questionnaires/*/contents')]
    #[RequireAuthority('User')]
    public function get_content($uri_arr, $uri_query_map, $body) : ResponseModel{
        $qnid = $uri_arr[2];
        if (!is_numeric($qnid)){
            return Response::invalid_argument();
        }
        return $this->qa_service->get_content(intval($qnid));
    }

    #[RequestMapping('POST', '/user-api/questionnaires/*/answer')]
    #[RequireAuthority('User')]
    public function submit_answer($uri_arr, $uri_query_map, $body) : ResponseModel{
        $qna_dto = JSON::unserialize($body, QuestionnaireAnswerDto::class);
        $qa_arr = $qna_dto->get_answers();
        $qna_dto->set_answers(array_map(function ($qa) {
            return JSON::unserialize(JSON::serialize($qa), QuestionAnswerDto::class);
        }, $qa_arr));
        return $this->qa_service->submit_answer($qna_dto);
    }

}