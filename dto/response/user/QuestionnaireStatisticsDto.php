<?php

namespace dto\response\user;

class QuestionnaireStatisticsDto {
    private ?string $title;
    private ?int $count;
    private array $question;

    public function __construct(?string $title = null, ?int $count = null, array $question = []) {
        $this->title = $title;
        $this->count = $count;
        $this->question = $question;
    }

    public function get_title(): ?string {
        return $this->title;
    }

    public function set_title(?string $title): void {
        $this->title = $title;
    }

    public function get_count(): ?int {
        return $this->count;
    }

    public function set_count(?int $count): void {
        $this->count = $count;
    }

    public function get_question(): array {
        return $this->question;
    }

    public function set_question(array $question): void {
        $this->question = $question;
    }

}