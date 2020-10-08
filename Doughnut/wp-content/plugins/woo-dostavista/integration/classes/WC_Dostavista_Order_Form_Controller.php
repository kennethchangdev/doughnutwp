<?php

use WooDostavista\AppOrderForm\AppOrderForm;
use WooDostavista\AppOrderForm\AppOrderFormProcessor;
use WooDostavista\BackpaymentDetails\BackpaymentDetail;
use WooDostavista\BackpaymentDetails\BackpaymentDetailManager;
use WooDostavista\BankCard\BankCardsManager;
use WooDostavista\DostavistaAuth\DostavistaClientManager;
use WooDostavista\DostavistaOrders\DostavistaOrder;
use WooDostavista\DostavistaOrders\DostavistaOrderManager;
use WooDostavista\DvCmsModuleApiClient\DvCmsModuleApiClient;
use WooDostavista\DvCmsModuleApiClient\DvCmsModuleApiHttpException;
use WooDostavista\DvCmsModuleApiClient\OrderCalculationResultDto;
use WooDostavista\DvCmsModuleApiClient\Response\OrderResponseModel;
use WooDostavista\DvCmsModuleApiClient\Enums\VehicleTypeEnum;
use WooDostavista\DvCmsModuleApiClient\Enums\PaymentMethodEnum;
use WooDostavista\Enums\ScheduleDateEnum;
use WooDostavista\Enums\ScheduleTimeEnum;
use WooDostavista\ModuleSettings\ModuleSettings;
use WooDostavista\Utils\ObjectsByProperty;
use WooDostavista\Utils\TimeZoneManager;
use WooDostavista\View\JsonResponse;
use WooDostavista\View\View;
use WooDostavista\Warehouses\Warehouse;
use WooDostavista\Warehouses\WarehouseManager;
use WooDostavista\WooOrders\WooOrderDto;

defined('ABSPATH') || exit;

class WC_Dostavista_Order_Form_Controller
{
    public static function index()
    {
        global $wpdb;

        // Пропустим запрос через милвару, проверяющую  законченность визарда
        WC_Dostavista_Middlewares::preventUnfinishedWizard();

        $wooOrderIds = ($_REQUEST['ids'] ?? []);;
        foreach ($wooOrderIds as &$id) {
            $id = (int) $id;
        }
        unset($id);

        $wooOrders = [];

        if ($wooOrderIds) {
            foreach ($wooOrderIds as $wooOrderId) {
                $wooOrder = wc_get_order($wooOrderId);
                if ($wooOrder) {
                    $wooOrders[] = new WooOrderDto($wooOrder);
                }
            }
        }

        $settings = new ModuleSettings($wpdb);

        $dostavistaOrderManager = new DostavistaOrderManager($wpdb);
        $dostavistaOrders = $dostavistaOrderManager->getByWooOrderIds($wooOrderIds);

        $timeEnum        = ScheduleTimeEnum::getEnum();
        $dateEnum        = ScheduleDateEnum::getEnum();
        $vehicleTypeEnum = VehicleTypeEnum::getEnum();

        $warehouseManager = new WarehouseManager($wpdb);
        /** @var Warehouse[] $warehouses */
        $warehouses = ObjectsByProperty::get('id', $warehouseManager->getList());
        $defaultWarehouse = $warehouses[$settings->getDefaultPickupWarehouseId()] ?? new Warehouse();

        $backpaymentDetailManager = new BackpaymentDetailManager($wpdb);
        /** @var BackpaymentDetail $backpaymentDetails */
        $backpaymentDetails = ObjectsByProperty::get('id', $backpaymentDetailManager->getList());
        $defaultBackpaymentDetail = $backpaymentDetails[$settings->getDefaultBackpaymentDetailId()] ?? new BackpaymentDetail();

        $timeZoneManager = new TimeZoneManager(get_option('timezone_string'), get_option('gmt_offset'));

        $appOrderForm = new AppOrderForm(
            $wooOrders, $settings, $defaultWarehouse, $defaultBackpaymentDetail, $timeZoneManager->getDateTimeZone()
        );

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
            __DIR__ . '/../views/order-form.php',
            [
                'settings'                 => $settings,
                'orderForm'                => $appOrderForm,
                'warehouses'               => $warehouses,
                'defaultWarehouse'         => $defaultWarehouse,
                'backpaymentDetails'       => $backpaymentDetails,
                'defaultBackpaymentDetail' => $defaultBackpaymentDetail,
                'dostavistaOrders'         => $dostavistaOrders,
                'timeEnum'                 => $timeEnum,
                'dateEnum'                 => $dateEnum,
                'vehicleTypeEnum'          => $vehicleTypeEnum,
                'paymentTypes'             => $paymentTypes,
                'paymentBankCards'         => $paymentBankCards,
            ]
        );

