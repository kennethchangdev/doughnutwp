<?php

use WooDostavista\BackpaymentDetails\BackpaymentDetail;
use WooDostavista\BackpaymentDetails\BackpaymentDetailManager;
use WooDostavista\BankCard\BankCardsManager;
use WooDostavista\DvCmsModuleApiClient\Enums\VehicleTypeEnum;
use WooDostavista\DostavistaAuth\DostavistaClientManager;
use WooDostavista\DvCmsModuleApiClient\Enums\PaymentMethodEnum;
use WooDostavista\ModuleConfig\ModuleConfig;
use WooDostavista\ModuleMetric\ModuleMetricManager;
use WooDostavista\ModuleSettings\ModuleSettings;
use WooDostavista\Utils\ObjectsByProperty;
use WooDostavista\View\JsonResponse;
use WooDostavista\View\View;
use WooDostavista\Warehouses\Warehouse;
use WooDostavista\Warehouses\WarehouseManager;
use WooDostavista\DvCmsModuleApiClient\DvCmsModuleApiClient;

defined('ABSPATH') || exit;

class WC_Dostavista_Settings_Controller
{
    public static function index()
    {
        global $wpdb;

        // Пропустим запрос через милвару, проверяющую  законченность визарда
        WC_Dostavista_Middlewares::preventUnfinishedWizard();

        $settings = new ModuleSettings($wpdb);
        $moduleConfig = new ModuleConfig();
        $warehouseManager = new WarehouseManager($wpdb);
        /** @var Warehouse[] $warehouses */
        $warehouses = ObjectsByProperty::get('id', $warehouseManager->getList());

        $backpaymentDetailManager = new BackpaymentDetailManager($wpdb);
        /** @var BackpaymentDetail[] $backpaymentDetails */
        $backpaymentDetails = ObjectsByProperty::get('id', $backpaymentDetailManager->getList());

        $wooOrderStatusEnum = wc_get_order_statuses();

        /** @var WC_Payment_Gateway[] $wooPaymentGateways */
        $wooPaymentGateways = WC()->payment_gateways->get_available_payment_gateways();
        $wooPaymentMethodEnum = [];
        foreach ($wooPaymentGateways as $code => $wooPaymentGateway) {
            $wooPaymentMethodEnum[$code] = $wooPaymentGateway->get_title();
        }

        $dvCmsModuleApiClient = new DvCmsModuleApiClient(
            $settings->getApiUrl(), $settings->getAuthToken()
        );

        // Получим доступные методы оплаты
        $allowedPaymentsMethod = DostavistaClientManager::getAllowedPaymentMethods();
        $paymentTypes = [];
        foreach ($allowedPaymentsMethod as $allowedPaymentType) {
            $paymentTypes[$allowedPaymentType] = WC_Dostavista_Lang::get('payment_type_' . $allowedPaymentType);
        }

        // Получим список доступных карт пользователя, и сформируем список типов оплаты
        $bankCards = BankCardsManager::getShopBankCards($dvCmsModuleApiClient);
        $paymentBankCards = [];
        foreach ($bankCards as $bankCard) {
            $paymentBankCards[$bankCard->getBankCardId()] =  WC_Dostavista_Lang::getHtml('payment_type_card') . ' ' . $bankCard->getBankCardNumberMask();
        }

        $view = new View(
            __DIR__ . '/../views/settings.php',
            [
                'settings'             => $settings,
                'warehouses'           => $warehouses,
                'backpaymentDetails'   => $backpaymentDetails,
                'vehicleTypeEnum'      => VehicleTypeEnum::getEnum(),
                'wooOrderStatusEnum'   => $wooOrderStatusEnum,
                'wooPaymentMethodEnum' => $wooPaymentMethodEnum,
                'moduleConfig'         => $moduleConfig,
                'paymentTypes'         => $paymentTypes,
                'isNewCardLinkEnabled' => !in_array(PaymentMethodEnum::PAYMENT_METHOD_NON_CASH, $allowedPaymentsMethod) && WC_Dostavista_Lang::getCountry() === WC_Dostavista_Lang::COUNTRY_RU,
                'paymentBankCards'     => $paymentBankCards,
            ]
        );

        echo $view->getRenderedHtml();
    }

    public static function store()
    {
        global $wpdb;

        $postBody = file_get_contents('php://input');
        $data     = json_decode($postBody, true) ?? [];

        $settings = new ModuleSettings($wpdb);

        $authToken      = $settings->getAuthToken();
        $apiCallbackKey = $settings->getApiCallbackSecretKey();

        $options = [
            'cms_module_api_auth_token',
            'dostavista_is_api_test_server',
            'default_pickup_warehouse_id',
            'default_vehicle_type_id',
            'default_matter',
            'default_order_weight_kg',
            'dostavista_payment_markup_amount',
            'dostavista_payment_discount_amount',
            'fix_order_payment_amount',
            'free_delivery_woocommerce_order_sum',
            'order_processing_time_hours',
            'default_backpayment_detail_id',
            'delivery_point_note_prefix',
            'is_insurance_enabled',
            'is_buyout_enabled',
            'is_matter_weight_prefix_enabled',
            'is_contact_person_notification_enabled',
            'delivery_point_note_prefix',
            'cash_payment_method_code',
            'dostavista_cms_module_api_callback_secret',
            'woocommerce_order_status_draft',
            'woocommerce_order_status_available',
            'woocommerce_order_status_courier_assigned',
            'woocommerce_order_status_active',
            'woocommerce_order_status_parcel_picked_up',
            'woocommerce_order_status_courier_departed',
            'woocommerce_order_status_courier_arrived',
            'woocommerce_order_status_completed',
            'woocommerce_order_status_failed',
            'woocommerce_order_status_canceled',
            'woocommerce_order_status_delayed',
            'default_payment_card_id',
            'default_payment_type',
        ];

        foreach ($options as $option) {
            if (isset($data[$option])) {
                switch ($option) {
                    case "cms_module_api_auth_token":
                        $settings->setData(
                            $data['dostavista_is_api_test_server'] ? 'dostavista_cms_module_api_test_auth_token' : 'dostavista_cms_module_api_prod_auth_token',
                            $data[$option]
                        );
                        break;
                    default:
                        $settings->setData($option, $data[$option]);
                        break;
                }
            }
        }

        $moduleConfig = new ModuleConfig();
        $moduleMetricManager = new ModuleMetricManager(
            $wpdb,
            $moduleConfig->getDvCmsModuleApiProdUrl(),
            $settings->getApiUrl(),
            $settings->getAuthToken()
        );

        if ($settings->getAuthToken() && $authToken !== $settings->getAuthToken()) {
            $moduleMetricManager->tokenInstall();
        }

        if ($settings->getApiCallbackSecretKey() && $apiCallbackKey !== $settings->getApiCallbackSecretKey()) {
            $moduleMetricManager->callbackKeyInstall();
        }

        (new JsonResponse([]))->render();
    }
}
