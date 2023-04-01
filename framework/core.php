<?php

namespace framework;

use framework\request_mapping\RequestMapping;
use framework\response\response;
use ReflectionClass;

require_once 'framework/response/response.php';
require_once 'framework/app_config.php';
require_once 'framework/auth_filter.php';
require_once 'framework/request_mapping/RequestMapping.php';

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
                    str_replace('-', '_', $_SERVER['REQUEST_URI'])
                    , '/'
                )
            );
        $http_method = $_SERVER['REQUEST_METHOD'];
        $body = file_get_contents('php://input');

        $uri_analysis = $this->get_class_name($uri_arr);
        if (!$uri_analysis) {
            response::http404();
        }
        $controller_class = $uri_analysis[0];
        $params = $uri_analysis[1];

        $controller = new $controller_class();
        $func_name = $this->get_func_name(
            $controller_class,
            $http_method,
            explode('/', trim($_SERVER['REQUEST_URI'], '/'))
        );
        if ($func_name === false){
            response::http404();
        }

        //权限验证
        $headers = getallheaders();
        $token = $headers['token'] ?? NULL;
        $this->security($token, $controller_class, $func_name);
        $controller->$func_name($params, $body);

    }

    private function get_func_name($class_name, $http_method, $target_uri_arr) : string | bool{
        assert(class_exists($class_name));
        $reflection = new ReflectionClass($class_name);
        foreach ($reflection->getMethods() as $method) {
            foreach ($method->getAttributes() as $attribute) {
                if ($attribute->getName()===trim(RequestMapping::class, '\\')){
                    $attribute = $attribute->newInstance();
                    if ($attribute->method !== $http_method){
                        continue;
                    }
                    $uri_arr_len = sizeof($target_uri_arr);
                    $attr_uri_arr = $attribute->uri_arr;
                    if ($uri_arr_len === sizeof($attr_uri_arr)){
                        $equal = true;
                        for ( $i = 0; $i < $uri_arr_len ; $i++) {
                            if (!($attr_uri_arr[$i] === '*' || $attr_uri_arr[$i] === $target_uri_arr[$i])){
                                $equal = false;
                                break;
                            }
                        }
                        if ($equal){
                            return $method->getName();
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

    private function get_class_name($uri_arr): bool|array {
        $temp_arr = $uri_arr;
        $temp_arr[] = NULL;
        do {
            if ($temp_arr != []) {
                array_pop($temp_arr);
            } else {
                return false;
            }
            $class_name = implode('\\', $temp_arr) . '_controller';
        } while (!class_exists($class_name));
        $params = [];
        foreach (array_diff_key($uri_arr, $temp_arr) as $p) {
            $params[] = $p;
        }
        return array($class_name, $params);
    }

}