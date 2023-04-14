<?php

namespace dto\request\user;

class QuestionnaireAnswerDto {
    private int $id;
    private array $answers;

    public function __construct(int $id, array $answers) {
        $this->id = $id;
        $this->answers = $answers;
    }

    public function get_id(): int {
        return $this->id;
    }

    public function set_id(int $id): void {
        $this->id = $id;
    }

    public function get_answers(): array {
        return $this->answers;
    }

    public function set_answers(array $answers): void {
        $this->answers = $answers;
    }

}