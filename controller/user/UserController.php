<?php

namespace user;

use dto\request\user\UserInfoDto;
use dto\request\user\SigninReqDto;
use framework\exception\JSONSerializeException;
use framework\RequestMapping;
use framework\response\Response;
use framework\response\ResponseModel;
use framework\util\JSON;
use security\RequireAuthority;
use UserService;

require_once 'dto/request/user/UserInfoDto.php';
require_once 'dto/request/user/SigninReqDto.php';
require_once 'service/user/UserService.php';

class UserController {

    private UserService $user_service;

    public function __construct() {
        $this->user_service = new UserService();
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
        return $this->user_service->user_signin($signin_dto);
    }

    #[RequestMapping('PUT', '/user-api/users/*')]
    #[RequireAuthority('User')]
    public function update_user($uri_arr, $uri_query_map, $body): ResponseModel {
        try {
            $update_dto = JSON::unserialize($body, UserInfoDto::class);
        } catch (JSONSerializeException) {
            echo 'a';
            return Response::invalid_argument();
        }
        if (intval($uri_arr[2]) !== $GLOBALS['USER']->get_id()) {
            return Response::permission_denied();
        }
        return $this->user_service->user_update($update_dto);
    }

}