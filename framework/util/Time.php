<?php

namespace framework\util;

class Time {
    public static function current_time_millis(): int {
        $time = explode(' ',microtime());
        $ts =  $time[1] . str_pad((int)(floatval($time[0])*1000), 3, '0');
        return (int)$ts;
    }
}