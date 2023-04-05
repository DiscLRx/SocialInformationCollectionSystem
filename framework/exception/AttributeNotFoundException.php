<?php

namespace framework\exception;

use framework\log\LogLevel;

class AttributeNotFoundException extends LoggerException {
    public function __construct(string $attribute_name, string $class_name, string $func_name, LogLevel $level = LogLevel::FATAL){
        $msg = "在方法 {$class_name}->{$func_name} 上找不到注解 {$attribute_name}";
        parent::__construct($msg, $level);
    }
}