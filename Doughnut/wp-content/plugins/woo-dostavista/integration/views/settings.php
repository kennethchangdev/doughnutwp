<?php

use WooDostavista\BackpaymentDetails\BackpaymentDetail;
use WooDostavista\ModuleSettings\ModuleSettings;
use WooDostavista\Warehouses\Warehouse;
use WooDostavista\ModuleConfig\ModuleConfig;
use WooDostavista\DvCmsModuleApiClient\Enums\PaymentMethodEnum;

/**
 * @var ModuleSettings $settings
 * @var Warehouse[] $warehouses
 * @var BackpaymentDetail[] $backpaymentDetails
 * @var array $vehicleTypeEnum
 * @var array $wooOrderStatusEnum
 * @var array $wooPaymentMethodEnum
 * @var ModuleConfig $moduleConfig
 * @var array $paymentTypes
 * @var bool $isNewCardLinkEnabled
 * @var array $paymentBankCards
 */

wp_enqueue_script('jquery');
wp_enqueue_script('woo_dostavista_settings', plugin_dir_url(WC_DOSTAVISTA_PLUGIN_FILE) . '/assets/js/settings.js', ['jquery']);
wp_enqueue_script('woo_dostavista_licode_preloader', plugin_dir_url(WC_DOSTAVISTA_PLUGIN_FILE) . '/assets/js/licode.preloader.js', ['jquery']);

?>
<h1 class="woo-dostavista-heading"><?= WC_Dostavista_Lang::getHtml('settings_heading') ?></h1>

