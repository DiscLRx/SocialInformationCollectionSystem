<?php

namespace framework\exception;

use framework\log\LogLevel;

class DatabaseException extends LoggerException {

    public function __construct(string $msg = "", LogLevel $level = LogLevel::ERROR) {
        parent::__construct($msg, $level);
    }

}