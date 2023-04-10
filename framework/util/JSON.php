<?php

namespace framework\util;

use Error;
use Exception;
use framework\exception\JSONSerializeException;
use ReflectionClass;
use ReflectionObject;

class JSON {

    public static function serialize(object $obj): string {
        try {
            return self::__serialize__($obj);
        } catch (Exception|Error $e) {
            $jse = new JSONSerializeException("序列化对象失败");
            $jse->set_causeby_exception($e);
            throw $jse;
        }
    }

    private static function __serialize__(object $obj): string {
        $json_str = "{";
        $ref = new ReflectionObject($obj);
        $props = $ref->getProperties();
        $props_count = count($props);
        $item_arr = array();
        for ($i = 0; $i < $props_count; $i++) {
            $name = $props[$i]->getName();
            $value = $props[$i]->getValue($obj);
            if (!isset($value)) {
                continue;
            }
            if (is_object($value)) {
                $value = self::__serialize__($value);
            } else if (is_string($value)) {
                $value = "\"{$value}\"";
            } else if (is_bool($value)) {
                if ($value) {
                    $value = "true";
                } else {
                    $value = "false";
                }
            } else if (is_array($value)) {
                $value = json_encode($value);
            }
            $item_arr[] = "\"{$name}\":{$value}";
        }
        $json_str .= implode(',', $item_arr);
        return $json_str . "}";
    }

    public static function unserialize(string $json, string $class): mixed {
        try {
            return self::__unserialize__($json, $class);
        } catch (Exception|Error $e) {
            $jse = new JSONSerializeException("无法将json字符串反序列化为 \"{$class}\"");
            $jse->set_causeby_exception($e);
            throw $jse;
        }
    }

    private static function __unserialize__($json, string $class): mixed {
        $ref = new ReflectionClass($class);
        $obj = $ref->newInstanceWithoutConstructor();
        $j_obj = json_decode($json);
        foreach ($ref->getProperties() as $prop) {
            $key = $prop->getName();
            if (!isset($j_obj->$key)){
                $prop->setValue($obj, null);
                continue;
            }
            if (is_object($j_obj->$key)) {
                $ref->getProperty($key)->setValue(
                    $obj,
                    self::__unserialize__(
                        json_encode($j_obj->$key),
                        $ref->getProperty($key)->getType()->getName()
                    )
                );
            } else {
                $ref->getProperty($key)->setValue($obj, $j_obj->$key);
            }
        }
        return $obj;
    }

}