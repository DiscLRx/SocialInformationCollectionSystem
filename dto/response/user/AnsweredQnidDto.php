<?php

namespace dto\response\user;

class AnsweredQnidDto {
    private array $qnid;

    public function __construct(array $qnid) {
        $this->qnid = $qnid;
    }

    public function get_qnid(): array {
        return $this->qnid;
    }

    public function set_qnid(array $qnid): void {
        $this->qnid = $qnid;
    }

}