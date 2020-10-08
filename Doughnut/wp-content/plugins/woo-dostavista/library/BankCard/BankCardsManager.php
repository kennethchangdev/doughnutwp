<?php

namespace WooDostavista\BankCard;

use WooDostavista\DvCmsModuleApiClient\DvCmsModuleApiClient;
use WooDostavista\DvCmsModuleApiClient\DvCmsModuleApiHttpException;
use WooDostavista\DvCmsModuleApiClient\Response\BankCardsResponseModel;
use WooDostavista\GeneralCache\Cache;
use WooDostavista\GeneralCache\CacheItem;

class BankCardsManager
{
    /**
     * @param DvCmsModuleApiClient $dvCmsModuleApiClient
     * @param int $shopId
     * @return BankCard[]|array
     */
    public static function getShopBankCards(DvCmsModuleApiClient $dvCmsModuleApiClient): array
    {
        global $wpdb;

        $cache = new Cache($wpdb);

        $cacheKey   = static::getShopCardsCacheKey(); // Ключ кеша
        $cacheGroup = static::getShopCacheGroup(); // Группа кеша

        $cacheTtl = 86400; // Храним сутки

        if ($cache->hasItem($cacheKey, $cacheGroup)) {
            $cacheItem = $cache->getItem($cacheKey, $cacheGroup);
            return BankCardCollectionSerializer::unserialize(
                $cacheItem->get()
            );
        } else {
            try {
                $responseData = $dvCmsModuleApiClient->bankCards();
                if ($responseData->isSuccessful()) {
                    $bankCardsResponceModel = new BankCardsResponseModel($responseData->getData());
                    $cards = $bankCardsResponceModel->getCards();

                    // Закешируем полученный результат
                    $cacheItem = new CacheItem(
                        $cacheKey,
                        BankCardCollectionSerializer::serialize($cards),
                        $cacheGroup,
                        $cacheTtl
                    );
                    $cache->save($cacheItem);
                    return $cards;
                }
            } catch (DvCmsModuleApiHttpException $e) {

            }
        }
        return [];
    }

    public static function updateShopBankCardsCache(DvCmsModuleApiClient $dvCmsModuleApiClient): array
    {
        global $wpdb;

        $cache = new Cache($wpdb);

        $cacheKey   = static::getShopCardsCacheKey(); // Ключ кеша
        $cacheGroup = static::getShopCacheGroup(); // Группа кеша
        if ($cache->hasItem($cacheKey, $cacheGroup)) {
            $cache->deleteItem($cacheKey, $cacheGroup);
        }

        return static::getShopBankCards($dvCmsModuleApiClient);
    }

    private static function getShopCardsCacheKey(): string
    {
        return "shop:cards";
    }

    private static function getShopCacheGroup(): string
    {
        return 'woodostavista';
    }
}