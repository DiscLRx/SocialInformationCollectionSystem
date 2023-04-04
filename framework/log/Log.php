<?php

namespace framework\log;

use framework\AppEnv;

class Log {

    public static function log(LogLevel $level, string $msg): void {
        $ts = explode(' ',microtime());
        $data = date('[Y-m-d H:i:s.', $ts[1]) . (int)(floatval($ts[0])*1000) . '][' . $level->name . '] ' . $msg . "\n";
        $f = fopen(AppEnv::$log_file, 'a');
        fwrite($f, $data);
    }

    public static function debug(string $msg): void {
        if (AppEnv::$log_file === "" ||
            match (AppEnv::$log_level) {
                "debug" => false,
                default => true
            }) {
            return;
        }
        self::log(LogLevel::DEBUG, $msg);
    }

    public static function info(string $msg): void {
        if (AppEnv::$log_file === "" ||
            match (AppEnv::$log_level) {
                "debug", "info" => false,
                default => true
            }) {
            return;
        }
        self::log(LogLevel::INFO, $msg);
    }

    public static function warn(string $msg): void {
        if (AppEnv::$log_file === "" ||
            match (AppEnv::$log_level) {
                "debug", "info", "warn"  => false,
                default => true
            }) {
            return;
        }
        self::log(LogLevel::WARN, $msg);
    }

    public static function error(string $msg): void {
        if (AppEnv::$log_file === "" ||
            match (AppEnv::$log_level) {
                "debug", "info", "warn", "error" => false,
                default => true
            }) {
            return;
        }
        self::log(LogLevel::ERROR, $msg);
    }

    public static function fatal(string $msg): void {
        if (AppEnv::$log_file === "" ||
            match (AppEnv::$log_level) {
                "debug", "info", "warn", "error", "fatal" => false,
                default => true
            }) {
            return;
        }
        self::log(LogLevel::FATAL, $msg);
    }
}