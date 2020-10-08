<?php

defined('ABSPATH') || exit;

final class WC_Dostavista
{
    public function init()
    {
        define('WC_DOSTAVISTA_PLUGIN_BASENAME', plugin_basename(WC_DOSTAVISTA_PLUGIN_FILE));

        $this->load_classes();

        (new WC_Dostavista_Installer())->init();

        $this->init_hooks();
    }

    public function load_classes()
    {
        require_once __DIR__ . '/WC_Dostavista_Installer.php';
        require_once __DIR__ . '/WC_Dostavista_Router.php';
        require_once __DIR__ . '/WC_Dostavista_Admin_Menu.php';
        require_once __DIR__ . '/WC_Dostavista_Api_Callback_Handler.php';
        require_once __DIR__ . '/WC_Dostavista_Settings_Controller.php';
        require_once __DIR__ . '/WC_Dostavista_Wizard_Controller.php';
        require_once __DIR__ . '/WC_Dostavista_Warehouses_Controller.php';
        require_once __DIR__ . '/WC_Dostavista_Backpayment_Details_Controller.php';
        require_once __DIR__ . '/WC_Dostavista_Support_Controller.php';
        require_once __DIR__ . '/WC_Dostavista_Order_Form_Controller.php';
        require_once __DIR__ . '/WC_Dostavista_Lang.php';
        require_once __DIR__ . '/WC_Dostavista_Updater.php';
        require_once __DIR__ . '/WC_Dostavista_Middlewares.php';
        require_once __DIR__ . '/WC_Dostavista_Client_Auth_Controller.php';

        // PSR-4 autoloader WooDostavista Library
        spl_autoload_register(function($className) {
            $filteredClassName = str_replace('WooDostavista\\', '', $className);
            $baseDestination = str_replace('\\', DIRECTORY_SEPARATOR, $filteredClassName) . '.php';
            $fileDestination = __DIR__ . '/../../library/' . $baseDestination;
            if (is_file($fileDestination)) {
                require $fileDestination;
            }
        });
    }

    public function init_hooks()
    {
        register_activation_hook(WC_DOSTAVISTA_PLUGIN_FILE, [WC_Dostavista_Installer::class, 'install_handler']);
    }
}
