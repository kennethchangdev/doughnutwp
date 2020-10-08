<?php

namespace WooDostavista\Enums;

use WC_Dostavista_Lang;

class ScheduleDateEnum
{
    public static function getEnum(): array
    {
        $enum = [];
        for ($i = 0; $i <= 9; $i++) {
            $dateValue = date('d.m.Y', strtotime("+$i days"));
            $dateTitles = [
                WC_Dostavista_Lang::getHtml('schedule_date_enum_today'),
                WC_Dostavista_Lang::getHtml('schedule_date_enum_tomorrow'),
            ];
            $enum[$dateValue] = $dateTitles[$i] ?? $dateValue;
        }

        return $enum;
    }
}
