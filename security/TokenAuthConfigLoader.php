<?php

namespace security;

use framework\util\JWT;

class TokenAuthConfigLoader {
    private JWT $jwt;
    private object $expcfg;
    private object $rfrcfg;

    public function __construct(){
        $tacjson = file_get_contents('configuration/TokenAuthConfig.json');
        $tacobj = json_decode($tacjson);
        $this->jwt = new JWT($tacobj->jwt_key);
        $this->expcfg = $tacobj->token_expire;
        $this->rfrcfg = $tacobj->token_refresh;
    }

    public function get_jwt(): JWT {
        return $this->jwt;
    }

    public function get_expcfg(): object {
        return $this->expcfg;
    }

    public function get_rfrcfg(): object {
        return $this->rfrcfg;
    }

}