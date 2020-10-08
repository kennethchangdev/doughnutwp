<?php

use WooDostavista\BackpaymentDetails\BackpaymentDetail;
use WooDostavista\ModuleSettings\ModuleSettings;
use WooDostavista\Warehouses\Warehouse;
use WooDostavista\DvCmsModuleApiClient\Enums\PaymentMethodEnum;

/**
 * @var ModuleSettings $settings
 * @var Warehouse[] $warehouses
 * @var Warehouse $defaultWarehouse
 * @var BackpaymentDetail[] $backpaymentDetails
 * @var BackpaymentDetail $defaultBackpaymentDetail
 * @var array $scheduleTimeEnum
 * @var array $vehicleTypeEnum
 * @var array $wooOrderStatusEnum
 * @var array $wooPaymentMethodEnum
 * @var bool $isStep1Success
 * @var bool $isStep2Success
 * @var array $shippingZoneEnum
 * @var array $shippingContinents
 * @var array $allowedCountries
 * @var array $paymentTypes
 * @var bool $isApiTokenValid
 * @var array $paymentBankCards
 */

wp_enqueue_script('jquery');
wp_enqueue_script('woo_dostavista_wizard', plugin_dir_url(WC_DOSTAVISTA_PLUGIN_FILE) . '/assets/js/wizard.js', ['jquery']);
wp_enqueue_script('woo_dostavista_licode_preloader', plugin_dir_url(WC_DOSTAVISTA_PLUGIN_FILE) . '/assets/js/licode.preloader.js', ['jquery']);

/** @var int $wizardStartStep Начальный шаг, с которого мы начинаем визард */
$wizardStartStep = $isApiTokenValid ? $settings->getWizardLastFinishedStep() + 1 : 1;
?>
<h1 class="woo-dostavista-heading"><?= WC_Dostavista_Lang::getHtml('wizard_heading') ?></h1>

