<?php

namespace security;

use framework\AuthFilter;
use framework\exception\AttributeNotFoundException;
use framework\log\LogLevel;
use ReflectionClass;

require_once 'security/RequireAuthority.php';

class TokenAuthFilter implements AuthFilter {

    public function do_filter(?string $token, string $class_name, string $func_name): bool {
        $require_auth = $this->get_require_authorities($class_name, $func_name);
        $user_auth = $this->token_verify($token);
        return match ($user_auth) {
            "Admin", $require_auth => true,
            default => false
        };
    }

    private function get_require_authorities(string $class_name, string $func_name) {
        $reflection = new ReflectionClass($class_name);
        $method = $reflection->getMethod($func_name);

        foreach ($method->getAttributes() as $attribute) {
            if ($attribute->getName() === trim(RequireAuthority::class, '\\')) {
                return $attribute->newInstance()->value;
            }
        }
        throw new AttributeNotFoundException("RequireAuthority", $class_name, $func_name, LogLevel::ERROR);
    }

    private function token_verify(string $token): string {
        // TODO token校验
        return "Admin";
    }

    public function do_after_accept(): void {}

    public function do_after_denied(): void {}
}