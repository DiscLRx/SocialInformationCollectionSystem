<?php

namespace dao;

use entity\Question;
use framework\PDOExecutor;
use PDO;

require_once 'dao/QuestionDao.php';

class QuestionDaoImpl extends PDOExecutor implements QuestionDao {

    /**
     * @inheritDoc
     */
    public function select_by_id(int $id): Question {
        $stmt = $this->db->prepare('SELECT * FROM question WHERE id=:id');
        $stmt->bindParam('id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $fetch_ret = $stmt->fetch(PDO::FETCH_ASSOC);
        return new Question(
            $fetch_ret['id'],
            $fetch_ret['questionnaire_id'],
            $fetch_ret['order'],
            $fetch_ret['type'],
            $fetch_ret['content']
        );
    }

    /**
     * @inheritDoc
     */
    public function select_by_questionnaireid(int $questionnaire_id): array {
        $stmt = $this->db->prepare('SELECT * FROM question WHERE questionnaire_id=:questionnaire_id');
        $stmt->bindParam('questionnaire_id', $questionnaire_id, PDO::PARAM_INT);
        $stmt->execute();
        $fetch_ret = $stmt->fetch(PDO::FETCH_ASSOC);
        return array_map(function ($item) {
            return new Question(
                $item['id'],
                $item['questionnaire_id'],
                $item['order'],
                $item['type'],
                $item['content']
            );
        }, $fetch_ret);
    }

    /**
     * @inheritDoc
     */
    public function insert(int $questionnaire_id, int $order, string $type, string $content): int {
        $stmt = $this->db->prepare(
            'INSERT INTO question(questionnaire_id, `order`, type, content) VALUES (:questionnaire_id, :order, :type, :content)'
        );
        $stmt->bindParam('questionnaire_id', $questionnaire_id);
        $stmt->bindParam('order', $order);
        $stmt->bindParam('type', $type);
        $stmt->bindParam('content', $content);
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * @inheritDoc
     */
    public function update_by_id(int $id, int $questionnaire_id, int $order, string $type, string $content): int {
        $stmt = $this->db->prepare(
            'UPDATE question SET questionnaire_id=:questionnaire_id, `order`=:order, type=:type, content=:content WHERE id=:id'
        );
        $stmt->bindParam('questionnaire_id', $questionnaire_id);
        $stmt->bindParam('order', $order);
        $stmt->bindParam('type', $type);
        $stmt->bindParam('content', $content);
        $stmt->bindParam('id', $id);
        $stmt->execute();
        return $stmt->rowCount();
    }
}