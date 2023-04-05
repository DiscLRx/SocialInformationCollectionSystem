<?php

namespace framework\exception;

use framework\log\LogLevel;

class LoadConfigException extends LoggerException {
    public function __construct(string $msg, LogLevel $level = LogLevel::FATAL){
        parent::__construct($msg, $level);
    }
}