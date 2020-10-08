<?php

use WooDostavista\WizardResult\WizardResultManager;

defined('ABSPATH') || exit;

class WC_Dostavista_Middlewares
{
    public static function preventUnfinishedWizard()
    {
        if (!WizardResultManager::getIsWizardFinished()) {
            wp_redirect(admin_url('admin.php?page=woo_dostavista_wizard'));
        }
    }
}