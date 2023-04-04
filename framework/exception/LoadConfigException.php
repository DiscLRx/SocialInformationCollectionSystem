<?php

namespace framework\exception;

use framework\log\LogLevel;

class LoadConfigException extends LoggerException {
    public function __construct(string $class_name, LogLevel $level = LogLevel::FATAL){
        parent::__construct($class_name, $level);
    }
}