<?php

namespace framework\response;

class ResponseStringModel extends ResponseModel {
    public string $data;

    public function __construct(int $code, string $data) {
        parent::__construct($code);
        $this->data = $data;
    }


}