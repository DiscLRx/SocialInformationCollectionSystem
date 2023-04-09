<?php

namespace framework\response;

require_once 'ResponseModel.php';
require_once 'ResponseDataModel.php';

class Response {

    public static function http404() {
        http_response_code(404);
        return null;
    }

    public static function http405() {
        http_response_code(405);
        return null;
    }

    public static function http500() {
        http_response_code(500);
        return null;
    }

    public static function success($data = null): ResponseModel {
        return self::set_response(0, $data);
    }

    public static function permission_denied($data = null): ResponseModel {
        return self::set_response(1, $data);
    }

    public static function invalid_argument($data = null): ResponseModel {
        return self::set_response(2, $data);
    }

    public static function unknown_error($data = null): ResponseModel {
        return self::set_response(5, $data);
    }

    private static function set_response(int $code, $data = null): ResponseModel {
        http_response_code(200);
        if (isset($data)) {
            return new ResponseDataModel($code, $data);
        } else {
            return new ResponseModel($code);
        }
    }

}