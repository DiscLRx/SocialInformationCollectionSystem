<?php

namespace dao;

use entity\Option;
use framework\PDOExecutor;
use PDO;

require_once 'dao/OptionDao.php';

class OptionDaoImpl extends PDOExecutor implements OptionDao {

    public function __construct() {
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    public function select_by_id(int $id): ?Option {
        $stmt = $this->db->prepare('SELECT * FROM `option` WHERE id=:id');
        $stmt->bindParam('id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $fetch_ret = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($fetch_ret === false) {
            return null;
        }
        return new Option(
            $fetch_ret['id'],
            $fetch_ret['question_id'],
            $fetch_ret['order'],
            $fetch_ret['content']
        );
    }

    /**
     * @inheritDoc
     */
    public function select_by_questionid(int $question_id): array {
        $stmt = $this->db->prepare('SELECT * FROM `option` WHERE question_id=:question_id');
        $stmt->bindParam('question_id', $question_id, PDO::PARAM_INT);
        $stmt->execute();
        $fetch_ret = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(function ($item) {
            return new Option(
                $item['id'],
                $item['question_id'],
                $item['order'],
                $item['content']
            );
        }, $fetch_ret);
    }

    /**
     * @inheritDoc
     */
    public function insert(int $question_id, int $order, string $content): int {
        $stmt = $this->db->prepare(
            'INSERT INTO `option`(question_id, `order`, content) VALUES (:question_id, :order, :content)'
        );
        $stmt->bindParam('question_id', $question_id, PDO::PARAM_INT);
        $stmt->bindParam('order', $order, PDO::PARAM_INT);
        $stmt->bindParam('content', $content);
        $stmt->execute();
        if ($stmt->rowCount()===0){
            return 0;
        } else {
            return $this->db->lastInsertId();
        }
    }

    /**
     * @inheritDoc
     */
    public function update_by_id(int $id, int $question_id, int $order, string $content): int {
        $stmt = $this->db->prepare(
            'UPDATE `option` SET id=:id, question_id=:question_id, `order`=:order, content=:content WHERE id=:id'
        );
        $stmt->bindParam('question_id', $question_id, PDO::PARAM_INT);
        $stmt->bindParam('order', $order, PDO::PARAM_INT);
        $stmt->bindParam('content', $content);
        $stmt->bindParam('id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }
}