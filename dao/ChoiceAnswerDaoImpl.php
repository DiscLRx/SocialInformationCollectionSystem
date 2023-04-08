<?php

namespace dao;

use entity\Option;
use entity\User;
use framework\PDOExecutor;
use PDO;

require_once 'dao/ChoiceAnswerDao.php';

class ChoiceAnswerDaoImpl extends PDOExecutor implements ChoiceAnswerDao {

    /**
     * @inheritDoc
     */
    public function select_user_by_optionid(int $option_id): array {
        $stmt = $this->db->prepare(
            'SELECT user.* FROM user, choice_answer WHERE choice_answer.option_id=:option_id AND user.id=choice_answer.user_id'
        );
        $stmt->bindParam('option_id', $option_id, PDO::PARAM_INT);
        $stmt->execute();
        $fetch_ret = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(function ($item) {
            return new User(
                $item['id'],
                $item['username'],
                $item['password'],
                $item['nickname'],
                $item['phone'],
                $item['authority'],
                $item['enable']
            );
        }, $fetch_ret);
    }

    /**
     * @inheritDoc
     */
    public function select_option_by_userid(int $user_id): array {
        $stmt = $this->db->prepare(
            'SELECT `option`.* FROM `option`, choice_answer WHERE choice_answer.user_id=:user_id AND `option`.id=choice_answer.option_id'
        );
        $stmt->bindParam('user_id', $user_id, PDO::PARAM_INT);
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
    public function insert(int $option_id, int $user_id): int {
        $stmt = $this->db->prepare(
            'INSERT INTO choice_answer(option_id, user_id) VALUES (:option_id, :user_id)'
        );
        $stmt->bindParam('option_id', $option_id, PDO::PARAM_INT);
        $stmt->bindParam('user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }
}