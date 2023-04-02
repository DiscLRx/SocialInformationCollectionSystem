<?php

namespace framework\response;

class ResponseModel {
    /**
     * @var int
     * 0 success
     * 1 permission denied
     * 5 unknown error
     */
    public int $code;

    public function __construct(int $code) {
        $this->code = $code;
    }
}