<div id="woo-dostavista-wizard" data-start-step="<?= $wizardStartStep ?>">
    <div class="woo-dostavista-form">
        <div class="step" data-step-index="1" data-api-token="<?= _wp_specialchars($settings->getAuthToken()) ?>" data-api-url="<?= _wp_specialchars($settings->getApiUrl()) ?>">
            <h2 class="form-section"><?= WC_Dostavista_Lang::getHtml('wizard_step_1_title') ?></h2>

            <div class="woo-dostavista-warning-block" <?= $isStep1Success ? 'style="display:none"' : '' ?>>
                <?= WC_Dostavista_Lang::getHtml('wizard_step_1_warning') ?>
            </div>

            <div class="woo-dostavista-success-block" <?= !$isStep1Success ? 'style="display:none"' : '' ?>>
                <?= WC_Dostavista_Lang::getHtml('wizard_step_1_success') ?>
            </div>

            <div class="login-form" <?= $isStep1Success ? 'style="display:none"' : '' ?>>
                <table class="form-table">
                    <tr>
                        <td><label><?= WC_Dostavista_Lang::getHtml('wizard_step_1_login') ?>:</label></td>
                        <td>
                            <input type="text" name="client_login">
                            <div class="input-note">
                                <?= WC_Dostavista_Lang::getHtml('wizard_step_1_login_text') ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><label><?= WC_Dostavista_Lang::getHtml('wizard_step_1_password') ?>:</label></td>
                        <td>
                            <input type="password" name="client_password">
                        </td>
                    </tr>
                    <tr style="display: none;">
                        <td></td>
                        <td>
                            <input type="checkbox" name="is_apitest" id="is_apitest">
                            <label for="is_apitest">
                                <?= WC_Dostavista_Lang::getHtml('wizard_step_1_use_apitest_server') ?>
                            </label>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="step-actions">
                <a href="<?= WC_Dostavista_Lang::getHtml('dv_main_host') ?>/" target="_blank" class="button-primary-light nav-next-1-register" <?= $isStep1Success ? 'style="display:none"' : '' ?>>
                    <?= WC_Dostavista_Lang::getHtml('wizard_step_1_register') ?>
                </a>
                <button class="button-primary nav-next-1-reset" <?= !$isStep1Success ? 'style="display:none"' : '' ?>><?= WC_Dostavista_Lang::getHtml('wizard_step_1_new_token') ?></button>
                <button class="button-primary nav-next-1-install" <?= $isStep1Success ? 'style="display:none"' : '' ?>><?= WC_Dostavista_Lang::getHtml('wizard_step_1_new_token_next_step') ?></button>
                <button class="button-primary nav-next-1" <?= !$isStep1Success ? 'style="display:none"' : '' ?>><?= WC_Dostavista_Lang::getHtml('wizard_next_step') ?></button>
            </div>
        </div>

        <div class="step" data-step-index="2" style="display:none;" data-warehouse-id="<?= _wp_specialchars($defaultWarehouse->id) ?>" data-warehouse-name="<?= _wp_specialchars($defaultWarehouse->name) ?>">
            <h2 class="form-section"><?= WC_Dostavista_Lang::getHtml('wizard_step_3_title') ?></h2>
            <table class="form-table">
                <tr>
                    <td><label><?= WC_Dostavista_Lang::getHtml('warehouse_name') ?><span class="required">*</span>:</label></td>
                    <td>
                        <input type="text" name="name" value="<?= _wp_specialchars($defaultWarehouse->name) ?>">
                    </td>
                </tr>
                <tr>
                    <td><label><?= WC_Dostavista_Lang::getHtml('warehouse_city') ?><span class="required">*</span>:</label></td>
                    <td>
                        <input type="text" name="city" value="<?= _wp_specialchars($defaultWarehouse->city) ?>">
                    </td>
                </tr>
                <tr>
                    <td><label><?= WC_Dostavista_Lang::getHtml('warehouse_address') ?><span class="required">*</span>:</label></td>
                    <td>
                        <input type="text" name="address" value="<?= _wp_specialchars($defaultWarehouse->address) ?>">
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
                                <option value="<?= _wp_specialchars($value) ?>" <?= $value === date('H:i', strtotime("{$defaultWarehouse->workStartTime}:00")) ? 'selected' : '' ?>><?= _wp_specialchars($title) ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label><?= WC_Dostavista_Lang::getHtml('warehouse_finish_time') ?>:</label></td>
                    <td>
                        <select name="work_finish_time" class="custom-select">
                            <?php foreach ($scheduleTimeEnum as $value => $title) { ?>
                                <option value="<?= _wp_specialchars($value) ?>" <?= $value === date('H:i', strtotime("{$defaultWarehouse->workFinishTime}:00")) ? 'selected' : '' ?>><?= _wp_specialchars($title) ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label><?= WC_Dostavista_Lang::getHtml('warehouse_contact_name') ?>:</label></td>
                    <td>
                        <input type="text" name="contact_name" value="<?= _wp_specialchars($defaultWarehouse->contactName) ?>">
                        <div class="input-note">
                            <?= WC_Dostavista_Lang::getHtml('warehouse_contact_name_text') ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><label><?= WC_Dostavista_Lang::getHtml('warehouse_contact_phone') ?><span class="required">*</span>:</label></td>
                    <td>
                        <input type="text" name="contact_phone" value="<?= _wp_specialchars($defaultWarehouse->contactPhone) ?>">
                    </td>
                </tr>
                <tr>
                    <td><label><?= WC_Dostavista_Lang::getHtml('warehouse_note') ?>:</label></td>
                    <td>
                        <textarea name="note" rows="4"><?= _wp_specialchars($defaultWarehouse->note) ?></textarea>
                        <div class="input-note">
                            <?= WC_Dostavista_Lang::getHtml('warehouse_note_text') ?>
                        </div>
                    </td>
                </tr>
            </table>

            <div class="step-actions">
                <button class="button nav-back-2"><?= WC_Dostavista_Lang::getHtml('wizard_back_step') ?></button>
                <button class="button-primary nav-next-2"><?= WC_Dostavista_Lang::getHtml('wizard_next_step') ?></button>
            </div>
        </div>

        <div class="step" data-step-index="3" style="display:none;" data-backpayment-detail-id="<?= _wp_specialchars($defaultBackpaymentDetail->id) ?>">
            <h2 class="form-section"><?= WC_Dostavista_Lang::getHtml('wizard_step_4_title') ?></h2>

            <table class="form-table">
                <tr>
                    <td><label><?= WC_Dostavista_Lang::getHtml('order_form_vehicle_type') ?><span class="required">*</span>:</label></td>
                    <td>
                        <select name="default_vehicle_type_id">
                            <?php foreach ($vehicleTypeEnum as $id => $title) { ?>
                                <option value="<?= _wp_specialchars($id) ?>" <?= $id === $settings->getDefaultVehicleTypeId() ? 'selected' : '' ?>><?= _wp_specialchars($title) ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr id="payment-card-id">
                    <td>
                        <label><?= WC_Dostavista_Lang::getHtml('shop_account_order_default_payment_type') ?>:</label>
                    </td>
                    <td>
                        <select autocomplete="off" name="default_payment_type">
                            <?php foreach ($paymentTypes as $paymentTypeId => $title) { ?>
                                <option
                                    data-is-card="<?= in_array($paymentTypeId, [PaymentMethodEnum::PAYMENT_METHOD_BANK, PaymentMethodEnum::PAYMENT_METHOD_QIWI]) ?>"
                                    value="<?= _wp_specialchars($paymentTypeId) ?>"
                                    <?= $paymentTypeId === $settings->getDefaultPaymentCardId() ? 'selected' : '' ?>
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
                    <td><label><?= WC_Dostavista_Lang::getHtml('shop_account_order_default_order_weight_kg') ?><span class="required">*</span>:</label></td>
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
                    <td><label><?= WC_Dostavista_Lang::getHtml('shop_account_free_delivery_order_sum') ?>:</label></td>
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
                    <td><label><?= WC_Dostavista_Lang::getHtml('order_form_backpayment_details') ?>:</label></td>
                    <td>
                        <textarea name="default_backpayment_details" rows="4"><?= _wp_specialchars($defaultBackpaymentDetail->description) ?></textarea>
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
                        <label for="general-contact-person-notification-field"><?= WC_Dostavista_Lang::getHtml('shop_account_contact_person_notification_enabled') ?></label>
                    </td>
                </tr>
            </table>

            <div class="step-actions">
                <button class="button nav-back-3"><?= WC_Dostavista_Lang::getHtml('wizard_back_step') ?></button>
                <button class="button-primary nav-next-3"><?= WC_Dostavista_Lang::getHtml('wizard_next_step') ?></button>
            </div>
        </div>

        <div class="step" data-step-index="4" style="display:none;">
            <h2 class="form-section"><?= WC_Dostavista_Lang::getHtml('wizard_step_5_title') ?></h2>

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

            <div class="step-actions">
                <button class="button nav-back-4"><?= WC_Dostavista_Lang::getHtml('wizard_back_step') ?></button>
                <button class="button-primary nav-next-4"><?= WC_Dostavista_Lang::getHtml('wizard_next_step') ?></button>
            </div>
        </div>

        <div class="step" data-step-index="5" style="display:none;">
            <h2 class="form-section"><?= WC_Dostavista_Lang::getHtml('wizard_step_6_title') ?></h2>
            <table class="form-table">
                <tr class="hidden">
                    <td><label>API Callback secret key:</label></td>
                    <td>
                        <input type="text" name="dostavista_cms_module_api_callback_secret" value="<?= _wp_specialchars($settings->getApiCallbackSecretKey()) ?>">
                    </td>
                </tr>
                <tr  class="hidden">
                    <td><label>API Callback URL:</label></td>
                    <td>
                        <input type="text" name="" value="<?= admin_url('admin-post.php?action=woo_dostavista/api_callback_handler') ?>" readonly>
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
            </table>

            <div class="step-actions">
                <button class="button nav-back-5"><?= WC_Dostavista_Lang::getHtml('wizard_back_step') ?></button>
                <button class="button-primary nav-next-5"><?= WC_Dostavista_Lang::getHtml('wizard_next_step') ?></button>
            </div>
        </div>

        <div class="step" data-step-index="6" style="display:none;" data-success="<?= (int) $isStep2Success ?>">
            <h2 class="form-section"><?= WC_Dostavista_Lang::getHtml('wizard_step_2_title') ?></h2>

            <div class="woo-dostavista-warning-block" <?= $isStep2Success ? 'style="display:none"' : '' ?>>
                <?= WC_Dostavista_Lang::getHtml('wizard_step_2_warning') ?>
            </div>

            <div class="woo-dostavista-success-block" <?= !$isStep2Success ? 'style="display:none"' : '' ?>>
                <?= WC_Dostavista_Lang::getHtml('wizard_step_2_success') ?>
            </div>

            <div class="shipping-method-form" <?= $isStep2Success ? 'style="display:none"' : '' ?>>
                <p><?= WC_Dostavista_Lang::getHtml('wizard_step_2_set_zones') ?>:</p>
                <div class="shipping-zone-list" data-empty="<?= (int) empty($shippingZoneEnum) ?>">
                    <?php
                    if (!$shippingZoneEnum) {
                        echo WC_Dostavista_Lang::getHtml('wizard_step_2_zone_empty');
                    }
                    ?>
                    <?php foreach ($shippingZoneEnum as $zoneId => $title) { ?>
                        <div>
                            <input type="checkbox" id="shipping-zone-<?= _wp_specialchars($zoneId) ?>" value="<?= _wp_specialchars($zoneId) ?>" checked>
                            <label for="shipping-zone-<?= _wp_specialchars($zoneId) ?>"><?= _wp_specialchars($title) ?></label>
                        </div>
                    <?php } ?>
                </div>

                <a href="#" class="open-shipping-zone-form"><?= WC_Dostavista_Lang::getHtml('wizard_step_2_add_zone') ?></a>

                <div class="shipping-zone-form" style="display: none;">
                    <table class="form-table">
                        <tr>
                            <td><label><?= WC_Dostavista_Lang::getHtml('wizard_step_2_zone_name') ?>:</label></td>
                            <td><input type="text" name="name" value=""></td>
                        </tr>
                        <tr>
                            <td><label><?= WC_Dostavista_Lang::getHtml('wizard_step_2_zones') ?>:</label></td>
                            <td>
                                <select multiple="multiple" name="locations" class="chosen_select">
                                    <?php
                                    foreach ($shippingContinents as $continentCode => $continent) {
                                        ?><option value="continent:<?= esc_attr($continentCode)?>"><?= esc_html($continent['name']) ?></option><?php

                                        $countries = array_intersect(array_keys($allowedCountries), $continent['countries']);
                                        foreach ($countries as $countryCode) {
                                            ?><option value="country:<?= esc_attr($countryCode)?>"><?= esc_html('&nbsp;&nbsp; ' . $allowedCountries[$countryCode])?></option><?php

                                            $states = WC()->countries->get_states($countryCode);
                                            if ($states) {
                                                foreach ($states as $state_code => $state_name) {
                                                    ?><option value="state:<?= esc_attr($countryCode . ':' . $state_code) ?>"><?= esc_html('&nbsp;&nbsp;&nbsp;&nbsp; ' . $state_name . ', ' . $allowedCountries[$countryCode]) ?></option><?php
                                                }
                                            }
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><input class="button-primary add-shipping-zone" type="button" value="<?= WC_Dostavista_Lang::getHtml('wizard_add_shipping_zone') ?>"></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="step-actions">
                <button class="button nav-back-6"><?= WC_Dostavista_Lang::getHtml('wizard_back_step') ?></button>
                <button class="button-primary nav-next-6" ><?= WC_Dostavista_Lang::getHtml('wizard_next_step') ?></button>
                <button class="button-primary nav-next-6-install" <?= $isStep2Success ? 'style="display:none"' : '' ?>><?= WC_Dostavista_Lang::getHtml('wizard_install') ?></button>
            </div>
        </div>

        <div class="step" data-step-index="7" style="display:none;">
            <div class="woo-dostavista-success-block">
                <?= WC_Dostavista_Lang::getHtml('wizard_module_configured') ?>
            </div>

            <div class="step-actions">
                <button class="button nav-back-7"><?= WC_Dostavista_Lang::getHtml('wizard_back_step') ?></button>
                <a id="btn-orders-page" href="<?= _wp_specialchars(admin_url('edit.php?post_type=shop_order')) ?>" class="button button-dostavista">
                    <?= WC_Dostavista_Lang::getHtml('orders_page_title') ?>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    if (typeof(woodostavista) === 'undefined') {
        woodostavista = {};
    }

    woodostavista.wizard = {
        'translations' :  {
            'set_zones_required' : "<?= WC_Dostavista_Lang::getHtml('wizard_alert_set_zones_required') ?>",
            'set_zone_error' : "<?= WC_Dostavista_Lang::getHtml('wizard_alert_set_zone_error') ?>",
            'fields_required' : "<?= WC_Dostavista_Lang::getHtml('wizard_alert_fields_required') ?>",
            'save_warehouse_error' : "<?= WC_Dostavista_Lang::getHtml('wizard_alert_save_warehouse_error') ?>",
            'default_order_weight_integer' : "<?= WC_Dostavista_Lang::getHtml('wizard_alert_default_order_weight_integer') ?>",
            'save_settings_error' : "<?= WC_Dostavista_Lang::getHtml('wizard_alert_save_settings_error') ?>",
            'create_zone_error' : "<?= WC_Dostavista_Lang::getHtml('wizard_alert_create_zone_error') ?>",
            'client_404' : "<?= WC_Dostavista_Lang::getHtml('wizard_alert_client_404') ?>",
            'name_and_zone_required' : "<?= WC_Dostavista_Lang::getHtml('wizard_alert_name_and_zone_required') ?>",
            'payment_type_cash' : "<?= WC_Dostavista_Lang::getHtml('payment_type_cash') ?>",
            'payment_type_card' : "<?= WC_Dostavista_Lang::getHtml('payment_type_card') ?>",
        }
    };
</script>
