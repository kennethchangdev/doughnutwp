<?php


namespace WooDostavista\GeneralCache;

use wpdb;
use WooDostavista\GeneralCache\CacheItem;
use DateTime;

class DBCacheManager
{
    /** @var wpdb */
    private $wpdb;

    public function __construct(wpdb $wpdb)
    {
        $this->wpdb = $wpdb;
        $this->createTablesIfNotExists();
    }

    public function createTablesIfNotExists()
    {
        $this->wpdb->query(
            "
                CREATE TABLE IF NOT EXISTS `woo_dostavista_cache` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `key` VARCHAR(255) NOT NULL,
                    `value` TEXT NOT NULL,
                    `expired_datetime` DATETIME DEFAULT NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE INDEX `idx_dostavista_cache_key_unique` (`key` ASC)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            "
        );
    }

    public function setData(string $key, string $value, string $expiredDatetime = null)
    {
        if ($this->issetKey($key)) {
            $this->wpdb->query(
                $this->wpdb->prepare(
                    "UPDATE `woo_dostavista_cache` SET `value` = %s, `expired_datetime` = %s  WHERE `key` = %s",
                    trim($value),
                    $expiredDatetime ?? null,
                    $key
                )
            );
        } else {
            $this->wpdb->query(
                $this->wpdb->prepare(
                    "INSERT INTO `woo_dostavista_cache` (`key`, `value`, `expired_datetime`) VALUES (%s, %s, %s)",
                    $key,
                    trim($value),
                    $expiredDatetime ?? null
                )
            );
        }
    }

    public function issetKey(string $key): bool
    {
        $rows = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM `woo_dostavista_cache` WHERE `key` = %s LIMIT 1", $key
            )
        );
        if (!$rows) {
            return false;
        }

        // Если запись в бд есть, то сначала ее проверим, не протухла ли она
        if (isset($rows[0]->expired_datetime) && $rows[0]->expired_datetime) {
            $expiredDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $rows[0]->expired_datetime);

            // Посчитаем разницу во времени
            if ($expiredDateTime < (new DateTime())) {
                $this->deleteData($key);
                return false;
            }
        }
        return true;
    }

    public function deleteData(string $key)
    {
        $this->wpdb->query(
            $this->wpdb->prepare(
                "DELETE FROM `woo_dostavista_cache` WHERE `key` = %s",
                $key
            )
        );
    }

    /**
     * @param string $key
     * @return string|null
     */
    public function getData(string $key)
    {
        if (!$this->issetKey($key)) {
            return null;
        }

        $rows = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM `woo_dostavista_cache` WHERE `key` = %s LIMIT 1", $key
            )
        );

        if (isset($rows[0]->value)) {
            return $rows[0]->value;
        }

        return null;
    }

    public function getExpirationInSeconds(string $key): int
    {
        if (!$this->issetKey($key)) {
            return 0;
        }

        $rows = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM `woo_dostavista_cache` WHERE `key` = %s LIMIT 1", $key
            )
        );

        if (isset($rows[0]->expired_datetime)) {
            $expiredDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $rows[0]->expired_datetime);

            // Посчитаем разницу во времени в секундах
            return $diff = $expiredDateTime->getTimestamp() - (new DateTime())->getTimestamp();
        }

        return 0;
    }
}