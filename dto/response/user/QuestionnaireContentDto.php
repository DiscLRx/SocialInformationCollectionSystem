<?php

namespace dto\response\user;

class QuestionnaireContentDto {
    private int $id;
    private string $title;
    private array $question;

    public function __construct(int $id, string $title, array $question = []) {
        $this->id = $id;
        $this->title = $title;
        $this->question = $question;
    }

    public function get_id(): int {
        return $this->id;
    }

    public function set_id(int $id): void {
        $this->id = $id;
    }

    public function get_title(): string {
        return $this->title;
    }

    public function set_title(string $title): void {
        $this->title = $title;
    }

    public function get_question_(): array {
        return $this->question;
    }

    public function set_question(array $question): void {
        $this->question = $question;
    }
}