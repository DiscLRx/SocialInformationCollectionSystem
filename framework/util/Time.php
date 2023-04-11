<?php

namespace framework\util;

class Time {
    public static function current_ts(): int {
        $time = explode(' ', microtime());
        $ts = $time[1] . str_pad((int)(floatval($time[0]) * 1000), 3, '0');
        return (int)$ts;
    }

    public static function ts_after(int $origin_ts, int $day, int $hour = 0, int $minute = 0, int $second = 0, int $millisecond = 0): int {
        return self::calculate_ts($origin_ts, $day, $hour, $minute, $second, $millisecond, true);
    }

    public static function ts_before(int $origin_ts, int $day, int $hour = 0, int $minute = 0, int $second = 0, int $millisecond = 0): int {
        return self::calculate_ts($origin_ts, $day, $hour, $minute, $second, $millisecond, false);
    }

    private static function calculate_ts(int $origin_ts, int $day, int $hour, int $minute, int $second, int $millisecond, bool $after): int {
        $offset = $day * 86400000 + $hour * 3600000 + $minute * 60000 + $second * 1000 + $millisecond;
        if ($after) {
            return $origin_ts + $offset;
        } else {
            return $origin_ts - $offset;
        }
    }

}