<div id="woo-dostavista-settings">
    <form action="" method="post" class="woo-dostavista-form">
        <div style="display: none;">
            <h2 class="form-section">Dostavista CMS MODULE API</h2>
            <table class="form-table">
                <tr>
                    <td><label>Auth-Token:</label></td>
                    <td>
                        <input
                                autocomplete="off"
                                data-token-test="<?= _wp_specialchars($settings->getTestAuthToken()) ?>"
                                data-token-prod="<?= _wp_specialchars($settings->getProdAuthToken()) ?>"
                                type="text"
                                name="cms_module_api_auth_token"
                                value="<?= _wp_specialchars($settings->getAuthToken()) ?>"
                        >
                    </td>
                </tr>
                <tr>
                    <td><label><?= WC_Dostavista_Lang::getHtml('shop_account_dostavista_cms_module_api_server') ?>:</label></td>
                    <td>
                        <select autocomplete="off" name="dostavista_is_api_test_server">
                            <option value="1" <?= $settings->getIsApiTest() ? 'selected' : ""; ?>>
                                <?= WC_Dostavista_Lang::getHtml('shop_account_dostavista_cms_module_api_server_test') ?>
                            </option>
                            <option value="0" <?= !$settings->getIsApiTest() ? 'selected' : ""; ?>>
                                <?= WC_Dostavista_Lang::getHtml('shop_account_dostavista_cms_module_api_server_production') ?>
                            </option>
                        </select>
                    </td>
                </tr>
            </table>
        </div>
        <h2 class="form-section"><?= WC_Dostavista_Lang::getHtml('shop_account_pickup_point_title') ?></h2>
        <table class="form-table">
            <tr>
                <td><label><?= WC_Dostavista_Lang::getHtml('shop_account_default_warehouse_select') ?>:</label></td>
                <td>
                    <select name="default_pickup_warehouse_id">
                        <option value=""><?= WC_Dostavista_Lang::getHtml('select_option_any') ?></option>
                        <?php foreach ($warehouses as $warehouse) { ?>
                            <option value="<?= _wp_specialchars($warehouse->id) ?>" <?= $warehouse->id === $settings->getDefaultPickupWarehouseId() ? 'selected' : '' ?>><?= _wp_specialchars($warehouse->name) ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <a href="<?= admin_url('admin.php?page=woo_dostavista_warehouses') ?>"><?= WC_Dostavista_Lang::getHtml('shop_account_warehouses') ?></a>
                </td>
            </tr>
        </table>

        <h2 class="form-section"><?= WC_Dostavista_Lang::getHtml('shop_account_order_general_title') ?></h2>
        <table class="form-table">
            <tr>
                <td><label><?= WC_Dostavista_Lang::getHtml('shop_account_order_vehicle_type') ?>:</label></td>
                <td>
                    <select name="default_vehicle_type_id">
                        <?php foreach ($vehicleTypeEnum as $id => $title) { ?>
                            <option value="<?= _wp_specialchars($id) ?>" <?= $id === $settings->getDefaultVehicleTypeId() ? 'selected' : '' ?>><?= _wp_specialchars($title) ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label><?= WC_Dostavista_Lang::getHtml('shop_account_order_default_payment_type') ?>:</label></td>
                <td>
                    <select autocomplete="off" name="default_payment_type">
                        <?php foreach ($paymentTypes as $paymentTypeId => $title) { ?>
                            <option
                                data-is-card="<?= in_array($paymentTypeId, [PaymentMethodEnum::PAYMENT_METHOD_BANK, PaymentMethodEnum::PAYMENT_METHOD_QIWI]) ?>"
                                value="<?= _wp_specialchars($paymentTypeId) ?>"
                                <?= $paymentTypeId === $settings->getDefaultPaymentType() ? 'selected' : '' ?>
                            >
                                <?= _wp_specialchars($title) ?>
                            </option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label><?= WC_Dostavista_Lang::getHtml('shop_account_order_default_card') ?>:</label></td>
                <td>
                    <select autocomplete="off" name="default_payment_card_id">
                        <?php foreach ($paymentBankCards as $paymentBankCardId => $title) { ?>
                            <option value="<?= _wp_specialchars($paymentBankCardId) ?>" <?= $paymentBankCardId === $settings->getDefaultPaymentCardId() ? 'selected' : '' ?>><?= _wp_specialchars($title) ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr class="<?= $isNewCardLinkEnabled ? '' : 'hidden' ?>">
                <td></td>
                <td>
                    <a target="_blank" href="<?= WC_Dostavista_Lang::getHtml('dv_main_host') ?>/cabinet/settings">
                        <?= WC_Dostavista_Lang::getHtml('shop_account_order_add_new_card') ?>
                    </a>
                </td>
            </tr>
            <tr>
                <td><label><?= WC_Dostavista_Lang::getHtml('shop_account_order_default_matter') ?>:</label></td>
                <td>
                    <input type="text" name="default_matter" value="<?= _wp_specialchars($settings->getDefaultMatter()) ?>">
                    <div class="input-note">
                        <?= WC_Dostavista_Lang::getHtml('shop_account_order_default_matter_text') ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <label>
                        <?= WC_Dostavista_Lang::getHtml('shop_account_order_default_order_weight_kg') ?>:
                    </label>
                </td>
                <td>
                    <input type="text" name="default_order_weight_kg" value="<?= _wp_specialchars($settings->getDefaultOrderWeightKg()) ?>">
                    <div class="input-note">
                        <?= WC_Dostavista_Lang::getHtml('shop_account_order_default_order_weight_kg_text') ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td><label><?= WC_Dostavista_Lang::getHtml('shop_account_order_payment_markup') ?>:</label></td>
                <td>
                    <input type="text" name="dostavista_payment_markup_amount" value="<?= _wp_specialchars($settings->getDostavistaPaymentMarkupAmount()) ?>">
                </td>
            </tr>
            <tr>
                <td><label><?= WC_Dostavista_Lang::getHtml('shop_account_order_payment_discount') ?>:</label></td>
                <td>
                    <input type="text" name="dostavista_payment_discount_amount" value="<?= _wp_specialchars($settings->getDostavistaPaymentDiscountAmount()) ?>">
                </td>
            </tr>
            <tr>
                <td><label><?= WC_Dostavista_Lang::getHtml('shop_account_fix_order_payment') ?>:</label></td>
                <td>
                    <input type="text" name="fix_order_payment_amount" value="<?= _wp_specialchars($settings->getFixOrderPaymentAmount()) ?>">
                    <div class="input-note">
                        <?= WC_Dostavista_Lang::getHtml('shop_account_fix_order_payment_text') ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td><label> <?= WC_Dostavista_Lang::getHtml('shop_account_free_delivery_order_sum') ?>:</label></td>
                <td>
                    <input type="text" name="free_delivery_woocommerce_order_sum" value="<?= _wp_specialchars($settings->getFreeDeliveryWooCommerceOrderSum()) ?>">
                </td>
            </tr>
            <tr>
                <td><label><?= WC_Dostavista_Lang::getHtml('order_processing_time_hours') ?>:</label></td>
                <td>
                    <input type="text" name="order_processing_time_hours" value="<?= _wp_specialchars($settings->getOrderProcessingTimeHours()) ?>">
                </td>
            </tr>
            <tr>
                <td>
                    <label>
                        <?= WC_Dostavista_Lang::getHtml('order_form_select_details') ?>:
                    </label>
                </td>
                <td>
                    <select name="default_backpayment_detail_id">
                        <option value=""><?= WC_Dostavista_Lang::getHtml('select_option_any') ?></option>
                        <?php foreach ($backpaymentDetails as $backpaymentDetail) { ?>
                            <option value="<?= _wp_specialchars($backpaymentDetail->id) ?>" <?= $backpaymentDetail->id === $settings->getDefaultBackpaymentDetailId() ? 'selected' : '' ?> data-description="<?= _wp_specialchars($backpaymentDetail->description) ?>"><?= _wp_specialchars(mb_strimwidth($backpaymentDetail->description, 0, 60, '...')) ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label><?= WC_Dostavista_Lang::getHtml('shop_account_default_backpayment_details') ?>:</label></td>
                <td>
                    <textarea name="default_backpayment_details" rows="4" readonly=""><?= _wp_specialchars($backpaymentDetails[$settings->getDefaultBackpaymentDetailId()]->description ?? '') ?></textarea>
                </td>
            </tr>
            <tr>
                <td><label><?= WC_Dostavista_Lang::getHtml('shop_account_order_delivery_point_note_prefix') ?>:</label></td>
                <td>
                    <textarea name="delivery_point_note_prefix" rows="4"><?= _wp_specialchars($settings->getDeliveryPointNotePrefix()) ?></textarea>
                </td>
            </tr>

            <tr>
                <td></td>
                <td>
                    <input type="checkbox" name="is_insurance_enabled" id="general-insurance-field" <?= $settings->isInsuranceEnabled() ? 'checked' : '' ?>>
                    <label for="general-insurance-field"><?= WC_Dostavista_Lang::getHtml('shop_account_order_insurance_enabled') ?></label>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <input type="checkbox" name="is_buyout_enabled" id="general-buyout-field" <?= $settings->isBuyoutEnabled() ? 'checked' : '' ?>>
                    <label for="general-buyout-field"><?= WC_Dostavista_Lang::getHtml('shop_account_order_buyout_enabled') ?></label>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <input type="checkbox" name="is_matter_weight_prefix_enabled" id="general-weight-prefix-field" <?= $settings->isMatterWeightPrefixEnabled() ? 'checked' : '' ?>>
                    <label for="general-weight-prefix-field"><?= WC_Dostavista_Lang::getHtml('shop_account_matter_weight_prefix_enabled') ?></label>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <input type="checkbox" name="is_contact_person_notification_enabled" id="general-contact-person-notification-field" <?= $settings->isContactPersonNotificationEnabled() ? 'checked' : '' ?>>
                    <label for="general-contact-person-notification-field"><?= WC_Dostavista_Lang::getHtml('order_form_contact_person_notification') ?></label>
                </td>
            </tr>
        </table>

        <h2 class="form-section"><?= WC_Dostavista_Lang::getHtml('shop_account_order_woo_fields_attach') ?></h2>
        <table class="form-table">
            <tr>
                <td><label><?= WC_Dostavista_Lang::getHtml('shop_account_order_payment_type') ?>:</label></td>
                <td>
                    <select name="cash_payment_method_code">
                        <option value=""><?= WC_Dostavista_Lang::getHtml('select_option_any') ?></option>
                        <?php foreach ($wooPaymentMethodEnum as $value => $title) { ?>
                            <option value="<?= _wp_specialchars($value) ?>" <?= $value === $settings->getCashPaymentMethodCode() ? 'selected' : ''?>><?= _wp_specialchars($title) ?></option>
                        <?php } ?>
                    </select>
                    <div class="input-note">
                        <?= WC_Dostavista_Lang::getHtml('shop_account_payment_type_required') ?>
                    </div>
                </td>
            </tr>
        </table>

        <h2 class="form-section"><?= WC_Dostavista_Lang::getHtml('shop_account_order_data_sync_title') ?></h2>
        <table class="form-table">
            <tr style="display: none;">
                <td><label>API Callback secret key:</label></td>
                <td>
                    <input type="text" name="dostavista_cms_module_api_callback_secret" value="<?= _wp_specialchars($settings->getApiCallbackSecretKey()) ?>">
                </td>
            </tr>
            <tr style="display: none;">
                <td><label>API Callback URL:</label></td>
                <td>
                    <input type="text" name="" value="<?= admin_url('admin-post.php?action=woo_dostavista/api_callback_handler') ?>" readonly>
                    <div class="input-note">
                    </div>
                </td>
            </tr>
            <tr>
                <td><label><?= WC_Dostavista_Lang::getHtml('shop_account_order_status_draft') ?>:</label></td>
                <td>
                    <select name="woocommerce_order_status_draft">
                        <option value=""><?= WC_Dostavista_Lang::getHtml('select_option_any') ?></option>
                        <?php foreach ($wooOrderStatusEnum as $value => $title) { ?>
                            <option value="<?= _wp_specialchars($value) ?>" <?= $value === $settings->getWooCommerceOrderStatusDraft() ? 'selected' : ''?>><?= _wp_specialchars($title) ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label><?= WC_Dostavista_Lang::getHtml('shop_account_order_status_courier_search') ?>:</label></td>
                <td>
                    <select name="woocommerce_order_status_available">
                        <option value=""><?= WC_Dostavista_Lang::getHtml('select_option_any') ?></option>
                        <?php foreach ($wooOrderStatusEnum as $value => $title) { ?>
                            <option value="<?= _wp_specialchars($value) ?>" <?= $value === $settings->getWooCommerceOrderStatusAvailable() ? 'selected' : ''?>><?= _wp_specialchars($title) ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label><?= WC_Dostavista_Lang::getHtml('shop_account_order_status_courier_assigned') ?>:</label></td>
                <td>
                    <select name="woocommerce_order_status_courier_assigned">
                        <option value=""><?= WC_Dostavista_Lang::getHtml('select_option_any') ?></option>
                        <?php foreach ($wooOrderStatusEnum as $value => $title) { ?>
                            <option value="<?= _wp_specialchars($value) ?>" <?= $value === $settings->getWooCommerceOrderStatusCourierAssigned() ? 'selected' : ''?>><?= _wp_specialchars($title) ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>

            <tr>
                <td><label><?= WC_Dostavista_Lang::getHtml('shop_account_order_status_courier_picks') ?>:</label></td>
                <td>
                    <select name="woocommerce_order_status_active">
                        <option value=""><?= WC_Dostavista_Lang::getHtml('select_option_any') ?></option>
                        <?php foreach ($wooOrderStatusEnum as $value => $title) { ?>
                            <option value="<?= _wp_specialchars($value) ?>" <?= $value === $settings->getWooCommerceOrderStatusActive() ? 'selected' : ''?>><?= _wp_specialchars($title) ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label><?= WC_Dostavista_Lang::getHtml('shop_account_order_status_courier_picked') ?>:</label></td>
                <td>
                    <select name="woocommerce_order_status_parcel_picked_up">
                        <option value=""><?= WC_Dostavista_Lang::getHtml('select_option_any') ?></option>
                        <?php foreach ($wooOrderStatusEnum as $value => $title) { ?>
                            <option value="<?= _wp_specialchars($value) ?>" <?= $value === $settings->getWooCommerceOrderStatusParcelPickedUp() ? 'selected' : ''?>><?= _wp_specialchars($title) ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label><?= WC_Dostavista_Lang::getHtml('shop_account_order_status_courier_goes') ?>:</label></td>
                <td>
                    <select name="woocommerce_order_status_courier_departed">
                        <option value=""><?= WC_Dostavista_Lang::getHtml('select_option_any') ?></option>
                        <?php foreach ($wooOrderStatusEnum as $value => $title) { ?>
                            <option value="<?= _wp_specialchars($value) ?>" <?= $value === $settings->getWooCommerceOrderStatusCourierDeparted() ? 'selected' : ''?>><?= _wp_specialchars($title) ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label><?= WC_Dostavista_Lang::getHtml('shop_account_order_status_courier_waits') ?>:</label></td>
                <td>
                    <select name="woocommerce_order_status_courier_arrived">
                        <option value=""><?= WC_Dostavista_Lang::getHtml('select_option_any') ?></option>
                        <?php foreach ($wooOrderStatusEnum as $value => $title) { ?>
                            <option value="<?= _wp_specialchars($value) ?>" <?= $value === $settings->getWooCommerceOrderStatusCourierArrived() ? 'selected' : ''?>><?= _wp_specialchars($title) ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label><?= WC_Dostavista_Lang::getHtml('shop_account_order_status_delivered') ?>:</label></td>
                <td>
                    <select name="woocommerce_order_status_completed">
                        <option value=""><?= WC_Dostavista_Lang::getHtml('select_option_any') ?></option>
                        <?php foreach ($wooOrderStatusEnum as $value => $title) { ?>
                            <option value="<?= _wp_specialchars($value) ?>" <?= $value === $settings->getWooCommerceOrderStatusCompleted() ? 'selected' : ''?>><?= _wp_specialchars($title) ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label><?= WC_Dostavista_Lang::getHtml('shop_account_order_status_delivery_failed') ?>:</label></td>
                <td>
                    <select name="woocommerce_order_status_failed">
                        <option value=""><?= WC_Dostavista_Lang::getHtml('select_option_any') ?></option>
                        <?php foreach ($wooOrderStatusEnum as $value => $title) { ?>
                            <option value="<?= _wp_specialchars($value) ?>" <?= $value === $settings->getWooCommerceOrderStatusFailed() ? 'selected' : ''?>><?= _wp_specialchars($title) ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label><?= WC_Dostavista_Lang::getHtml('shop_account_order_status_delivery_canceled') ?>:</label></td>
                <td>
                    <select name="woocommerce_order_status_canceled">
                        <option value=""><?= WC_Dostavista_Lang::getHtml('select_option_any') ?></option>
                        <?php foreach ($wooOrderStatusEnum as $value => $title) { ?>
                            <option value="<?= _wp_specialchars($value) ?>" <?= $value === $settings->getWooCommerceOrderStatusCanceled() ? 'selected' : ''?>><?= _wp_specialchars($title) ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label><?= WC_Dostavista_Lang::getHtml('shop_account_order_status_delivery_delayed') ?>:</label></td>
                <td>
                    <select name="woocommerce_order_status_delayed">
                        <option value=""><?= WC_Dostavista_Lang::getHtml('select_option_any') ?></option>
                        <?php foreach ($wooOrderStatusEnum as $value => $title) { ?>
                            <option value="<?= _wp_specialchars($value) ?>" <?= $value === $settings->getWooCommerceOrderStatusDelayed() ? 'selected' : ''?>><?= _wp_specialchars($title) ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>

                </td>
                <td>
                    <a href="#" id="dostavista-logout">
                        <button><?= WC_Dostavista_Lang::getHtml('shop_account_logout_title') ?></button>
                    </a>
                    <div class="input-note">
                        <?= WC_Dostavista_Lang::getHtml('shop_account_logout_description') ?>
                    </div>
                </td>
            </tr>
        </table>

        <input class="button-primary" type="submit" value="<?= WC_Dostavista_Lang::getHtml('shop_account_order_save_settings') ?>">
    </form>
</div>

<script>
    if (typeof(woodostavista) === 'undefined') {
        woodostavista = {};
    }

    woodostavista.settings = {
        'translations' :  {
            'settings_save_success' : "<?= WC_Dostavista_Lang::getHtml('settings_alert_success_save') ?>",
            'settings_save_error' : "<?= WC_Dostavista_Lang::getHtml('settings_alert_error_save') ?>",
        }
    };
</script>
