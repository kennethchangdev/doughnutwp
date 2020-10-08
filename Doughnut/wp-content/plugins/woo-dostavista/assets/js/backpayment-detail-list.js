jQuery(function () {
    var $ = jQuery;

    var area = $('#woo-dostavista-backpayment-detail-list');

    area.on('click', 'a.backpayment-detail-delete', function (e) {
        e.preventDefault();
        var _link = this;

        if (confirm(woodostavista.backpayments.translations.remove_confirm)) {
            var table = $(_link).parents('table:first');
            licode.preloader.on(table);

            var id = $(_link).parents('tr:first').data('backpaymentDetailId');
            woodostavista.frontendHttpClient.deleteBackpaymentDetail(id, function (success, error, parameterErrors, response) {
                if (success) {
                    window.location.reload();
                } else {
                    licode.preloader.off(table);
                    alert(woodostavista.backpayments.translations.load_requisites_error);
                }
            });
        }
    });
});
