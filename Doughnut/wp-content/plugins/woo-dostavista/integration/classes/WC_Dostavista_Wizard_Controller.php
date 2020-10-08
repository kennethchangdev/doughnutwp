<?php

use WooDostavista\BackpaymentDetails\BackpaymentDetail;
use WooDostavista\BackpaymentDetails\BackpaymentDetailManager;
use WooDostavista\BankCard\BankCardsManager;
use WooDostavista\DostavistaAuth\DostavistaApiAuthTokenVerifier;
use WooDostavista\DostavistaAuth\DostavistaClientManager;
use WooDostavista\DvCmsModuleApiClient\DvCmsModuleApiClient;
use WooDostavista\DvCmsModuleApiClient\DvCmsModuleApiHttpException;
use WooDostavista\DvCmsModuleApiClient\Enums\VehicleTypeEnum;
use WooDostavista\Enums\ScheduleTimeEnum;
use WooDostavista\ModuleConfig\ModuleConfig;
use WooDostavista\ModuleMetric\ModuleMetricManager;
use WooDostavista\ModuleSettings\ModuleSettings;
use WooDostavista\Utils\ObjectsByProperty;
use WooDostavista\View\JsonResponse;
use WooDostavista\View\View;
use WooDostavista\Warehouses\Warehouse;
use WooDostavista\Warehouses\WarehouseManager;
use WooDostavista\WizardResult\WizardResultManager;
use WooDostavista\DvCmsModuleApiClient\Enums\PaymentMethodEnum;

defined('ABSPATH') || exit;

class WC_Dostavista_Wizard_Controller
{
    public static function index()
    {
        global $wpdb;

        $settings = new ModuleSettings($wpdb);

        $warehouseManager = new WarehouseManager($wpdb);
        /** @var Warehouse[] $warehouses */
        $warehouses = ObjectsByProperty::get('id', $warehouseManager->getList());
        $defaultWarehouse = $warehouses[$settings->getDefaultPickupWarehouseId()] ?? new Warehouse();

        $backpaymentDetailManager = new BackpaymentDetailManager($wpdb);
        /** @var BackpaymentDetail[] $backpaymentDetails */
        $backpaymentDetails = ObjectsByProperty::get('id', $backpaymentDetailManager->getList());
        $defaultBackpaymentDetail = $backpaymentDetails[$settings->getDefaultBackpaymentDetailId()] ?? new BackpaymentDetail();

        $wooOrderStatusEnum   = wc_get_order_statuses();

        /** @var WC_Payment_Gateway[] $wooPaymentGateways */
        $wooPaymentGateways = WC()->payment_gateways->get_available_payment_gateways();
        $wooPaymentMethodEnum = [];
        foreach ($wooPaymentGateways as $code => $wooPaymentGateway) {
            $wooPaymentMethodEnum[$code] = $wooPaymentGateway->get_title();
        }
        $isApiTokenValid = DostavistaApiAuthTokenVerifier::isCmsModuleApiTokenValid();
        $isStep1Success = (bool) $settings->getAuthToken() && $isApiTokenValid;

        $isStep2Success = false;
        $shippingZoneEnum = [];
        $wcShippingZonesData = WC_Shipping_Zones::get_zones();
        foreach ($wcShippingZonesData as $wcShippingZoneData) {
            $shippingZoneEnum[$wcShippingZoneData['zone_id']] = $wcShippingZoneData['zone_name'];
            if (!$isStep2Success) {
                foreach ($wcShippingZoneData['shipping_methods'] as $wcShippingMethod) {
                    if ($wcShippingMethod instanceof WC_Dostavista_Shipping_Method) {
                        $isStep2Success = true;
                        break;
                    }
                }
            }
        }

        $shippingContinents = WC()->countries->get_shipping_continents();
        $allowedCountries   = WC()->countries->get_allowed_countries();

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
            __DIR__ . '/../views/wizard.php',
            [
                'settings'                 => $settings,
                'warehouses'               => $warehouses,
                'defaultWarehouse'         => $defaultWarehouse,
                'backpaymentDetails'       => $backpaymentDetails,
                'defaultBackpaymentDetail' => $defaultBackpaymentDetail,
                'scheduleTimeEnum'         => ScheduleTimeEnum::getEnum(),
                'vehicleTypeEnum'          => VehicleTypeEnum::getEnum(),
                'wooOrderStatusEnum'       => $wooOrderStatusEnum,
                'wooPaymentMethodEnum'     => $wooPaymentMethodEnum,
                'isStep1Success'           => $isStep1Success,
                'isStep2Success'           => $isStep2Success,
                'shippingZoneEnum'         => $shippingZoneEnum,
                'shippingContinents'       => $shippingContinents,
                'allowedCountries'         => $allowedCountries,
                'paymentTypes'             => $paymentTypes,
                'isApiTokenValid'          => $isApiTokenValid,
                'paymentBankCards'          => $paymentBankCards,
            ]
        );

