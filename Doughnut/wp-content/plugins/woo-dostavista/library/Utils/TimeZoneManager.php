<?php

namespace WooDostavista\Utils;

use DateTimeZone;

class TimeZoneManager
{
    /** @var DateTimeZone */
    private $dateTimeZone;

    public function __construct(string $optionTimeZoneString, string $optionGmtOffset)
    {
        if (!empty($optionTimeZoneString)) {
            $this->dateTimeZone = new DateTimeZone($optionTimeZoneString);
        } else {
            $offset  = $optionGmtOffset;
            if ($offset) {
                $hours              = (int) $offset;
                $minutes            = abs(($offset - (int) $offset) * 60);
                $offset             = sprintf('%+03d:%02d', $hours, $minutes);
                $this->dateTimeZone = new DateTimeZone($offset);
            } else {
                $this->dateTimeZone = new DateTimeZone(date_default_timezone_get() ?: ini_get('date.timezone'));
            }
        }
    }

    public function getDateTimeZone(): DateTimeZone
    {
        return $this->dateTimeZone;
    }
}
