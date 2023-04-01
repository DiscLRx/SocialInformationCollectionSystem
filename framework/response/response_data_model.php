<?php

namespace framework\response;

class response_data_model extends response_model {
    public object $data;

    public function __construct($code, $data) {
        parent::__construct($code);
        $this->data = $data;
    }
}