<?php

namespace WooDostavista\WooOrders;

use WC_Product;

class WooProductDto
{
    const WEIGHT_UNIT_G  = 'g';
    const WEIGHT_UNIT_KG = 'kg';

    /** @var WC_Product */
    private $wcProduct;

    public function __construct(WC_Product $wcProduct)
    {
        $this->wcProduct = $wcProduct;
    }

    public function getTitle(): string
    {
        return $this->wcProduct->get_name();
    }

    public function getWeightKg(): float
    {
        $weightKg = (float) $this->wcProduct->get_weight();
        $weightUnit = get_option('woocommerce_weight_unit');
        if ($weightUnit === static::WEIGHT_UNIT_G) {
            $weightKg = $weightKg / 1000;
        }

        return (float) $weightKg;
    }
}
