<?php

use dao\AnswerRecordDao;
use dao\AnswerRecordDaoImpl;
use dao\UserDao;
use dao\UserDaoImpl;
use dto\request\user\UserInfoDto;
use dto\response\admin\UserManageDisplayDto;
use dto\response\user\AnsweredQnidDto;
use entity\User;
use framework\exception\DatabaseException;
use framework\RedisExecutor;
use framework\response\Response;
use framework\response\ResponseModel;
use framework\util\JSON;

require_once 'dao/UserDaoImpl.php';
require_once 'dao/AnswerRecordDaoImpl.php';
require_once 'dto/response/user/AnsweredQnidDto.php';
require_once 'dto/response/admin/UserManageDisplayDto.php';

class UserService {

    private UserDao $user_dao;
    private AnswerRecordDao $answer_record_dao;
    private RedisExecutor $redis;

    public function __construct() {
        $this->user_dao = new UserDaoImpl();
        $this->answer_record_dao = new AnswerRecordDaoImpl();
        $this->redis = new RedisExecutor(0);
    }


    public function user_signup(UserInfoDto $signup_dto): ResponseModel {

        $username = trim($signup_dto->get_username(), ' ');
        $password = trim($signup_dto->get_password(), ' ');
        $nickname = trim($signup_dto->get_nickname(), ' ');
        $phone = trim($signup_dto->get_phone(), ' ');

        if (empty($username) || empty($password) || empty($nickname) || empty($phone)){
            return Response::invalid_argument();
        }

        $check_ret =
            $this->username_check($username) &&
            $this->password_check($password) &&
            $this->nickame_check($nickname) &&
            $this->phone_check($phone);
        if (!$check_ret) {
            return Response::invalid_argument();
        }

        $user = $this->user_dao->select_by_username($username);
        if (isset($user)) {
            return Response::invalid_argument('用户名已存在');
        }

        $password = password_hash($password, PASSWORD_ARGON2ID);

        $ins_ret = $this->user_dao->insert($username, $password, $nickname, $phone, "User");
        if ($ins_ret === 0) {
            throw new DatabaseException("用户表插入失败");
        }

        return Response::success();
    }

    private function username_check($username): bool {
        $flag =
            strlen($username) > 30 ||
            str_contains($username, ' ');
        return !$flag;
    }

    private function password_check($password): bool {
        $flag =
            strlen($password) > 30 ||
            str_contains($password, ' ');
        return !$flag;
    }

    private function nickame_check($nickame): bool {
        $flag =
            strlen($nickame) > 30 ||
            str_contains($nickame, ' ');
        return !$flag;
    }

    private function phone_check($phone): bool {
        $flag =
            strlen($phone) > 20 ||
            !is_numeric($phone);
        return !$flag;
    }

    public function user_update(UserInfoDto $update_dto): ResponseModel {

        $username = trim($update_dto->get_username(), ' ');
        $password = trim($update_dto->get_password(), ' ');
        $nickname = trim($update_dto->get_nickname(), ' ');
        $phone = trim($update_dto->get_phone(), ' ');

        $check_ret =
            $this->username_check($username) &&
            $this->password_check($password) &&
            $this->nickame_check($nickname) &&
            $this->phone_check($phone);
        if (!$check_ret) {
            return Response::invalid_argument();
        }

        $auth_user = $GLOBALS['USER'];
        $search_user = $this->user_dao->select_by_username($username);
        if (isset($search_user) && $search_user->get_username()!==$auth_user->get_username()) {
            return Response::invalid_argument('用户名已存在');
        }

        $diff = false;

        $uid = $auth_user->get_id();
        if (empty($username)){
            $username = $auth_user->get_password();
        } else {
            if (!$this->username_check($username)){
                return Response::invalid_argument();
            }
            $diff = true;
        }
        if (empty($nickname)){
            $nickname = $auth_user->get_nickname();
        } else {
            if (!$this->nickame_check($nickname)){
                return Response::invalid_argument();
            }
            $diff = true;
        }
        if (empty($phone)){
            $phone = $auth_user->phone();
        } else {
            if (!$this->phone_check($phone)){
                return Response::invalid_argument();
            }
            $diff = true;
        }
        if (empty($password)){
            $line = $this->user_dao->update_by_id_ex_password($uid, $username, $nickname, $phone, "User", true);
        } else {
            if (!$this->password_check($password)){
                return Response::invalid_argument();
            }
            $diff = true;
            $password = password_hash($password, PASSWORD_ARGON2ID);
            $line = $this->user_dao->update_by_id($uid, $username, $password, $nickname, $phone, "User", true);
        }
        if ($diff && $line !== 1) {
            return Response::unknown_error();
        }

        $user = new User($uid, $username, null, $nickname, $phone, "User", true);
        $this->redis->set("uid_{$uid}", JSON::serialize($user));

        return Response::success();
    }

    public function get_answered_questionnaireid(): ResponseModel {
        $uid = $GLOBALS['USER']->get_id();
        $qnid_arr = $this->answer_record_dao->select_questionnaireid_by_userid($uid);
        $aqnid_dto = new AnsweredQnidDto($qnid_arr);
        return Response::success($aqnid_dto);
    }

    public function get_all_users(): ResponseModel{
        $user_arr = $this->user_dao->select();
        $umd_arr = array_map(
            fn($user) => new UserManageDisplayDto(
                $user->get_id(),
                $user->get_username(),
                $user->get_nickname(),
                $user->get_phone(),
                $user->is_enable()
            ) ,$user_arr);
        return Response::success($umd_arr);
    }

}