<?php

use WooDostavista\Warehouses\Warehouse;

/**
 * @var Warehouse[] $warehouses
 * @var int|null $defaultWarehouseId
 */

wp_enqueue_script('jquery');
wp_enqueue_script('woo_dostavista_warehouse_list', plugin_dir_url(WC_DOSTAVISTA_PLUGIN_FILE) . '/assets/js/warehouse-list.js', ['jquery']);
wp_enqueue_script('woo_dostavista_licode_preloader', plugin_dir_url(WC_DOSTAVISTA_PLUGIN_FILE) . '/assets/js/licode.preloader.js', ['jquery']);

?>
<h1 class="woo-dostavista-heading"><?= WC_Dostavista_Lang::getHtml('warehouse_list_page_title') ?></h1>

<p>
    <a href="<?= admin_url('admin.php?page=woo_dostavista_warehouse_edit') ?>"><?= WC_Dostavista_Lang::getHtml('warehouse_list_actions_new') ?></a>
</p>

<div id="woo-dostavista-warehouse-list">
    <table class="wp-list-table widefat fixed striped posts">
        <thead>
            <tr>
                <th><?= WC_Dostavista_Lang::getHtml('warehouse_list_name') ?></th>
                <th><?= WC_Dostavista_Lang::getHtml('warehouse_list_city') ?></th>
                <th><?= WC_Dostavista_Lang::getHtml('warehouse_list_address') ?></th>
                <th><?= WC_Dostavista_Lang::getHtml('warehouse_list_schedule') ?></th>
                <th><?= WC_Dostavista_Lang::getHtml('warehouse_list_contact_name') ?></th>
                <th><?= WC_Dostavista_Lang::getHtml('warehouse_list_contact_phone') ?></th>
                <th><?= WC_Dostavista_Lang::getHtml('warehouse_list_comment') ?></th>
                <th><?= WC_Dostavista_Lang::getHtml('warehouse_list_default') ?></th>
                <th><?= WC_Dostavista_Lang::getHtml('warehouse_list_actions') ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($warehouses as $warehouse) { ?>
            <tr data-warehouse-id="<?= _wp_specialchars($warehouse->id) ?>">
                <td>
                    <a href="<?= admin_url('admin.php?page=woo_dostavista_warehouse_edit&id=' . (int) $warehouse->id) ?>">
                        <?= _wp_specialchars($warehouse->name) ?>
                    </a>
                </td>
                <td><?= _wp_specialchars($warehouse->city) ?></td>
                <td><?= _wp_specialchars($warehouse->address) ?></td>
                <td><?= _wp_specialchars($warehouse->workStartTime . ' - ' . $warehouse->workFinishTime) ?></td>
                <td><?= _wp_specialchars($warehouse->contactName) ?></td>
                <td><?= _wp_specialchars($warehouse->contactPhone) ?></td>
                <td><?= _wp_specialchars($warehouse->note) ?></td>
                <td><?= $defaultWarehouseId === $warehouse->id ? '&check;' : '' ?></td>
                <td><a href="#" class="warehouse-delete"><?= WC_Dostavista_Lang::getHtml('warehouse_list_actions_delete') ?></a></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<script>
    if (typeof(woodostavista) === 'undefined') {
        woodostavista = {};
    }

    woodostavista.warehouse = {
        'translation' : {
            remove_confirm_message : "<?= WC_Dostavista_Lang::getHtml('warehouse_alert_remove_confirm_message') ?>",
            remove_error : "<?= WC_Dostavista_Lang::getHtml('warehouse_alert_remove_error') ?>",
        }
    };
</script>
