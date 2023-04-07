<?php

namespace exception;

use framework\exception\LoggerException;
use framework\log\LogLevel;

require_once 'framework/exception/LoggerException.php';

class DatabaseException extends LoggerException {

    public function __construct(string $msg = "", LogLevel $level = LogLevel::ERROR) {
        parent::__construct($msg, $level);
    }

}