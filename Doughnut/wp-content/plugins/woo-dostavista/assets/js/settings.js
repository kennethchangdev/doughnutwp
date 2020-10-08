jQuery(function () {
    var $ = jQuery;

    var form = $('#woo-dostavista-settings form:first');
    form.submit(function (e) {
        e.preventDefault();

        licode.preloader.on(form);

        var data = {};
        form.find(':input').each(function () {
            var jInput = $(this);
            var name = jInput.attr('name');
            if (typeof (name) !== 'undefined') {
                data[name] = jInput.is(':checkbox') ? jInput.prop('checked') : jInput.val();
            }
        });

        woodostavista.frontendHttpClient.storeSettings(data, function(success, error, parameterErrors, response) {
            licode.preloader.off(form);

            if (success) {
                alert(woodostavista.settings.translations.settings_save_success);
            } else {
                alert(woodostavista.settings.translations.settings_save_error);
            }
        });
    });

    // Логика поля Фиксированная стоимость оплаты
    $(function () {
        var orderPaymentMarkupField   = form.find('input[name="dostavista_payment_markup_amount"]');
        var orderPaymentDiscountField = form.find('input[name="dostavista_payment_discount_amount"]');
        var fixOrderPaymentField      = form.find('input[name="fix_order_payment_amount"]');

        function fixOrderPaymentProcessor() {
            orderPaymentMarkupField.prop('disabled', fixOrderPaymentField.val().trim() > 0);
            orderPaymentDiscountField.prop('disabled', fixOrderPaymentField.val().trim() > 0);
        }

        fixOrderPaymentField.change(function () {
            fixOrderPaymentProcessor();
        });

        fixOrderPaymentField.keydown(function () {
            fixOrderPaymentProcessor();
        });

        fixOrderPaymentProcessor();
    });

    // Выбор реквизитов для перевода выручки
    $(function () {
        form.find(':input[name="default_backpayment_detail_id"]').change(function () {
            form.find(':input[name="default_backpayment_details"]').val($(this).find('option:selected').data('description'));
        });
    });

    // Смена АPI сервера
    $(function () {
        form.find('select[name="dostavista_is_api_test_server"]').on('change', function () {
            let authTokenInput = form.find('input[name="cms_module_api_auth_token"]:first');
            authTokenInput.val(
                $(this).val() == 1 ? authTokenInput.data('token-test') : authTokenInput.data('token-prod')
            );
        });
    });

    // Logout
    $(function () {
        form.find('#dostavista-logout').on('click', function (e) {
            e.preventDefault();

            licode.preloader.on(form);
            woodostavista.frontendHttpClient.dostavistaClientLogout(function(success, error, parameterErrors, response) {
                licode.preloader.off(form);
                if (success) {
                   window.location.reload();
                } else {
                    alert(woodostavista.settings.translations.settings_save_error);
                }
            });
        });
    });

    $(function () {
        $('select[name="default_payment_type"]:first').on('change', defaultPaymentTypeChanged);
        defaultPaymentTypeChanged();
        function defaultPaymentTypeChanged()
        {
            if ($("select[name='default_payment_type'] option:selected").data('is-card')) {
                $('select[name="default_payment_card_id"]:first').parents('tr:first').show();
            } else {
                $('select[name="default_payment_card_id"]:first').parents('tr:first').hide();
            }
        }
    });
});
