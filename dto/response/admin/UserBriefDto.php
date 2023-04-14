<?php

namespace dto\response\admin;

class UserBriefDto {
    private int $id;
    private string $username;

    public function __construct(int $id, string $username) {
        $this->id = $id;
        $this->username = $username;
    }

    public function get_id(): int {
        return $this->id;
    }

    public function set_id(int $id): void {
        $this->id = $id;
    }

    public function get_username(): string {
        return $this->username;
    }

    public function set_username(string $username): void {
        $this->username = $username;
    }

}