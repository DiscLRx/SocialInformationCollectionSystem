<?php

namespace framework;

use framework\exception\DatabaseException;
use framework\exception\FileNotFoundException;
use framework\exception\LoadConfigException;
use PDO;
use PDOException;


class PDOExecutor {

    protected PDO $db;

    protected function __construct() {

        $config_file = AppEnv::$database_config_file;
        if (!file_exists($config_file)){
            throw new FileNotFoundException($config_file);
        }

        $json = file_get_contents($config_file);
        $json_obj = json_decode($json);
        if (!isset($json_obj)) {
            throw new LoadConfigException("无法解析配置文件 \"{$config_file}\"");
        }

        $err_msg = "数据库配置文件 \"{$config_file}\" 配置缺失";
        $host = $json_obj->host ?? throw new LoadConfigException($err_msg);
        $port = $json_obj->port ?? throw new LoadConfigException($err_msg);
        $database = $json_obj->database ?? throw new LoadConfigException($err_msg);
        $username = $json_obj->username ?? throw new LoadConfigException($err_msg);
        $password = $json_obj->password ?? throw new LoadConfigException($err_msg);

        try {
            $this->db = new PDO("mysql:host={$host}:{$port};dbname={$database}", $username, $password, array(PDO::ATTR_PERSISTENT => true));
        } catch (PDOException $e) {
            $de = new DatabaseException("连接数据库出错");
            $de->set_causeby_exception($e);
            throw $de;
        }

    }

}