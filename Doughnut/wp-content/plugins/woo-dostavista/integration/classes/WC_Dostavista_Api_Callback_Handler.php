<?php

use WooDostavista\DostavistaOrders\DostavistaOrderManager;
use WooDostavista\DvCmsModuleApiClient\Response\DeliveryResponseModel;
use WooDostavista\DvCmsModuleApiClient\Response\OrderResponseModel;
use WooDostavista\ModuleSettings\ModuleSettings;

defined('ABSPATH') || exit;

class WC_Dostavista_Api_Callback_Handler
{
    public static function handler()
    {
        global $wpdb;

        $settings = new ModuleSettings($wpdb);

        $callbackSecretKey = $settings->getApiCallbackSecretKey();
        if (!$callbackSecretKey) {
            echo 'ERROR: API callback secret key is empty';
            exit;
        }

        if (!isset($_SERVER['HTTP_X_DV_SIGNATURE'])) {
            echo 'ERROR: Signature not found';
            exit;
        }

        $dataJson = file_get_contents('php://input');

        $signature = hash_hmac('sha256', $dataJson, $callbackSecretKey);
        if ($signature != $_SERVER['HTTP_X_DV_SIGNATURE']) {
            echo 'ERROR: Signature not found';
            exit;
        }

        $data = json_decode($dataJson, true);

        if (!empty($data['delivery'])) {
            static::processDeliveryEvent($data['delivery']);
        } else if (!empty($data['order'])) {
            $responseDostavistaOrder = new OrderResponseModel($data['order']);

            $dostavistaOrderManager = new DostavistaOrderManager($wpdb);
            $dostavistaOrder = $dostavistaOrderManager->getByDostavistaOrderId($responseDostavistaOrder->getOrderId());
            if (!$dostavistaOrder) {
                echo 'INFO: Dostavista order not found';
                exit;
            }

            $dostavistaOrder->courierName  = $responseDostavistaOrder->getCourier() ? $responseDostavistaOrder->getCourier()->getName() : '';
            $dostavistaOrder->courierPhone = $responseDostavistaOrder->getCourier() ? $responseDostavistaOrder->getCourier()->getPhone() : '';
            $dostavistaOrderManager->save($dostavistaOrder);
        }
    }

    private static function processDeliveryEvent(array $deliveryData)
    {
        global $wpdb;

        $settings = new ModuleSettings($wpdb);

        $deliveryDto = new DeliveryResponseModel($deliveryData);

        $clientOrderId = $deliveryDto->getClientOrderId();
        if (!$clientOrderId) {
            return;
        }

        $deliveryStatusMap = [
            DeliveryResponseModel::STATUS_DRAFT            => $settings->getWooCommerceOrderStatusDraft(),
            DeliveryResponseModel::STATUS_PLANNED          => $settings->getWooCommerceOrderStatusAvailable(),
            DeliveryResponseModel::STATUS_COURIER_ASSIGNED => $settings->getWooCommerceOrderStatusCourierAssigned(),
            DeliveryResponseModel::STATUS_ACTIVE           => $settings->getWooCommerceOrderStatusActive(),
            DeliveryResponseModel::STATUS_PARCEL_PICKED_UP => $settings->getWooCommerceOrderStatusParcelPickedUp(),
            DeliveryResponseModel::STATUS_COURIER_DEPARTED => $settings->getWooCommerceOrderStatusCourierDeparted(),
            DeliveryResponseModel::STATUS_COURIER_ARRIVED  => $settings->getWooCommerceOrderStatusCourierArrived(),
            DeliveryResponseModel::STATUS_FINISHED         => $settings->getWooCommerceOrderStatusCompleted(),
            DeliveryResponseModel::STATUS_FAILED           => $settings->getWooCommerceOrderStatusFailed(),
            DeliveryResponseModel::STATUS_CANCELED         => $settings->getWooCommerceOrderStatusCanceled(),
            DeliveryResponseModel::STATUS_DELAYED          => $settings->getWooCommerceOrderStatusDelayed(),
        ];

        foreach ($deliveryStatusMap as $dvStatus => $wooOrderStatusCode) {
            if ($deliveryDto->getStatus() === $dvStatus && $wooOrderStatusCode) {
                $wooOrder = wc_get_order($clientOrderId);
                if ($wooOrder) {
                    $wooOrder->set_status($wooOrderStatusCode);
                    $wooOrder->save();
                }
            }
        }
    }
}
