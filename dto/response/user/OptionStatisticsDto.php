<?php

namespace dto\response\user;

class OptionStatisticsDto {
    private int $order;
    private string $content;
    private int $count;

    public function __construct(?int $order = null, ?string $content = null, ?int $count = null) {
        $this->order = $order;
        $this->content = $content;
        $this->count = $count;
    }

    public function get_order(): ?int {
        return $this->order;
    }

    public function set_order(?int $order): void {
        $this->order = $order;
    }

    public function get_content(): ?string {
        return $this->content;
    }

    public function set_content(?string $content): void {
        $this->content = $content;
    }

    public function get_count(): ?int {
        return $this->count;
    }

    public function set_count(?int $count): void {
        $this->count = $count;
    }

}