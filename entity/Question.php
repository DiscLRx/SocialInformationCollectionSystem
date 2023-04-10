<?php

namespace entity;

class Question {
    private int $id;
    private int $questionnaire_id;
    private int $order;
    private string $type;
    private string $content;
    private ?array $option_arr;

    public function __construct(int $id, int $questionnaire_id, int $order, string $type, string $content, ?array $option_arr = null) {
        $this->id = $id;
        $this->questionnaire_id = $questionnaire_id;
        $this->order = $order;
        $this->type = $type;
        $this->content = $content;
        $this->option_arr = $option_arr;
    }

    public function get_id(): int {
        return $this->id;
    }

    public function set_id(int $id): void {
        $this->id = $id;
    }

    public function get_questionnaire_id(): int {
        return $this->questionnaire_id;
    }

    public function set_questionnaire_id(int $questionnaire_id): void {
        $this->questionnaire_id = $questionnaire_id;
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

    public function get_option_arr(): ?array {
        return $this->option_arr;
    }

    public function set_option_arr(?array $option_arr): void {
        $this->option_arr = $option_arr;
    }

}