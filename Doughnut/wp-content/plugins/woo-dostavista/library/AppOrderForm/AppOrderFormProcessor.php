<?php

namespace WooDostavista\AppOrderForm;

use DateTime;
use DateTimeZone;
use WooDostavista\DvCmsModuleApiClient\DvCmsModuleApiClient;
use WooDostavista\DvCmsModuleApiClient\DvCmsModuleApiHttpException;
use WooDostavista\DvCmsModuleApiClient\DvCmsModuleApiResponse;
use WooDostavista\DvCmsModuleApiClient\Enums\PaymentMethodEnum;
use WooDostavista\DvCmsModuleApiClient\Request\OrderRequestModel;
use WooDostavista\DvCmsModuleApiClient\Request\PlainPointRequestModel;
use WooDostavista\DvCmsModuleApiClient\Request\SdekPointRequestModel;

class AppOrderFormProcessor
{
    /** @var DvCmsModuleApiClient */
    private $dvCmsModuleApiClient;

    /** @var array */
    private $requestData;

    /** @var DateTimeZone */
    private $dateTimeZone;

    /** @var array */
    private $allowedPaymentMethods;

    public function  __construct(DvCmsModuleApiClient $dvCmsModuleApiClient, array $requestData, DateTimeZone $dateTimeZone, array $allowedPaymentMethods)
    {
        $this->dvCmsModuleApiClient  = $dvCmsModuleApiClient;
        $this->requestData           = $requestData;
        $this->dateTimeZone          = $dateTimeZone;
        $this->allowedPaymentMethods = $allowedPaymentMethods;
    }

    private function getDeliveryPointIndexes(): array
    {
        $indexes = [];
        foreach ($this->requestData as $key => $value) {
            if (strpos($key, 'point_type_') !== false) {
                $indexes[] = (int) str_replace('point_type_', '', $key);
            }
        }

        return $indexes;
    }

    private function getDeliverySdekPointPackageIndexes(int $deliveryPointIndexes): array
    {
        $indexes = [];
        foreach ($this->requestData as $key => $value) {
            if (strpos($key, "sdek_point_{$deliveryPointIndexes}_package_") !== false) {
                $indexes[] = (int) str_replace("sdek_point_{$deliveryPointIndexes}_package_", '', $key);
            }
        }

        return array_unique($indexes);
    }

    public function getWooOrderIds(): array
    {
        $ids = [];
        foreach ($this->getDeliveryPointIndexes() as $index) {
            $id = (int) $this->requestData['woo_order_id_' . $index];
            if ($id) {
                $ids[] = $id;
            }
        }

        return $ids;
    }

