<?php

namespace common;

use dao\UserDao;
use dao\UserDaoImpl;
use dto\request\user\SigninReqDto;
use dto\response\user\SigninResDto;
use entity\User;
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
        $user = $this->auth($signin_dto, 'Admin');
        if (!isset($user)){
            return Response::permission_denied('用户名或密码错误');
        }
        if (!$user->is_enable()){
            return Response::permission_denied('用户不可用');
        }
        return $this->on_success($user);
    }

    public function user_signin(SigninReqDto $signin_dto): ResponseModel {
        $user = $this->auth($signin_dto, 'User');
        if (!isset($user)){
            return Response::permission_denied('用户名或密码错误');
        }
        if (!$user->is_enable()){
            return Response::permission_denied('用户不可用');
        }
        return $this->on_success($user);
    }

    private function auth(SigninReqDto $signin_dto, string $authority): ?User{
        $username = trim($signin_dto->get_username(), ' ');
        $password = trim($signin_dto->get_password(), ' ');
        $user = $this->user_dao->select_by_username($username);
        if (!isset($user) || $user->get_authority() !== $authority) {
            return null;
        }
        if (!password_verify($password, $user->get_password())) {
            return null;
        }
        return $user;
    }

    private function on_success($user): ResponseModel {
        $user->set_password(null);
        $uid = $user->get_id();
        $this->redis->set("uid_{$uid}", JSON::serialize($user));
        $token = $this->get_token($uid);
        return Response::success(
            new SigninResDto($uid, $user->get_nickname(), $token)
        );
    }

    private function get_token($uid): string {
        $jwt = (new TokenAuthConfigLoader())->get_jwt();
        $payload = array(
            "uid" => $uid,
            "ts" => Time::current_ts()
        );
        return $jwt->create($payload);
    }

}