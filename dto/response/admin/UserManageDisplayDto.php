<?php

namespace dto\response\admin;

class UserManageDisplayDto {
    private int $id;
    private string $username;
    private string $nickname;
    private string $phone;
    private bool $enable;

    public function __construct(int $id, string $username, string $nickname, string $phone, bool $enable) {
        $this->id = $id;
        $this->username = $username;
        $this->nickname = $nickname;
        $this->phone = $phone;
        $this->enable = $enable;
    }

    public function getId(): int {
        return $this->id;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function setUsername(string $username): void {
        $this->username = $username;
    }

    public function getNickname(): string {
        return $this->nickname;
    }

    public function setNickname(string $nickname): void {
        $this->nickname = $nickname;
    }

    public function getPhone(): string {
        return $this->phone;
    }

    public function setPhone(string $phone): void {
        $this->phone = $phone;
    }

    public function isEnable(): bool {
        return $this->enable;
    }

    public function setEnable(bool $enable): void {
        $this->enable = $enable;
    }

}