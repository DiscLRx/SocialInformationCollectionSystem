<?php

namespace admin;

use common\AuthenticationService;
use dto\request\user\SigninReqDto;
use framework\exception\JSONSerializeException;
use framework\RequestMapping;
use framework\response\Response;
use framework\response\ResponseModel;
use framework\util\JSON;
use security\RequireAuthority;

require_once 'service/common/AuthenticationService.php';
require_once 'dto/request/user/SigninReqDto.php';

class AdminController  {

    private AuthenticationService $auth_service;

    public function __construct() {
        $this->auth_service = new AuthenticationService();
    }

    #[RequestMapping('POST','/admin-api/admin/auth')]
    #[RequireAuthority('PermitAll')]
    public function admin_signin($uri_arr, $uri_query_map, $body): ResponseModel {
        try {
            $signin_dto = JSON::unserialize($body, SigninReqDto::class);
        } catch (JSONSerializeException) {
            return Response::invalid_argument();
        }
        return $this->auth_service->signin($signin_dto);
    }
}