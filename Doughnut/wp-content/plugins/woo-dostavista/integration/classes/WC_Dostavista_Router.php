<?php

defined('ABSPATH') || exit;

class WC_Dostavista_Router
{
    private function addAuthPostRoute(string $action, array $callbackFunction)
    {
        add_action('admin_post_woo_dostavista/' . $action, $callbackFunction);
    }

    private function addNotAuthPostRoute(string $action, array $callbackFunction)
    {
        add_action('admin_post_nopriv_woo_dostavista/' . $action, $callbackFunction);
    }

    public function init()
    {
        $this->addNotAuthPostRoute('api_callback_handler', [WC_Dostavista_Api_Callback_Handler::class, 'handler']);
        $this->addAuthPostRoute('store_settings', [WC_Dostavista_Settings_Controller::class, 'store']);
        $this->addAuthPostRoute('store_warehouse', [WC_Dostavista_Warehouses_Controller::class, 'store']);
        $this->addAuthPostRoute('delete_warehouse', [WC_Dostavista_Warehouses_Controller::class, 'delete']);
        $this->addAuthPostRoute('store_backpayment_detail', [WC_Dostavista_Backpayment_Details_Controller::class, 'store']);
        $this->addAuthPostRoute('delete_backpayment_detail', [WC_Dostavista_Backpayment_Details_Controller::class, 'delete']);
        $this->addAuthPostRoute('calculate_order', [WC_Dostavista_Order_Form_Controller::class, 'calculate']);
        $this->addAuthPostRoute('create_order', [WC_Dostavista_Order_Form_Controller::class, 'create']);
        $this->addAuthPostRoute('create_auth_token', [WC_Dostavista_Wizard_Controller::class, 'create_auth_token']);
        $this->addAuthPostRoute('install_shipping_method', [WC_Dostavista_Wizard_Controller::class, 'install_shipping_method']);
        $this->addAuthPostRoute('add_shipping_zone', [WC_Dostavista_Wizard_Controller::class, 'add_shipping_zone']);
        $this->addAuthPostRoute('get_payment_types', [WC_Dostavista_Wizard_Controller::class, 'get_payment_types']);
        $this->addAuthPostRoute('set-wizard-last-finished-step', [WC_Dostavista_Wizard_Controller::class, 'set_last_finished_step']);
        $this->addAuthPostRoute('dostavista-logout', [WC_Dostavista_Client_Auth_Controller::class, 'logout_dostavista']);
    }
}
