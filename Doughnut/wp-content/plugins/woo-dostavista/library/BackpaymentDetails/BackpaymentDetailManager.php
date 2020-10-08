<?php

namespace WooDostavista\BackpaymentDetails;

use stdClass;
use wpdb;

class BackpaymentDetailManager
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
                CREATE TABLE IF NOT EXISTS `woo_dostavista_backpayment_details` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `description` TEXT DEFAULT '',
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            "
        );
    }

    public function save(BackpaymentDetail $backpaymentDetail)
    {
        if ($backpaymentDetail->id) {
            $this->wpdb->query(
                $this->wpdb->prepare(
                    "
                        UPDATE `woo_dostavista_backpayment_details`
                        SET `description` = %s
                        WHERE `id` = %d
                    ",
                    $backpaymentDetail->description,
                    $backpaymentDetail->id
                )
            );
        } else {
            $this->wpdb->query(
                $this->wpdb->prepare(
                    "INSERT INTO `woo_dostavista_backpayment_details` (`description`) VALUES (%s)",
                    $backpaymentDetail->description
                )
            );

            $backpaymentDetail->id = (int) $this->wpdb->insert_id;
        }
    }

    /**
     * @param int $id
     * @return BackpaymentDetail|null
     */
    public function getById(int $id)
    {
        $models = $this->getByIds([$id]);
        if ($models) {
            return $models[0];
        }

        return null;
    }

    /**
     * @param array $ids
     * @return BackpaymentDetail[]
     */
    public function getByIds(array $ids): array
    {
        if (!$ids) {
            return [];
        }

        $models = [];

        $idsWhereSql = join(',', $ids);
        $rows = $this->wpdb->get_results("SELECT * FROM `woo_dostavista_backpayment_details` WHERE `id` IN ({$idsWhereSql}) ORDER BY `id` ASC");
        if ($rows) {
            foreach ($rows as $row) {
                $models[] = $this->populateModel($row);
            }
        }

        return $models;
    }

    /**
     * @return BackpaymentDetail[]
     */
    public function getList(): array
    {
        $models = [];

        $rows = $this->wpdb->get_results("SELECT * FROM `woo_dostavista_backpayment_details` ORDER BY `id` ASC");
        if ($rows) {
            foreach ($rows as $row) {
                $models[] = $this->populateModel($row);
            }
        }

        return $models;
    }

    public function delete(int $id): bool
    {
        $model = $this->getById($id);
        if (!$model) {
            return false;
        }

        $this->wpdb->query("DELETE FROM `woo_dostavista_backpayment_details` WHERE `id` = " . (int) $model->id);

        return true;
    }

    private function populateModel(stdClass $row): BackpaymentDetail
    {
        $backpaymentDetail = new BackpaymentDetail();

        $backpaymentDetail->id          = (int) $row->id;
        $backpaymentDetail->description = $row->description;

        return $backpaymentDetail;
    }
}
