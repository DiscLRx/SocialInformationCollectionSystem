<?php


use common\QuestionnaireBasicService;
use dto\response\user\QuestionnaireContentDto;
use dto\universal\OptionInfoDto;
use dto\universal\QuestionInfoDto;
use framework\response\Response;
use framework\response\ResponseModel;

require_once 'dao/QuestionnaireDaoImpl.php';
require_once 'dao/QuestionDaoImpl.php';
require_once 'dao/OptionDaoImpl.php';
require_once 'service/common/QuestionnaireBasicService.php';
require_once 'dto/response/user/QuestionnaireContentDto.php';
require_once 'dto/common/QuestionInfoDto.php';
require_once 'dto/common/OptionInfoDto.php';

class QuestionnnaireAnswerService extends QuestionnaireBasicService {

    public function __construct() {
        parent::__construct();
    }

    public function get_content(int $qnid): ResponseModel {
        $qn = $this->get_questionnaire($qnid);

        $qn_content_dto = new QuestionnaireContentDto(
            $qn->get_id(),
            $qn->get_title()
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
        $qn_content_dto->set_question($q_dto_arr);

        return Response::success($qn_content_dto);
    }

}