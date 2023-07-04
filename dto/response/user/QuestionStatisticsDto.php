<?php

namespace dto\response\user;

class QuestionStatisticsDto {
    private ?int $order;
    private ?string $type;
    private ?string $content;
    private array $answers;

    public function __construct(?int $order = null, ?string $type = null, ?string $content = null, array $answers = []) {
        $this->order = $order;
        $this->type = $type;
        $this->content = $content;
        $this->answers = $answers;
    }

    public function get_order(): ?int {
        return $this->order;
    }

    public function set_order(?int $order): void {
        $this->order = $order;
    }

    public function get_type(): ?string {
        return $this->type;
    }

    public function set_type(?string $type): void {
        $this->type = $type;
    }

    public function get_content(): ?string {
        return $this->content;
    }

    public function set_content(?string $content): void {
        $this->content = $content;
    }

    public function get_answers(): array {
        return $this->answers;
    }

    public function set_answers(array $answers): void {
        $this->answers = $answers;
    }

}