<?php

namespace framework\exception;

use framework\log\Log;
use framework\log\LogLevel;
use RuntimeException;

class LoggerException extends RuntimeException {

    protected LogLevel $level;
    protected string $msg;

    protected function __construct(string $class_name, LogLevel $level = LogLevel::ERROR){
        parent::__construct($class_name);
        $this->level = $level;
        $this->msg = $class_name;
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
        $trace_arr = $this->getTrace();

        foreach ($trace_arr as $trace_id => $item) {
            $line = "Trace #{$trace_id}: " .
                (isset($item['file']) ? "file \"{$item['file']}\" " : "") .
                (isset($item['line']) ? "line {$item['line']}:    " : "") .
                ((isset($item['class'])) ? "{$item['class']} " : "class? ") .
                ((isset($item['type'])) ? "{$item['type']} " : "type? ") .
                ((isset($item['function'])) ? "{$item['function']}()" : "function?");
            Log::nextline($line);
        }
    }
}