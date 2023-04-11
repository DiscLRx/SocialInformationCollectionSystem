<?php

namespace framework;

use framework\response\ResponseModel;

interface AuthFilter {
    /**
     * @param string|null $token 身份验证令牌
     * @param string $class_name
     * @param string $func_name 要执行的controller方法名称
     * @return array ['result'=>(bool)验证结果, 'data'=>传递给后处理器的参数]
     */
    public function do_filter(?string $token, string $class_name, string $func_name): array;
    public function do_after_accept(mixed $data): void;
    public function do_after_denied(mixed $data): ResponseModel;
}