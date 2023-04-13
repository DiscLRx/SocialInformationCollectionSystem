<?php

use dao\UserDao;
use dao\UserDaoImpl;
use dto\request\user\UserInfoDto;
use dto\request\user\SigninReqDto;
use dto\response\user\SigninResDto;
use entity\User;
use framework\exception\DatabaseException;
use framework\RedisExecutor;
use framework\response\Response;
use framework\response\ResponseModel;
use framework\util\JSON;
use framework\util\Time;
use security\TokenAuthConfigLoader;

require_once 'dao/UserDaoImpl.php';
require_once 'dto/response/user/SigninResDto.php';
require_once 'security/TokenAuthConfigLoader.php';

class UserService {

    private UserDao $user_dao;
    private RedisExecutor $redis;

    public function __construct() {
        $this->user_dao = new UserDaoImpl();
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

    public function user_signin(SigninReqDto $signin_dto): ResponseModel {
        $username = trim($signin_dto->get_username(), ' ');
        $password = trim($signin_dto->get_password(), ' ');

        $user = $this->user_dao->select_by_username($username);

        $ret = false;
        if (isset($user)) {
            $ret = password_verify($password, $user->get_password());
        }

        if ($ret) {
            $uid = $user->get_id();

            $jwt = (new TokenAuthConfigLoader())->get_jwt();
            $payload = array(
                "uid" => $uid,
                "ts" => Time::current_ts()
            );
            $token = $jwt->create($payload);

            $user->set_password(null);
            $this->redis->set("uid_{$uid}", JSON::serialize($user));

            return Response::success(
                new SigninResDto($uid, $user->get_nickname(), $token)
            );
        } else {
            return Response::permission_denied('用户名或密码错误');
        }
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

}