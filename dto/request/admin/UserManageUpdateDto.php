<?php

namespace dto\request\admin;

class UserManageUpdateDto {
    private string $username;
    private string $nickname;
    private string $password;
    private string $phone;
    private bool $enable;

    public function __construct(string $username, string $nickname, string $password, string $phone, bool $enable) {
        $this->username = $username;
        $this->nickname = $nickname;
        $this->password = $password;
        $this->phone = $phone;
        $this->enable = $enable;
    }

    public function get_username(): string {
        return $this->username;
    }

    public function set_username(string $username): void {
        $this->username = $username;
    }

    public function get_nickname(): string {
        return $this->nickname;
    }

    public function set_nickname(string $nickname): void {
        $this->nickname = $nickname;
    }

    public function get_password(): string {
        return $this->password;
    }

    public function set_password(string $password): void {
        $this->password = $password;
    }

    public function get_phone(): string {
        return $this->phone;
    }

    public function set_phone(string $phone): void {
        $this->phone = $phone;
    }

    public function is_enable(): bool {
        return $this->enable;
    }

    public function set_enable(bool $enable): void {
        $this->enable = $enable;
    }

}