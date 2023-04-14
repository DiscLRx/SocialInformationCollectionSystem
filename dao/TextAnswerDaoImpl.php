<?php

namespace dao;

use entity\TextAnswer;
use framework\PDOExecutor;
use PDO;

require_once 'dao/TextAnswerDao.php';

class TextAnswerDaoImpl extends PDOExecutor implements TextAnswerDao {

    public function __construct(?PDO $db = null) {
        parent::__construct($db);
    }

    /**
     * @inheritDoc
     */
    public function select_by_questionid_userid(int $question_id, int $user_id): ?TextAnswer {
        $stmt = $this->db->prepare(
            'SELECT * FROM text_answer WHERE question_id=:question_id AND user_id=:user_id'
        );
        $stmt->bindParam('question_id', $question_id);
        $stmt->bindParam('user_id', $user_id);
        $stmt->execute();
        $fetch_ret = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($fetch_ret === false) {
            return null;
        }
        return new TextAnswer(
            $fetch_ret['question_id'],
            $fetch_ret['user_id'],
            $fetch_ret['text']
        );
    }

    /**
     * @inheritDoc
     */
    public function select_by_questionid(int $question_id): array {
        $stmt = $this->db->prepare(
            'SELECT * FROM text_answer WHERE question_id=:question_id'
        );
        $stmt->bindParam('question_id', $question_id, PDO::PARAM_INT);
        $stmt->execute();
        $fetch_ret = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(function ($item) {
            return new TextAnswer(
                $item['question_id'],
                $item['user_id'],
                $item['text']
            );
        }, $fetch_ret);
    }

    /**
     * @inheritDoc
     */
    public function select_by_userid(int $user_id): array {
        $stmt = $this->db->prepare(
            'SELECT * FROM text_answer WHERE user_id=:user_id'
        );
        $stmt->bindParam('user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $fetch_ret = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(function ($item) {
            return new TextAnswer(
                $item['question_id'],
                $item['user_id'],
                $item['text']
            );
        }, $fetch_ret);
    }

    /**
     * @inheritDoc
     */
    public function insert(int $question_id, int $user_id, string $text): int {
        $stmt = $this->db->prepare(
            'INSERT INTO text_answer(question_id, user_id, text) VALUES (:question_id, :user_id, :text)'
        );
        $stmt->bindParam('question_id', $question_id, PDO::PARAM_INT);
        $stmt->bindParam('user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam('text', $text);
        $stmt->execute();
        return $stmt->rowCount();
    }
}