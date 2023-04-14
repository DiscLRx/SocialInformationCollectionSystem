<?php

namespace dto\request\user;

class QuestionAnswerDto {
    private int $order;
    private array $answer;

    public function __construct(int $order, array $answer) {
        $this->order = $order;
        $this->answer = $answer;
    }

    public function get_order(): int {
        return $this->order;
    }

    public function set_order(int $order): void {
        $this->order = $order;
    }

    public function get_answer(): array {
        return $this->answer;
    }

    public function set_answer(array $answer): void {
        $this->answer = $answer;
    }

}