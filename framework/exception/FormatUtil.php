<?php

namespace framework\exception;

class FormatUtil {
    public static function trace_line(int $index, array $item): string {
        return "Trace #{$index}: " .
            (isset($item['file']) ? "file \"{$item['file']}\" " : "") .
            (isset($item['line']) ? "line {$item['line']}:    " : "") .
            ((isset($item['class'])) ? "{$item['class']} " : "class? ") .
            ((isset($item['type'])) ? "{$item['type']} " : "type? ") .
            ((isset($item['function'])) ? "{$item['function']}()" : "function?");
    }
}