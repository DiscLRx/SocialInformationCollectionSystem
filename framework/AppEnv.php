<?php

namespace framework;

/**
 *框架核心配置文件将加载至此类
 */
class AppEnv {
    /**
     * @var string  类名对应文件路径映射配置文件
     */
    public static string $class_path_mapper = "";
    public static string $database_config_file = "";
    public static string $jwt_key = "";
    /**
     * @var string  日志文件名称
     */
    public static string $log_file = "sics.log";
    /**
     * @var string  日志级别
     */
    public static string $log_level = "info";
    /**
     * @var string  访问过滤器实现类名称
     */
    public static string $auth_filter_impl = "";
    /**
     * @var string  统一异常处理器实现类名称
     */
    public static string $exception_handler_impl = "";
    /**
     * @var array   controller类名称
     */
    public static array $controller_classes = [];

}