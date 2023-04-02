<?php

namespace framework;

use framework\response\response;
use ReflectionClass;

require_once 'framework/response/response.php';
require_once 'framework/app_config.php';
require_once 'framework/auth_filter.php';
require_once 'framework/RequestMapping.php';

final class core {

    function run(): void {
        spl_autoload_register(function ($class_name) {
            $file = NULL;
            $json = file_get_contents('configurations/ClassPathMapper.json');
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
        $this->load_app_config();
        $this->route();
    }

    private function load_app_config(): void {
        $reflect = new ReflectionClass(app_config::class);
        $props = $reflect->getProperties();

        $json = file_get_contents('configurations/ApplicationConfig.json');
        $cfg_obj = json_decode($json);
        foreach ($props as $prop) {
            $field_name = $prop->getName();
            app_config::$$field_name = $cfg_obj->$field_name;
        }
    }

    private function route(): void {
        $uri_arr =
            explode(
                '/',
                trim(
                    $_SERVER['REQUEST_URI']
                    , '/'
                )
            );
        $http_method = $_SERVER['REQUEST_METHOD'];
        $body = file_get_contents('php://input');

        $req_analysis = $this->get_controller_func($http_method, $uri_arr);
        if ($req_analysis===false){
            echo "class or func not found";
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

    private function get_controller_func($http_method, $req_uri_arr) : array | bool {

        foreach (app_config::$controller_classes as $class_name) {
            $reflection = new ReflectionClass($class_name);
            foreach ($reflection->getMethods() as $method) {
                foreach ($method->getAttributes() as $attribute) {
                    if ($attribute->getName()===trim(RequestMapping::class, '\\')){
                        $attribute = $attribute->newInstance();

                        // 匹配http method
                        if ($attribute->method !== $http_method){
                            break;
                        }

                        $attr_uri_arr = $attribute->uri_arr;
                        // 匹配参数数组长度
                        if (sizeof($req_uri_arr) !== sizeof($attr_uri_arr)){
                            break;
                        }

                        // 替换请求uri的参数为 *
                        $keys = array_keys($attr_uri_arr, '*');
                        foreach ($keys as $key) {
                            $req_uri_arr[$key] = '*';
                        }

                        if ($req_uri_arr === $attr_uri_arr){
                            return array($class_name, $method->getName());
                        }
                    }
                }
            }
        }
        return false;
    }

    private function security($token, $controller_class, $func_name): void {
        $auth_filter_name = app_config::$auth_filter_impl;
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