        echo $view->getRenderedHtml();
    }

    public static function calculate()
    {
        global $wpdb;

        $settings = new ModuleSettings($wpdb);

        $dvCmsModuleApiClient = new DvCmsModuleApiClient(
            $settings->getApiUrl(), $settings->getAuthToken()
        );

        $postBody = file_get_contents('php://input');
        $data     = json_decode($postBody, true) ?? [];

        $timeZoneManager = new TimeZoneManager(get_option('timezone_string'), get_option('gmt_offset'));

        // Получим доступные методы оплаты
        $paymentMethods = DostavistaClientManager::getAllowedPaymentMethods();

        $orderFormProcessor = new AppOrderFormProcessor($dvCmsModuleApiClient, $data, $timeZoneManager->getDateTimeZone(), $paymentMethods);

        try {
            $dvApiResponse = $orderFormProcessor->calculateOrder();
            $orderResponseModel = new OrderResponseModel($dvApiResponse->getData()['order'] ?? []);

            $orderCalculationResultDto = new OrderCalculationResultDto($orderResponseModel);

            (new JsonResponse(
                [
                    'order_calculation_result' => $orderCalculationResultDto->getData(),
                    'form_parameter_errors'    => $orderFormProcessor->getFormParameterErrors($dvApiResponse),
                ]
            ))->render();

        } catch (DvCmsModuleApiHttpException $exception) {
            (new JsonResponse(['error' => 'dostavista_cms_module_api_order_calculation_error'], 500))->render();
        }
    }

    public static function create()
    {
        global $wpdb;

        $settings = new ModuleSettings($wpdb);

        $dvCmsModuleApiClient = new DvCmsModuleApiClient(
            $settings->getApiUrl(), $settings->getAuthToken()
        );

        $postBody = file_get_contents('php://input');
        $data     = json_decode($postBody, true) ?? [];

        $timeZoneManager = new TimeZoneManager(get_option('timezone_string'), get_option('gmt_offset'));

        // Получим доступные методы оплаты
        $paymentMethods = DostavistaClientManager::getAllowedPaymentMethods();

        $orderFormProcessor = new AppOrderFormProcessor($dvCmsModuleApiClient, $data, $timeZoneManager->getDateTimeZone(), $paymentMethods);

        try {
            $dvApiResponse = $orderFormProcessor->createOrder();
            $orderId = !empty($dvApiResponse->getData()['order']['order_id'])
                ? (int) $dvApiResponse->getData()['order']['order_id']
                : null;

            if ($orderId) {
                $dostavistaOrderManager = new DostavistaOrderManager($wpdb);
                $dostavistaOrder = $dostavistaOrderManager->getByDostavistaOrderId($orderId);
                if (!$dostavistaOrder) {
                    $dostavistaOrder = new DostavistaOrder();
                    $dostavistaOrder->dostavistaOrderId = $orderId;
                }

                $dostavistaOrder->wooOrderIds = $orderFormProcessor->getWooOrderIds();
                $dostavistaOrderManager->save($dostavistaOrder);

                (new JsonResponse(['order_id' => $orderId]))->render();
                exit();
            } else {
                (new JsonResponse(['error' => $dvApiResponse->getErrors()[0] ?? 'dostavista_cms_module_api_order_creation_error'], 500))->render();
            }
        } catch (DvCmsModuleApiHttpException $exception) {
            $responseBody = $exception->getResponseBody();
            $responseData = json_decode($responseBody, true);
            if (!is_array($responseData)) {
                $responseData = [];
            }

            (new JsonResponse(['error' => $responseData['errors'][0] ?? 'dostavista_cms_module_api_order_creation_error'], 500))->render();
        }
    }
}
