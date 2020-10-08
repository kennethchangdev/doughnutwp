<?php

use WooDostavista\AppOrderForm\AppOrderForm;
use WooDostavista\BackpaymentDetails\BackpaymentDetail;
use WooDostavista\DostavistaOrders\DostavistaOrder;
use WooDostavista\ModuleSettings\ModuleSettings;
use WooDostavista\Warehouses\Warehouse;
use WooDostavista\DvCmsModuleApiClient\Enums\PaymentMethodEnum;

/**
 * @var ModuleSettings $settings
 * @var AppOrderForm $orderForm
 * @var Warehouse[] $warehouses
 * @var Warehouse|null $defaultWarehouse
 * @var BackpaymentDetail[] $backpaymentDetails
 * @var BackpaymentDetail|null $defaultBackpaymentDetail
 * @var DostavistaOrder[] $dostavistaOrders
 * @var array $timeEnum
 * @var array $dateEnum
 * @var array $vehicleTypeEnum
 * @var array $paymentTypes
 * @var array $paymentBankCards
 */

wp_enqueue_script('jquery');
wp_enqueue_script('woo_dostavista_order-form', plugin_dir_url(WC_DOSTAVISTA_PLUGIN_FILE) . '/assets/js/order-form.js', ['jquery']);
wp_enqueue_script('woo_dostavista_licode_preloader', plugin_dir_url(WC_DOSTAVISTA_PLUGIN_FILE) . '/assets/js/licode.preloader.js', ['jquery']);

?>
<h1 class="woo-dostavista-heading"><?= WC_Dostavista_Lang::getHtml('order_form_create_heading') ?></h1>

