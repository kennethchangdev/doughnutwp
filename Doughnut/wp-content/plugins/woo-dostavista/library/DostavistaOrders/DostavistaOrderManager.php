<?php

namespace WooDostavista\DostavistaOrders;

use wpdb;

class DostavistaOrderManager
{
    /** @var wpdb */
    private $wpdb;

    public function __construct(wpdb $wpdb)
    {
        $this->wpdb = $wpdb;
        $this->createTablesIfNotExists();
    }

    public function createTablesIfNotExists()
    {
        $this->wpdb->query(
            "
                CREATE TABLE IF NOT EXISTS `woo_dostavista_dv_orders` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `dostavista_order_id` INT(11) NOT NULL,
                    `courier_name` VARCHAR(255) DEFAULT '',
                    `courier_phone` VARCHAR(100) DEFAULT '',
                    `created_datetime` DATETIME NOT NULL,
                    PRIMARY KEY (`id`),
                    KEY `idx_dostavista_order_id` (`dostavista_order_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            "
        );

        $this->wpdb->query(
            "
                CREATE TABLE IF NOT EXISTS `woo_dostavista_dv_woo_orders` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `dostavista_order_id` INT(11) NOT NULL,
                    `woo_order_id` INT(11) NOT NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE INDEX `idx_dostavista_order_id_opencart_order_id` (`dostavista_order_id`, `woo_order_id`),
                    KEY `idx_woo_order_id` (`woo_order_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci
            "
        );
    }

    public function save(DostavistaOrder $dostavistaOrder)
    {
        if (!$dostavistaOrder->createdDatetime) {
            $dostavistaOrder->createdDatetime = date('Y-m-d H:i:s');
        }

        if ($dostavistaOrder->id) {
            $this->wpdb->query(
                $this->wpdb->prepare(
                    "
                        UPDATE `woo_dostavista_dv_orders`
                        SET 
                            `dostavista_order_id` = %d,
                            `courier_name` = %s,
                            `courier_phone` = %s
                        WHERE `id` = %d    
                    ",
                    $dostavistaOrder->dostavistaOrderId,
                    $dostavistaOrder->courierName,
                    $dostavistaOrder->courierPhone,
                    $dostavistaOrder->id
                )
            );

            $this->wpdb->query(
                $this->wpdb->prepare(
                    "DELETE FROM `woo_dostavista_dv_woo_orders` WHERE `dostavista_order_id` = %d",
                    $dostavistaOrder->dostavistaOrderId
                )
            );
        } else {
            $this->wpdb->query(
                $this->wpdb->prepare(
                    "
                        INSERT INTO `woo_dostavista_dv_orders` (`dostavista_order_id`, `courier_name`, `courier_phone`, `created_datetime`)
                        VALUES (%d, %s, %s, %s) 
                    ",
                    $dostavistaOrder->dostavistaOrderId,
                    $dostavistaOrder->courierName,
                    $dostavistaOrder->courierPhone,
                    $dostavistaOrder->createdDatetime
                )
            );

            $dostavistaOrder->id = $this->wpdb->insert_id;

            if ($dostavistaOrder->wooOrderIds) {
                $wooOrderIdWhereSql = join(',', $dostavistaOrder->wooOrderIds);
                $this->wpdb->query(
                    "DELETE FROM `woo_dostavista_dv_woo_orders` WHERE `woo_order_id` IN ({$wooOrderIdWhereSql})"
                );
            }
        }

        foreach ($dostavistaOrder->wooOrderIds as $wooOrderId) {
            $this->wpdb->query(
                $this->wpdb->prepare(
                    "
                       INSERT INTO `woo_dostavista_dv_woo_orders` (`dostavista_order_id`, `woo_order_id`)
                       VALUES (%d, %d)
                    ",
                    $dostavistaOrder->dostavistaOrderId,
                    $wooOrderId
                )
            );
        }
    }

    /**
     * @param int $id
     * @return DostavistaOrder|null
     */
    public function getByDostavistaOrderId(int $id)
    {
        $dostavistaOrders = $this->getByDostavistaOrderIds([$id]);
        if ($dostavistaOrders) {
            return $dostavistaOrders[0];
        }

        return null;
    }

    /**
     * @param array $ids
     * @return DostavistaOrder[]
     */
    public function getByDostavistaOrderIds(array $ids): array
    {
        if (!$ids) {
            return [];
        }

        $dostavistaOrders = [];

        $idsWhereSql = join(',', $ids);
        $dvOrderRows = $this->wpdb->get_results("SELECT * FROM `woo_dostavista_dv_orders` WHERE `dostavista_order_id` IN ({$idsWhereSql})");
        if ($dvOrderRows) {
            $dvWooOrderRows = $this->wpdb->get_results("SELECT * FROM `woo_dostavista_dv_woo_orders` WHERE `dostavista_order_id` IN ({$idsWhereSql})");

            foreach ($dvOrderRows as $dvOrderRow) {
                $dostavistaOrder                    = new DostavistaOrder();
                $dostavistaOrder->id                = (int) $dvOrderRow->id;
                $dostavistaOrder->dostavistaOrderId = (int) $dvOrderRow->dostavista_order_id;
                $dostavistaOrder->courierName       = $dvOrderRow->courier_name;
                $dostavistaOrder->courierPhone      = $dvOrderRow->courier_phone;
                $dostavistaOrder->createdDatetime   = $dvOrderRow->created_datetime;

                if ($dvWooOrderRows) {
                    foreach ($dvWooOrderRows as $dvWooOrderRow) {
                        if ($dvWooOrderRow->dostavista_order_id === $dvOrderRow->dostavista_order_id) {
                            $dostavistaOrder->wooOrderIds[] = (int) $dvWooOrderRow->woo_order_id;
                        }
                    }
                }

                $dostavistaOrders[] = $dostavistaOrder;
            }
        }

        return $dostavistaOrders;
    }

    /**
     * @param int[] $ids
     * @return DostavistaOrder[]
     */
    public function getByWooOrderIds(array $ids): array
    {
        if (!$ids) {
            return [];
        }

        $idsWhereSql = join(',', $ids);
        $rows = $this->wpdb->get_results("SELECT * FROM `woo_dostavista_dv_woo_orders` WHERE `woo_order_id` IN ({$idsWhereSql})");
        if ($rows) {
            $dostavistaOrderIds = array_column($rows, 'dostavista_order_id');
            return $this->getByDostavistaOrderIds($dostavistaOrderIds);
        }

        return [];
    }
}
