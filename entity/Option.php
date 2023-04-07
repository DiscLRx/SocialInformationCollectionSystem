<?php

namespace entity;

class Option {
    private int $id;
    private int $question_id;
    private int $order;
    private string $content;

    public function __construct(int $id, int $question_id, int $order, string $content) {
        $this->id = $id;
        $this->question_id = $question_id;
        $this->order = $order;
        $this->content = $content;
    }

    public function get_id(): int {
        return $this->id;
    }

    public function set_id(int $id): void {
        $this->id = $id;
    }

    public function get_question_id(): int {
        return $this->question_id;
    }

    public function set_question_id(int $question_id): void {
        $this->question_id = $question_id;
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