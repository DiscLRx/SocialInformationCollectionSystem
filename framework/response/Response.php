<?php

namespace framework\response;

require_once 'ResponseModel.php';
require_once 'ResponseDataModel.php';

class Response {

    public static function http404(): null {
        http_response_code(404);
        return null;
    }

    public static function http405(): null {
        http_response_code(405);
        return null;
    }

    public static function http500(): null {
        http_response_code(500);
        return null;
    }

    public static function success($data = null): ResponseModel {
        http_response_code(200);
        if (isset($data)) {
            $response_result = new ResponseDataModel(0, $data);
        } else {
            $response_result = new ResponseModel(0);
        }
        return $response_result;
    }

    public static function permission_denied(): ResponseModel {
        http_response_code(200);
        return new ResponseModel(1);
    }


}