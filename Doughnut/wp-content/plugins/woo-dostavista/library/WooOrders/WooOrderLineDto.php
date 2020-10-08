<?php

namespace WooDostavista\WooOrders;

class WooOrderLineDto
{
    /** @var WooProductDto */
    private $wooProductDto;

    /** @var int */
    private $quantity;

    public function __construct(WooProductDto $wooProductDto, int $quantity)
    {
        $this->wooProductDto = $wooProductDto;
        $this->quantity      = $quantity;
    }

    public function getWooProductDto(): WooProductDto
    {
        return $this->wooProductDto;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}
