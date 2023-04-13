<?php

namespace framework;

use framework\exception\DatabaseException;
use framework\exception\FileNotFoundException;
use framework\exception\LoadConfigException;
use Redis;
use RedisException;

class RedisExecutor {
    private Redis $redis;
    public function __construct(int $db = 0){

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

        $this->redis = new Redis();
        try {
            $this->redis->connect($host, $port);
            $this->redis->auth($password);
            $this->redis->select($db);
        } catch (RedisException $e) {
            $de = new DatabaseException("初始化redis连接失败");
            $de->set_causeby_exception($e);
            throw new $de;
        }
    }

    public function select(int $db): bool {
        try {
            return $this->redis->select($db);
        } catch (RedisException $e) {
            $de = new DatabaseException("切换redis数据库失败");
            $de->set_causeby_exception($e);
            throw new $de;
        }
    }

    public function set($key, string $value, mixed $timeout = null): bool {
        try {
            return $this->redis->set($key, $value, $timeout);
        } catch (RedisException $e) {
            $de = new DatabaseException("设置redis键值失败");
            $de->set_causeby_exception($e);
            throw new $de;
        }
    }

    public function get($key) {
        try {
            return $this->redis->get($key);
        } catch (RedisException $e) {
            $de = new DatabaseException("查询redis键值失败");
            $de->set_causeby_exception($e);
            throw new $de;
        }
    }

    public function del( $key, ...$otherKeys): bool|int {
        try {
            return $this->redis->del($key, ...$otherKeys);
        } catch (RedisException $e) {
            $de = new DatabaseException("删除redis键值失败");
            $de->set_causeby_exception($e);
            throw new $de;
        }
    }

    public function keys(string $partten): array|false {
        try {
            return $this->redis->keys($partten);
        } catch (RedisException $e) {
            $de = new DatabaseException("查询redis key失败");
            $de->set_causeby_exception($e);
            throw new $de;
        }
    }

}

