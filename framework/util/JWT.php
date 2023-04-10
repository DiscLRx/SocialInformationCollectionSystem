<?php

namespace framework\util;

use Firebase\JWT\JWT as FJWT;
use Firebase\JWT\Key as FKey;
use framework\AppEnv;
use stdClass;

class JWT {

    private string $jwt_key;

    public function __construct(string $jwt_key = null){
        $this->jwt_key = $jwt_key ?? AppEnv::$jwt_key;
    }

    public function create($payload): string {
        return FJWT::encode($payload, $this->jwt_key, 'HS256');
    }

    public function decode($jwt): stdClass {
        return FJWT::decode($jwt, new FKey($this->jwt_key, 'HS256'));
    }

}