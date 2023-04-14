<?php

namespace dao;

use framework\PDOExecutor;
use PDO;

require_once 'dao/AnswerRecordDao.php';

class AnswerRecordDaoImpl extends PDOExecutor implements AnswerRecordDao {

    public function __construct(?PDO $db = null) {
        parent::__construct($db);
    }

    /**
     * @inheritDoc
     */
    public function select_questionnaireid_by_userid(int $user_id): array {
        $stmt = $this->db->prepare(
            'SELECT questionnaire_id FROM answer_record WHERE user_id=:user_id'
        );
        $stmt->bindParam('user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $fetch_ret = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(function ($item) {
            return $item['questionnaire_id'];
        }, $fetch_ret);
    }

    /**
     * @inheritDoc
     */
    public function select_userid_by_questionnaireid(int $questionnaire_id): array {
        $stmt = $this->db->prepare(
            'SELECT user_id FROM answer_record WHERE questionnaire_id=:questionnaire_id'
        );
        $stmt->bindParam('questionnaire_id', $questionnaire_id, PDO::PARAM_INT);
        $stmt->execute();
        $fetch_ret = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(function ($item) {
            return $item['questionnaire_id'];
        }, $fetch_ret);
    }

    /**
     * @inheritDoc
     */
    public function count_by_userid(int $user_id): int {
        $stmt = $this->db->prepare(
            'SELECT count(*) as `count` FROM answer_record WHERE user_id=:user_id'
        );
        $stmt->bindParam('user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $fetch_ret = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fetch_ret['count'];
    }

    /**
     * @inheritDoc
     */
    public function count_by_questionnaireid(int $questionnaire_id): int {
        $stmt = $this->db->prepare(
            'SELECT count(*) as `count` FROM answer_record WHERE questionnaire_id=:questionnaire_id'
        );
        $stmt->bindParam('questionnaire_id', $questionnaire_id, PDO::PARAM_INT);
        $stmt->execute();
        $fetch_ret = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fetch_ret['count'];
    }

    /**
     * @inheritDoc
     */
    public function count_by_userid_questionnaireid(int $user_id, int $questionnaire_id): int {
        $stmt = $this->db->prepare(
            'SELECT count(*) as `count` FROM answer_record WHERE user_id=:user_id AND questionnaire_id=:questionnaire_id'
        );
        $stmt->bindParam('user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam('questionnaire_id', $questionnaire_id, PDO::PARAM_INT);
        $stmt->execute();
        $fetch_ret = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fetch_ret['count'];
    }

    /**
     * @inheritDoc
     */
    public function insert(int $user_id, int $questionnaire_id): int {
        $stmt = $this->db->prepare(
            'INSERT INTO answer_record(user_id, questionnaire_id) VALUES (:user_id, :questionnaire_id)'
        );
        $stmt->bindParam('user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam('questionnaire_id', $questionnaire_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }
}