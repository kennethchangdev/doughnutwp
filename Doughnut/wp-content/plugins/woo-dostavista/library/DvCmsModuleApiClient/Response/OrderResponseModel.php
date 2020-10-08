<?php

namespace WooDostavista\DvCmsModuleApiClient\Response;

class OrderResponseModel
{
    const STATUS_NEW       = 'new';
    const STATUS_AVAILABLE = 'available';
    const STATUS_ACTIVE    = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELED  = 'canceled';

    /** @var array */
    private $responseOrderData;

    /** @var string */
    private $orderId;

    /** @var string */
    private $matter;

    /** @var string */
    private $status;

    /** @var CourierResponseModel|null */
    private $courier;

    /** @var float */
    private $deliveryFeeAmount;

    /** @var float */
    private $intercityDeliveryFeeAmount;

    /** @var float */
    private $insuranceFeeAmount;

    /** @var float */
    private $weightFeeAmount;

    /** @var float */
    private $moneyTransferFeeAmount;

    /** @var float */
    private $loadingFeeAmount;

    /** @var float */
    private $paymentAmount;

    /** @var string */
    private $itineraryDocumentUrl = '';

    /** @var string */
    private $receiptDocumentUrl = '';

    /** @var string */
    private $waybillDocumentUrl = '';

    /** @var PlainPointResponseModel[] */
    private $plainPoints = [];

    /** @var SdekPointResponseModel[] */
    private $sdekPoints = [];

    /** @var int */
    private $vehicleTypeId;

    public function __construct(array $responseOrderData)
    {
        $this->responseOrderData = $responseOrderData;

        $this->orderId                    = $responseOrderData['order_id'] ?? '';
        $this->matter                     = $responseOrderData['matter'] ?? '';
        $this->status                     = $responseOrderData['status'] ?? '';
        $this->courier                    = !empty($responseOrderData['courier']) ? new CourierResponseModel($responseOrderData['courier']) : null;
        $this->deliveryFeeAmount          = (float) ($responseOrderData['delivery_fee_amount'] ?? 0);
        $this->intercityDeliveryFeeAmount = (float) ($responseOrderData['intercity_delivery_fee_amount'] ?? 0);
        $this->insuranceFeeAmount         = (float) ($responseOrderData['insurance_fee_amount'] ?? 0);
        $this->weightFeeAmount            = (float) ($responseOrderData['weight_fee_amount'] ?? 0);
        $this->moneyTransferFeeAmount     = (float) ($responseOrderData['money_transfer_fee_amount'] ?? 0);
        $this->loadingFeeAmount           = (float) ($responseOrderData['loading_fee_amount'] ?? 0);
        $this->paymentAmount              = (float) ($responseOrderData['payment_amount'] ?? 0);

        $this->itineraryDocumentUrl = (string) ($responseOrderData['itinerary_document_url'] ?? '');
        $this->receiptDocumentUrl   = (string) ($responseOrderData['receipt_document_url'] ?? '');
        $this->waybillDocumentUrl   = (string) ($responseOrderData['waybill_document_url'] ?? '');

        $this->vehicleTypeId = (int) ($responseOrderData['vehicle_type_id'] ?? 6);

        if (isset($responseOrderData['points']) && is_array($responseOrderData['points'])) {
            foreach ($responseOrderData['points'] as $pointResponseData) {
                if (is_array($pointResponseData)) {
                    if ($pointResponseData['point_type'] == PlainPointResponseModel::POINT_TYPE) {
                        $this->plainPoints[] = new PlainPointResponseModel($pointResponseData);
                    } elseif ($pointResponseData['point_type'] == SdekPointResponseModel::POINT_TYPE) {
                        $this->sdekPoints[] = new SdekPointResponseModel($pointResponseData);
                    }
                }
            }
        }
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function getMatter(): string
    {
        return $this->matter;
    }

    public function getStatus(): string
    {
        return $this->status ?: '';
    }

    /**
     * @return CourierResponseModel|null
     */
    public function getCourier()
    {
        return $this->courier;
    }

    public function getPlainDeliveryFeeAmount(): float
    {
        return $this->deliveryFeeAmount;
    }

    public function getIntercityDeliveryFeeAmount(): float
    {
        return $this->intercityDeliveryFeeAmount;
    }

    public function getTotalDeliveryFeeAmount(): float
    {
        return $this->deliveryFeeAmount + $this->intercityDeliveryFeeAmount;
    }

    public function getInsuranceFeeAmount(): float
    {
        return $this->insuranceFeeAmount;
    }

    public function getWeightFeeAmount(): float
    {
        return $this->weightFeeAmount;
    }

    public function getMoneyTransferFeeAmount(): float
    {
        return $this->moneyTransferFeeAmount;
    }

    public function getLoadingFeeAmount(): float
    {
        return $this->loadingFeeAmount;
    }

    public function getItineraryDocumentUrl(): string
    {
        return $this->itineraryDocumentUrl;
    }

    public function getReceiptDocumentUrl(): string
    {
        return $this->receiptDocumentUrl;
    }

    public function getWaybillDocumentUrl(): string
    {
        return $this->waybillDocumentUrl;
    }

    public function getPaymentAmount(): float
    {
        return $this->paymentAmount;
    }

    /**
     * @return PlainPointResponseModel[]
     */
    public function getPlainPoints(): array
    {
        return $this->plainPoints;
    }

    /**
     * @return SdekPointResponseModel[]
     */
    public function getSdekPoints(): array
    {
        return $this->sdekPoints;
    }

    public function getVehicleTypeId(): int
    {
        return $this->vehicleTypeId;
    }
}
