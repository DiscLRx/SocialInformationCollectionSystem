<?php

namespace framework\response;

class ResponseObjectModel extends ResponseModel {
    public object $data;

    public function __construct(int $code, object $data) {
        parent::__construct($code);
        $this->data = $data;
    }
}