<?php

namespace dto\response\admin;

class UserBriefListDto {
    private array $user_brief_arr;

    public function __construct(array $user_brief_arr) {
        $this->user_brief_arr = $user_brief_arr;
    }

    public function get_user_brief_arr(): array {
        return $this->user_brief_arr;
    }

    public function set_user_brief_arr(array $user_brief_arr): void {
        $this->user_brief_arr = $user_brief_arr;
    }

}