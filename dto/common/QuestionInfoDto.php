<?php

namespace dto\universal;

class QuestionInfoDto {
    private int $order;
    private string $type;
    private string $content;
    private array $option;

    public function __construct( int $order, string $type, string $content, array $option = []) {
        $this->order = $order;
        $this->type = $type;
        $this->content = $content;
        $this->option = $option;
    }

    public function get_order(): int {
        return $this->order;
    }

    public function set_order(int $order): void {
        $this->order = $order;
    }

    public function get_type(): string {
        return $this->type;
    }

    public function set_type(string $type): void {
        $this->type = $type;
    }

    public function get_content(): string {
        return $this->content;
    }

    public function set_content(string $content): void {
        $this->content = $content;
    }

    public function get_option(): array {
        return $this->option;
    }

    public function set_option(array $option): void {
        $this->option = $option;
    }

}