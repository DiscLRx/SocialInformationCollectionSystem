<?php

namespace framework\exception;

use framework\log\Log;
use framework\log\LogLevel;

class ErrorException extends LoggerException {
    private mixed $err_code;
    private mixed $err_str;
    private mixed $err_file;
    private mixed $err_line;
    public function __construct($err_code, $err_str, $err_file, $err_line, LogLevel $level = LogLevel::FATAL){
        parent::__construct("Some Error Happend", $level);
        $this->err_code = $err_code;
        $this->err_str = $err_str;
        $this->err_file = $err_file;
        $this->err_line = $err_line;
    }

    public function log(){
        parent::log();
        Log::nextline("ERROR_CODE           {$this->err_code}", 33);
        Log::nextline("ERROR_DESCRIPTION    {$this->err_str}", 33);
        Log::nextline("ERROR_FILE           {$this->err_file}", 33);
        Log::nextline("ERROR_LINE           {$this->err_line}", 33);
    }

    public function log_location(){
        $this->log();
    }

    public function log_trace() {
        $this->log();
    }

}