<?php

namespace dao;

use entity\User;
use framework\PDOExecutor;
use PDO;

require_once 'dao/UserDao.php';

class UserDaoImpl extends PDOExecutor implements UserDao {

    public function __construct() {
        parent::__construct();
    }

    public function select_all_user(): array {
        $stmt = $this->db->query('SELECT * FROM user');
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

    public function select_user_by_id(int $id): User {
        $stmt = $this->db->prepare('SELECT * FROM user WHERE id=:id');
        $stmt->bindParam('id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $fetch_ret = $stmt->fetch(PDO::FETCH_ASSOC);
        return new User(
            $fetch_ret['id'],
            $fetch_ret['username'],
            $fetch_ret['password'],
            $fetch_ret['nickname'],
            $fetch_ret['phone'],
            $fetch_ret['authority'],
            $fetch_ret['enable']
        );
    }

    public function insert_user(string $username, string $password, string $nickname, string $phone, string $authority): int {
        $stmt = $this->db->prepare(
            'INSERT INTO user(username, password, nickname, phone, authority) VALUES (:username, :password, :nickname, :phone, :authority)'
        );
        $stmt->bindParam('username', $username);
        $stmt->bindParam('password', $password);
        $stmt->bindParam('nickname', $nickname);
        $stmt->bindParam('phone', $phone);
        $stmt->bindParam('authority', $authority);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function update_user(int $id, string $username, string $password, string $nickname, string $phone, string $authority, bool $enable): int {
        $stmt = $this->db->prepare(
            'UPDATE user SET username=:username, password=:password, nickname=:nickname, phone=:phone, authority=:authority, enable=:enable WHERE id=:id'
        );
        $stmt->bindParam('id', $id, PDO::PARAM_INT);
        $stmt->bindParam('username', $username);
        $stmt->bindParam('password', $password);
        $stmt->bindParam('nickname', $nickname);
        $stmt->bindParam('phone', $phone);
        $stmt->bindParam('authority', $authority);
        $stmt->bindParam('enable', $enable, PDO::PARAM_BOOL);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function update_user_enable(int $id, bool $enable): int {
        $stmt = $this->db->prepare('UPDATE user SET enable=:enable WHERE id=:id');
        $stmt->bindParam('enable', $enable, PDO::PARAM_BOOL);
        $stmt->bindParam('id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }
}