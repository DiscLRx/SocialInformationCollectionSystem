<?php

namespace framework\exception;

use Error;
use Exception;
use framework\log\Log;
use framework\log\LogLevel;
use framework\util\FormatUtil;
use RuntimeException;

class LoggerException extends RuntimeException {

    protected LogLevel $level;
    protected string $msg;
    private Exception|Error|null $cause_by_exception;

    public function __construct(string $msg = "", LogLevel $level = LogLevel::ERROR) {
        parent::__construct($msg);
        $this->level = $level;
        $this->msg = $msg;
        $this->cause_by_exception = null;
    }

    public function set_causeby_exception(Exception $cause_by_exception = null) {
        if ($cause_by_exception instanceof LoggerException) {
            $this->cause_by_exception = $cause_by_exception->get_causeby_exception();;
        } else {
            $this->cause_by_exception = $cause_by_exception;
        }
    }

    public function get_causeby_exception(): Exception|Error|null {
        return $this->cause_by_exception;
    }

    public function log() {
        Log::log($this->level, $this->msg);
    }

    public function log_location() {
        Log::log($this->level, $this->msg);
        Log::nextline("at \"{$this->getFile()}\" line {$this->getLine()}");
    }

    public function log_trace() {
        $this->log_self_trace();
        if (isset($this->cause_by_exception)) {
            $this->log_causeby_exception_trace();
        }
    }

    public function log_self_trace() {
        Log::log($this->level, $this->msg);
        Log::multiline($this->getTrace(), foreach_handler: function ($index, $item) {
            return FormatUtil::trace_line($index, $item);
        });
    }

    public function log_causeby_exception_trace() {
        Log::log($this->level, $this->msg . ' Is Cause By: ' . $this->cause_by_exception->getMessage());
        Log::multiline($this->cause_by_exception->getTrace(), foreach_handler: function ($index, $item) {
            return FormatUtil::trace_line($index, $item);
        });
    }

}