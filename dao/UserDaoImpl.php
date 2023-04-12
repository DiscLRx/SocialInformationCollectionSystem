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

    /**
     * @inheritDoc
     */
    public function select(): array {
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

    /**
     * @inheritDoc
     */
    public function select_by_id(int $id): ?User {
        $stmt = $this->db->prepare('SELECT * FROM user WHERE id=:id');
        $stmt->bindParam('id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $fetch_ret = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($fetch_ret === false) {
            return null;
        }
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

    /**
     * @inheritDoc
     */
    public function select_by_username(string $username): ?User {
        $stmt = $this->db->prepare('SELECT * FROM user WHERE username=:username');
        $stmt->bindParam('username', $username);
        $stmt->execute();
        $fetch_ret = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($fetch_ret === false) {
            return null;
        }
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

    /**
     * @inheritDoc
     */
    public function insert(string $username, string $password, string $nickname, string $phone, string $authority): int {
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

    /**
     * @inheritDoc
     */
    public function update_by_id(int $id, string $username, string $password, string $nickname, string $phone, string $authority, bool $enable): int {
        $stmt = $this->db->prepare(
            'UPDATE user SET username=:username, password=:password, nickname=:nickname, phone=:phone, authority=:authority, enable=:enable WHERE id=:id'
        );
        $stmt->bindParam('username', $username);
        $stmt->bindParam('password', $password);
        $stmt->bindParam('nickname', $nickname);
        $stmt->bindParam('phone', $phone);
        $stmt->bindParam('authority', $authority);
        $stmt->bindParam('enable', $enable, PDO::PARAM_BOOL);
        $stmt->bindParam('id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * @inheritDoc
     */
    public function update_by_id_ex_password(int $id, string $username, string $nickname, string $phone, string $authority, bool $enable): int {
        $stmt = $this->db->prepare(
            'UPDATE user SET username=:username, nickname=:nickname, phone=:phone, authority=:authority, enable=:enable WHERE id=:id'
        );
        $stmt->bindParam('username', $username);
        $stmt->bindParam('nickname', $nickname);
        $stmt->bindParam('phone', $phone);
        $stmt->bindParam('authority', $authority);
        $stmt->bindParam('enable', $enable, PDO::PARAM_BOOL);
        $stmt->bindParam('id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * @inheritDoc
     */
    public function update_enable_by_id(int $id, bool $enable): int {
        $stmt = $this->db->prepare('UPDATE user SET enable=:enable WHERE id=:id');
        $stmt->bindParam('enable', $enable, PDO::PARAM_BOOL);
        $stmt->bindParam('id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

}