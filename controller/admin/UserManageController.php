<?php

namespace admin;

use dto\request\admin\UserManageUpdateDto;
use entity\User;
use framework\exception\JSONSerializeException;
use framework\RequestMapping;
use framework\response\Response;
use framework\response\ResponseModel;
use framework\util\JSON;
use security\RequireAuthority;
use UserService;

require_once 'dto/request/admin/UserManageUpdateDto.php';
require_once 'service/user/UserService.php';

class UserManageController {

    private UserService $user_service;

    public function __construct() {
        $this->user_service = new UserService();
    }

    #[RequestMapping('GET', '/admin-api/users')]
    #[RequireAuthority('Admin')]
    public function get_all_users($uri_arr, $uri_query_map, $body) : ResponseModel{
        return $this->user_service->get_all_users();
    }

    #[RequestMapping('PUT', '/admin-api/users/*')]
    #[RequireAuthority('Admin')]
    public function update_user($uri_arr, $uri_query_map, $body): ResponseModel {
        try {
            $update_dto = JSON::unserialize($body, UserManageUpdateDto::class);
        } catch (JSONSerializeException) {
            return Response::invalid_argument();
        }
        $uid = $uri_arr[2];
        if (!is_numeric($uid)){
            Response::invalid_argument();
        }
        $user = new User(
            $uid,
            $update_dto->get_username(),
            $update_dto->get_password(),
            $update_dto->get_nickname(),
            $update_dto->get_phone(),
            'User',
            $update_dto->is_enable()
        );
        return $this->user_service->user_manage_update($user);
    }

}