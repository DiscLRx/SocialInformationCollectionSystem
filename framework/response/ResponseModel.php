<?php

namespace framework\response;

class ResponseModel {
    /**
     * @var int
     * 状态码编号规则:
     * 0 正常
     * 1xx 权限异常
     * 2xx 参数异常
     * 5 未知错误
     * 状态码:
     * 0 success
     * 11 require to signin
     * 12 permission denied
     * 13 refresh token
     * 21 invalid argument
     * 5 unknown error
     */
    public int $code;

    public function __construct(int $code) {
        $this->code = $code;
    }
}