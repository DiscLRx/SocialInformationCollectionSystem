<?php

namespace dto\response\user;

class QuestionnaireListDto {
    private array $questionnaires;

    public function __construct(array $questionnaires) {
        $this->questionnaires = $questionnaires;
    }

    public function get_questionnaires(): array {
        return $this->questionnaires;
    }

    public function set_questionnaires(array $questionnaires): void {
        $this->questionnaires = $questionnaires;
    }

}