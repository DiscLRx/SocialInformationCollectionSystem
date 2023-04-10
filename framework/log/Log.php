<?php

namespace framework\log;

use framework\AppEnv;
use framework\util\Time;

class Log {

    public static function log(LogLevel $level, string $msg): void {
        $ts = Time::current_time_millis();
        $date = date('[Y-m-d H:i:s.', intval($ts / 1000)) . $ts % 1000 . ']';
        $data = $date . '[' . str_pad($level->name, 5) . '] ' . $msg . "\n";
        $f = fopen(AppEnv::$log_file, 'a');
        fwrite($f, $data);
        fclose($f);
    }

    public static function nextline(string $msg, int $pre_space = 8): void {
        $pre_str = str_repeat(' ', $pre_space);
        $data = "{$pre_str}{$msg}\n";
        $f = fopen(AppEnv::$log_file, 'a');
        fwrite($f, $data);
        fclose($f);
    }

    public static function multiline(array $items, int $pre_space = 8, ?callable $foreach_handler = null): void {
        foreach ($items as $index => $item) {
            $line = isset($foreach_handler) ? $foreach_handler($index, $item) : $item;
            Log::nextline($line, $pre_space);
        }
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
                "debug", "info", "warn" => false,
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