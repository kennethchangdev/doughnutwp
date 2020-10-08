<?php

namespace WooDostavista\DvCmsModuleApiClient\Request;

class SdekPointRequestModel implements PointRequestModelInterface
{
    const DELIVERY_METHOD_WAREHOUSE_DOOR      = 'warehouse_door';
    const DELIVERY_METHOD_WAREHOUSE_WAREHOUSE = 'warehouse_warehouse';

    /** @var int */
    private $addressCityId;

    /** @var string */
    private $addressStreet = '';

    /** @var string */
    private $addressBuildingNumber = '';

    /** @var string */
    private $addressApartmentNumber = '';

    /** @var string */
    private $deliveryMethod = self::DELIVERY_METHOD_WAREHOUSE_DOOR;

    /** @var int|null */
    private $warehouseId;

    /** @var string */
    private $contactPersonPhone = '';

    /** @var string */
    private $contactPersonName = '';

    /** @var int */
    private $weightKg;

    /** @var int */
    private $lengthCm;

    /** @var int */
    private $widthCm;

    /** @var int */
    private $heightCm;

    /** @var string|null */
    private $clientOrderId;

    /** @var string|null */
    private $note;

    /** @var array */
    private $packages = [];

    public function setAddressCityId(int $addressCityId): SdekPointRequestModel
    {
        $this->addressCityId = $addressCityId;
        return $this;
    }

    public function setAddress(string $addressStreet, string $addressBuildingNumber, string $addressApartmentNumber = ''): SdekPointRequestModel
    {
        $this->addressStreet          = $addressStreet;
        $this->addressBuildingNumber  = $addressBuildingNumber;
        $this->addressApartmentNumber = $addressApartmentNumber;
        return $this;
    }

    public function setDeliveryMethod(string $deliveryMethod): SdekPointRequestModel
    {
        $this->deliveryMethod = $deliveryMethod;
        return $this;
    }

    public function setWarehouseId(int $warehouseId): SdekPointRequestModel
    {
        $this->warehouseId = $warehouseId;
        return $this;
    }

    public function setContactPerson(string $name, string $phone): SdekPointRequestModel
    {
        $this->contactPersonName  = $name;
        $this->contactPersonPhone = $phone;
        return $this;
    }

    public function setWeightKg(int $weightKg): SdekPointRequestModel
    {
        $this->weightKg = $weightKg;
        return $this;
    }

    public function setDimensions(int $lengthCm, int $widthCm, int $heightCm): SdekPointRequestModel
    {
        $this->lengthCm = $lengthCm;
        $this->widthCm  = $widthCm;
        $this->heightCm = $heightCm;
        return $this;
    }

    public function setClientOrderId(string $clientOrderId): SdekPointRequestModel
    {
        $this->clientOrderId = $clientOrderId;
        return $this;
    }

    public function setNote(string $note): SdekPointRequestModel
    {
        $this->note = $note;
        return $this;
    }

    public function addPackage(string $wareCode, float $itemPaymentAmount, int $itemsCount, string $description): SdekPointRequestModel
    {
        $this->packages[] = [
            'ware_code'           => $wareCode,
            'item_payment_amount' => $itemPaymentAmount,
            'items_count'         => $itemsCount,

            // Костыль, т.к. API не дружит с пустым description
            'description'         => empty($description) ? '.' : $description,
        ];

        return $this;
    }

    public function getRequestData(): array
    {
        $data = [
            'point_type'           => 'sdek',
            'address_city_id'      => $this->addressCityId,
            'weight_kg'            => $this->weightKg,
            'length_cm'            => $this->lengthCm,
            'width_cm'             => $this->widthCm,
            'height_cm'            => $this->heightCm,
            'sdek_delivery_method' => $this->deliveryMethod,
            'contact_person'       => [
                'name'  => $this->contactPersonName,
                'phone' => $this->contactPersonPhone,
            ],
            'client_order_id'      => $this->clientOrderId,
            'note'                 => $this->note,
            'packages'             => $this->packages,
        ];

        if ($this->deliveryMethod == static::DELIVERY_METHOD_WAREHOUSE_DOOR) {
            $data['address_street']           = $this->addressStreet;
            $data['address_building_number']  = $this->addressBuildingNumber;
            $data['address_apartment_number'] = $this->addressApartmentNumber;
        } else {
            $data['sdek_warehouse_id'] = $this->warehouseId;
        }

        return $data;
    }
}
