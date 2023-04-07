<?php

namespace entity;

class Visitor {
    private int $id;
    private array $choice_answer_arr;
    private array $text_answer_arr;

    public function __construct(int $id, array $choice_answer_arr = [], array $text_answer_arr = []) {
        $this->id = $id;
        $this->choice_answer_arr = $choice_answer_arr;
        $this->text_answer_arr = $text_answer_arr;
    }

    public function get_id(): int {
        return $this->id;
    }

    public function set_id(int $id): void {
        $this->id = $id;
    }

    public function get_choice_answer_arr(): array {
        return $this->choice_answer_arr;
    }

    public function set_choice_answer_arr(array $choice_answer_arr): void {
        $this->choice_answer_arr = $choice_answer_arr;
    }

    public function get_text_answer_arr(): array {
        return $this->text_answer_arr;
    }

    public function set_text_answer_arr(array $text_answer_arr): void {
        $this->text_answer_arr = $text_answer_arr;
    }

}