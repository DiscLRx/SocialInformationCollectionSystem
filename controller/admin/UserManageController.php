<?php

namespace admin;

use framework\RequestMapping;
use framework\response\Response;
use framework\response\ResponseModel;
use security\RequireAuthority;
use UserService;

require_once 'service/user/UserService.php';

class UserManageController {

    private UserService $user_service;

    public function __construct() {
        $this->user_service = new UserService();
    }

    #[RequestMapping('GET', '/admin-api/users')]
    #[RequireAuthority('Admin')]
    public function get_all_user_brief($uri_arr, $uri_query_map, $body) : ResponseModel{
        return $this->user_service->get_all_user_brief();
    }

}