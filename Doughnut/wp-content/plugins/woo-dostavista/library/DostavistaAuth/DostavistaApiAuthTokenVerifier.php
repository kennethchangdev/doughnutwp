<?php

namespace WooDostavista\DostavistaAuth;

use WooDostavista\DvCmsModuleApiClient\DvCmsModuleApiClient;
use WooDostavista\DvCmsModuleApiClient\DvCmsModuleApiHttpException;
use WooDostavista\GeneralCache\Cache;
use WooDostavista\GeneralCache\CacheItem;
use WooDostavista\ModuleSettings\ModuleSettings;

class DostavistaApiAuthTokenVerifier
{
    public static function isCmsModuleApiTokenValid(): bool
    {
        global $wpdb;
        $settings = new ModuleSettings($wpdb);

        if (!trim($settings->getAuthToken())) {
            return false;
        }

        $dvCmsModuleApiClient = new DvCmsModuleApiClient($settings->getApiUrl(), $settings->getAuthToken());
        $cache = new Cache($wpdb);

        // Закешируем результат
        $cacheKey   = "dv:api:token:{$settings->getIsApiTest()}:{$settings->getAuthToken()}:valid";
        $cacheGroup = 'api';
        $cacheTtl = 86400; // Храним сутки

        if ($cache->hasItem($cacheKey)) {
            $cacheResult = $cache->getItem($cacheKey, $cacheGroup);
            return (bool) $cacheResult->get();
        } else {
            try {
                $responseResult = false;
                // Оправим любой запрос на достависту и обработаем ответ
                $response = $dvCmsModuleApiClient->getVehicleTypes();
                $errors = $response->getErrors();
                if ($response->isSuccessful()) {
                    $responseResult = true;
                } elseif (!$response->isSuccessful() && isset($errors[0]) && $errors[0] === 'invalid_auth_token') {
                    $responseResult = false;
                }
            } catch (DvCmsModuleApiHttpException $exception) {
                return true;
            }

            $cacheItem = new CacheItem(
                $cacheKey,
                (int) $responseResult,
                $cacheGroup,
                $cacheTtl
            );

            $cache->save($cacheItem);

            return (bool) $responseResult;
        }

        return false;
    }
}