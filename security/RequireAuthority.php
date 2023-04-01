<?php

namespace security;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class RequireAuthority {
    public array $value;
    public function __construct(...$value) {
        $this->value = $value === [] ? array("PermitAll") : $value;
    }
}