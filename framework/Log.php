<?php

namespace framework;

class Log {
    private static function log(string $level, string $msg) {
        $ts = explode(' ',microtime());
        $data = date('[Y-m-d H:i:s.', $ts[1]) . (int)(floatval($ts[0])*1000) . ']' . $level . ' ' . $msg . "\n";
        $f = fopen(AppEnv::$log_file, 'a');
        fwrite($f, $data);
    }

    public static function debug(string $msg) {
        if (AppEnv::$log_file === "" ||
            match (AppEnv::$log_level) {
                "debug" => false,
                default => true
            }) {
            return;
        }
        self::log('[DEBUG]', $msg);
    }

    public static function info(string $msg) {
        if (AppEnv::$log_file === "" ||
            match (AppEnv::$log_level) {
                "debug", "info" => false,
                default => true
            }) {
            return;
        }
        self::log('[INFO]', $msg);
    }

    public static function warn(string $msg) {
        if (AppEnv::$log_file === "" ||
            match (AppEnv::$log_level) {
                "debug", "info", "warn"  => false,
                default => true
            }) {
            return;
        }
        self::log('[WARN]', $msg);
    }

    public static function error(string $msg) {
        if (AppEnv::$log_file === "" ||
            match (AppEnv::$log_level) {
                "debug", "info", "warn", "error" => false,
                default => true
            }) {
            return;
        }
        self::log('[ERROR]', $msg);
    }

    public static function fatal(string $msg) {
        if (AppEnv::$log_file === "" ||
            match (AppEnv::$log_level) {
                "debug", "info", "warn", "error", "fatal" => false,
                default => true
            }) {
            return;
        }
        self::log('[FATAL]', $msg);
    }
}