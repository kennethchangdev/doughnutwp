<?php
/**
 * Plugin Name: WooCommerce MrSpeedy
 * Plugin URI: https://mrspeedy.ph
 * Description: MrSpeedy - same day delivery service plugin for WooCommerce
 * Version: 1.2
 * Author: MrSpeedy
 * Author URI: https://mrspeedy.ph
 */

if (!defined('WC_DOSTAVISTA_PLUGIN_FILE')) {
    define('WC_DOSTAVISTA_PLUGIN_FILE', __FILE__);
}

require_once __DIR__ . '/integration/classes/WC_Dostavista.php';
(new WC_Dostavista())->init();