        echo $view->getRenderedHtml();
    }

    public static function create_auth_token()
    {
        $postBody = file_get_contents('php://input');
        $data     = json_decode($postBody, true) ?? [];

        global $wpdb;
        $settings = new ModuleSettings($wpdb);

        $clientLogin    = $data['client_login'] ?? '';
        $clientPassword = $data['client_password'] ?? '';
        $isApiTest      = (bool) ($data['is_apitest'] ?? false);

        $moduleConfig = new ModuleConfig();

        $apiUrl = $isApiTest
            ? $moduleConfig->getDvCmsModuleApiTestUrl()
            : $moduleConfig->getDvCmsModuleApiProdUrl();

        $dvCmsModuleApiClient = new DvCmsModuleApiClient($apiUrl);

        if (filter_var($clientLogin, FILTER_VALIDATE_EMAIL) !== false) {
            $response = $dvCmsModuleApiClient->createOrganizationAuthToken(
                $clientLogin, $clientPassword
            );
        } else {
            $response = $dvCmsModuleApiClient->createPersonAuthToken(
                $clientLogin, $clientPassword
            );
        }

        $authToken = $response->getData()['auth_token'] ?? null;
        if ($authToken) {
            $settings->setData('dostavista_is_api_test_server', (int) $isApiTest);
            if ($isApiTest) {
                $settings->setCmsModuleApiTestAuthToken($authToken);
            } else {
                $settings->setCmsModuleApiProdAuthToken($authToken);
            }

            $moduleMetricManager = new ModuleMetricManager(
                $wpdb,
                $moduleConfig->getDvCmsModuleApiProdUrl(),
                $settings->getApiUrl(),
                $settings->getAuthToken()
            );

            $moduleMetricManager->tokenCreate();
            $moduleMetricManager->tokenInstall();

            try {
                $dvCmsModuleApiClient = new DvCmsModuleApiClient($apiUrl, $authToken);
                $apiEditSettingsResponse = $dvCmsModuleApiClient->editApiSettings(admin_url('admin-post.php?action=woo_dostavista/api_callback_handler'));
                $settings->setData('dostavista_cms_module_api_callback_secret', $apiEditSettingsResponse->getCallbackSecretKey());
                $moduleMetricManager->callbackKeyInstall();
            } catch (DvCmsModuleApiHttpException $cmsModuleApiHttpException) {

            } catch (Throwable $exception) {
            }

            // Установим 1й шаг визарда пройденным
            WizardResultManager::setLastFinishedStep(1);

            (new JsonResponse(
                [
                    'api_url'                 => $apiUrl,
                    'api_auth_token'          => $authToken,
                    'api_callback_secret_key' => $settings->getApiCallbackSecretKey(),
                ]
            ))->render();
        } else {
            (new JsonResponse(['error' => 'invalid_client'], 400))->render();
        }
    }

    public static function install_shipping_method()
    {
        $postBody = file_get_contents('php://input');
        $data     = json_decode($postBody, true) ?? [];

        if (empty($data['zone_ids'])) {
            (new JsonResponse(['error' => 'required_zone_ids'], 400))->render();
            exit();
        }

        require_once __DIR__ . '/WC_Dostavista_Shipping_Method.php';
        require_once __DIR__ . '/WC_Dostavista_Shipping_Method_Widget.php';

        $zoneIds = $data['zone_ids'];
        foreach ($zoneIds as $zoneId) {
            $zone = new WC_Shipping_Zone($zoneId);
            if (!$zone->add_shipping_method(WC_Dostavista_Shipping_Method::METHOD_ID)) {
                (new JsonResponse(['error' => 'shipping_method_install_fail'], 400))->render();
                exit();
            }
        }

        (new JsonResponse([]))->render();
    }

    public static function add_shipping_zone()
    {
        $postBody = file_get_contents('php://input');
        $data     = json_decode($postBody, true) ?? [];

        $name      = $data['name'] ?? null;
        $locations = $data['locations'] ?? null;

        if (!$name || !$locations || !is_array($locations)) {
            (new JsonResponse(['error' => 'invalid_parameters'], 400))->render();
            exit();
        }

        $zone = new WC_Shipping_Zone();
        $zone->set_zone_name(wc_clean($name));

        $locations = array_filter(array_map('wc_clean', (array) $locations));
        foreach ( $locations as $location ) {
            $locationParts = explode(':', $location);
            switch ($locationParts[0]) {
                case 'state':
                    $zone->add_location($locationParts[1] . ':' . $locationParts[2], 'state');
                    break;
                case 'country':
                    $zone->add_location($locationParts[1], 'country');
                    break;
                case 'continent':
                    $zone->add_location($locationParts[1], 'continent');
                    break;
            }
        }

        $zone->save();

        $responseData = [
            'zone_id'   => $zone->get_id(),
            'zone_name' => (string) $name,
        ];

        (new JsonResponse($responseData))->render();
    }

    public static function get_payment_types()
    {
        global $wpdb;
        $settings = new ModuleSettings($wpdb);
        $dvCmsModuleApiClient = new DvCmsModuleApiClient(
            $settings->getApiUrl(), $settings->getAuthToken()
        );

        $bankCardsData = [];
        try {
            // Получим список доступных карт пользователя, и сформируем список типов оплаты
            $bankCards = BankCardsManager::updateShopBankCardsCache($dvCmsModuleApiClient);
            $bankCardsData = [];
            foreach ($bankCards as $bankCard) {
                $bankCardsData[] = [
                    'id'   => $bankCard->getBankCardId(),
                    'mask' => $bankCard->getBankCardNumberMask(),
                    'type' => $bankCard->getCardType(),
                ];
            }

            // Получим доступные методы оплаты
            $paymentMethods = DostavistaClientManager::getAllowedPaymentMethods();
            $paymentMethodsData = [];

            foreach ($paymentMethods as $paymentMethod) {
                $paymentMethodsData[] = [
                    'code'    => $paymentMethod,
                    'name'    => WC_Dostavista_Lang::get('payment_type_' . $paymentMethod),
                    'is_card' => in_array($paymentMethod, [PaymentMethodEnum::PAYMENT_METHOD_QIWI, PaymentMethodEnum::PAYMENT_METHOD_BANK])
                ];
            }
        } catch (DvCmsModuleApiHttpException $e) {

        }

        (new JsonResponse([
            'success'         => 'true',
            'cards'           => $bankCardsData,
            'payment_methods' => $paymentMethodsData,
        ]))->render();
    }

    public static function set_last_finished_step()
    {
        $postBody = file_get_contents('php://input');
        $data     = json_decode($postBody, true) ?? [];
        $stepNumber = ((int) $data['step_number'])?? null;

        if (!$stepNumber) {
            (new JsonResponse([
                'success' => 'false',
                'message' => 'Step number is required'
            ]))->render();
            exit();
        }

        WizardResultManager::setLastFinishedStep($stepNumber);

        (new JsonResponse([
            'success' => 'true',
        ]))->render();
    }
}
