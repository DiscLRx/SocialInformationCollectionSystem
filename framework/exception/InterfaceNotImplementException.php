<?php

namespace framework\exception;

use framework\log\LogLevel;

class InterfaceNotImplementException extends LoggerException {
    public function __construct(string $interface_name ,string $class_name, LogLevel $level = LogLevel::FATAL){
        $msg = "类 \"{$class_name}\" 未实现接口 \"{$interface_name}\"";
        parent::__construct($msg, $level);
    }
}