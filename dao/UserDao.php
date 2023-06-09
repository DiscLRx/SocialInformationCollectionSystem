<?php

namespace dao;

use entity\User;

require_once 'entity/User.php';

/**
 * 数据库表user接口
 */
interface UserDao {

    /**
     * 查询所有用户
     * @return array    包含User对象的array
     */
    public function select(): array;

    /**
     * 根据用户id查询用户
     * @param int $id   用户id
     * @return ?User    User对象
     */
    public function select_by_id(int $id): ?User;


    /**
     * 获取所有用户的用户id和用户名
     * @return array    包含用户id与用户名关联数组的array
     */
    public function select_id_username(): array;

    /**
     * 根据用户名查询用户
     * @param string $username  用户名
     * @return ?User            User对象
     */
    public function select_by_username(string $username): ?User;

    /**
     * 新增用户
     * @param string $username  用户名
     * @param string $password  密码
     * @param string $nickname  昵称
     * @param string $phone     电话
     * @param string $authority 权限
     * @return int              受影响的行数为0时返回0，否则返回插入值的自增id
     */
    public function insert(string $username, string $password, string $nickname, string $phone, string $authority): int;

    /**
     * 更新用户信息
     * @param int $id           用户id
     * @param string $username  用户名
     * @param string $password  密码
     * @param string $nickname  昵称
     * @param string $phone     电话
     * @param string $authority 权限
     * @param bool $enable      可用性
     * @return int              受影响的行数
     */
    public function update_by_id(int $id, string $username, string $password, string $nickname, string $phone, string $authority, bool $enable): int;

    /**
     * 更新除密码外的用户信息
     * @param int $id
     * @param string $username
     * @param string $nickname
     * @param string $phone
     * @param string $authority
     * @param bool $enable
     * @return int
     */
    public function update_by_id_ex_password(int $id, string $username, string $nickname, string $phone, string $authority, bool $enable): int;

    /**
     * 更新用户的可用性
     * @param int $id       用户id
     * @param bool $enable  可用性
     * @return int          受影响的行数
     */
    public function update_enable_by_id(int $id, bool $enable): int;

}