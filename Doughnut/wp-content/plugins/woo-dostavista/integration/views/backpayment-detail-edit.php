<?php

use WooDostavista\BackpaymentDetails\BackpaymentDetail;

/**
 * @var BackpaymentDetail|null $backpaymentDetail
 */

wp_enqueue_script('jquery');
wp_enqueue_script('woo_dostavista_backpayment_detail_edit', plugin_dir_url(WC_DOSTAVISTA_PLUGIN_FILE) . '/assets/js/backpayment-detail-edit.js', ['jquery']);
wp_enqueue_script('woo_dostavista_licode_preloader', plugin_dir_url(WC_DOSTAVISTA_PLUGIN_FILE) . '/assets/js/licode.preloader.js', ['jquery']);

?>
<h1 class="woo-dostavista-heading"><?= WC_Dostavista_Lang::getHtml('backpayment_detail_form_edit_heading') ?></h1>

<?php if ($backpaymentDetail === null) { ?>
    <p><?= WC_Dostavista_Lang::getHtml('backpayment_detail_form_requisites_404') ?></p>
    <?php exit; ?>
<?php } ?>

<div id="woo-dostavista-backpayment-detail-edit">
    <form action="" method="post" class="woo-dostavista-form" data-id="<?= _wp_specialchars($backpaymentDetail->id) ?>">
        <table class="form-table">
            <tr>
                <td><label><?= WC_Dostavista_Lang::getHtml('backpayment_detail_form_requisites') ?>:</label></td>
                <td>
                    <textarea name="description" rows="5"><?= _wp_specialchars($backpaymentDetail->description) ?></textarea>
                </td>
            </tr>
        </table>

        <input class="button-primary" type="submit" value="<?= WC_Dostavista_Lang::getHtml('backpayment_detail_form_save') ?>">
    </form>
</div>

<script>
    if (typeof(woodostavista) === 'undefined') {
        woodostavista = {};
    }

    woodostavista.backpayments = {
        'translations' :  {
            'save_requisites_error' : "<?= WC_Dostavista_Lang::getHtml('backpayment_alert_save_requisites_error') ?>",
        }
    };
</script>
