<?php

namespace WooDostavista\DostavistaOrders;

class DostavistaOrder
{
    /** @var int */
    public $id;

    /** @var int */
    public $dostavistaOrderId;

    /** @var string */
    public $courierName = '';

    /** @var string */
    public $courierPhone = '';

    /** @var string */
    public $createdDatetime;

    /** @var int[] */
    public $wooOrderIds = [];
}
