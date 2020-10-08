<?php

namespace WooDostavista\ModuleSettings;

use wpdb;
use WooDostavista\ModuleConfig\ModuleConfig;

class ModuleSettings
{
    const DEFAULT_VEHICLE_TYPE_ID = 6;

    /** @var wpdb */
    private $wpdb;

    /** @var array */
    private $data;

    public function __construct(wpdb $wpdb)
    {
        $this->wpdb = $wpdb;
        $this->createTablesIfNotExists();
        $this->loadData();
    }

    private function createTablesIfNotExists()
    {
        $this->wpdb->query(
            "
                CREATE TABLE IF NOT EXISTS `woo_dostavista_settings` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `key` VARCHAR(255) DEFAULT '',
                    `value` VARCHAR(8192) DEFAULT '',
                    PRIMARY KEY (`id`),
                    UNIQUE KEY (`key`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            "
        );
    }

    private function loadData()
    {
        $rows = $this->wpdb->get_results("SELECT * FROM `woo_dostavista_settings`");
        foreach ($rows as $row) {
            $this->data[$row->key] = $row->value;
        }
    }

    public function setData(string $key, string $value)
    {
        if ($this->issetKey($key)) {
            $this->wpdb->query(
                $this->wpdb->prepare(
                    "UPDATE `woo_dostavista_settings` SET `value` = %s WHERE `key` = %s",
                    trim($value), $key
                )
            );
        } else {
            $this->wpdb->query(
                $this->wpdb->prepare(
                    "INSERT INTO `woo_dostavista_settings` (`key`, `value`) VALUES (%s, %s)",
                    $key, trim($value)
                )
            );
        }

        $this->data[$key] = $value;
    }

    public function getData(): array
    {
        return $this->data ?? [];
    }

    public function issetKey(string $key): bool
    {
        $queryResult = $this->wpdb->query(
            $this->wpdb->prepare("SELECT `value` FROM `woo_dostavista_settings` WHERE `key` = %s", $key)
        );
        return (bool) $queryResult;
    }

    public function getIsApiTest(): bool
    {
        return (bool) $this->data['dostavista_is_api_test_server'] ?? false;
    }

    public function getAuthToken(): string
    {
        return $this->getIsApiTest() ? $this->getTestAuthToken() : $this->getProdAuthToken();
    }

    public function getTestAuthToken(): string
    {
        return $this->data['dostavista_cms_module_api_test_auth_token'] ?? '';
    }

    public function getProdAuthToken(): string
    {
        return $this->data['dostavista_cms_module_api_prod_auth_token'] ?? '';
    }

    public function getApiUrl(): string
    {
        $moduleConfig = new ModuleConfig();
        return $this->getIsApiTest() ? $moduleConfig->getDvCmsModuleApiTestUrl() : $moduleConfig->getDvCmsModuleApiProdUrl();
    }

    public function setCmsModuleApiProdAuthToken(string $token)
    {
        $this->setData('dostavista_cms_module_api_prod_auth_token', $token);
    }

    public function setCmsModuleApiTestAuthToken(string $token)
    {
        $this->setData('dostavista_cms_module_api_test_auth_token', $token);
    }

    public function getDefaultPickupWarehouseId(): int
    {
        return (int) ($this->data['default_pickup_warehouse_id'] ?? 0);
    }

    public function getDefaultVehicleTypeId(): int
    {
        return (int) ($this->data['default_vehicle_type_id'] ?? static::DEFAULT_VEHICLE_TYPE_ID);
    }

    public function getDefaultMatter(): string
    {
        return (string) ($this->data['default_matter'] ?? '');
    }

    public function getDefaultOrderWeightKg(): int
    {
        return max(0, (int) ($this->data['default_order_weight_kg'] ?? 1));
    }

    public function getDostavistaPaymentMarkupAmount(): float
    {
        return round(max(0, (float) ($this->data['dostavista_payment_markup_amount'] ?? 0)), 2);
    }

    public function getDostavistaPaymentDiscountAmount(): float
    {
        return round(max(0, (float) ($this->data['dostavista_payment_discount_amount'] ?? 0)), 2);
    }

    public function getFixOrderPaymentAmount(): float
    {
        return round(max(0, (float) ($this->data['fix_order_payment_amount'] ?? 0)), 2);
    }

    public function getFreeDeliveryWooCommerceOrderSum(): int
    {
        return max(0, (int) ($this->data['free_delivery_woocommerce_order_sum'] ?? 0));
    }

    public function getOrderProcessingTimeHours(): int
    {
        return max(0, (int) ($this->data['order_processing_time_hours'] ?? 0));
    }

    public function getDefaultBackpaymentDetailId(): int
    {
        return (int) ($this->data['default_backpayment_detail_id'] ?? 0);
    }

    public function isInsuranceEnabled(): bool
    {
        return (bool) ($this->data['is_insurance_enabled'] ?? true);
    }

    public function isBuyoutEnabled(): bool
    {
        return (bool) ($this->data['is_buyout_enabled'] ?? false);
    }

    public function isMatterWeightPrefixEnabled(): bool
    {
        return (bool) ($this->data['is_matter_weight_prefix_enabled'] ?? false);
    }

    public function isContactPersonNotificationEnabled(): bool
    {
        return (bool) ($this->data['is_contact_person_notification_enabled'] ?? false);
    }

    public function getDeliveryPointNotePrefix(): string
    {
        return (string) ($this->data['delivery_point_note_prefix'] ?? '');
    }

    public function getCashPaymentMethodCode(): string
    {
        return (string) ($this->data['cash_payment_method_code'] ?? '');
    }

    public function getApiCallbackSecretKey(): string
    {
        return (string) ($this->data['dostavista_cms_module_api_callback_secret'] ?? '');
    }

    public function getWooCommerceOrderStatusDraft(): string
    {
        return (string) ($this->data['woocommerce_order_status_draft'] ?? '');
    }

    public function getWooCommerceOrderStatusAvailable(): string
    {
        return (string) ($this->data['woocommerce_order_status_available'] ?? '');
    }

    public function getWooCommerceOrderStatusCourierAssigned(): string
    {
        return (string) ($this->data['woocommerce_order_status_courier_assigned'] ?? '');
    }

    public function getWooCommerceOrderStatusActive(): string
    {
        return (string) ($this->data['woocommerce_order_status_active'] ?? '');
    }

    public function getWooCommerceOrderStatusParcelPickedUp(): string
    {
        return (string) ($this->data['woocommerce_order_status_parcel_picked_up'] ?? '');
    }

    public function getWooCommerceOrderStatusCourierDeparted(): string
    {
        return (string) ($this->data['woocommerce_order_status_courier_departed'] ?? '');
    }

    public function getWooCommerceOrderStatusCourierArrived(): string
    {
        return (string) ($this->data['woocommerce_order_status_courier_arrived'] ?? '');
    }

    public function getWooCommerceOrderStatusCompleted(): string
    {
        return (string) ($this->data['woocommerce_order_status_completed'] ?? '');
    }

    public function getWooCommerceOrderStatusFailed(): string
    {
        return (string) ($this->data['woocommerce_order_status_failed'] ?? '');
    }

    public function getWooCommerceOrderStatusCanceled(): string
    {
        return (string) ($this->data['woocommerce_order_status_canceled'] ?? '');
    }

    public function getWooCommerceOrderStatusDelayed(): string
    {
        return (string) ($this->data['woocommerce_order_status_delayed'] ?? '');
    }

    public function getDefaultPaymentCardId(): int
    {
        return (int) ($this->data['default_payment_card_id'] ?? 0);
    }

    public function getDefaultPaymentType(): string
    {
        return (string) ($this->data['default_payment_type'] ?? '');
    }

    public function getWizardLastFinishedStep(): int
    {
        return (int) ($this->data['wizard_last_finished_step'] ?? 0);
    }

    public function setWizardLastFinishedStep(int $stepNumber)
    {
        $this->setData('wizard_last_finished_step', $stepNumber);
    }
}
