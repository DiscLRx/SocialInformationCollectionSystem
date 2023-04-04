<?php

namespace framework\response;

class ResponseDataModel extends ResponseModel {
    public mixed $data;

    public function __construct(int $code, mixed $data) {
        parent::__construct($code);
        $this->data = $data;
    }
}