<?php

use WooDostavista\WizardResult\WizardResultManager;

defined('ABSPATH') || exit;

class WC_Dostavista_Admin_Menu
{
    const GENERAL_CAPABILITY = 'manage_woocommerce';

    public function init()
    {
        add_action('admin_menu', [$this, 'install_admin_menu'], 9);
    }

    public function install_admin_menu()
    {
        $icon = WC_Dostavista_Lang::getHtml('dostavista_menu_logo');
        $isWizardFinished = WizardResultManager::getIsWizardFinished();

        add_menu_page('Dostavista', WC_Dostavista_Lang::getHtml('menu_dv_name'), static::GENERAL_CAPABILITY, 'woo_dostavista', [$this, 'settings_page'], $icon, '40');
        if ($isWizardFinished) {
            add_submenu_page('woo_dostavista', WC_Dostavista_Lang::getHtml('page_title_settings'), WC_Dostavista_Lang::getHtml('menu_settings'), static::GENERAL_CAPABILITY, 'woo_dostavista', [$this, 'settings_page']);
        }
        add_submenu_page('woo_dostavista', WC_Dostavista_Lang::getHtml('page_title_wizard'), WC_Dostavista_Lang::getHtml('menu_wizard'), static::GENERAL_CAPABILITY, 'woo_dostavista_wizard', [$this, 'wizard_page']);

        if ($isWizardFinished) {
            add_submenu_page('woo_dostavista', WC_Dostavista_Lang::getHtml('page_title_warehouses'), WC_Dostavista_Lang::getHtml('menu_warehouses'), static::GENERAL_CAPABILITY, 'woo_dostavista_warehouses', [$this, 'warehouses_index']);
            add_submenu_page('woo_dostavista_warehouses', WC_Dostavista_Lang::getHtml('page_title_warehouses_edit'), '', static::GENERAL_CAPABILITY, 'woo_dostavista_warehouse_edit', [$this, 'warehouses_edit']);

            add_submenu_page('woo_dostavista', WC_Dostavista_Lang::getHtml('page_title_requisites'), WC_Dostavista_Lang::getHtml('menu_requisites'), static::GENERAL_CAPABILITY, 'woo_dostavista_backpayment_details', [$this, 'backpayment_details_index']);
            add_submenu_page('woo_dostavista_backpayment_details', WC_Dostavista_Lang::getHtml('page_title_requisites_edit'), '', static::GENERAL_CAPABILITY, 'woo_dostavista_backpayment_detail_edit', [$this, 'backpayment_detail_edit']);
        }
        add_submenu_page('woo_dostavista', WC_Dostavista_Lang::getHtml('page_title_support'), WC_Dostavista_Lang::getHtml('menu_support'), static::GENERAL_CAPABILITY, 'woo_dostavista_support', [$this, 'support_page']);

        // Not menu pages
        add_submenu_page('woo_dostavista_private',  WC_Dostavista_Lang::getHtml('page_title_order_form'),  WC_Dostavista_Lang::getHtml('menu_order_form'), static::GENERAL_CAPABILITY, 'woo_dostavista_order_form', [$this, 'order_form']);
    }

    public function settings_page()
    {
        WC_Dostavista_Settings_Controller::index();
    }

    public function wizard_page()
    {
        WC_Dostavista_Wizard_Controller::index();
    }

    public function warehouses_index()
    {
        WC_Dostavista_Warehouses_Controller::index();
    }

    public function warehouses_edit()
    {
        WC_Dostavista_Warehouses_Controller::edit();
    }

    public function backpayment_details_index()
    {
        WC_Dostavista_Backpayment_Details_Controller::index();
    }

    public function backpayment_detail_edit()
    {
        WC_Dostavista_Backpayment_Details_Controller::edit();
    }

    public function support_page()
    {
        WC_Dostavista_Support_Controller::index();
    }

    public function order_form()
    {
        WC_Dostavista_Order_Form_Controller::index();
    }
}
