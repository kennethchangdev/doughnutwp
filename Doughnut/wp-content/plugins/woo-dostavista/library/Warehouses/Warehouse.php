<?php

namespace WooDostavista\Warehouses;

class Warehouse
{
    /** @var int|null */
    public $id;

    /** @var string */
    public $name;

    /** @var string */
    public $city;

    /** @var string */
    public $address;

    /** @var string */
    public $workStartTime = '08:00';

    /** @var string */
    public $workFinishTime = '20:00';

    /** @var string */
    public $contactName;

    /** @var string */
    public $contactPhone;

    /** @var string */
    public $note;

    public function getFullAddress(): string
    {
        if ($this->address && $this->city && strpos($this->address, $this->city) === false) {
            return $this->city . ', ' . $this->address;
        } else {
            return $this->address;
        }
    }
}
