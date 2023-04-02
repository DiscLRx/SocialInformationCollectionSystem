<?php

namespace framework;

interface AuthFilter {
    /**
     * @param string|NULL $token 身份验证令牌
     * @param $class_name
     * @param string $func_name 要执行的controller方法名称
     * @return bool true for accept, false for denied
     */
    public function do_filter(?string $token, $class_name, string $func_name) : bool;
    public function do_after_accept(): void;
    public function do_after_denied(): void;
}