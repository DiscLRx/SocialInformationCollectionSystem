<?php

namespace framework;

use Error;
use Exception;
use framework\exception\ClassNotFoundException;
use framework\exception\ErrorException;
use framework\exception\FileNotFoundException;
use framework\exception\LoadConfigException;
use framework\log\Log;
use framework\response\Response;
use framework\response\ResponseModel;
use ReflectionClass;
use ReflectionObject;

require_once 'framework/response/Response.php';
require_once 'framework/AppEnv.php';
require_once 'framework/AuthFilter.php';
require_once 'framework/RequestMapping.php';
require_once 'framework/log/Log.php';
require_once 'framework/log/LogLevel.php';
require_once 'framework/exception/LoggerException.php';
require_once 'framework/exception/LoadConfigException.php';
require_once 'framework/exception/ClassNotFoundException.php';
require_once 'framework/exception/FileNotFoundException.php';
require_once 'framework/exception/ErrorException.php';

final class Core {

    /**
     * @param string $app_env_config 框架核心配置文件的路径
     * @return void
     */
    function run(string $app_env_config): void {

        try {
            $this->php_basic_setting();
            try {
                $this->load_app_config($app_env_config);
            } catch (Exception|Error $e) {
                throw new LoadConfigException($e->getMessage());
            }
            $res_body = $this->route();
            if ($res_body !== null) {
                echo json_encode($res_body);
            }
        } catch (FileNotFoundException|ClassNotFoundException|LoadConfigException $e){
            $e->log_trace();
            Response::http500();
        } catch (Exception|Error $e) {
            Log::fatal($e->getMessage());
            Response::http500();
        }
    }

    private function php_basic_setting(): void {
        set_error_handler(function ($err_code, $err_str, $err_file, $err_line) {
            throw new ErrorException($err_code, $err_str, $err_file, $err_line);
        }, E_ERROR | E_WARNING);
        spl_autoload_register(function ($class_name) {
            $file = null;
            $json = file_get_contents(AppEnv::$class_path_mapper);
            $mappers = json_decode($json);
            foreach ($mappers as $mapper) {
                if ($mapper->name == $class_name) {
                    $file = $mapper->path;
                }
            }
            if ($file===null) {
                throw new FileNotFoundException($file);
            }
            if (!file_exists($file)) {
                throw new FileNotFoundException($file);
            }
            require_once $file;
        });
    }

    private function load_app_config($app_env_config): void {
        if (!file_exists($app_env_config)) {
            throw new LoadConfigException("无法读取配置文件{$app_env_config}");
        }
        $json = file_get_contents($app_env_config);

        $json_obj = json_decode($json);
        if ($json_obj === null) {
            throw new LoadConfigException("无法解析配置文件{$app_env_config}");
        }
        $json_props = (new ReflectionObject($json_obj))->getProperties();
        $appenv_props = (new ReflectionClass(AppEnv::class))->getProperties();
        $json_props_name = [];
        foreach ($json_props as $jprop) {
            $json_props_name[] = $jprop->getName();
        }
        foreach ($appenv_props as $aprop) {
            $aprop_name = $aprop->getName();
            if (in_array($aprop_name, $json_props_name)) {
                if (($atype = $aprop->getType()->getName()) !== ($jtype = gettype($json_obj->$aprop_name))) {
                    throw new LoadConfigException("配置文件{$app_env_config}值类型错误, 字段{$aprop->getName()}应为{$atype}, 但提供的值为{$jtype}");
                }
                AppEnv::$$aprop_name = $json_obj->$aprop_name;
            }
        }
    }

    private function route(): ResponseModel|null {
        $uri = $_SERVER['REQUEST_URI'];
        $http_method = $_SERVER['REQUEST_METHOD'];
        Log::info("{$http_method} {$uri} From {$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']}");

        //分割路径和参数部分
        $uri = explode('?', $uri, 2);

        $uri_arr = explode('/', trim($uri[0], '/'));
        foreach ($uri_arr as $index => $value) {
            $uri_arr[$index] = $this->uri_decode($value);
        }

        $uri_query_map = [];
        if (isset($uri[1])) {
            $uri_query = explode('&', $uri[1]);
            foreach ($uri_query as $item) {
                $kv = explode('=', $item, 2);
                $uri_query_map[$this->uri_decode($kv[0])] = $this->uri_decode($kv[1]);
            }
        }

        $req_analysis = $this->get_controller_func($http_method, $uri_arr);
        if ($req_analysis === false) {
            return Response::http404();
        }
        $controller_class = $req_analysis[0];
        $func_name = $req_analysis[1];

        //权限验证
        $headers = getallheaders();
        $token = $headers['token'] ?? null;
        if (!$this->security($token, $controller_class, $func_name)) {
            return Response::permission_denied();
        }

        $body = file_get_contents('php://input');

        $controller = new $controller_class();
        return $controller->$func_name($uri_arr, $uri_query_map, $body);
    }

    private function uri_decode(string $uri): string {
        $uri = str_replace('+', '%2B', $uri);
        return urldecode($uri);
    }

    private function get_controller_func($http_method, $req_uri_arr): array|bool {

        foreach (AppEnv::$controller_classes as $class_name) {
            if (!class_exists($class_name)) {
                throw new ClassNotFoundException($class_name);
            }
            $reflection = new ReflectionClass($class_name);
            foreach ($reflection->getMethods() as $method) {
                foreach ($method->getAttributes() as $attribute) {
                    if ($attribute->getName() === trim(RequestMapping::class, '\\')) {
                        $attribute = $attribute->newInstance();

                        // 匹配http method
                        if ($attribute->method !== $http_method) {
                            break;
                        }

                        $attr_uri_arr = $attribute->uri_arr;
                        // 匹配参数数组长度
                        if (sizeof($req_uri_arr) !== sizeof($attr_uri_arr)) {
                            break;
                        }

                        // 替换请求uri的参数为 *
                        $keys = array_keys($attr_uri_arr, '*');
                        foreach ($keys as $key) {
                            $req_uri_arr[$key] = '*';
                        }

                        if ($req_uri_arr === $attr_uri_arr) {
                            return array($class_name, $method->getName());
                        }
                    }
                }
            }
        }
        return false;
    }

    private function security($token, $controller_class, $func_name): bool {
        $auth_filter_name = AppEnv::$auth_filter_impl;
        if ($auth_filter_name === "") {
            return true;
        }
        $auth_filter = new $auth_filter_name();
        $filter_ret = $auth_filter->do_filter($token, $controller_class, $func_name);
        if ($filter_ret) {
            $auth_filter->do_after_accept();
        } else {
            $auth_filter->do_after_denied();
        }
        return $filter_ret;
    }

}