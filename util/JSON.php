<?php

namespace util;

use Exception;
use framework\exception\LoggerException;
use ReflectionClass;
use ReflectionException;

class JSON {
    public static function unserialize(string $json, object|string $obj_or_class) {
        $dcod = json_decode($json);
        $stack = array();
        if (is_object($obj_or_class)) {
            $obj = $obj_or_class;
        } else {
            try {
                $obj=(new ReflectionClass($obj_or_class))->newInstance();
            } catch (ReflectionException $e) {
                $le = new LoggerException("无法使用无参构造方法实例化类 \"{$obj_or_class}\"");
                $le->set_causeby_exception($e);
                return $le;
            }
        }
        $stack[] = array($obj, $dcod);

        try {
            while (count($stack) > 0) {
                $pair = array_pop($stack);
                $sub_obj = $pair[0];
                $dcod = $pair[1];
                $prop = get_object_vars($dcod);
                foreach ($prop as $key => $val) {
                    if (property_exists($sub_obj, $key)) {
                        if (is_object($val)) {
                            $stack[] = array($sub_obj->$key, $val);
                        } else {
                            $sub_obj->$key = $val;
                        }
                    }
                }
            }
        } catch (Exception $e) {
            $le = new LoggerException("反序列化JSON字符串失败");
            $le->set_causeby_exception($e);
            return $le;
        }
        return $obj;
    }
}