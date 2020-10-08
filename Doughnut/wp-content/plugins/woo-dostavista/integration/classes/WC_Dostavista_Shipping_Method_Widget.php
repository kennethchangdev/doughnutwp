<?php

defined('ABSPATH') || exit;

class WC_Dostavista_Shipping_Method_Widget
{
    public function __construct()
    {

    }

    public function init()
    {
//        add_action('woocommerce_after_shipping_rate', [$this, 'display_custom_fields']);
//        add_action('woocommerce_checkout_fields', [$this, 'display_custom_checkout_fields']);
        add_action('wp_footer', [$this, 'add_js']);
    }

    /**
     * @param WC_Shipping_Rate $method
     */
    public function display_custom_fields($method)
    {
        if ($method->get_id() === 'dostavista') {
            ?>
            <input type="text" name="dostavista_shipping_required_date">
            <input type="text" name="dostavista_shipping_required_start_time">
            <input type="text" name="dostavista_shipping_required_finish_time">
            <?php
        }
    }

    public function display_custom_checkout_fields($fields)
    {
        $fields['shipping']['dostavista_shipping_required_date'] = [
            'type'     => 'text',
            'label'    => WC_Dostavista_Lang::getHtml('shipping_required_date') . ' ' . time(),
            'required' => true,
        ];
        $fields['shipping']['required_start_time'] = [
            'type'     => 'text',
            'label'    => WC_Dostavista_Lang::getHtml('shipping_required_start_time'),
            'required' => true,
        ];
        $fields['shipping']['required_finish_time'] = [
            'type'     => 'text',
            'label'    => WC_Dostavista_Lang::getHtml('shipping_required_finish_time'),
            'required' => true,
        ];

        return $fields;
    }

    public function add_js()
    {
        echo '<script type="text/javascript" src="' . plugin_dir_url(WC_DOSTAVISTA_PLUGIN_FILE) . 'assets/js/shipping-widget.js' . '"></script>';
    }
}
