<?php

namespace user;

use common\AuthenticationService;
use dto\universal\UserInfoDto;
use dto\request\user\SigninReqDto;
use entity\User;
use framework\exception\JSONSerializeException;
use framework\RequestMapping;
use framework\response\Response;
use framework\response\ResponseModel;
use framework\util\JSON;
use security\RequireAuthority;
use UserService;

require_once 'dto/common/UserInfoDto.php';
require_once 'dto/request/user/SigninReqDto.php';
require_once 'service/user/UserService.php';
require_once 'service/common/AuthenticationService.php';

class UserController {

    private UserService $user_service;
    private AuthenticationService $auth_service;

    public function __construct() {
        $this->user_service = new UserService();
        $this->auth_service = new AuthenticationService();
    }

    #[RequestMapping('POST', '/user-api/users')]
    #[RequireAuthority('PermitAll')]
    public function user_signup($uri_arr, $uri_query_map, $body): ResponseModel {
        try {
            $signup_dto = JSON::unserialize($body, UserInfoDto::class);
        } catch (JSONSerializeException) {
            return Response::invalid_argument();
        }
        return $this->user_service->user_signup($signup_dto);
    }

    #[RequestMapping('POST', '/user-api/users/auth')]
    #[RequireAuthority('PermitAll')]
    public function user_signin($uri_arr, $uri_query_map, $body): ResponseModel {
        try {
            $signin_dto = JSON::unserialize($body, SigninReqDto::class);
        } catch (JSONSerializeException) {
            return Response::invalid_argument();
        }
        return $this->auth_service->user_signin($signin_dto);
    }

    #[RequestMapping('PUT', '/user-api/users/*')]
    #[RequireAuthority('User')]
    public function update_user($uri_arr, $uri_query_map, $body): ResponseModel {
        try {
            $update_dto = JSON::unserialize($body, UserInfoDto::class);
        } catch (JSONSerializeException) {
            return Response::invalid_argument();
        }
        $uid = $GLOBALS['USER']->get_id();
        if (intval($uri_arr[2]) !== $uid) {
            return Response::permission_denied();
        }
        $user = new User(
            $uid,
            $update_dto->get_username(),
            $update_dto->get_password(),
            $update_dto->get_nickname(),
            $update_dto->get_phone(),
            'User',
            $GLOBALS['USER']->is_enable()
        );
        return $this->user_service->user_update($user);
    }

    #[RequestMapping('GET', '/user-api/users/*/answered')]
    #[RequireAuthority('User')]
    public function get_answered_questionnaireid($uri_arr, $uri_query_map, $body): ResponseModel {
        if (intval($uri_arr[2]) !== $GLOBALS['USER']->get_id()) {
            return Response::permission_denied();
        }
        return $this->user_service->get_answered_questionnaireid();
    }

    #[RequestMapping('GET', '/user-api/users/*')]
    #[RequireAuthority('User')]
    public function get_user($uri_arr, $uri_query_map, $body): ResponseModel {    
        $uid=intval($uri_arr[2]);
        if (intval($uri_arr[2]) !== $GLOBALS['USER']->get_id()) {
            return Response::permission_denied();
        }
        return $this->user_service->get_user($uid);
    }

}