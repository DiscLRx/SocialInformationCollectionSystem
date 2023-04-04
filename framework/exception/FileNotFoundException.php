<?php

namespace framework\exception;

use framework\log\LogLevel;

class FileNotFoundException extends LoggerException {
    public function __construct(string|null $file_name, LogLevel $level = LogLevel::FATAL){
        if ($file_name===null){
            $msg = "文件名为 null";
        } else {
            $msg = "找不到文件 \"{$file_name}\"";
        }
        parent::__construct($msg, $level);
    }
}