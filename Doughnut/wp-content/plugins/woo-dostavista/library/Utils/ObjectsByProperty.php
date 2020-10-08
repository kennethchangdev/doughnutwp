<?php

namespace WooDostavista\Utils;

class ObjectsByProperty
{
    public static function get(string $property, array $objects)
    {
        $objectsByKey = [];
        foreach($objects as $object) {
            $objectsByKey[$object->{$property}] = $object;
        }

        return $objectsByKey;
    }
}
