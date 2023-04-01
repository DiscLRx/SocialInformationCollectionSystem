<?php

namespace framework\response;

class response {


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
        $response_result =  $data == NULL ? new response_model(0) : new response_data_model(0, $data);
        echo json_encode($response_result);
        exit();
    }

    public static function permission_denied() : void{
        http_response_code(200);
        $response_result =  new response_model(1);
        echo json_encode($response_result);
        exit();
    }


}