<div id="woo-dostavista-order-form">
    <div class="summary-block">
        <table class="form-table">
            <tr>
                <td><label><?= WC_Dostavista_Lang::getHtml('order_form_summary_delivery_fee') ?>:</label></td>
                <td>
                    <strong data-calculation="delivery_fee_amount" class="mr-1">0</strong>&nbsp;<?= WC_Dostavista_Lang::getHtml('order_form_rub') ?>
                </td>
            </tr>
            <tr>
                <td><label><?= WC_Dostavista_Lang::getHtml('order_form_summary_weight_fee') ?>:</label></td>
                <td>
                    <strong data-calculation="weight_fee_amount" class="mr-1">0</strong>&nbsp;<?= WC_Dostavista_Lang::getHtml('order_form_rub') ?>
                </td>
            </tr>
            <tr>
                <td><label><?= WC_Dostavista_Lang::getHtml('order_form_summary_insurance_fee') ?>:</label></td>
                <td>
                    <strong data-calculation="insurance_fee_amount" class="mr-1">0</strong>&nbsp;<?= WC_Dostavista_Lang::getHtml('order_form_rub') ?>
                </td>
            </tr>
            <tr>
                <td><label><?= WC_Dostavista_Lang::getHtml('order_form_summary_money_transfer_fee') ?>:</label></td>
                <td>
                    <strong data-calculation="money_transfer_fee_amount" class="mr-1">0</strong>&nbsp;<?= WC_Dostavista_Lang::getHtml('order_form_rub') ?>
                </td>
            </tr>
            <tr>
                <td><label><?= WC_Dostavista_Lang::getHtml('order_form_summary_loading_fee') ?>:</label></td>
                <td>
                    <strong data-calculation="loading_fee_amount" class="mr-1">0</strong>&nbsp;<?= WC_Dostavista_Lang::getHtml('order_form_rub') ?>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <hr class="total">
                </td>
            </tr>
            <tr>
                <td><label><?= WC_Dostavista_Lang::getHtml('order_form_summary_payment_amount') ?>:</label></td>
                <td style="background-color: #fffa65">
                    <strong data-calculation="payment_amount" class="mr-1">0</strong>&nbsp;<?= WC_Dostavista_Lang::getHtml('order_form_rub') ?>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="errors" style="display: none;">

                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="content-container">
        <?php if ($dostavistaOrders) { ?>
            <div class="woo-dostavista-warning-block">
                <div><?= WC_Dostavista_Lang::getHtml('order_form_orders_history_text') ?>:</div>
                <?php foreach ($dostavistaOrders as $dostavistaOrder) { ?>
                    <div>&mdash; <?= WC_Dostavista_Lang::getHtml('order_form') ?> <strong><?= _wp_specialchars($dostavistaOrder->dostavistaOrderId) ?></strong> <?= date('d.m.Y H:i:s', strtotime($dostavistaOrder->createdDatetime)) ?></div>
                <?php } ?>
            </div>
        <?php } ?>

        <form action="" method="post" class="woo-dostavista-form">
            <h2 class="form-section"><?= WC_Dostavista_Lang::getHtml('order_form_general_title') ?></h2>
            <table class="form-table">
                <tr>
                    <td><label><?= WC_Dostavista_Lang::getHtml('order_form_vehicle_type') ?>:</label></td>
                    <td>
                        <select name="vehicle_type_id">
                            <?php foreach ($vehicleTypeEnum as $value => $title) { ?>
                                <option value="<?= _wp_specialchars($value) ?>" <?= $value === $settings->getDefaultVehicleTypeId() ? 'selected' : '' ?>><?= _wp_specialchars($title) ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label><?= WC_Dostavista_Lang::getHtml('order_form_default_payment_type') ?>:</label></td>
                    <td>
                        <select autocomplete="off" name="payment_type">
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
                    <td><label><?= WC_Dostavista_Lang::getHtml('payment_type_card') ?>:</label></td>
                    <td>
                        <select autocomplete="off" name="bank_card_id">
                            <?php foreach ($paymentBankCards as $paymentCardId => $title) { ?>
                                <option value="<?= _wp_specialchars($paymentCardId) ?>" <?= $paymentCardId === $settings->getDefaultPaymentCardId() ? 'selected' : '' ?>><?= _wp_specialchars($title) ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label><?= WC_Dostavista_Lang::getHtml('order_form_matter') ?>:</label></td>
                    <td>
                        <input type="text" name="matter" value="<?= _wp_specialchars($orderForm->getMatterWithFeatures()) ?>">
                    </td>
                </tr>
                <tr>
                    <td><label><?= WC_Dostavista_Lang::getHtml('order_form_total_weight_kg') ?>:</label></td>
                    <td>
                        <input type="text" name="total_weight_kg" value="<?= _wp_specialchars($orderForm->getItemsTotalWeightKg()) ?>">
                    </td>
                </tr>
                <tr>
                    <td><label><?= WC_Dostavista_Lang::getHtml('order_form_loaders_count') ?>:</label></td>
                    <td>
                        <input type="text" name="loaders_count" value="0">
                    </td>
                </tr>
                <tr>
                    <td><label><?= WC_Dostavista_Lang::getHtml('order_form_insurance') ?>:</label></td>
                    <td>
                        <input type="text" name="insurance_amount" value="<?= _wp_specialchars($orderForm->getInsuranceAmount()) ?>">
                    </td>
                </tr>
                <tr>
                    <td><label><?= WC_Dostavista_Lang::getHtml('order_form_select_details') ?>:</label></td>
                    <td>
                        <select name="backpayment_detail_id">
                            <option value=""><?= WC_Dostavista_Lang::getHtml('select_option_any') ?></option>
                            <?php foreach ($backpaymentDetails as $backpaymentDetail) { ?>
                                <option value="<?= _wp_specialchars($backpaymentDetail->id) ?>" data-description="<?= _wp_specialchars($backpaymentDetail->description) ?>" <?= $backpaymentDetail->id === $orderForm->getBackpaymentDetailId() ? 'selected' : '' ?>><?= _wp_specialchars(mb_strimwidth($backpaymentDetail->description, 0, 60, '...')) ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label><?= WC_Dostavista_Lang::getHtml('order_form_backpayment_details') ?>:</label></td>
                    <td>
                        <textarea name="backpayment_details" rows="4"><?= _wp_specialchars($backpaymentDetails[$orderForm->getBackpaymentDetailId()]->description ?? '') ?></textarea>
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

            <h2 class="form-section"><?= WC_Dostavista_Lang::getHtml('order_form_pickup_title') ?></h2>
            <table class="form-table">
                <tr>
                    <td><label><?= WC_Dostavista_Lang::getHtml('order_form_pickup_warehouse_select') ?>:</label></td>
                    <td>
                        <select id="pickup-warehouse-select">
                            <option value=""></option>
                            <?php foreach ($warehouses as $warehouse) { ?>
                                <option
                                        value="<?= _wp_specialchars($warehouse->id) ?>" <?= $warehouse->id === $settings->getDefaultPickupWarehouseId() ? 'selected' : '' ?>
                                        data-address="<?= _wp_specialchars($warehouse->getFullAddress()) ?>"
                                        data-work-start-time="<?= _wp_specialchars($warehouse->workStartTime) ?>"
                                        data-work-finish-time="<?= _wp_specialchars($warehouse->workFinishTime) ?>"
                                        data-contact-name="<?= _wp_specialchars($warehouse->contactName) ?>"
                                        data-contact-phone="<?= _wp_specialchars($warehouse->contactPhone) ?>"
                                        data-note="<?= _wp_specialchars($warehouse->note) ?>"
                                ><?= _wp_specialchars($warehouse->name) ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label><?= WC_Dostavista_Lang::getHtml('order_form_pickup_address') ?>:</label></td>
                    <td>
                        <input type="text" name="pickup_address" value="<?= _wp_specialchars($defaultWarehouse->getFullAddress()) ?>">
                    </td>
                </tr>
                <tr>
                    <td><label><?= WC_Dostavista_Lang::getHtml('order_form_pickup_required_date') ?>:</label></td>
                    <td>
                        <select name="pickup_required_date">
                            <?php foreach ($dateEnum as $value => $title) { ?>
                                <?php
                                if (strtotime($orderForm->getPickupRequiredDate()) > strtotime($value)) {
                                    continue;
                                }
                                ?>
                                <option value="<?= _wp_specialchars($value) ?>" <?= $value === $orderForm->getPickupRequiredDate() ? 'selected' : '' ?> <?= $value === date('d.m.Y') ? 'data-today="1"' : ''?>><?= _wp_specialchars($title) ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label><?= WC_Dostavista_Lang::getHtml('order_form_time_from') ?>:</label></td>
                    <td>
                        <select name="pickup_required_start_time" data-min-today-time="<?= _wp_specialchars($orderForm->getPickupRequiredStartTime()) ?>">
                            <?php foreach ($timeEnum as $value => $title) { ?>
                                <option value="<?= _wp_specialchars($value) ?>" <?= $value === $orderForm->getPickupRequiredStartTime() ? 'selected' : '' ?>><?= _wp_specialchars($title) ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label><?= WC_Dostavista_Lang::getHtml('order_form_time_to') ?>:</label></td>
                    <td>
                        <select name="pickup_required_finish_time">
                            <?php foreach ($timeEnum as $value => $title) { ?>
                                <option value="<?= _wp_specialchars($value) ?>" <?= $value === $defaultWarehouse->workFinishTime ? 'selected' : '' ?>><?= _wp_specialchars($title) ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label><?= WC_Dostavista_Lang::getHtml('order_form_pickup_contact_name') ?>:</label></td>
                    <td>
                        <input type="text" name="pickup_contact_name" value="<?= _wp_specialchars($defaultWarehouse->contactName) ?>">
                    </td>
                </tr>
                <tr>
                    <td><label><?= WC_Dostavista_Lang::getHtml('order_form_pickup_contact_phone') ?>:</label></td>
                    <td>
                        <input type="text" name="pickup_contact_phone" value="<?= _wp_specialchars($defaultWarehouse->contactPhone) ?>">
                    </td>
                </tr>
                <tr>
                    <td><label><?= WC_Dostavista_Lang::getHtml('order_form_pickup_buyout') ?>:</label></td>
                    <td>
                        <input type="text" name="pickup_buyout_amount" value="<?= _wp_specialchars($orderForm->getPickupBuyoutAmount()) ?>">
                    </td>
                </tr>
                <tr>
                    <td><label><?= WC_Dostavista_Lang::getHtml('order_form_pickup_note') ?>:</label></td>
                    <td>
                        <textarea name="pickup_note" rows="4"><?= _wp_specialchars($defaultWarehouse->note) ?></textarea>
                    </td>
                </tr>
            </table>

            <div class="add-delivery-point">+&nbsp;<?= WC_Dostavista_Lang::getHtml('order_form_add_delivery_point') ?></div>

            <?php foreach ($orderForm->getWooOrders() as $pointIndex => $wooOrder) { ?>
            <div class="point-form" data-point-index="<?= _wp_specialchars($pointIndex) ?>">
                <h2 class="form-section">
                    <span><?= WC_Dostavista_Lang::getHtml('order_form_delivery_point_title') ?> <span><?= $pointIndex + 1 ?></span></span>
                    <div class="point-actions">
                        <span class="point-up"><img src="<?= plugin_dir_url(WC_DOSTAVISTA_PLUGIN_FILE) . '' ?>/assets/img/icon-circle-arrow-top.png" alt=""></span>
                        <span class="point-down"><img src="<?= plugin_dir_url(WC_DOSTAVISTA_PLUGIN_FILE) . '' ?>/assets/img/icon-circle-arrow-bottom.png" alt=""></span>
                        <span class="point-remove"><img src="<?= plugin_dir_url(WC_DOSTAVISTA_PLUGIN_FILE) . '' ?>/assets/img/icon-circle-x.png" alt=""></span>
                    </div>
                </h2>
                <input type="hidden" name="woo_order_id_<?= $pointIndex ?>" value="<?= _wp_specialchars($wooOrder->getId()) ?>">
                <input type="hidden" name="point_type_<?= $pointIndex ?>" value="plain">

                <table class="form-table">
                    <tr>
                        <td><label><?= WC_Dostavista_Lang::getHtml('order_form_plain_point_warehouse_select') ?>:</label></td>
                        <td>
                            <select class="delivery-warehouse-select">
                                <option value=""></option>
                                <?php foreach ($warehouses as $warehouse) { ?>
                                    <option
                                            value="<?= _wp_specialchars($warehouse->id) ?>" <?= $warehouse->id === $settings->getDefaultPickupWarehouseId() ? 'selected' : '' ?>
                                            data-address="<?= _wp_specialchars($warehouse->getFullAddress()) ?>"
                                            data-work-start-time="<?= _wp_specialchars($warehouse->workStartTime) ?>"
                                            data-work-finish-time="<?= _wp_specialchars($warehouse->workFinishTime) ?>"
                                            data-contact-name="<?= _wp_specialchars($warehouse->contactName) ?>"
                                            data-contact-phone="<?= _wp_specialchars($warehouse->contactPhone) ?>"
                                            data-note="<?= _wp_specialchars($warehouse->note) ?>"
                                    ><?= _wp_specialchars($warehouse->name) ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label><?= WC_Dostavista_Lang::getHtml('order_form_plain_point_address') ?>:</label></td>
                        <td>
                            <input type="text" name="plain_point_address_<?= $pointIndex ?>" value="<?= _wp_specialchars($wooOrder->getAddressWithCityPrefix()) ?>">
                        </td>
                    </tr>
                    <tr>
                        <td><label><?= WC_Dostavista_Lang::getHtml('order_form_plain_point_required_date') ?>:</label></td>
                        <td>
                            <select name="plain_point_required_date_<?= $pointIndex ?>">
                                <?php foreach ($dateEnum as $value => $title) { ?>
                                    <?php
                                    if (strtotime($orderForm->getPickupRequiredDate()) > strtotime($value)) {
                                        continue;
                                    }
                                    ?>
                                    <option value="<?= _wp_specialchars($value) ?>" <?= $value === date('d.m.Y') ? 'data-today="1"' : ''?> <?= strtotime($value) == strtotime($wooOrder->getRequiredShippingDate()) ? 'selected' : '' ?>><?= _wp_specialchars($title) ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label><?= WC_Dostavista_Lang::getHtml('order_form_plain_point_time_from') ?>:</label></td>
                        <td>
                            <select name="plain_point_required_start_time_<?= $pointIndex ?>">
                                <?php
                                foreach ($timeEnum as $value => $title) {
                                    $time = $orderForm->getDeliveryRequiredStartTime($wooOrder);
                                    ?>
                                    <option value="<?= _wp_specialchars($value) ?>" <?= ($value === $time) ? 'selected' : '' ?>><?= _wp_specialchars($title) ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label><?= WC_Dostavista_Lang::getHtml('order_form_plain_point_time_to') ?>:</label></td>
                        <td>
                            <select name="plain_point_required_finish_time_<?= $pointIndex ?>">
                                <?php
                                foreach ($timeEnum as $value => $title) {
                                    $time = $orderForm->getDeliveryRequiredFinishTime($wooOrder);
                                    ?>
                                    <option value="<?= _wp_specialchars($value) ?>" <?= ($value === $time) ? 'selected' : '' ?>><?= _wp_specialchars($title) ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label><?= WC_Dostavista_Lang::getHtml('order_form_plain_point_contact_name') ?>:</label></td>
                        <td>
                            <input type="text" name="plain_point_recipient_name_<?= $pointIndex ?>" value="<?= _wp_specialchars($wooOrder->getContactPersonName()) ?>">
                        </td>
                    </tr>
                    <tr>
                        <td><label><?= WC_Dostavista_Lang::getHtml('order_form_plain_point_contact_phone') ?>:</label></td>
                        <td>
                            <input type="text" name="plain_point_recipient_phone_<?= $pointIndex ?>" value="<?= _wp_specialchars($wooOrder->getContactPersonPhone()) ?>">
                        </td>
                    </tr>
                    <tr>
                        <td><label><?= WC_Dostavista_Lang::getHtml('order_form_plain_point_note') ?>:</label></td>
                        <td>
                            <textarea name="plain_point_note_<?= $pointIndex ?>" rows="4"><?= _wp_specialchars($orderForm->getNoteWithPrefix($wooOrder)) ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td><label><?= WC_Dostavista_Lang::getHtml('order_form_plain_point_taking') ?>:</label></td>
                        <td>
                            <input type="text" name="plain_point_taking_amount_<?= $pointIndex ?>" value="<?= _wp_specialchars($orderForm->getPlainPointTakingAmount($wooOrder)) ?>">
                        </td>
                    </tr>
                    <tr>
                        <td><label><?= WC_Dostavista_Lang::getHtml('order_form_plain_point_client_order_id') ?>:</label></td>
                        <td>
                            <input type="text" name="plain_point_client_order_id_<?= $pointIndex ?>" value="<?= _wp_specialchars($wooOrder->getId()) ?>">
                        </td>
                    </tr>
                    <tr>
                        <td><label><?= WC_Dostavista_Lang::getHtml('order_form_plain_point_delivery_price') ?>:</label></td>
                        <td>
                            <input type="text" value="<?= _wp_specialchars($wooOrder->getDeliveryPrice()) ?>" readonly>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="add-delivery-point">+&nbsp;<?= WC_Dostavista_Lang::getHtml('order_form_add_delivery_point') ?></div>
            <?php } ?>

            <input class="button button-dostavista" type="submit" value="<?= WC_Dostavista_Lang::getHtml('button_create_order_to_dv') ?>">
        </form>
    </div>
</div>

<script>
    if (typeof(woodostavista) === 'undefined') {
        woodostavista = {};
    }

    woodostavista.order = {
        parameterErrorTranslations :  {
            required: "<?= WC_Dostavista_Lang::getHtml('order_form_error_message_required') ?>",
            invalid_value: "<?= WC_Dostavista_Lang::getHtml('order_form_error_message_invalid_value') ?>",
            min_length: "<?= WC_Dostavista_Lang::getHtml('order_form_error_message_min_length') ?>",
            max_length: "<?= WC_Dostavista_Lang::getHtml('order_form_error_message_max_length') ?>",
            min_value: "<?= WC_Dostavista_Lang::getHtml('order_form_error_message_min_value') ?>",
            max_value: "<?= WC_Dostavista_Lang::getHtml('order_form_error_message_max_value') ?>",
            invalid_integer: "<?= WC_Dostavista_Lang::getHtml('order_form_error_message_invalid_integer') ?>",
            invalid_phone: "<?= WC_Dostavista_Lang::getHtml('order_form_error_message_invalid_phone') ?>",
            different_regions: "<?= WC_Dostavista_Lang::getHtml('order_form_error_message_different_regions') ?>",
            invalid_region: "<?= WC_Dostavista_Lang::getHtml('order_form_error_message_invalid_region') ?>",
            address_not_found: "<?= WC_Dostavista_Lang::getHtml('order_form_error_message_address_not_found') ?>",
            min_date: "<?= WC_Dostavista_Lang::getHtml('order_form_error_message_min_date') ?>",
            max_date: "<?= WC_Dostavista_Lang::getHtml('order_form_error_message_max_date') ?>",
            cannot_be_past: "<?= WC_Dostavista_Lang::getHtml('order_form_error_message_cannot_be_past') ?>",
            earlier_than_previous_point: "<?= WC_Dostavista_Lang::getHtml('order_form_error_message_earlier_than_previous_point') ?>",
            unexpected_error: "<?= WC_Dostavista_Lang::getHtml('order_form_error_message_unexpected_error') ?>",
            start_after_end: "<?= WC_Dostavista_Lang::getHtml('order_form_error_message_start_after_end') ?>",
        },
        errorTranslations :  {
            invalid_parameters: "<?= WC_Dostavista_Lang::getHtml('order_form_error_message_invalid_parameters') ?>",
            unapproved_contract: "<?= WC_Dostavista_Lang::getHtml('order_form_error_message_unapproved_contract') ?>",
            buyout_not_allowed: "<?= WC_Dostavista_Lang::getHtml('order_form_error_message_buyout_not_allowed') ?>",
            insufficient_balance: "<?= WC_Dostavista_Lang::getHtml('order_form_error_message_insufficient_balance') ?>",
            buyout_amount_limit_exceeded: "<?= WC_Dostavista_Lang::getHtml('order_form_error_message_buyout_amount_limit_exceeded') ?>",
            requests_limit_exceeded: "<?= WC_Dostavista_Lang::getHtml('order_form_error_message_requests_limit_exceeded') ?>",
        },
        translation : {
            calculate_error : "<?= WC_Dostavista_Lang::getHtml('order_form_error_message_calculate_error') ?>",
            create_order_error_text : "<?= WC_Dostavista_Lang::getHtml('order_form_error_message_create_order_error_text') ?>",
            field_error_text : "<?= WC_Dostavista_Lang::getHtml('order_form_error_message_field_error_text') ?>",
            success_message : "<?= WC_Dostavista_Lang::getHtml('order_form_success_message') ?>",
        }
    };
</script>
