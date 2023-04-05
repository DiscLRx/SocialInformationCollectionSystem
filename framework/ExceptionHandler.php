<?php

namespace framework;

use Error;
use Exception;

interface ExceptionHandler {
    public function handle(Exception|Error $e);
}