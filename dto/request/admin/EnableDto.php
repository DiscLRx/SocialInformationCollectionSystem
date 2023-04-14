<?php

namespace dto\request\admin;

class EnableDto {
    private bool $enable;

    public function __construct(bool $enable) {
        $this->enable = $enable;
    }

    public function is_enable(): bool {
        return $this->enable;
    }

    public function set_enable(bool $enable): void {
        $this->enable = $enable;
    }

}