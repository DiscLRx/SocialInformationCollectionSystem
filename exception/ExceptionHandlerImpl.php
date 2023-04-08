<?php

namespace exception;

use Error;
use Exception;
use framework\exception\LoggerException;
use framework\ExceptionHandler;
use framework\response\Response;
use framework\response\ResponseModel;

class ExceptionHandlerImpl implements ExceptionHandler {


    /**
     * @throws Exception 未定义异常
     */
    public function handle(Exception|Error $e): ResponseModel {
        if ($e instanceof LoggerException) {
            $e->log_trace();
            return Response::unknown_error();
        } else {
            throw $e;
        }
    }

}