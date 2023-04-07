<?php

namespace framework\exception;

use framework\log\LogLevel;

class FileNotFoundException extends LoggerException {
    public function __construct(string|null $file_name, LogLevel $level = LogLevel::FATAL){
        if (isset($file_name)){
            $msg = "找不到文件 \"{$file_name}\"";
        } else {
            $msg = "文件名为 null";
        }
        parent::__construct($msg, $level);
    }
}