    private function getOrderRequestModelFromRequestData()
    {
        $data = $this->requestData;

        $orderRequestModel = (new OrderRequestModel())
            ->setMatter($data['matter'])
            ->setTotalWeightKg((int) $data['total_weight_kg'])
            ->setInsuranceAmount((float) $data['insurance_amount'])
            ->setVehicleTypeId((int) $data['vehicle_type_id'])
            ->setLoadersCount((int) $data['loaders_count'])
            ->setBackpaymentDetails($data['backpayment_details'])
            ->setContactPersonNotification((bool) $data['is_contact_person_notification_enabled'])
        ;

        $pickupDate       = $data['pickup_required_date'];
        $pickupStartTime  = $data['pickup_required_start_time'];
        $pickupFinishTime = $data['pickup_required_finish_time'];

        $pickupPlainPoint = new PlainPointRequestModel();
        $pickupPlainPoint
            ->setAddress($data['pickup_address'])
            ->setRequiredTimeInterval(
                (new DateTime("{$pickupDate} {$pickupStartTime}", $this->dateTimeZone))->format('c'),
                (new DateTime("{$pickupDate} {$pickupFinishTime}", $this->dateTimeZone))->format('c')
            )
            ->setContactPerson($data['pickup_contact_name'], $data['pickup_contact_phone'])
            ->setNote($data['pickup_note'])
            ->setBuyoutAmount((float) $data['pickup_buyout_amount'])
        ;

        $orderRequestModel->addPoint($pickupPlainPoint);

        foreach ($this->getDeliveryPointIndexes() as $index) {
            $deliveryPointType = $data['point_type_' . $index];
            if ($deliveryPointType == 'plain') {
                $deliveryDate       = $data['plain_point_required_date_' . $index];
                $deliveryStartTime  = $data['plain_point_required_start_time_' . $index];
                $deliveryFinishTime = $data['plain_point_required_finish_time_' . $index];
                $deliveryPlainPoint = (new PlainPointRequestModel())
                    ->setAddress($data['plain_point_address_' . $index])
                    ->setRequiredTimeInterval(
                        (new DateTime("{$deliveryDate} {$deliveryStartTime}", $this->dateTimeZone))->format('c'),
                        (new DateTime("{$deliveryDate} {$deliveryFinishTime}", $this->dateTimeZone))->format('c')
                    )
                    ->setContactPerson($data['plain_point_recipient_name_' . $index], $data['plain_point_recipient_phone_' . $index])
                    ->setNote($data['plain_point_note_' . $index])
                    ->setClientOrderId($data['plain_point_client_order_id_' . $index])
                    ->setTakingAmount((float) $data['plain_point_taking_amount_' . $index])
                ;

                $orderRequestModel->addPoint($deliveryPlainPoint);
            } elseif ($deliveryPointType == 'sdek') {
                $deliverySdekPoint = (new SdekPointRequestModel())
                    ->setAddressCityId((int) $data['sdek_point_address_city_id_' . $index])
                    ->setWeightKg((int) $data['sdek_point_weight_kg_' . $index])
                    ->setDimensions(
                        (int) $data['sdek_point_length_cm_' . $index],
                        (int) $data['sdek_point_width_cm_' . $index],
                        (int) $data['sdek_point_height_cm_' . $index]
                    )
                    ->setDeliveryMethod($data['sdek_point_delivery_method_' . $index])
                    ->setContactPerson(
                        $data['sdek_point_recipient_name_' . $index],
                        $data['sdek_point_recipient_phone_' . $index]
                    )
                    ->setAddress(
                        $data['sdek_point_address_street_' . $index],
                        $data['sdek_point_address_building_number_' . $index],
                        $data['sdek_point_address_apartment_number_' . $index]
                    )
                    ->setWarehouseId((int) $data['sdek_point_warehouse_id_' . $index])
                    ->setClientOrderId($data['sdek_point_client_order_id_' . $index])
                    ->setNote($data['sdek_point_note_' . $index])
                ;

                foreach ($this->getDeliverySdekPointPackageIndexes($index) as $packageIndex) {
                    $deliverySdekPoint->addPackage(
                        $data['sdek_point_' . $index . '_package_' . $packageIndex . '_ware_code'],
                        (float) $data['sdek_point_' . $index . '_package_' . $packageIndex . '_item_payment_amount'],
                        (int) $data['sdek_point_' . $index . '_package_' . $packageIndex . '_items_count'],
                        $data['sdek_point_' . $index . '_package_' . $packageIndex . '_description']
                    );
                }

                $orderRequestModel->addPoint($deliverySdekPoint);
            }
        }

        // Установим метод оплаты и данные по карте. Если указана id карты, то выбираем тип оплаты картой (если такой метод доступен пользователю)
        if (in_array($data['payment_type'], $this->allowedPaymentMethods)) {
            $orderRequestModel->setPaymentMethod($data['payment_type']);
        }

        if (!empty($data['bank_card_id']) && in_array($data['payment_type'], [PaymentMethodEnum::PAYMENT_METHOD_BANK, PaymentMethodEnum::PAYMENT_METHOD_QIWI])) {
            $orderRequestModel->setBankCardId($data['bank_card_id']);
        }

        return $orderRequestModel;
    }

    /**
     * @return DvCmsModuleApiResponse
     * @throws DvCmsModuleApiHttpException
     */
    public function calculateOrder(): DvCmsModuleApiResponse
    {
        $orderRequestModel = $this->getOrderRequestModelFromRequestData();
        return $this->dvCmsModuleApiClient->calculateOrder($orderRequestModel);
    }

    /**
     * @return DvCmsModuleApiResponse
     * @throws DvCmsModuleApiHttpException
     */
    public function createOrder(): DvCmsModuleApiResponse
    {
        $orderRequestModel = $this->getOrderRequestModelFromRequestData();
        return $this->dvCmsModuleApiClient->createOrder($orderRequestModel);
    }

