<?php

namespace security;

use entity\User;
use framework\AuthFilter;
use framework\RedisExecutor;
use framework\util\JSON;
use framework\util\JWT;
use ReflectionClass;
use RuntimeException;

require_once 'security/RequireAuthority.php';

class TokenAuthFilter implements AuthFilter {

    public function do_filter(?string $token, string $class_name, string $func_name): bool {
        $require_auths = $this->get_require_authorities($class_name, $func_name);
        if (in_array("PermitAll", $require_auths)){
            return true;
        }
        if (!isset($token)) {
            return false;
        }
        $user_auth = $this->token_verify($token);
        if ($user_auth===false){
            return false;
        }
        if (in_array($user_auth, $require_auths) || $user_auth==="Admin") {
            return true;
        } else {
            return false;
        }
    }

    private function get_require_authorities(string $class_name, string $func_name):array {
        $reflection = new ReflectionClass($class_name);
        $method = $reflection->getMethod($func_name);

        foreach ($method->getAttributes() as $attribute) {
            if ($attribute->getName() === trim(RequireAuthority::class, '\\')) {
                return $attribute->newInstance()->value;
            }
        }
        return ["PermitAll"];
    }

    private function token_verify(string $token): string|bool {

        $jwt = new JWT();
        try {
            $payload = (array)$jwt->decode($token);
        } catch (RuntimeException $e) {
            return false;
        }
        $uid = $payload['uid'];

        $redis = new RedisExecutor();
        $user = JSON::unserialize($redis->get($uid), User::class);
        return $user->get_authority();
    }

    public function do_after_accept(): void {}

    public function do_after_denied(): void {}
}