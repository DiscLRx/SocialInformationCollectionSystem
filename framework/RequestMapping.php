<?php

namespace framework;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class RequestMapping {
    public string $method;
    public array $uri_arr;

    public function __construct(string $method, string $uri) {
        $this->method = $method;
        $this->uri_arr = explode('/', trim( $uri, '/'));;
    }

}