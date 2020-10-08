<?php

namespace WooDostavista\DostavistaAuth;


use WooDostavista\DvCmsModuleApiClient\DvCmsModuleApiClient;
use WooDostavista\DvCmsModuleApiClient\DvCmsModuleApiHttpException;
use WooDostavista\DvCmsModuleApiClient\Response\ClientProfileResponseModel;
use WooDostavista\GeneralCache\Cache;
use WooDostavista\GeneralCache\CacheItem;
use WooDostavista\ModuleSettings\ModuleSettings;

class DostavistaClientManager
{
    public static function getAllowedPaymentMethods(): array
    {
        global $wpdb;
        $settings = new ModuleSettings($wpdb);

        $dvCmsModuleApiClient = new DvCmsModuleApiClient($settings->getApiUrl(), $settings->getAuthToken());
        $cache = new Cache($wpdb);

        // Закешируем результат
        $cacheKey   = "dv:payment:methods:{$settings->getIsApiTest()}:{$settings->getAuthToken()}";
        $cacheGroup = 'api';
        $cacheTtl   = 86400; // Храним сутки

        $paymentMethods = [];
        if ($cache->hasItem($cacheKey)) {
            $cacheItem = $cache->getItem($cacheKey, $cacheGroup);
            $paymentMethods = unserialize($cacheItem->get());
        } else {
            // Получим данные профиля из достависты
            try {
                $dvResponse = $dvCmsModuleApiClient->getClientProfile();
                if ($dvResponse->isSuccessful() && isset($dvResponse->getData()['client'])) {
                    $clientProfileResponseModel = new ClientProfileResponseModel($dvResponse->getData()['client']);
                    $paymentMethods = $clientProfileResponseModel->getPaymentMethods();
                }
            } catch (DvCmsModuleApiHttpException $e) {
                return [];
            }

            $cacheItem = new CacheItem(
                $cacheKey,
                serialize($paymentMethods),
                $cacheGroup,
                $cacheTtl
            );

            $cache->save($cacheItem);
        }

        return $paymentMethods;
    }

    public static function logoutFromDostavista(): bool
    {
        global $wpdb;
        $settings = new ModuleSettings($wpdb);

        $settings->setCmsModuleApiTestAuthToken('');
        $settings->setCmsModuleApiProdAuthToken('');

        return true;
    }
}
