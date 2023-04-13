<?php

namespace dto\request\user;

class SigninReqDto {
    private string $username;
    private string $password;

    public function __construct(string $username, string $password) {
        $this->username = $username;
        $this->password = $password;
    }

    public function get_username(): string {
        return $this->username;
    }

    public function set_username(string $username): void {
        $this->username = $username;
    }

    public function get_password(): string {
        return $this->password;
    }

    public function set_password(string $password): void {
        $this->password = $password;
    }
}