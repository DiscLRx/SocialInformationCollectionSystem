<?php

namespace dto\response\user;

class SigninResDto {
    private int $uid;
    private string $nickname;
    private string $token;

    public function __construct(int $uid, string $nickname, string $token) {
        $this->uid = $uid;
        $this->nickname = $nickname;
        $this->token = $token;
    }

    public function get_uid(): int {
        return $this->uid;
    }

    public function set_uid(int $uid): void {
        $this->uid = $uid;
    }

    public function get_nickname(): string {
        return $this->nickname;
    }

    public function set_nickname(string $nickname): void {
        $this->nickname = $nickname;
    }


    public function get_token(): string {
        return $this->token;
    }

    public function set_token(string $token): void {
        $this->token = $token;
    }

}