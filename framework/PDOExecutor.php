<?php

namespace framework;

use framework\exception\DatabaseException;
use framework\exception\FileNotFoundException;
use framework\exception\LoadConfigException;
use PDO;
use PDOException;
use framework\log\Log;


class PDOExecutor {

    protected ?PDO $db;

    protected function __construct(?PDO $db = null) {
        if (isset($db)){
            $this->db = $db;
            return;
        }

        $config_file = AppEnv::$database_config_file;
        if (!file_exists($config_file)){
            throw new FileNotFoundException($config_file);
        }

        $json = file_get_contents($config_file);
        $mysql_config = json_decode($json)->mysql;
        if (!isset($mysql_config)) {
            throw new LoadConfigException("无法解析配置文件 \"{$config_file}\"");
        }

        $err_msg = "数据库配置文件 \"{$config_file}\" 配置缺失";
        $host = $mysql_config->host ?? throw new LoadConfigException($err_msg);
        $port = $mysql_config->port ?? throw new LoadConfigException($err_msg);
        $database = $mysql_config->database ?? throw new LoadConfigException($err_msg);
        $username = $mysql_config->username ?? throw new LoadConfigException($err_msg);
        $password = $mysql_config->password ?? throw new LoadConfigException($err_msg);

        try {
            $this->db = new PDO("mysql:host={$host}:{$port};dbname={$database}", $username, $password, array(PDO::ATTR_PERSISTENT => true));
        } catch (PDOException $e) {
            $this->pdo_exception_handler($e);
        }

    }

    private function pdo_exception_handler(PDOException $e){
        $de = new DatabaseException("连接数据库出错");
        $de->set_causeby_exception($e);
        throw $de;
    }

    public function get_db(): PDO {
        return $this->db;
    }

    public function set_db(PDO $db): void {
        $this->db = $db;
    }

    public function start_transaction():bool {
        try {
            $ret = $this->db->beginTransaction();
            Log::debug("开始事务");
            return $ret;
        } catch (PDOException $e) {
            $this->pdo_exception_handler($e);
        }
    }

    public function commit():bool {
        try {
            $ret = $this->db->commit();
            Log::debug("提交事务");
            return $ret;
        } catch (PDOException $e) {
            $this->pdo_exception_handler($e);
        }
    }

    public function rollback():bool {
        try {
            $ret = $this->db->rollBack();
            Log::debug("回滚事务");
            return $ret;
        } catch (PDOException $e) {
            $this->pdo_exception_handler($e);
        }
    }

    public function close(): void {
        $this->db = null;
        Log::debug("关闭连接");
    }

}