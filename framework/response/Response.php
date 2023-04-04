<?php

namespace framework\response;

require_once 'ResponseModel.php';
require_once 'ResponseDataModel.php';

class Response {

    public static function http404() {
        http_response_code(404);
        return NULL;
    }

    public static function http405() {
        http_response_code(405);
        return NULL;
    }

    public static function http500() {
        http_response_code(500);
        return NULL;
    }

    public static function success($data = NULL): ResponseModel {
        http_response_code(200);
        if ($data == NULL) {
            $response_result = new ResponseModel(0);
        } else {
            $response_result = new ResponseDataModel(0, $data);
        }
        return $response_result;
    }

    public static function permission_denied(): ResponseModel {
        http_response_code(200);
        return new ResponseModel(1);
    }


}