    public function getFormParameterErrors(DvCmsModuleApiResponse $apiResponse): array
    {
        $errors = [];

        $responseParameterErrors = $apiResponse->getParameterErrors();
        if (!count($responseParameterErrors)) {
            $responseParameterErrors = $apiResponse->getParameterWarnings();
        }

        foreach ($responseParameterErrors as $parameterName => $data) {
            if ($parameterName == 'points') {
                continue;
            }

            switch ($parameterName) {
                case 'matter':
                case 'total_weight_kg':
                case 'insurance_amount':
                    $errors[$parameterName] = static::getMappedParameterError($data);
                    break;
            }
        }

        if (!empty($responseParameterErrors['points'])) {
            if (!empty($responseParameterErrors['points'][0])) {
                foreach ($responseParameterErrors['points'][0] as $parameterName => $data) {
                    if (empty($data)) {
                        continue;
                    }

                    switch ($parameterName) {
                        case 'address':
                            $errors['pickup_address'] = static::getMappedParameterError($data);
                            break;
                        case 'required_start_datetime':
                            $errors['pickup_required_start_time'] = static::getMappedParameterError($data);
                            break;
                        case 'required_finish_datetime':
                            $errors['pickup_required_finish_time'] = static::getMappedParameterError($data);
                            break;
                        case 'contact_person':
                            if (!empty($data['name'])) {
                                $errors['pickup_contact_name'] = static::getMappedParameterError($data['name']);
                            }
                            if (!empty($data['phone'])) {
                                $errors['pickup_contact_phone'] = static::getMappedParameterError($data['phone']);
                            }
                            break;
                        case 'note':
                            $errors['pickup_note'] = static::getMappedParameterError($data);
                            break;
                    }
                }
            }

            foreach ($responseParameterErrors['points'] as $index => $pointData) {
                if ($index == 0) {
                    continue;
                }

                $deliveryPointIndex = $index - 1;

                if (!$pointData) {
                    continue;
                }

                foreach ($pointData as $parameterName => $data) {
                    if (empty($data)) {
                        continue;
                    }

                    if (
                        isset($this->requestData['point_type_' . $deliveryPointIndex])
                        && $this->requestData['point_type_' . $deliveryPointIndex] == 'plain'
                    ) {
                        switch ($parameterName) {
                            case 'address':
                                $errors['plain_point_address_' . $deliveryPointIndex] = static::getMappedParameterError($data);
                                break;
                            case 'required_start_datetime':
                                $errors['plain_point_required_start_time_' . $deliveryPointIndex] = static::getMappedParameterError($data);
                                break;
                            case 'required_finish_datetime':
                                $errors['plain_point_required_finish_time_' . $deliveryPointIndex] = static::getMappedParameterError($data);
                                break;
                            case 'contact_person':
                                if (!empty($data['name'])) {
                                    $errors['plain_point_recipient_name_' . $deliveryPointIndex] = static::getMappedParameterError($data);
                                }
                                if (!empty($data['phone'])) {
                                    $errors['plain_point_recipient_phone_' . $deliveryPointIndex] = static::getMappedParameterError($data);
                                }
                                break;
                            case 'note':
                                $errors['plain_point_note_' . $deliveryPointIndex] = static::getMappedParameterError($data);
                                break;
                            case 'client_order_id':
                                $errors['plain_point_client_order_id_' . $deliveryPointIndex] = static::getMappedParameterError($data);
                                break;
                            case 'taking_amount':
                                $errors['plain_point_taking_amount_' . $deliveryPointIndex] = static::getMappedParameterError($data);
                                break;
                        }
                    }

                    if (
                        isset($this->requestData['point_type_' . $deliveryPointIndex])
                        && $this->requestData['point_type_' . $deliveryPointIndex] == 'sdek'
                    ) {
                        switch ($parameterName) {
                            case 'address_city_id':
                                $errors['sdek_point_address_city_id_' . $deliveryPointIndex] = static::getMappedParameterError($data);
                                break;
                            case 'weight_kg':
                                $errors['sdek_point_weight_kg_' . $deliveryPointIndex] = static::getMappedParameterError($data);
                                break;
                            case 'length_cm':
                                $errors['sdek_point_length_cm_' . $deliveryPointIndex] = static::getMappedParameterError($data);
                                break;
                            case 'width_cm':
                                $errors['sdek_point_width_cm_' . $deliveryPointIndex] = static::getMappedParameterError($data);
                                break;
                            case 'height_cm':
                                $errors['sdek_point_height_cm_' . $deliveryPointIndex] = static::getMappedParameterError($data);
                                break;
                            case 'sdek_delivery_method':
                                $errors['sdek_point_delivery_method_' . $deliveryPointIndex] = static::getMappedParameterError($data);
                                break;
                            case 'contact_person':
                                if (!empty($data['name'])) {
                                    $errors['sdek_point_recipient_name_' . $deliveryPointIndex] = static::getMappedParameterError($data);
                                }
                                if (!empty($data['phone'])) {
                                    $errors['sdek_point_recipient_phone_' . $deliveryPointIndex] = static::getMappedParameterError($data);
                                }
                                break;
                            case 'address_street':
                                $errors['sdek_point_address_street_' . $deliveryPointIndex] = static::getMappedParameterError($data);
                                break;
                            case 'address_building_number':
                                $errors['sdek_point_address_building_number_' . $deliveryPointIndex] = static::getMappedParameterError($data);
                                break;
                            case 'address_apartment_number':
                                $errors['sdek_point_address_apartment_number_' . $deliveryPointIndex] = static::getMappedParameterError($data);
                                break;
                            case 'sdek_warehouse_id':
                                $errors['sdek_point_warehouse_id_' . $deliveryPointIndex] = static::getMappedParameterError($data);
                                break;
                            case 'note':
                                $errors['sdek_point_note_' . $deliveryPointIndex] = static::getMappedParameterError($data);
                                break;
                            case 'client_order_id':
                                $errors['sdek_point_client_order_id_' . $deliveryPointIndex] = static::getMappedParameterError($data);
                                break;
                        }
                    }
                }
            }
        }

        return $errors;
    }

    private static function getMappedParameterError(array $apiParameterErrors): string
    {
        return $apiParameterErrors[0] ?? 'invalid_value';
    }
}
