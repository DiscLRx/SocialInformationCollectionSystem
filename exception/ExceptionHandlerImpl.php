<?php

namespace exception;

use Error;
use Exception;
use framework\exception\LoggerException;
use framework\ExceptionHandler;

class ExceptionHandlerImpl implements ExceptionHandler {


    /**
     * @throws Exception 未定义异常
     */
    public function handle(Exception|Error $e) {
        if ($e instanceof LoggerException) {
            $e->log_trace();
        } else {
            throw $e;
        }
    }

}