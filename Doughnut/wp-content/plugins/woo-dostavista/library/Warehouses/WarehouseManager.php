<?php

namespace WooDostavista\Warehouses;

use stdClass;
use wpdb;

class WarehouseManager
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
                CREATE TABLE IF NOT EXISTS `woo_dostavista_warehouses` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `name` VARCHAR(255) DEFAULT '',
                    `address` VARCHAR(255) DEFAULT '',
                    `work_start_time` VARCHAR(5) DEFAULT '08:00',
                    `work_finish_time` VARCHAR(5) DEFAULT '20:00',
                    `contact_name` VARCHAR(255) DEFAULT '',
                    `contact_phone` VARCHAR(100) DEFAULT '',
                    `note` TEXT DEFAULT '',
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            "
        );

        // Alter table with update
        $rows = $this->wpdb->get_results("SHOW COLUMNS FROM `woo_dostavista_warehouses` LIKE 'city'");
        if (!$rows) {
            $this->wpdb->query("ALTER TABLE `woo_dostavista_warehouses` ADD COLUMN `city` VARCHAR(255) DEFAULT ''");
        }
    }

    public function save(Warehouse $warehouse)
    {
        if ($warehouse->id) {
            $this->wpdb->query(
                $this->wpdb->prepare(
                    "
                        UPDATE `woo_dostavista_warehouses`
                        SET 
                            `name` = %s,
                            `city` = %s,
                            `address` = %s,
                            `work_start_time` = %s,
                            `work_finish_time` = %s,
                            `contact_name` = %s,
                            `contact_phone` = %s,
                            `note` = %s
                        WHERE `id` = %d
                    ",
                    $warehouse->name,
                    $warehouse->city,
                    $warehouse->address,
                    $warehouse->workStartTime,
                    $warehouse->workFinishTime,
                    $warehouse->contactName,
                    $warehouse->contactPhone,
                    $warehouse->note,
                    $warehouse->id
                )
            );
        } else {
            $this->wpdb->query(
                $this->wpdb->prepare(
                    "
                        INSERT INTO `woo_dostavista_warehouses` 
                          (`name`, `city`, `address`, `work_start_time`, `work_finish_time`, `contact_name`, `contact_phone`, `note`)
                        VALUES (%s, %s, %s, %s, %s, %s, %s, %s) 
                    ",
                    $warehouse->name,
                    $warehouse->city,
                    $warehouse->address,
                    $warehouse->workStartTime,
                    $warehouse->workFinishTime,
                    $warehouse->contactName,
                    $warehouse->contactPhone,
                    $warehouse->note
                )
            );

            $warehouse->id = (int) $this->wpdb->insert_id;
        }
    }

    /**
     * @param int $id
     * @return Warehouse|null
     */
    public function getById(int $id)
    {
        $warehouses = $this->getByIds([$id]);
        if ($warehouses) {
            return $warehouses[0];
        }

        return null;
    }

    /**
     * @param array $ids
     * @return Warehouse[]
     */
    public function getByIds(array $ids): array
    {
        if (!$ids) {
            return [];
        }

        $warehouses = [];

        $idsWhereSql = join(',', $ids);
        $rows = $this->wpdb->get_results("SELECT * FROM `woo_dostavista_warehouses` WHERE `id` IN ({$idsWhereSql}) ORDER BY `id` ASC");
        if ($rows) {
            foreach ($rows as $row) {
                $warehouses[] = $this->populateModel($row);
            }
        }

        return $warehouses;
    }

    /**
     * @return Warehouse[]
     */
    public function getList(): array
    {
        $warehouses = [];

        $rows = $this->wpdb->get_results("SELECT * FROM `woo_dostavista_warehouses` ORDER BY `id` ASC");
        if ($rows) {
            foreach ($rows as $row) {
                $warehouses[] = $this->populateModel($row);
            }
        }

        return $warehouses;
    }

    public function delete(int $id): bool
    {
        $warehouse = $this->getById($id);
        if (!$warehouse) {
            return false;
        }

        $this->wpdb->query("DELETE FROM `woo_dostavista_warehouses` WHERE `id` = " . (int) $warehouse->id);

        return true;
    }

    private function populateModel(stdClass $row): Warehouse
    {
        $warehouse = new Warehouse();
        $warehouse->id             = (int) $row->id;
        $warehouse->name           = $row->name;
        $warehouse->city           = $row->city;
        $warehouse->address        = $row->address;
        $warehouse->workStartTime  = date('H:i', strtotime($row->work_start_time));
        $warehouse->workFinishTime = date('H:i', strtotime($row->work_finish_time));
        $warehouse->contactName    = $row->contact_name;
        $warehouse->contactPhone   = $row->contact_phone;
        $warehouse->note           = $row->note;

        return $warehouse;
    }

    /**
     * @return Warehouse|null
     */
    public function getFirstItemByCityName(string $cityName)
    {
        $data = $this->wpdb->get_results("SELECT * FROM `woo_dostavista_warehouses` WHERE city = '{$cityName}' LIMIT 1");
        if ($data && isset($data[0])) {
            return $this->populateModel($data[0]);
        }
        return null;
    }
}
