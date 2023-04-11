<?php

namespace framework;

use framework\exception\FileNotFoundException;
use framework\exception\LoadConfigException;
use framework\log\Log;
use framework\log\LogLevel;
use framework\util\FormatUtil;
use Redis;
use RedisException;

class RedisExecutor {
    private Redis $db;
    public function __construct(){

        $config_file = AppEnv::$database_config_file;
        if (!file_exists($config_file)){
            throw new FileNotFoundException($config_file);
        }

        $json = file_get_contents($config_file);
        $redis_config = json_decode($json)->redis;
        if (!isset($redis_config)) {
            throw new LoadConfigException("无法解析配置文件 \"{$config_file}\"");
        }

        $err_msg = "数据库配置文件 \"{$config_file}\" 配置缺失";
        $host = $redis_config->host ?? throw new LoadConfigException($err_msg);
        $port = $redis_config->port ?? throw new LoadConfigException($err_msg);
        $password = $redis_config->password ?? throw new LoadConfigException($err_msg);

        $this->db = new Redis();
        try {
            $this->db->connect($host, $port);
            $this->db->auth($password);
        } catch (RedisException $e) {
            $this->log_redis_exception($e);
        }
    }

    private function log_redis_exception($e): void {
        Log::log(LogLevel::ERROR ,$e->getMessage());
        Log::multiline($e->getTrace(), foreach_handler: function ($index, $item) {
            return FormatUtil::trace_line($index, $item);
        });
    }

    public function set($key, string $value, mixed $timeout = null): bool {
        try {
            return $this->db->set($key, $value, $timeout);
        } catch (RedisException $e) {
            $this->log_redis_exception($e);
            return false;
        }
    }

    public function get($key) {
        try {
            return $this->db->get($key);
        } catch (RedisException $e) {
            $this->log_redis_exception($e);
            return false;
        }
    }

    public function keys(string $partten): array|false {
        try {
            return $this->db->keys($partten);
        } catch (RedisException $e) {
            $this->log_redis_exception($e);
            return false;
        }
    }

}

