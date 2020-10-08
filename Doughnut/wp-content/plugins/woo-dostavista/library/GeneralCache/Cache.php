<?php

namespace WooDostavista\GeneralCache;

use WooDostavista\GeneralCache\CacheItem;
use DateTime;
use DateInterval;
use wpdb;

class Cache
{
    /** @var DBCacheManager */
    private $dbCacheManager;

    public function __construct(wpdb $wpdb)
    {
        $this->dbCacheManager = new DBCacheManager($wpdb);
    }

    public function save(CacheItem $item)
    {
        $expirationInSeconds = $item->getExpirationInSeconds();

        // Сначала сохраним в кеш ВП
        if ($expirationInSeconds) {
            wp_cache_set(
                $item->getKey(),
                $item->get(),
                $item->getGroup() ?? '',
                $expirationInSeconds
            );
        } else {
            wp_cache_set(
                $item->getKey(),
                $item->get(),
                $item->getGroup() ?? ''
            );
        }

        // Расчитаем ttl кеша
        $expiredDatetime = !$expirationInSeconds
            ? null
            : (new DateTime())->add(new DateInterval("PT{$expirationInSeconds}S"));

        // Установим данные в БД
        $this->dbCacheManager->setData(
            $item->getKey(),
            $item->get(),
            $expiredDatetime ? $expiredDatetime->format('Y-m-d H:i:s') : null
        );
    }

    public function hasItem(string $key, string $group = ''): bool
    {
        /*
         * По умолчанию кэширование объектов в WordPress непостоянно,
         *  т.е. работает для одного запроса (генерации одной страницы) и не работает между запросами.
         *  По ходу генерации страницы объектный кэш записывается в оперативную память и берется от туда.
         * Так происходит при каждом запросе.
         *
         * По этому сначала проверяем в вордпрессовском кеше, если там нет, то проверяем в бд.
         */
        if (wp_cache_get($key, $group)) {
            return true;
        } else {
            return $this->dbCacheManager->issetKey($key);
        }
    }

    public function deleteItem(string $key, string $group = ''): bool
    {
        if ($this->hasItem($key, $group)) {
           wp_cache_delete($key, $group);
           $this->dbCacheManager->deleteData($key);
        }
        return false;
    }

    /**
     * @param $key
     * @return CacheItem|null
     */
    public function getItem(string $key, string $group = '')
    {
        if ($this->hasItem($key, $group)) {
            if (wp_cache_get($key, $group)) {
                $item = new CacheItem(
                    $key,
                    wp_cache_get($key, $group),
                    $group,
                    0 // Т.к у вордпрессовского кеша нет метода получения срока хранения элемента, пишем 0
                );
                return $item;
            } else {
                return new CacheItem(
                    $key,
                    $this->dbCacheManager->getData($key),
                    $group,
                    $this->dbCacheManager->getExpirationInSeconds($key)
                );
            }
        }

        return null;
    }

    /**
     * @param array $data Данные по элементам кеша в формате ['cache_group' => 'cache_key1', cache_group' => 'cache_key2']
     */
    public function deleteItems(array $data)
    {
        foreach ($data as $group => $key) {
            if ($this->hasItem($key, $group)) {
                $this->deleteItem($key, $group);
            }
        }
    }

    /**
     * @param array $data  Данные по элементам кеша в формате ['cache_group' => 'cache_key1', cache_group' => 'cache_key2']
     * @return CacheItem[]|array
     */
    public function getItems(array $data): array
    {
        $items = [];

        foreach ($data as $group => $key) {
            if ($this->hasItem($key, $group)) {
                $items[] = $this->getItem($key, $group);
            }
        }

        return $items;
    }
}