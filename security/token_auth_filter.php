<?php

namespace security;

use Exception;
use framework\auth_filter;
use framework\response\response;
use ReflectionClass;
use ReflectionException;

require_once 'security/RequireAuthority.php';

class token_auth_filter implements auth_filter {

    public function do_filter(?string $token, $class_name, string $func_name): bool {
        $require_auth = $this->get_require_authorities($class_name, $func_name);
        $user_auth = $this->token_verify("");

        return true;
    }

    private function get_require_authorities(string $class_name, string $func_name){
        assert(class_exists($class_name));
        $reflection = new ReflectionClass($class_name);
        try {
            $method = $reflection->getMethod($func_name);
        } catch (ReflectionException $e) {
            // TODO 日志
        }
        try {
            foreach ($method->getAttributes() as $attribute) {
                if ($attribute->getName()===trim(RequireAuthority::class, '\\')) {
                    return $attribute->newInstance()->value;
                }
            }
            throw new Exception("attribute RequireAuthority not found");
        } catch (Exception $e) {
            // TODO 日志
        }
    }

    private function token_verify(string $token) : string{
        // TODO token校验
        return "";
    }

    public function do_after_accept(): void {}

    public function do_after_denied(): void {
        response::permission_denied();
    }
}