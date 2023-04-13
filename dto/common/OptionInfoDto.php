<?php

namespace dto\universal;

class OptionInfoDto {
    private int $order;
    private string $content;

    public function __construct(int $order, string $content) {
        $this->order = $order;
        $this->content = $content;
    }

    public function get_order(): int {
        return $this->order;
    }

    public function set_order(int $order): void {
        $this->order = $order;
    }

    public function get_content(): string {
        return $this->content;
    }

    public function set_content(string $content): void {
        $this->content = $content;
    }

}