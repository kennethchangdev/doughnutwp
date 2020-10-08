<?php

use WooDostavista\DvCmsModuleApiClient\DvCmsModuleApiClient;
use WooDostavista\DvCmsModuleApiClient\DvCmsModuleApiHttpException;
use WooDostavista\DvCmsModuleApiClient\Request\OrderRequestModel;
use WooDostavista\DvCmsModuleApiClient\Request\PlainPointRequestModel;
use WooDostavista\DvCmsModuleApiClient\Response\OrderResponseModel;
use WooDostavista\ModuleSettings\ModuleSettings;
use WooDostavista\Warehouses\WarehouseManager;
use WooDostavista\WooOrders\WooOrderLineDto;
use WooDostavista\WooOrders\WooProductDto;

defined('ABSPATH') || exit;

class WC_Dostavista_Shipping_Method extends WC_Shipping_Method
{
    const METHOD_ID = 'dostavista';

    public function __construct($instance_id = 0)
    {
        parent::__construct($instance_id);

        $this->id                 = static::METHOD_ID;
        $this->method_title       = WC_Dostavista_Lang::getHtml('dv_name');
        $this->method_description = WC_Dostavista_Lang::getHtml('dv_description');

        $this->supports = [
            'shipping-zones',
            'instance-settings',
            'instance-settings-modal',
        ];

        $this->init();
    }

    private function init()
    {
        $this->init_form_fields();
        $this->init_settings();

        $this->title = $this->get_option('title');

        add_action('woocommerce_update_options_shipping_' . $this->id, [$this, 'process_admin_options']);
    }

    public function init_form_fields()
    {
        $this->instance_form_fields = [
            'title' => [
                'title'       => __('Title', 'woocommerce'),
                'type'        => 'text',
                'description' => __('This controls the title which the user sees during checkout.', 'woocommerce'),
                'default'     => $this->method_title,
                'desc_tip'    => true,
            ],
        ];
    }

    public function calculate_shipping($package = [])
    {
        global $wpdb;

        $rate = null;

        $settings = new ModuleSettings($wpdb);
        if (!$settings->getAuthToken() || !$settings->getApiUrl() || !$settings->getDefaultPickupWarehouseId()) {
            return;
        }

        $warehouseManager = new WarehouseManager($wpdb);


        $deliveryCity    = $package['destination']['city'] ?? '';
        $deliveryAddress = $package['destination']['address'] ?? '';

        // Найдем склад в городе, который пришел из чекаута. Если такого склада нет, берем по дефолту
        $defaultWarehouse = $warehouseManager->getFirstItemByCityName($deliveryCity) ?? $warehouseManager->getById($settings->getDefaultPickupWarehouseId());
        if (!$defaultWarehouse) {
            return;
        }

        $dvCmsModuleApiClient = new DvCmsModuleApiClient(
            $settings->getApiUrl(), $settings->getAuthToken()
        );

        /** @var WooOrderLineDto[] $orderLines */
        $orderLines = [];
        foreach ($package['contents'] as $contentItem) {
            $orderLines[] = new WooOrderLineDto(
                new WooProductDto(new WC_Product($contentItem['product_id'])),
                $contentItem['quantity']
            );
        }

        $price = (float) ($package['cart_subtotal'] ?? 0);

        $weightKg = 0;
        foreach ($orderLines as $orderLine) {
            $weightKg += $orderLine->getWooProductDto()->getWeightKg() * $orderLine->getQuantity();
        }

        $weightKg = $weightKg ? max((int) $weightKg, 1) : $settings->getDefaultOrderWeightKg();

        $orderRequestModel = new OrderRequestModel();
        $orderRequestModel
            ->setMatter('Matter')
            ->setInsuranceAmount($settings->isInsuranceEnabled() ? $price : 0)
            ->setTotalWeightKg($weightKg)
            ->setVehicleTypeId($settings->getDefaultVehicleTypeId());

        $processingHours   = $settings->getOrderProcessingTimeHours();
        $processingMinutes = $processingHours ? $processingHours * 60 : 30;

        $pickupTime = strtotime("+{$processingMinutes} minutes");
        if ($defaultWarehouse->workFinishTime && $pickupTime > strtotime($defaultWarehouse->workFinishTime)) {
            $pickupTime = strtotime('tomorrow 08:00');
        }

        $pickupPoint = (new PlainPointRequestModel())
            ->setAddress($defaultWarehouse->getFullAddress())
            ->setRequiredTimeInterval(
                date('c', $pickupTime),
                date('c', strtotime('+30 minutes', $pickupTime))
            );

        if ($settings->isBuyoutEnabled()) {
            $pickupPoint->setBuyoutAmount($price);
        }

        $orderRequestModel->addPoint($pickupPoint);

        $plainDeliveryPoint = (new PlainPointRequestModel())
            ->setRequiredTimeInterval(
                date('c', strtotime('+4 hours', $pickupTime)),
                date('c', strtotime('+6 hours', $pickupTime))
            );

        if ($deliveryAddress && $deliveryCity && strpos($deliveryAddress, $deliveryCity) === false) {
            $deliveryAddress = $deliveryCity . ', ' . $deliveryAddress;
        }

        if (!$deliveryAddress && $deliveryCity) {
            $deliveryAddress = $deliveryCity;
        }

        if ($deliveryAddress) {
            $plainDeliveryPoint->setAddress($deliveryAddress);
        }

        if ($settings->isBuyoutEnabled()) {
            $plainDeliveryPoint->setTakingAmount($price);
        }

        $orderRequestModel->addPoint($plainDeliveryPoint);

        try {
            $response = $dvCmsModuleApiClient->calculateOrder($orderRequestModel);
            $orderResponseModel = new OrderResponseModel($response->getData()['order'] ?? []);

            if (
                $price
                && $settings->getFreeDeliveryWooCommerceOrderSum() > 0
                && $price >= $settings->getFreeDeliveryWooCommerceOrderSum()
            ) {
                $paymentAmount = 0;
            } elseif ((int) $settings->getFixOrderPaymentAmount()) {
                $paymentAmount = $settings->getFixOrderPaymentAmount();
            } else {
                $paymentAmount = $orderResponseModel->getPaymentAmount();

                $paymentAmount += $settings->getDostavistaPaymentMarkupAmount();
                $paymentAmount -= $settings->getDostavistaPaymentDiscountAmount();
                $paymentAmount = max(0, $paymentAmount);
            }

            $rate = [
                'id'    => $this->id,
                'label' => $this->title,
                'cost'  => $paymentAmount,
            ];
        } catch (DvCmsModuleApiHttpException $exception) {

        }

        if ($rate !== null) {
            $this->add_rate($rate);
            do_action('woocommerce_' . $this->id . '_shipping_add_rate', $this, $rate);
        }
    }
}
