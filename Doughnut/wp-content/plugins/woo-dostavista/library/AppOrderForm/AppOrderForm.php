<?php

namespace WooDostavista\AppOrderForm;

use DateTime;
use DateInterval;
use DateTimeZone;
use WooDostavista\BackpaymentDetails\BackpaymentDetail;
use WooDostavista\ModuleSettings\ModuleSettings;
use WooDostavista\Warehouses\Warehouse;
use WooDostavista\WooOrders\WooOrderDto;

class AppOrderForm
{
    /** @var WooOrderDto[] */
    private $wooOrders;

    /** @var ModuleSettings */
    private $settings;

    /** @var Warehouse */
    private $defaultWarehouse;

    /** @var BackpaymentDetail */
    private $defaultBackpaymentDetail;

    /** @var DateTimeZone */
    private $dateTimeZone;

    /**
     * @param WooOrderDto[] $wooOrders
     * @param ModuleSettings $settings
     * @param Warehouse $defaultWarehouse
     * @param BackpaymentDetail $defaultBackpaymentDetail
     * @param DateTimeZone $dateTimeZone
     */
    public function  __construct(
        array $wooOrders,
        ModuleSettings $settings,
        Warehouse $defaultWarehouse,
        BackpaymentDetail $defaultBackpaymentDetail,
        DateTimeZone $dateTimeZone
    ) {
        $this->wooOrders                = $wooOrders;
        $this->settings                 = $settings;
        $this->defaultWarehouse         = $defaultWarehouse;
        $this->defaultBackpaymentDetail = $defaultBackpaymentDetail;
        $this->dateTimeZone             = $dateTimeZone;
    }

    /**
     * @return WooOrderDto[]
     */
    public function getWooOrders(): array
    {
        return $this->wooOrders;
    }

    public function getMatterWithFeatures(): string
    {
        $matterWithPrefix = !empty($this->settings->getDefaultMatter())
            ? $this->settings->getDefaultMatter()
            : $this->wooOrders[0]->getMatter();

        if ($this->settings->isMatterWeightPrefixEnabled()) {
            $matterWithPrefix = ($this->getItemsTotalWeightKg() ?: $this->settings->getDefaultOrderWeightKg()) . ' kg.'
                . ' ' . $matterWithPrefix;
        }

        return $matterWithPrefix;
    }

    public function getItemsTotalWeightKg(): int
    {
        $result = 0;
        foreach ($this->wooOrders as $wooOrder) {
            $result += $wooOrder->getItemsTotalWeightKg();
        }

        return $result ?: $this->settings->getDefaultOrderWeightKg();
    }

    public function getInsuranceAmount(): float
    {
        if ($this->settings->isInsuranceEnabled()) {
            $insurance = 0;
            foreach ($this->wooOrders as $wooOrder) {
                $insurance += $this->getPlainPointTakingAmount($wooOrder) ?: $wooOrder->getItemsTotalPrice();
            }

            return $insurance;
        }

        return 0;
    }

    public function getPickupRequiredDate(): string
    {
        return $this->isNextDayPickup() ? date('d.m.Y', strtotime('+1 day')) : date('d.m.Y');
    }

