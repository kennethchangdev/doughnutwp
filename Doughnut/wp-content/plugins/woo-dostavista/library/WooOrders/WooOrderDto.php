<?php

namespace WooDostavista\WooOrders;

use WC_Order;
use WC_Order_Item_Product;

class WooOrderDto
{
    /** @var WC_Order */
    private $wcOrder;

    /** @var WooOrderLineDto[] */
    private $wooOrderLines;

    public function __construct(WC_Order $wcOrder)
    {
        $this->wcOrder = $wcOrder;
    }

    public function getId(): int
    {
        return $this->wcOrder->get_id();
    }

    /**
     * @return WooOrderLineDto[]
     */
    public function getOrderLines(): array
    {
        if ($this->wooOrderLines === null) {
            $lines = [];

            foreach ($this->wcOrder->get_items() as $wcOrderItem) {
                $wcOrderItemProduct = new WC_Order_Item_Product($wcOrderItem->get_id());
                if ($wcOrderItemProduct) {
                    $wcProduct = $wcOrderItemProduct->get_product();
                    if ($wcProduct) {
                        $lines[] = new WooOrderLineDto(
                            new WooProductDto($wcProduct),
                            $wcOrderItemProduct->get_quantity()
                        );
                    }
                }
            }

            $this->wooOrderLines = $lines;
        }

        return $this->wooOrderLines;
    }

    public function getMatter(): string
    {
        $lines = $this->getOrderLines();
        if ($lines) {
            return $lines[0]->getWooProductDto()->getTitle();
        }

        return '';
    }

    public function getContactPersonName(): string
    {
        $firstName = $this->wcOrder->get_shipping_first_name();
        $lastName  = $this->wcOrder->get_shipping_last_name();

        return trim($firstName . ' ' . $lastName);
    }

    public function getContactPersonPhone(): string
    {
        return $this->wcOrder->get_billing_phone();
    }

    private function getAddress(): string
    {
        return $this->wcOrder->get_shipping_address_1()
            ? $this->wcOrder->get_shipping_address_1()
            : $this->wcOrder->get_billing_address_1();
    }

    private function getCity(): string
    {
        return $this->wcOrder->get_shipping_city()
            ? $this->wcOrder->get_shipping_city()
            : $this->wcOrder->get_billing_city();
    }

    public function getAddressWithCityPrefix(): string
    {
        $city    = $this->getCity();
        $address = $this->getAddress();

        if ($address && $city && strpos($address, $city) === false) {
            $address = $city . ', ' . $address;
        }

        return $address;
    }

    public function getItemsTotalPrice(): float
    {
        $totalPrice = $this->wcOrder->get_total();
        return $totalPrice - $this->getDeliveryPrice();
    }

    public function getDeliveryPrice(): float
    {
        return $this->wcOrder->get_shipping_total();
    }

    public function getItemsTotalWeightKg(): int
    {
        $totalWeightKg = 0.0;
        foreach ($this->getOrderLines() as $wooOrderLine) {
            $totalWeightKg += $wooOrderLine->getWooProductDto()->getWeightKg() * $wooOrderLine->getQuantity();
        }

        // Известный вес до 1кг мы хотим округлять до целого кг, т.к. модуль использует только целые килограммы.
        // Вес более 1кг округляем далее в меньшую сторону (приводим к int)
        return $totalWeightKg ? max((int) $totalWeightKg, 1) : 0;
    }

    public function getComment(): string
    {
        return $this->wcOrder->get_shipping_address_2();
    }

    public function getPaymentMethodCode(): string
    {
        return $this->wcOrder->get_payment_method();
    }

    public function getRequiredShippingDate(): string
    {
        return date('Y-m-d');
    }

    public function getRequiredShippingStartTime(): string
    {
        return '16:00';
    }

    public function getRequiredShippingFinishTime(): string
    {
        return '21:00';
    }
}
