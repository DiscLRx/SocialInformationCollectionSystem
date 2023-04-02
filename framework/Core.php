<?php

namespace framework;

use Error;
use Exception;
use framework\response\Response;
use ReflectionClass;
use ReflectionObject;

require_once 'framework/response/Response.php';
require_once 'framework/AppEnv.php';
require_once 'framework/AuthFilter.php';
require_once 'framework/RequestMapping.php';

final class Core {

    /**
     * @param string $app_env_config 框架核心配置文件的路径
     * @return void
     */
    function run(string $app_env_config): void {
        try {
            $this->load_app_config($app_env_config);
        } catch (Exception|Error $e) {
            echo $e->getMessage();
            Response::http500();
        }
        spl_autoload_register(function ($class_name) {
            $file = NULL;
            $json = file_get_contents(AppEnv::$class_path_mapper);
            $mappers = json_decode($json);
            foreach ($mappers as $mapper) {
                if ($mapper->name == $class_name) {
                    $file = $mapper->path;
                }
            }
            if ($file === NULL) {
                return;
            }
            include $file;
        });
        $this->route();
    }

    private function load_app_config($app_env_config): void {
        if (!file_exists($app_env_config)) {
            throw new Exception("无法读取配置文件{$app_env_config}");
        }
        $json = file_get_contents($app_env_config);

        $json_obj = json_decode($json);
        if ($json_obj===NULL){
            throw new Exception("无法解析配置文件{$app_env_config}");
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
                if (($atype = $aprop->getType()->getName()) !==  ($jtype = gettype($json_obj->$aprop_name))){
                    throw new Exception("配置文件{$app_env_config}值类型错误, 字段{$aprop->getName()}应为{$atype}, 但提供的值为{$jtype}");
                }
                AppEnv::$$aprop_name = $json_obj->$aprop_name;
            }
        }
    }

    private function route(): void {
        $uri_arr = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
        $http_method = $_SERVER['REQUEST_METHOD'];
        $body = file_get_contents('php://input');

        $req_analysis = $this->get_controller_func($http_method, $uri_arr);
        if ($req_analysis === false) {
            response::http404();
        }
        $controller_class = $req_analysis[0];
        $func_name = $req_analysis[1];
        $controller = new $controller_class();

        //权限验证
        $headers = getallheaders();
        $token = $headers['token'] ?? NULL;
        $this->security($token, $controller_class, $func_name);

        $controller->$func_name($uri_arr, $body);
    }

    private function get_controller_func($http_method, $req_uri_arr): array|bool {

        foreach (AppEnv::$controller_classes as $class_name) {
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

    private function security($token, $controller_class, $func_name): void {
        $auth_filter_name = AppEnv::$auth_filter_impl;
        if ($auth_filter_name === "") {
            return;
        }
        $auth_filter = new $auth_filter_name();
        if ($auth_filter->do_filter($token, $controller_class, $func_name)) {
            $auth_filter->do_after_accept();
        } else {
            $auth_filter->do_after_denied();
        }
    }

}