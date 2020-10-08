<?php

class WC_Dostavista_Lang
{
    const COUNTRY_RU = 'ru'; // Россия
    const COUNTRY_BR = 'br'; // Бразилия
    const COUNTRY_ID = 'id'; // Индонезия
    const COUNTRY_IN = 'in'; // Индия
    const COUNTRY_MX = 'mx'; // Мексика
    const COUNTRY_MY = 'my'; // Малайзия
    const COUNTRY_PH = 'ph'; // Филипины
    const COUNTRY_TR = 'tr'; // Турция
    const COUNTRY_VN = 'vn'; // Вьетнам

    /** @var string */
    private static $country;

    /** @var array */
    private static $data = [];

    public static function get(string $key): string
    {
        $config = static::getConfigs();
        if (static::$country === null) {
            static::setCountry($config['country']);
        }

        if (!isset(static::$data[static::$country])) {
            static::$data[static::$country] = include(dirname(WC_DOSTAVISTA_PLUGIN_FILE) . '/i18n/' . static::$country . '.php');
            if (!static::$data[static::$country]) {
                static::$data[static::$country] = [];
            }
        }

        return static::$data[static::$country][$key] ?? $key;
    }

    public static function getHtml(string $key): string
    {
        return htmlspecialchars(_wp_specialchars(static::get($key)));
    }

    public static function setCountry(string $country)
    {
        static::$country = $country;
    }

    public static function getCountry(): string
    {
        if (!isset(static::$country)) {
            $config = static::getConfigs();
            static::setCountry($config['country']);
        }

        return static::$country ?? '';
    }

    public static function getConfigs(): array
    {
        return include(dirname(WC_DOSTAVISTA_PLUGIN_FILE) . "/library/ModuleConfig/module_config.php");
    }
}
