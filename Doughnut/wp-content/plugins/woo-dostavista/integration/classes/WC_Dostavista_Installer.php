<?php

use WooDostavista\ModuleConfig\ModuleConfig;
use WooDostavista\ModuleMetric\ModuleMetricManager;
use WooDostavista\ModuleSettings\ModuleSettings;

defined('ABSPATH') || exit;

class WC_Dostavista_Installer
{
    public function init()
    {
        // Обновляем приложение
        (new WC_Dostavista_Updater())->init();

        // Добавляем ссылку "Настройки" в списке плагинов в нашем модуле
        add_filter('plugin_action_links_' . WC_DOSTAVISTA_PLUGIN_BASENAME, [$this, 'plugin_action_links']);

        // Создаем меню модуля в админке
        (new WC_Dostavista_Admin_Menu())->init();

        // Запускаем роутер
        (new WC_Dostavista_Router())->init();

        // Подтягиваем службу доставки
        add_filter('woocommerce_shipping_methods', [$this, 'add_woocommerce_shipping_method']);
        add_action('woocommerce_shipping_init', [$this, 'include_woocommerce_shipping_method']);

        // Подключаем и настраиваем frontendHttpClient JS
        add_action('admin_head', [$this, 'include_frontend_http_client_js']);

        // Подключаем стили для админки
        add_action('admin_head', [$this, 'include_styles']);

        // Управляем страницой заказов WooCommerce через JS
        add_action('admin_head', [$this, 'add_woocommerce_orders_js']);
    }

    public static function install_handler()
    {
        global $wpdb;

        $settings = new ModuleSettings($wpdb);

        $moduleConfig = new ModuleConfig();
        $moduleMetricManager = new ModuleMetricManager(
            $wpdb,
            $moduleConfig->getDvCmsModuleApiProdUrl(),
            $settings->getApiUrl(),
            $settings->getAuthToken()
        );

        $moduleMetricManager->install();
    }

    public function plugin_action_links($links)
    {
        $action_links = [
            'settings' => '<a href="' . admin_url('admin.php?page=woo_dostavista') . '">'. WC_Dostavista_Lang::getHtml('plugin_action_link_settings') .'</a>',
        ];

        return array_merge($action_links, $links);
    }

    public function add_woocommerce_shipping_method($methods)
    {
        $methods['dostavista'] = 'WC_Dostavista_Shipping_Method';
        return $methods;
    }

    public function include_woocommerce_shipping_method()
    {
        require_once __DIR__ . '/WC_Dostavista_Shipping_Method.php';
        require_once __DIR__ . '/WC_Dostavista_Shipping_Method_Widget.php';

        (new WC_Dostavista_Shipping_Method_Widget())->init();
    }

    public function add_woocommerce_orders_js()
    {
        echo '<script>
                if (typeof(woodostavista) === "undefined") {
                    woodostavista = {};
                }
            
                woodostavista.wcOrder = {
                    translations :  {
                        check_orders_required : "'. WC_Dostavista_Lang::getHtml('wc_order_check_orders_required') .'",
                        send_to_dostavista : "'. WC_Dostavista_Lang::getHtml('wc_order_send_to_dostavista') .'",
                    }
                };
            </script>';
        echo '<script type="text/javascript" src="' . plugin_dir_url(WC_DOSTAVISTA_PLUGIN_FILE) . 'assets/js/woocommerce-orders.js' . '"></script>';
        echo '<script type="text/javascript">woodostavista.wcOrdersController.order_form_url = "' . admin_url('admin.php?page=woo_dostavista_order_form') . '"</script>';

    }

    public function include_frontend_http_client_js()
    {
        echo '<script type="text/javascript" src="' . plugin_dir_url(WC_DOSTAVISTA_PLUGIN_FILE) . 'assets/js/frontendHttpClient.js' . '"></script>';
        echo '<script type="text/javascript">woodostavista.frontendHttpClient.setPostUrl("' . admin_url('admin-post.php') . '")</script>';
    }

    public function include_styles()
    {
        // Подключаем пока общие стили модуля для админки
        wp_enqueue_style('woo-dostavista-admin', plugin_dir_url(WC_DOSTAVISTA_PLUGIN_FILE) . 'assets/css/admin.css');
    }
}
