<?php

namespace dto\response;

class RefreshTokenResDto {
    private string $token;

    public function __construct(string $token) {
        $this->token = $token;
    }

    public function get_token(): string {
        return $this->token;
    }

    public function set_token(string $token): void {
        $this->token = $token;
    }

}