<?php

use WooDostavista\BackpaymentDetails\BackpaymentDetail;
use WooDostavista\BackpaymentDetails\BackpaymentDetailManager;
use WooDostavista\ModuleSettings\ModuleSettings;
use WooDostavista\View\JsonResponse;
use WooDostavista\View\View;

defined('ABSPATH') || exit;

class WC_Dostavista_Backpayment_Details_Controller
{
    public static function index()
    {
        global $wpdb;

        // Пропустим запрос через милвару, проверяющую  законченность визарда
        WC_Dostavista_Middlewares::preventUnfinishedWizard();

        $backpaymentDetailManager = new BackpaymentDetailManager($wpdb);
        $backpaymentDetails       = $backpaymentDetailManager->getList();

        $settings = new ModuleSettings($wpdb);

        $view = new View(
            __DIR__ . '/../views/backpayment-detail-list.php',
            [
                'backpaymentDetails'         => $backpaymentDetails,
                'defaultBackpaymentDetailId' => $settings->getDefaultBackpaymentDetailId(),
            ]
        );

        echo $view->getRenderedHtml();
    }

    public static function edit()
    {
        global $wpdb;

        // Пропустим запрос через милвару, проверяющую  законченность визарда
        WC_Dostavista_Middlewares::preventUnfinishedWizard();

        $backpaymentDetailManager = new BackpaymentDetailManager($wpdb);

        $id = ($_REQUEST['id'] ?? null);
        if ($id) {
            $backpaymentDetail = $backpaymentDetailManager->getById((int) $id);
        } else {
            $backpaymentDetail = new BackpaymentDetail();
        }

        $view = new View(
            __DIR__ . '/../views/backpayment-detail-edit.php',
            [
                'backpaymentDetail' => $backpaymentDetail,
            ]
        );

        echo $view->getRenderedHtml();
    }

    public static function store()
    {
        global $wpdb;

        $backpaymentDetailManager = new BackpaymentDetailManager($wpdb);

        $postBody = file_get_contents('php://input');
        $data     = json_decode($postBody, true) ?? [];

        if (!$data) {
            (new JsonResponse(['error' => 'required_post_data']))->render();
            exit();
        }

        $id = ($data['id'] ?? null);
        if ($id) {
            $backpaymentDetail = $backpaymentDetailManager->getById((int) $id);
        } else {
            $backpaymentDetail = new BackpaymentDetail();
        }

        $backpaymentDetail->description = trim($data['description']);

        $backpaymentDetailManager->save($backpaymentDetail);

        (new JsonResponse(
            [
                'redirect_url' => admin_url('admin.php?page=woo_dostavista_backpayment_details'),
                'backpayment_detail' => [
                    'id' => $backpaymentDetail->id,
                ],
            ]
        ))->render();
    }

    public static function delete()
    {
        global $wpdb;

        $backpaymentDetailManager = new BackpaymentDetailManager($wpdb);

        $postBody = file_get_contents('php://input');
        $data     = json_decode($postBody, true) ?? [];

        $id = ($data['id'] ?? null);
        $backpaymentDetailManager->delete((int) $id);

        (new JsonResponse([]))->render();
    }
}
