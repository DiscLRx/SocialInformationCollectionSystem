<?php

namespace dao;

use entity\Questionnaire;
use framework\PDOExecutor;
use PDO;

require_once 'dao/QuestionnaireDao.php';

class QuestionnaireDaoImpl extends PDOExecutor implements QuestionnaireDao {

    public function __construct() {
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    public function select_by_id(int $id): ?Questionnaire {
        $stmt = $this->db->prepare('SELECT * FROM questionnaire WHERE id=:id');
        $stmt->bindParam('id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $fetch_ret = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($fetch_ret === false) {
            return null;
        }
        return new Questionnaire(
            $fetch_ret['id'],
            $fetch_ret['user_id'],
            $fetch_ret['title'],
            $fetch_ret['begin_date'],
            $fetch_ret['end_date'],
            $fetch_ret['enable']
        );
    }

    /**
     * @inheritDoc
     */
    public function select_by_userid(int $user_id): array {
        $stmt = $this->db->prepare('SELECT * FROM questionnaire WHERE user_id=:user_id');
        $stmt->bindParam('user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $fetch_ret = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(function ($item) {
            return new Questionnaire(
                $item['id'],
                $item['user_id'],
                $item['title'],
                $item['begin_date'],
                $item['end_date'],
                $item['enable']
            );
        }, $fetch_ret);
    }

    /**
     * @inheritDoc
     */
    public function insert(int $user_id, string $title, int $begin_date, int $end_date): int {
        $stmt = $this->db->prepare(
            'INSERT INTO questionnaire(user_id, title, begin_date, end_date) VALUES (:user_id, :title, :begin_date, :end_date)'
        );
        $stmt->bindParam('user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam('title', $title);
        $stmt->bindParam('begin_date', $begin_date, PDO::PARAM_INT);
        $stmt->bindParam('end_date', $end_date, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount() === 0){
            return 0;
        } else {
            return $this->db->lastInsertId();
        }
    }

    /**
     * @inheritDoc
     */
    public function update_by_id(int $id, int $user_id, string $title, int $begin_date, int $end_date, bool $enable): int {
        $stmt = $this->db->prepare(
            'UPDATE questionnaire SET user_id=:user_id, title=:title, begin_date=:begin_date, end_date=:end_date, enable=:enable WHERE id=:id'
        );
        $stmt->bindParam('id', $id, PDO::PARAM_INT);
        $stmt->bindParam('user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam('title', $title);
        $stmt->bindParam('begin_date', $begin_date, PDO::PARAM_INT);
        $stmt->bindParam('end_date', $end_date, PDO::PARAM_INT);
        $stmt->bindParam('enable', $enable, PDO::PARAM_BOOL);
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * @inheritDoc
     */
    public function update_enable_by_id(int $id, bool $enable): int {
        $stmt = $this->db->prepare('UPDATE questionnaire SET enable=:enable WHERE id=:id');
        $stmt->bindParam('enable', $enable, PDO::PARAM_BOOL);
        $stmt->bindParam('id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

}