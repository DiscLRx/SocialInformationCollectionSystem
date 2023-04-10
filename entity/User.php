<?php

namespace entity;

class User {
    private int $id;
    private string $username;
    private ?string $password;
    private string $nickname;
    private string $phone;
    private string $authority;
    private bool $enable;
    private ?array $questionnaire_arr;

    public function __construct(int $id, string $username, ?string $password, string $nickname, string $phone, string $authority, bool $enable, ?array $questionnaire_arr = null) {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->nickname = $nickname;
        $this->phone = $phone;
        $this->authority = $authority;
        $this->enable = $enable;
        $this->questionnaire_arr = $questionnaire_arr;
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

    public function get_password(): ?string {
        return $this->password;
    }

    public function set_password(?string $password): void {
        $this->password = $password;
    }

    public function get_nickname(): string {
        return $this->nickname;
    }

    public function set_nickname(string $nickname): void {
        $this->nickname = $nickname;
    }

    public function get_phone(): string {
        return $this->phone;
    }

    public function set_phone(string $phone): void {
        $this->phone = $phone;
    }

    public function get_authority(): string {
        return $this->authority;
    }

    public function set_authority(string $authority): void {
        $this->authority = $authority;
    }

    public function is_enable(): bool {
        return $this->enable;
    }

    public function set_enable(bool $enable): void {
        $this->enable = $enable;
    }

    public function get_questionnaire_arr(): ?array {
        return $this->questionnaire_arr;
    }

    public function set_questionnaire_arr(?array $questionnaire_arr): void {
        $this->questionnaire_arr = $questionnaire_arr;
    }

}