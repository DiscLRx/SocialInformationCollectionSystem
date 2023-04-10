<?php

namespace entity;

class Questionnaire {
    private int $id;
    private int $user_id;
    private string $title;
    private int $begin_date;
    private int $end_date;
    private bool $enable;
    private ?array $question_arr;

    public function __construct(int $id, int $user_id, string $title, int $begin_date, int $end_date, bool $enable, ?array $question_arr = null) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->title = $title;
        $this->begin_date = $begin_date;
        $this->end_date = $end_date;
        $this->enable = $enable;
        $this->question_arr = $question_arr;
    }

    public function get_id(): int {
        return $this->id;
    }

    public function set_id(int $id): void {
        $this->id = $id;
    }

    public function get_user_id(): int {
        return $this->user_id;
    }

    public function set_user_id(int $user_id): void {
        $this->user_id = $user_id;
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

    public function is_enable(): bool {
        return $this->enable;
    }

    public function set_enable(bool $enable): void {
        $this->enable = $enable;
    }

    public function get_question_arr(): ?array {
        return $this->question_arr;
    }

    public function set_question_arr(?array $question_arr): void {
        $this->question_arr = $question_arr;
    }

}