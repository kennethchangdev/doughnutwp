<?php

namespace WooDostavista\Enums;

class ScheduleTimeEnum
{
    public static function getEnum(): array
    {
        $enum = [];
        for ($h = 0; $h <= 23; $h++) {
            for ($i = 0; $i < 60; $i += 30) {
                $timeValue = date('H:i', strtotime("today {$h}:{$i}"));
                $enum[$timeValue] = $timeValue;
            }
        }

        return $enum;
    }
}
