jQuery(function () {
    var $ = jQuery;

    var requiredDateInput = $(':input[name="dostavista_shipping_required_date"]');
    var requiredStartTimeInput = $(':input[name="dostavista_shipping_required_start_time"]');
    var requiredFinishTimeInput = $(':input[name="dostavista_shipping_required_finish_time"]');

    requiredDateInput.change(recalculate);
    requiredStartTimeInput.change(recalculate);
    requiredFinishTimeInput.change(recalculate);

    function recalculate() {
        $(document.body).trigger('update_checkout');
    }
});
