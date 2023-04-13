<?php

namespace dto\request\user;

class QuestionnaireCreateDto {
    private string $title;
    private int $begin_date;
    private int $end_date;
    private array $question;

    public function __construct(string $title, int $begin_date, int $end_date, array $question = []) {
        $this->title = $title;
        $this->begin_date = $begin_date;
        $this->end_date = $end_date;
        $this->question = $question;
    }

    public function get_title(): string {
        return $this->title;
    }

    public function set_title(string $title): void {
        $this->title = $title;
    }

    public function get_begin_date(): int {
        return $this->begin_date;
    }

    public function set_begin_date(int $begin_date): void {
        $this->begin_date = $begin_date;
    }

    public function get_end_date(): int {
        return $this->end_date;
    }

    public function set_end_date(int $end_date): void {
        $this->end_date = $end_date;
    }

    public function get_question(): array {
        return $this->question;
    }

    public function set_question(array $question): void {
        $this->question = $question;
    }

}