    /**
     * @return string date('H:i')
     */
    public function getPickupRequiredStartTime(): string
    {
        // Интервал на обработку заказа
        $orderProcessingInterval = $this->settings->getOrderProcessingTimeHours()
            ? new DateInterval('PT' . $this->settings->getOrderProcessingTimeHours() . 'H')
            : false;

        if ($this->isNextDayPickup()) {
            return $orderProcessingInterval ?
                (new DateTime($this->defaultWarehouse->workStartTime, $this->dateTimeZone))
                    ->add($orderProcessingInterval)
                    ->format('H:i')
                : $this->defaultWarehouse->workStartTime;
        }

        $dateTime = (new DateTime('now', $this->dateTimeZone));

        // Если в настройках магазина указано время на обработку заказа, то добавим его
        if ($orderProcessingInterval) {
            $dateTime->add($orderProcessingInterval);
        }

        $nowMinutes = (int) $dateTime->format('i');
        $nowHours   = (int) $dateTime->format('H');

        $warehouseStartDateTime = (new DateTime($this->defaultWarehouse->workStartTime, $this->dateTimeZone));

        if ($dateTime->getTimestamp() >= $warehouseStartDateTime->getTimestamp()) {
            $m = $nowMinutes >= 30 ? 0 : 30;
            $h = $nowMinutes >= 30 ? $nowHours + 1 : $nowHours;

            return (new DateTime("$h:$m", $this->dateTimeZone))->format('H:i');
        }

        return $this->defaultWarehouse->workStartTime;
    }

    public function isNextDayPickup(): bool
    {
        $processingHours   = $this->settings->getOrderProcessingTimeHours();
        $processingMinutes = $processingHours ? $processingHours * 60 : 30;

        if (!$this->defaultWarehouse->workFinishTime) {
            return false;
        }

        $processingDateTime      = (new DateTime("+{$processingMinutes} minutes", $this->dateTimeZone));
        $warehouseFinishDateTime = (new DateTime($this->defaultWarehouse->workFinishTime, $this->dateTimeZone));

        return $processingDateTime->getTimestamp() >= $warehouseFinishDateTime->getTimestamp();
    }

    public function getPickupBuyoutAmount(): float
    {
        $result = 0;
        if ($this->settings->isBuyoutEnabled() && $this->settings->getCashPaymentMethodCode()) {
            foreach ($this->wooOrders as $wooOrder) {
                if ($this->isCashPayment($wooOrder)) {
                    $result += $wooOrder->getItemsTotalPrice();
                }
            }
        }

        return $result;
    }

    /**
     * @return int|null
     */
    public function getBackpaymentDetailId()
    {
        foreach ($this->wooOrders as $wooOrder) {
            if ($this->isCashPayment($wooOrder)) {
                return $this->defaultBackpaymentDetail->id;
            }
        }

        return null;
    }

    public function isCashPayment(WooOrderDto $wooOrder): bool
    {
        return ($this->settings->getCashPaymentMethodCode() && $wooOrder->getPaymentMethodCode() === $this->settings->getCashPaymentMethodCode());
    }

    public function getNoteWithPrefix(WooOrderDto $wooOrderDto): string
    {
        return trim($this->settings->getDeliveryPointNotePrefix() . ' ' . $wooOrderDto->getComment());
    }

    public function getPlainPointTakingAmount(WooOrderDto $wooOrder): float
    {
        return $this->isCashPayment($wooOrder) ? $wooOrder->getItemsTotalPrice() + $wooOrder->getDeliveryPrice() : 0;
    }

    public function getDeliveryRequiredStartTime(WooOrderDto $order): string
    {
        if ($order->getRequiredShippingDate() > date('Y-m-d')) {
            return $order->getRequiredShippingStartTime();
        }

        $nowDateTime   = (new DateTime('now', $this->dateTimeZone));
        $startDateTime = new DateTime($order->getRequiredShippingStartTime(), $this->dateTimeZone);

        $nowMinutes = (int) $nowDateTime->format('i');
        $nowHours   = (int) $nowDateTime->format('H');

        if ($nowDateTime->getTimestamp() >= $startDateTime->getTimestamp()) {
            $m = $nowMinutes >= 30 ? 0 : 30;
            $h = $nowMinutes >= 30 ? $nowHours + 1 : $nowHours;

            return (new DateTime("$h:$m", $this->dateTimeZone))->format('H:i');
        }

        return $startDateTime->format('H:i');
    }

    public function getDeliveryRequiredFinishTime(WooOrderDto $order): string
    {
        return $order->getRequiredShippingFinishTime();
    }
}
