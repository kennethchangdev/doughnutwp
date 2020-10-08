<?php

use WooDostavista\Warehouses\Warehouse;

/**
 * @var Warehouse|null $warehouse
 * @var array $scheduleTimeEnum
 */

wp_enqueue_script('jquery');
wp_enqueue_script('woo_dostavista_warehouse_edit', plugin_dir_url(WC_DOSTAVISTA_PLUGIN_FILE) . '/assets/js/warehouse-edit.js', ['jquery']);
wp_enqueue_script('woo_dostavista_licode_preloader', plugin_dir_url(WC_DOSTAVISTA_PLUGIN_FILE) . '/assets/js/licode.preloader.js', ['jquery']);

?>
<h1 class="woo-dostavista-heading"><?= WC_Dostavista_Lang::getHtml('warehouse_page_title') ?></h1>

<?php if ($warehouse === null) { ?>
    <p><?= WC_Dostavista_Lang::getHtml('warehouse_page_title') ?></p>
    <?php exit; ?>
<?php } ?>

<div id="woo-dostavista-warehouse-edit">
    <form action="" method="post" class="woo-dostavista-form" data-id="<?= _wp_specialchars($warehouse->id) ?>">
        <table class="form-table">
            <tr>
                <td><label><?= WC_Dostavista_Lang::getHtml('warehouse_name') ?>:</label></td>
                <td>
                    <input type="text" name="name" value="<?= _wp_specialchars($warehouse->name) ?>">
                </td>
            </tr>
            <tr>
                <td><label><?= WC_Dostavista_Lang::getHtml('warehouse_city') ?>:</label></td>
                <td>
                    <input type="text" name="city" value="<?= _wp_specialchars($warehouse->city) ?>">
                </td>
            </tr>
            <tr>
                <td><label><?= WC_Dostavista_Lang::getHtml('warehouse_address') ?>:</label></td>
                <td>
                    <input type="text" name="address" value="<?= _wp_specialchars($warehouse->address) ?>">
                    <div class="input-note">
                        <?= WC_Dostavista_Lang::getHtml('warehouse_address_text') ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td><label><?= WC_Dostavista_Lang::getHtml('warehouse_start_time') ?>:</label></td>
                <td>
                    <select name="work_start_time" class="custom-select">
                        <?php foreach ($scheduleTimeEnum as $value => $title) { ?>
                            <option value="<?= _wp_specialchars($value) ?>" <?= $value === date('H:i', strtotime("{$warehouse->workStartTime}:00")) ? 'selected' : '' ?>><?= _wp_specialchars($title) ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label><?= WC_Dostavista_Lang::getHtml('warehouse_finish_time') ?>:</label></td>
                <td>
                    <select name="work_finish_time" class="custom-select">
                        <?php foreach ($scheduleTimeEnum as $value => $title) { ?>
                            <option value="<?= _wp_specialchars($value) ?>" <?= $value === date('H:i', strtotime("{$warehouse->workFinishTime}:00")) ? 'selected' : '' ?>><?= _wp_specialchars($title) ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label><?= WC_Dostavista_Lang::getHtml('warehouse_contact_name') ?>:</label></td>
                <td>
                    <input type="text" name="contact_name" value="<?= _wp_specialchars($warehouse->contactName) ?>">
                    <div class="input-note">
                        <?= WC_Dostavista_Lang::getHtml('warehouse_contact_name_text') ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td><label><?= WC_Dostavista_Lang::getHtml('warehouse_contact_phone') ?>:</label></td>
                <td>
                    <input type="text" name="contact_phone" value="<?= _wp_specialchars($warehouse->contactPhone) ?>">
                </td>
            </tr>
            <tr>
                <td><label><?= WC_Dostavista_Lang::getHtml('warehouse_note') ?>:</label></td>
                <td>
                    <textarea name="note" rows="4"><?= _wp_specialchars($warehouse->note) ?></textarea>
                    <div class="input-note">
                        <?= WC_Dostavista_Lang::getHtml('warehouse_note_text') ?>
                    </div>
                </td>
            </tr>
        </table>

        <input class="button-primary" type="submit" value="<?= WC_Dostavista_Lang::getHtml('button_save') ?>">
    </form>
</div>

<script>
    if (typeof(woodostavista) === 'undefined') {
        woodostavista = {};
    }

    woodostavista.warehouse = {
        'translation' : {
            save_error : "<?= WC_Dostavista_Lang::getHtml('warehouse_alert_save_error') ?>",
        }
    };
</script>
