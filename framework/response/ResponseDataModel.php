<?php

namespace framework\response;

class ResponseDataModel extends ResponseModel {
    /**
     * @var mixed 响应的数据
     */
    public mixed $data;


    /**
     * @param int $code     状态码
     * @param mixed $data   响应数据
     */
    public function __construct(int $code, mixed $data) {
        parent::__construct($code);
        $this->data = $data;
    }
}