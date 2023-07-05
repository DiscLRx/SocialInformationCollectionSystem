<?php

namespace framework\log;

use framework\AppEnv;
use framework\util\Time;

class Log {

    public static function log(LogLevel $level, string $msg): void {
        $ts = Time::current_ts();
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
            self::nextline($line, $pre_space);
        }
    }

    public static function debug(string $msg, array $follow_items = null, int $pre_space = 8, ?callable $foreach_handler = null): void {
        if (AppEnv::$log_file === "" ||
            match (AppEnv::$log_level) {
                "debug" => false,
                default => true
            }) {
            return;
        }
        self::log(LogLevel::DEBUG, $msg);
        if (isset($follow_items)){
            self::multiline($follow_items, $pre_space, $foreach_handler);
        }
    }

    public static function info(string $msg, array $follow_items = null, int $pre_space = 8, ?callable $foreach_handler = null): void {
        if (AppEnv::$log_file === "" ||
            match (AppEnv::$log_level) {
                "debug", "info" => false,
                default => true
            }) {
            return;
        }
        self::log(LogLevel::INFO, $msg);
        if (isset($follow_items)){
            self::multiline($follow_items, $pre_space, $foreach_handler);
        }
    }

    public static function warn(string $msg, array $follow_items = null, int $pre_space = 8, ?callable $foreach_handler = null): void {
        if (AppEnv::$log_file === "" ||
            match (AppEnv::$log_level) {
                "debug", "info", "warn" => false,
                default => true
            }) {
            return;
        }
        self::log(LogLevel::WARN, $msg);
        if (isset($follow_items)){
            self::multiline($follow_items, $pre_space, $foreach_handler);
        }
    }

    public static function error(string $msg, array $follow_items = null, int $pre_space = 8, ?callable $foreach_handler = null): void {
        if (AppEnv::$log_file === "" ||
            match (AppEnv::$log_level) {
                "debug", "info", "warn", "error" => false,
                default => true
            }) {
            return;
        }
        self::log(LogLevel::ERROR, $msg);
        if (isset($follow_items)){
            self::multiline($follow_items, $pre_space, $foreach_handler);
        }
    }

    public static function fatal(string $msg, array $follow_items = null, int $pre_space = 8, ?callable $foreach_handler = null): void {
        if (AppEnv::$log_file === "" ||
            match (AppEnv::$log_level) {
                "debug", "info", "warn", "error", "fatal" => false,
                default => true
            }) {
            return;
        }
        self::log(LogLevel::FATAL, $msg);
        if (isset($follow_items)){
            self::multiline($follow_items, $pre_space, $foreach_handler);
        }
    }
}