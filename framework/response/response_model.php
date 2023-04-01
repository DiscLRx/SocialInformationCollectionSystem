<?php

namespace framework\response;

class response_model {
    /**
     * @var int
     * 0 success
     * 1 permission denied
     * 5 unknown error
     */
    public int $code;

    public function __construct($code) {
        $this->code = $code;
    }
}