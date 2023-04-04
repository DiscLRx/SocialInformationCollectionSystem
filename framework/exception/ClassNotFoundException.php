<?php

namespace framework\exception;

use framework\log\LogLevel;

class ClassNotFoundException extends LoggerException {
    public function __construct(string $class_name, LogLevel $level = LogLevel::FATAL){
        $msg = "找不到类 \"{$class_name}\"";
        parent::__construct($msg, $level);
    }
}