<?php

namespace WooDostavista\DvCmsModuleApiClient\Request;

class PlainPointRequestModel implements PointRequestModelInterface
{
    /** @var string|null */
    private $address;

    /** @var string */
    private $contactPersonPhone = '';

    /** @var string */
    private $contactPersonName = '';

    /** @var string|null */
    private $clientOrderId;

    /** @var float|null */
    private $latitude;

    /** @var float|null */
    private $longitude;

    /** @var string|null */
    private $requiredStartDatetime;

    /** @var string|null */
    private $requiredFinishDatetime;

    /** @var float */
    private $takingAmount = 0;

    /** @var float */
    private $buyoutAmount = 0;

    /** @var string|null */
    private $note;

    public function setAddress(string $address): PlainPointRequestModel
    {
        $this->address = $address;
        return $this;
    }

    public function setContactPerson(string $name, string $phone): PlainPointRequestModel
    {
        $this->contactPersonName  = $name;
        $this->contactPersonPhone = $phone;
        return $this;
    }

    public function setClientOrderId(string $clientOrderId): PlainPointRequestModel
    {
        $this->clientOrderId = $clientOrderId;
        return $this;
    }

    public function setLocation(float $latitude, float $longitude): PlainPointRequestModel
    {
        $this->latitude  = $latitude;
        $this->longitude = $longitude;
        return $this;
    }

    public function setRequiredTimeInterval(string $start, string $finish): PlainPointRequestModel
    {
        $this->requiredStartDatetime  = $start;
        $this->requiredFinishDatetime = $finish;
        return $this;
    }

    public function setTakingAmount(float $takingAmount): PlainPointRequestModel
    {
        $this->takingAmount = $takingAmount;
        return $this;
    }

    public function setBuyoutAmount(float $buyoutAmount): PlainPointRequestModel
    {
        $this->buyoutAmount = $buyoutAmount;
        return $this;
    }

    public function setNote(string $note): PlainPointRequestModel
    {
        $this->note = $note;
        return $this;
    }

    public function getRequestData(): array
    {
        return [
            'point_type'               => 'plain',
            'address'                  => $this->address,
            'contact_person'           => [
                'phone' => $this->contactPersonPhone,
                'name'  => $this->contactPersonName,
            ],
            'client_order_id'          => $this->clientOrderId,
            'latitude'                 => $this->latitude,
            'longitude'                => $this->longitude,
            'required_start_datetime'  => $this->requiredStartDatetime,
            'required_finish_datetime' => $this->requiredFinishDatetime,
            'taking_amount'            => $this->takingAmount,
            'buyout_amount'            => $this->buyoutAmount,
            'note'                     => $this->note,
        ];
    }
}
