<?php

namespace common;

use dao\UserDao;
use dao\UserDaoImpl;
use dto\request\user\SigninReqDto;
use dto\response\user\SigninResDto;
use framework\RedisExecutor;
use framework\response\Response;
use framework\response\ResponseModel;
use framework\util\JSON;
use framework\util\Time;
use security\TokenAuthConfigLoader;

require_once 'dao/UserDaoImpl.php';
require_once 'dto/response/user/SigninResDto.php';
require_once 'security/TokenAuthConfigLoader.php';

class AuthenticationService {

    private UserDao $user_dao;
    private RedisExecutor $redis;

    public function __construct() {
        $this->user_dao = new UserDaoImpl();
        $this->redis = new RedisExecutor(0);
    }

    public function admin_signin(SigninReqDto $signin_dto): ResponseModel {
        $username = trim($signin_dto->get_username(), ' ');
        $password = trim($signin_dto->get_password(), ' ');
        $user = $this->user_dao->select_by_username($username);
        if (!isset($user) || $user->get_authority() !== 'Admin') {
            return Response::permission_denied('用户名或密码错误');
        }
        if (password_verify($password, $user->get_password())) {
            return $this->get_response_on_success($user);
        } else {
            return Response::permission_denied('用户名或密码错误');
        }

    }

    public function user_signin(SigninReqDto $signin_dto): ResponseModel {
        $username = trim($signin_dto->get_username(), ' ');
        $password = trim($signin_dto->get_password(), ' ');
        $user = $this->user_dao->select_by_username($username);
        if (!isset($user)) {
            return Response::permission_denied('用户名或密码错误');
        }

        if (password_verify($password, $user->get_password())) {
            $user->set_password(null);
            $this->redis->set("uid_{$user->get_id()}", JSON::serialize($user));
            return $this->get_response_on_success($user);
        } else {
            return Response::permission_denied('用户名或密码错误');
        }
    }


    private function get_response_on_success($user): ResponseModel {
        $uid = $user->get_id();
        $jwt = (new TokenAuthConfigLoader())->get_jwt();
        $payload = array(
            "uid" => $uid,
            "ts" => Time::current_ts()
        );
        $token = $jwt->create($payload);

        return Response::success(
            new SigninResDto($uid, $user->get_nickname(), $token)
        );
    }

}