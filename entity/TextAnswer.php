<?php

namespace entity;

class TextAnswer {
    private int $question_id;
    private int $user_id;
    private string $text;

    public function __construct(int $question_id, int $user_id, string $text) {
        $this->question_id = $question_id;
        $this->user_id = $user_id;
        $this->text = $text;
    }

    public function get_question_id(): int {
        return $this->question_id;
    }

    public function set_question_id(int $question_id): void {
        $this->question_id = $question_id;
    }

    public function get_user_id(): int {
        return $this->user_id;
    }

    public function get_text(): string {
        return $this->text;
    }

    public function set_text(string $text): void {
        $this->text = $text;
    }

    public function set_user_id(int $user_id): void {
        $this->user_id = $user_id;
    }

}