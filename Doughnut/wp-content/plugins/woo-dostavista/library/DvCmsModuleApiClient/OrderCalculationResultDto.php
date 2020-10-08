<?php

namespace WooDostavista\DvCmsModuleApiClient;

use WooDostavista\DvCmsModuleApiClient\Response\OrderResponseModel;

class OrderCalculationResultDto
{
    /** @var OrderResponseModel */
    private $orderResponseModel;

    public function __construct(OrderResponseModel $orderResponseModel)
    {
        $this->orderResponseModel = $orderResponseModel;
    }

    public function getData(): array
    {
        return [
            'plain_delivery_fee_amount'     => $this->orderResponseModel->getPlainDeliveryFeeAmount(),
            'intercity_delivery_fee_amount' => $this->orderResponseModel->getIntercityDeliveryFeeAmount(),
            'delivery_fee_amount'           => $this->orderResponseModel->getTotalDeliveryFeeAmount(),
            'insurance_fee_amount'          => $this->orderResponseModel->getInsuranceFeeAmount(),
            'weight_fee_amount'             => $this->orderResponseModel->getWeightFeeAmount(),
            'money_transfer_fee_amount'     => $this->orderResponseModel->getMoneyTransferFeeAmount(),
            'loading_fee_amount'            => $this->orderResponseModel->getLoadingFeeAmount(),
            'payment_amount'                => $this->orderResponseModel->getPaymentAmount(),
            'vehicle_type_id'               => $this->orderResponseModel->getVehicleTypeId(),
        ];
    }
}
