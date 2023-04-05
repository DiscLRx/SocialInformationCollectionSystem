<?php

namespace framework\exception;

use framework\log\Log;
use framework\log\LogLevel;
use RuntimeException;

class LoggerException extends RuntimeException {

    protected LogLevel $level;
    protected string $msg;

    public function __construct(string $msg = "", LogLevel $level = LogLevel::ERROR){
        parent::__construct($msg);
        $this->level = $level;
        $this->msg = $msg;
    }

    public function log(){
        Log::log($this->level, $this->msg);
    }

    public function log_location(){
        Log::log($this->level, $this->msg);
        Log::nextline("at \"{$this->getFile()}\" line {$this->getLine()}");
    }

    public function log_trace(){
        Log::log($this->level, $this->msg);
        Log::multiline($this->getTrace(), foreach_handler: function ($index, $item) {
            return FormatUtil::trace_line($index, $item);
        });
    }

}