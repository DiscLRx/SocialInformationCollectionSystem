<?php

namespace framework\response;

use Exception;
use framework\response\ResponseModel;
use framework\response\ResponseStringModel;
use framework\response\ResponseObjectModel;

require_once 'ResponseModel.php';
require_once 'ResponseStringModel.php';
require_once 'ResponseObjectModel.php';

class Response {


    public static function http404(): void {
        http_response_code(404);
        exit();
    }

    public static function http405(): void {
        http_response_code(405);
        exit();
    }

    public static function http500(): void {
        http_response_code(500);
        exit();
    }

    public static function success($data = NULL): void {
        http_response_code(200);
        if ($data == NULL){
            $response_result = new ResponseModel(0);
        } else if (is_string($data)) {
            $response_result = new ResponseStringModel(0, $data);
        } else {
            $response_result = new ResponseObjectModel(0, $data);
        }
        echo json_encode($response_result);
        exit();
    }

    public static function permission_denied() : void{
        http_response_code(200);
        $response_result =  new ResponseModel(1);
        echo json_encode($response_result);
        exit();
    }


}