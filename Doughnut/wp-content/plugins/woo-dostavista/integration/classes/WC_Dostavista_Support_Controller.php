<?php

defined( 'ABSPATH' ) || exit;

class WC_Dostavista_Support_Controller
{
    public static function index()
    {
        ?>
        <h1><?= WC_Dostavista_Lang::getHtml('support_page_heading') ?></h1>

        <a href="https://dostavista.ru/woocommerce-setup-guide?utm_source=readymade_module&utm_medium=guide&utm_campaign=woocommerce_module">
            <?= WC_Dostavista_Lang::getHtml('support_page_link_install_instruction') ?>
        </a>
        <br>

        <a href="https://dostavista.ru/woocommerce-manual?utm_source=readymade_module&utm_medium=guide&utm_campaign=woocommerce_module">
            <?= WC_Dostavista_Lang::getHtml('support_page_link_settings_instruction') ?>
        </a>
        <br>

        <a href="mailto:api@dostavista.ru">
            <?= WC_Dostavista_Lang::getHtml('support_page_link_callback') ?>
        </a>
        <br>
        <?php
    }
}
