<?php


namespace service\questionnaire;

use common\QuestionnaireBasicService;
use dao\AnswerRecordDao;
use dao\AnswerRecordDaoImpl;
use dao\ChoiceAnswerDao;
use dao\ChoiceAnswerDaoImpl;
use dao\TextAnswerDao;
use dao\TextAnswerDaoImpl;
use dto\request\user\QuestionnaireAnswerDto;
use dto\response\user\QuestionnaireContentDto;
use dto\universal\OptionInfoDto;
use dto\universal\QuestionInfoDto;
use entity\Question;
use framework\exception\DatabaseException;
use framework\response\Response;
use framework\response\ResponseModel;
use framework\util\Time;

require_once 'dao/QuestionnaireDaoImpl.php';
require_once 'dao/QuestionDaoImpl.php';
require_once 'dao/OptionDaoImpl.php';
require_once 'service/common/QuestionnaireBasicService.php';
require_once 'dto/response/user/QuestionnaireContentDto.php';
require_once 'dto/common/QuestionInfoDto.php';
require_once 'dto/common/OptionInfoDto.php';
require_once 'dao/ChoiceAnswerDaoImpl.php';
require_once 'dao/TextAnswerDaoImpl.php';
require_once 'dao/AnswerRecordDaoImpl.php';

class QuestionnaireAnswerService extends QuestionnaireBasicService {

    private ChoiceAnswerDao $choice_answer_dao;
    private TextAnswerDao $text_answer_dao;
    private AnswerRecordDao $answer_record_dao;

    public function __construct() {
        parent::__construct();
        $this->choice_answer_dao = new ChoiceAnswerDaoImpl();
        $db = $this->choice_answer_dao->get_db();
        $this->text_answer_dao = new TextAnswerDaoImpl($db);
        $this->answer_record_dao = new AnswerRecordDaoImpl($db);
    }

    public function get_content(int $qnid): ResponseModel {
        $qn = $this->get_questionnaire($qnid);

        if (!isset($qn)) {
            return Response::invalid_argument();
        }

        $current_ts = Time::current_ts();
        $begin_date = $qn->get_begin_date();
        $end_date = $qn->get_end_date();
        if ($current_ts < $begin_date || $current_ts > $end_date) {
            return Response::invalid_argument('不在问卷有效期内');
        }

        if (!$qn->is_enable()) {
            return Response::reject_request('问卷不可用');
        }

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

    public function submit_answer(QuestionnaireAnswerDto $qna_dto): ResponseModel {

        $uid = $GLOBALS['USER']->get_id();
        $qnid = $qna_dto->get_id();
        $qn = $this->get_questionnaire($qnid);

        if (!isset($qn)) {
            return Response::invalid_argument();
        }

        $current_ts = Time::current_ts();
        $begin_date = $qn->get_begin_date();
        $end_date = $qn->get_end_date();
        if ($current_ts < $begin_date || $current_ts > $end_date) {
            return Response::invalid_argument('不在问卷有效期内');
        }

        if (!$qn->is_enable()) {
            return Response::reject_request('问卷不可用');
        }

        $answer_record = $this->answer_record_dao->count_by_userid_questionnaireid($uid, $qnid);
        if ($answer_record === 1) {
            return Response::reject_request();
        }

        $question_arr = $qn->get_question_arr();
        $this->choice_answer_dao->start_transaction();
        foreach ($qna_dto->get_answers() as $qa_dto) {
            $question = $this->get_question_by_order($question_arr, $qa_dto->get_order());
            if (!isset($question)) {
                $this->choice_answer_dao->rollback();
                return Response::invalid_argument();
            }
            $ret = match ($question->get_type()) {
                'single' => $this->single_type_handler(
                    $question->get_option_arr(),
                    $qa_dto->get_answer(),
                    $uid),
                'multi' => $this->multi_type_handler(
                    $question->get_option_arr(),
                    $qa_dto->get_answer(),
                    $uid),
                'text' => $this->text_type_handler(
                    $question->get_id(),
                    $qa_dto->get_answer(),
                    $uid
                ),
                default => false
            };
            if (!$ret) {
                $this->choice_answer_dao->rollback();
                return Response::invalid_argument();
            }
        }
        $ret = $this->answer_record_dao->insert($uid, $qnid);
        if ($ret !== 1) {
            $this->text_answer_dao->close();
            throw new DatabaseException('插入问卷作答记录失败');
        }
        $this->choice_answer_dao->commit();
        return Response::success();
    }

    private function single_type_handler(array $option_arr, array $answer, $uid): bool {
        if (count($answer) !== 1) {
            return false;
        }
        $order = array_pop($answer);
        foreach ($option_arr as $option) {
            if ($option->get_order() === $order) {
                $target_option = $option;
                break;
            }
        }
        if (!isset($target_option)) {
            return false;
        }
        $ret = $this->choice_answer_dao->insert($target_option->get_id(), $uid);
        if ($ret !== 1) {
            $this->text_answer_dao->close();
            throw new DatabaseException('插入单选题作答记录失败');
        }
        return true;
    }

    private function multi_type_handler(array $option_arr, array $answer, $uid): bool {
        foreach ($answer as $order) {
            $order_exist = false;
            foreach ($option_arr as $index => $option) {
                if ($option->get_order() === $order) {
                    $order_exist = true;
                    $ret = $this->choice_answer_dao->insert($option->get_id(), $uid);
                    if ($ret !== 1) {
                        $this->text_answer_dao->close();
                        throw new DatabaseException('插入多选题作答记录失败');
                    }
                    unset($option_arr[$index]);
                    break;
                }
            }
            if (!$order_exist) {
                return false;
            }
        }
        return true;
    }

    private function text_type_handler(int $question_id, array $answer, int $uid): bool {
        if (count($answer) !== 1) {
            return false;
        }
        $ret = $this->text_answer_dao->insert($question_id, $uid, array_pop($answer));
        if ($ret !== 1) {
            $this->text_answer_dao->close();
            throw new DatabaseException('插入填空题作答记录失败');
        }
        return true;
    }

    private function get_question_by_order(array &$question_arr, int $order): ?Question {
        foreach ($question_arr as $index => $q) {
            if ($q->get_order() === $order) {
                unset($question_arr[$index]);
                return $q;
            }
        }
        return null;
    }

}