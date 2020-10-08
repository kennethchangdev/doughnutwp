<?php

use WooDostavista\Enums\ScheduleTimeEnum;
use WooDostavista\ModuleSettings\ModuleSettings;
use WooDostavista\View\JsonResponse;
use WooDostavista\View\View;
use WooDostavista\Warehouses\Warehouse;
use WooDostavista\Warehouses\WarehouseManager;

defined('ABSPATH') || exit;

class WC_Dostavista_Warehouses_Controller
{
    public static function index()
    {
        global $wpdb;

        // Пропустим запрос через милвару, проверяющую  законченность визарда
        WC_Dostavista_Middlewares::preventUnfinishedWizard();

        $warehouseManager = new WarehouseManager($wpdb);
        $warehouses       = $warehouseManager->getList();

        $settings = new ModuleSettings($wpdb);

        $view = new View(
            __DIR__ . '/../views/warehouse-list.php',
            [
                'warehouses'         => $warehouses,
                'defaultWarehouseId' => $settings->getDefaultPickupWarehouseId(),
            ]
        );

        echo $view->getRenderedHtml();
    }

    public static function edit()
    {
        global $wpdb;

        // Пропустим запрос через милвару, проверяющую  законченность визарда
        WC_Dostavista_Middlewares::preventUnfinishedWizard();

        $warehouseManager = new WarehouseManager($wpdb);

        $id = ($_REQUEST['id'] ?? null);
        if ($id) {
            $warehouse = $warehouseManager->getById((int) $id);
        } else {
            $warehouse = new Warehouse();
        }

        $scheduleTimeEnum = ScheduleTimeEnum::getEnum();

        $view = new View(
            __DIR__ . '/../views/warehouse-edit.php',
            [
                'warehouse'        => $warehouse,
                'scheduleTimeEnum' => $scheduleTimeEnum,
            ]
        );

        echo $view->getRenderedHtml();
    }

    public static function store()
    {
        global $wpdb;

        $warehouseManager = new WarehouseManager($wpdb);

        $postBody = file_get_contents('php://input');
        $data     = json_decode($postBody, true) ?? [];

        if (!$data) {
            (new JsonResponse(['error' => 'required_post_data'], 500))->render();
            exit();
        }

        $id = ($data['id'] ?? null);
        if ($id) {
            $warehouse = $warehouseManager->getById((int) $id);
        } else {
            $warehouse = new Warehouse();
        }

        $warehouse->name           = trim($data['name'] ?: 'default');
        $warehouse->city           = trim($data['city']);
        $warehouse->address        = trim($data['address']);
        $warehouse->workStartTime  = trim($data['work_start_time']);
        $warehouse->workFinishTime = trim($data['work_finish_time']);
        $warehouse->contactName    = trim($data['contact_name']);
        $warehouse->contactPhone   = trim($data['contact_phone']);
        $warehouse->note           = trim($data['note']);

        $warehouseManager->save($warehouse);

        (new JsonResponse(
            [
                'redirect_url' => admin_url('admin.php?page=woo_dostavista_warehouses'),
                'warehouse' => [
                    'id' => $warehouse->id,
                ],
            ]
        ))->render();
    }

    public static function delete()
    {
        global $wpdb;

        $warehouseManager = new WarehouseManager($wpdb);

        $postBody = file_get_contents('php://input');
        $data     = json_decode($postBody, true) ?? [];

        $id = ($data['id'] ?? null);
        $warehouseManager->delete((int) $id);

        (new JsonResponse([]))->render();
    }
}
