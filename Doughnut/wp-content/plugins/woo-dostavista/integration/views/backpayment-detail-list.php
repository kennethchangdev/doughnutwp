<?php

use WooDostavista\BackpaymentDetails\BackpaymentDetail;

/**
 * @var BackpaymentDetail[] $backpaymentDetails
 * @var int|null $defaultBackpaymentDetailId
 */

wp_enqueue_script('jquery');
wp_enqueue_script('woo_dostavista_backpayment_detail_list', plugin_dir_url(WC_DOSTAVISTA_PLUGIN_FILE) . '/assets/js/backpayment-detail-list.js', ['jquery']);
wp_enqueue_script('woo_dostavista_licode_preloader', plugin_dir_url(WC_DOSTAVISTA_PLUGIN_FILE) . '/assets/js/licode.preloader.js', ['jquery']);

?>
<h1 class="woo-dostavista-heading"><?= WC_Dostavista_Lang::getHtml('backpayment_detail_list_heading') ?></h1>

<p>
    <a href="<?= admin_url('admin.php?page=woo_dostavista_backpayment_detail_edit') ?>"><?= WC_Dostavista_Lang::getHtml('backpayment_detail_list_action_add') ?></a>
</p>

<div id="woo-dostavista-backpayment-detail-list">
    <table class="wp-list-table widefat fixed striped posts">
        <thead>
            <tr>
                <th><?= WC_Dostavista_Lang::getHtml('backpayment_detail_list_requisites') ?></th>
                <th><?= WC_Dostavista_Lang::getHtml('backpayment_detail_list_default') ?></th>
                <th><?= WC_Dostavista_Lang::getHtml('backpayment_detail_list_actions') ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($backpaymentDetails as $backpaymentDetail) { ?>
            <tr data-backpayment-detail-id="<?= _wp_specialchars($backpaymentDetail->id) ?>">
                <td>
                    <a href="<?= admin_url('admin.php?page=woo_dostavista_backpayment_detail_edit&id=' . (int) $backpaymentDetail->id) ?>">
                        <?= _wp_specialchars($backpaymentDetail->description) ?>
                    </a>
                </td>
                <td><?= $defaultBackpaymentDetailId === $backpaymentDetail->id ? '&check;' : '' ?></td>
                <td><a href="#" class="backpayment-detail-delete"><?= WC_Dostavista_Lang::getHtml('backpayment_detail_list_action_delete') ?></a></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<script>
    if (typeof(woodostavista) === 'undefined') {
        woodostavista = {};
    }

    woodostavista.backpayments = {
        'translations' :  {
            'load_requisites_error' : "<?= WC_Dostavista_Lang::getHtml('backpayment_alert_load_requisites_error') ?>",
            'remove_confirm' : "<?= WC_Dostavista_Lang::getHtml('backpayment_alert_remove_confirm') ?>",
        }
    };
</script>
