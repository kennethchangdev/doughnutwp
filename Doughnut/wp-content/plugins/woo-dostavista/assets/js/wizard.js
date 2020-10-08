jQuery(function () {
    var $ = jQuery;
    var area = $('#woo-dostavista-wizard');
    var _settings = {};

    function getStepArea(stepIndex) {
        return area.find('div.step[data-step-index="' + stepIndex + '"]');
    }

    var _step = 1;
    function showStep(stepIndex) {
        getStepArea(_step).fadeOut(200, function() {
            getStepArea(stepIndex).show();
        });
        _step = stepIndex;
    }

    function saveSettingsDataFromStepForm(stepIndex) {
        for (i = 2; i <= stepIndex; i++) {
            stepArea = getStepArea(i);
            stepArea.find(':input').each(function () {
                var jInput = $(this);
                var name = jInput.attr('name');
                if (typeof (name) !== 'undefined' && name !== 'locations') {
                    _settings[name] = jInput.is(':checkbox') ? jInput.prop('checked') : (jInput.val() ? jInput.val().trim() : '');
                }
            });
        }
        _settings['dostavista_cms_module_api_auth_token'] = getStepArea(1).data('api-token');
        _settings['dostavista_cms_module_api_url']        = getStepArea(1).data('api-url');
    }

    // Начинаем не с 1го шага, а с того, который пользователь еще не заполнил
    var startStep = area.data('start-step');
    showStep(startStep);

    var orderPageUrl = $('#btn-orders-page').attr('href');
    function setStepIsFinished(stepIndex)
    {
        var stepArea = getStepArea(stepIndex);
        var data = {
            step_number : stepIndex
        };

        woodostavista.frontendHttpClient.setLastFinishedWizardStep(data, function (success) {
            licode.preloader.off(stepArea);
            if (success) {
                if (stepIndex >= 6) {
                    licode.preloader.on(stepArea);
                    window.location.href = orderPageUrl;
                } else {
                    showStep(stepIndex + 1);
                }
            }
        });
    }

    // Шаг 1
    area.find('.nav-next-1-install').click(function (e) {
        var data = {
            'client_login'   : getStepArea(1).find('input[name="client_login"]').val(),
            'client_password': getStepArea(1).find('input[name="client_password"]').val(),
            'is_apitest'     : !!getStepArea(1).find('input[name="is_apitest"]').prop('checked'),
        };

        licode.preloader.on(getStepArea(1));
        woodostavista.frontendHttpClient.createAuthToken(data, function (success, error, parameterErrors, response) {
            licode.preloader.off(getStepArea(1));
            if (!success) {
                alert(woodostavista.wizard.translations.client_404);
            } else {
                _settings['cms_module_api_auth_token'] = response['api_auth_token'];
                _settings['dostavista_is_api_test_server'] = !!getStepArea(1).find('input[name="is_apitest"]').prop('checked');
                area.find('input[name="dostavista_cms_module_api_callback_secret"]').val(response['api_callback_secret_key']);

                getStepArea(1).data('apiToken', response['api_auth_token']);
                getStepArea(1).data('apiUrl', response['api_url']);

                getStepArea(1).find('.login-form').hide();
                getStepArea(1).find('.woo-dostavista-warning-block').hide();
                getStepArea(1).find('.woo-dostavista-success-block').show();
                getStepArea(1).find('.nav-next-1-install').hide();
                getStepArea(1).find('.nav-next-1-register').hide();
                getStepArea(1).find('.nav-next-1-reset').show();
                getStepArea(1).find('.nav-next-1').show();

                // Обновим список банковских карт пользователя
                woodostavista.frontendHttpClient.getPaymentTypes(function (success, error, parameterErrors, response) {
                    if (success) {
                        var defaultPaymentTypeElement = $('select[name="default_payment_type"]:first');
                        var defaultPaymentCardElement = $('select[name="default_payment_card_id"]:first');
                        defaultPaymentTypeElement.html('');
                        defaultPaymentCardElement.html('');
                        $.each(response.payment_methods, function (index, val) {
                            var newPayOption = $('<option value="'+ val.code +'" data-is-card="'+ val.is_card +'">' + val.name + '</option>');
                            defaultPaymentTypeElement.append(newPayOption);
                        });
                        $.each(response.cards, function (index, val) {
                            var newCardOption = $('<option value="'+ val.id +'" >' + woodostavista.wizard.translations.payment_type_card + ' '+ val.mask +'</option>');
                            defaultPaymentCardElement.append(newCardOption);
                        });
                        defaultPaymentTypeChanged();
                    }
                });
                setStepIsFinished(1);
            }
        });
    });
    area.find('.nav-next-1').click(function (e) {
        _settings['cms_module_api_auth_token'] = getStepArea(1).data('apiToken');
        _settings['dostavista_is_api_test_server'] = !!getStepArea(1).find('input[name="is_apitest"]').prop('checked');

        setStepIsFinished(1);

        getStepArea(1).find('.login-form').hide();
        getStepArea(1).find('.woo-dostavista-warning-block').hide();
        getStepArea(1).find('.woo-dostavista-success-block').show();
        getStepArea(1).find('.nav-next-1-install').hide();
        getStepArea(1).find('.nav-next-1-register').hide();
        getStepArea(1).find('.nav-next-1-reset').show();
        getStepArea(1).find('.nav-next-1').show();
    });
    area.find('.nav-next-1-reset').click(function (e) {
        getStepArea(1).find('.login-form').show();
        getStepArea(1).find('.woo-dostavista-success-block').hide();
        getStepArea(1).find('.woo-dostavista-warning-block').show();
        if (!getStepArea(1).data('apiToken') || !getStepArea(1).data('apiUrl')) {
            getStepArea(1).find('.nav-next-1').hide();
        }
        getStepArea(1).find('.nav-next-1-reset').hide();
        getStepArea(1).find('.nav-next-1-install').show();
        getStepArea(1).find('.nav-next-1-register').show();
    });

    // Шаг 2
    area.find('.nav-back-2').click(function (e) {
        showStep(1);
    });
    area.find('.nav-next-2').click(function (e) {
        var data = {};
        var stepArea = getStepArea(2);
        stepArea.find(':input').each(function () {
            var jInput = $(this);
            var name = jInput.attr('name');
            if (typeof (name) !== 'undefined') {
                data[name] = jInput.is(':checkbox') ? jInput.prop('checked') : jInput.val();
            }
        });

        if (!data['city'] || !data['address'] || !data['contact_phone']) {
            alert(woodostavista.wizard.translations.fields_required);
        } else {
            if (stepArea.data('warehouseId')) {
                data['id'] = stepArea.data('warehouseId');
            }

            data['name'] = stepArea.data('warehouseName') ? stepArea.data('warehouseName') : 'default';

            licode.preloader.on(stepArea);
            woodostavista.frontendHttpClient.storeWarehouse(
                data,
                function (success, error, parameterErrors, response) {
                    licode.preloader.off(stepArea);
                    if (success) {
                        _settings['default_pickup_warehouse_id'] = response.warehouse.id;
                        stepArea.data('warehouseId', response.warehouse.id);
                        setStepIsFinished(2);
                    } else {
                        alert(woodostavista.wizard.translations.save_warehouse_error);
                    }
                }
            );
        }
    });

    // Шаг 3
    area.find('.nav-back-3').click(function (e) {
        showStep(2);
    });
    area.find('.nav-next-3').click(function (e) {
        saveSettingsDataFromStepForm(5);
        var stepArea = getStepArea(3);
        if (isNaN(_settings['default_order_weight_kg'])) {
            alert(woodostavista.wizard.translations.default_order_weight_integer);
        } else {
            if (!_settings['default_vehicle_type_id'] || !_settings['default_order_weight_kg']) {
                alert(woodostavista.wizard.translations.fields_required);
            } else {
                /*
                 * Реквизиты для перевода выручки - отдельная сущность. В визарде поддерживаем только дефолтные реквизиты.
                 * Когда они пустые, нужно удалить сущность.
                 */
                var backpaymentDetailId = stepArea.data('backpaymentDetailId');
                var backpaymentDetailDescription = stepArea.find(':input[name="default_backpayment_details"]').val().trim();

                if (backpaymentDetailDescription) {
                    licode.preloader.on(stepArea);

                    var data = {
                        'description': backpaymentDetailDescription
                    };

                    if (backpaymentDetailId) {
                        data['id'] = backpaymentDetailId;
                    }
                    woodostavista.frontendHttpClient.storeBackpaymentDetail(
                        data,
                        function (success, error, parameterErrors, response) {
                            licode.preloader.off(stepArea);
                            if (success) {
                                _settings['default_backpayment_detail_id'] = response.backpayment_detail.id;
                                stepArea.data('backpaymentDetailId', response.backpayment_detail.id);
                            }

                            setStepIsFinished(3);
                        }
                    );
                }

                if (!backpaymentDetailDescription && backpaymentDetailId) {
                    licode.preloader.on(stepArea);

                    woodostavista.frontendHttpClient.deleteBackpaymentDetail(backpaymentDetailId, function () {
                        licode.preloader.off(stepArea);
                        _settings['default_backpayment_detail_id'] = null;
                        stepArea.data('backpaymentDetailId', '');
                        showStep(4);
                    });
                } else {
                    setStepIsFinished(3);
                }
            }
        }

        storeSettings(
            _settings,
            function (success) {
                licode.preloader.off(getStepArea(3));

                if (success) {
                    setStepIsFinished(3);
                } else {
                    alert(woodostavista.wizard.translations.save_settings_error);
                }
            }
        );
    });

    // Шаг 4
    area.find('.nav-back-4').click(function (e) {
        showStep(3);
    });
    area.find('.nav-next-4').click(function (e) {
        saveSettingsDataFromStepForm(5);
        licode.preloader.on(getStepArea(4));

        storeSettings(
            _settings,
            function (success) {
                licode.preloader.off(getStepArea(4));

                if (success) {
                    setStepIsFinished(4);
                } else {
                    alert(woodostavista.wizard.translations.save_settings_error);
                }
            }
        );
    });

    // Шаг 5
    area.find('.nav-back-5').click(function (e) {
        showStep(4);
    });
    area.find('.nav-next-5').click(function (e) {
        saveSettingsDataFromStepForm(5);
        licode.preloader.on(getStepArea(5));

        storeSettings(
            _settings,
            function (success) {
                licode.preloader.off(getStepArea(5));

                if (success) {
                    setStepIsFinished(5);
                } else {
                    alert(woodostavista.wizard.translations.save_settings_error);
                }
            }
        );
    });

    // Шаг 6
    area.find('.nav-back-6').click(function (e) {
        showStep(5);
    });
    area.find('.nav-next-6').click(function (e) {
        setStepIsFinished(6);
    });
    area.find('.nav-next-6-install').click(function (e) {
        var stepArea = getStepArea(6);

        var zoneIds = [];
        stepArea.find('.shipping-method-form :input').each(function () {
            var jInput = $(this);
            if ($(this).prop("checked")) {
                zoneIds.push(parseInt(jInput.val()));
            }
        });

        if (zoneIds.length <= 0) {
            alert(woodostavista.wizard.translations.set_zones_required);
            return;
        }

        licode.preloader.on(stepArea);
        woodostavista.frontendHttpClient.installShippingMethod(
            zoneIds,
            function (success, error, parameterErrors, response) {
                licode.preloader.off(stepArea);
                if (success) {
                    stepArea.find('.shipping-method-form').hide();
                    stepArea.find('.woo-dostavista-warning-block').hide();
                    stepArea.find('.woo-dostavista-success-block').show();
                    stepArea.find('.nav-next-6-install').hide();
                    stepArea.find('.nav-next-6').show();
                } else {
                    alert(woodostavista.wizard.translations.set_zone_error);
                }
            }
        );
    });

    // Шаг 7
    area.find('.nav-back-7').click(function (e) {
        showStep(6);
    });

    function storeSettings(_settings, callback)
    {
        woodostavista.frontendHttpClient.storeSettings(
            _settings,
            callback
        );
    }

    // Логика поля Фиксированная стоимость оплаты
    $(function () {
        var orderPaymentMarkupField   = getStepArea(3).find('input[name="dostavista_payment_markup_amount"]');
        var orderPaymentDiscountField = getStepArea(3).find('input[name="dostavista_payment_discount_amount"]');
        var fixOrderPaymentField      = getStepArea(3).find('input[name="fix_order_payment_amount"]');

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

    // Добавление новых зон доставки
    $(function () {
        var shippingZoneList = getStepArea(6).find('.shipping-zone-list');
        var shippingZoneForm = getStepArea(6).find('.shipping-zone-form');

        getStepArea(6).find('.open-shipping-zone-form').click(function (e) {
            e.preventDefault();
            $(this).hide();
            shippingZoneForm.slideDown();
        });

        shippingZoneForm.find('.add-shipping-zone').click(function (e) {
            e.preventDefault();

            var name = shippingZoneForm.find('[name="name"]').val().trim();
            var locations = shippingZoneForm.find('[name="locations"]').val();

            if (!name || !locations || locations.length <= 0) {
                alert(woodostavista.wizard.translations.name_and_zone_required);
                return;
            }

            licode.preloader.on(shippingZoneForm);
            woodostavista.frontendHttpClient.addShippingZone(
                name, locations,
                function (success, error, parameterErrors, response) {
                    licode.preloader.off(shippingZoneForm);
                    if (success) {
                        if (shippingZoneList.data('empty')) {
                            shippingZoneList.html('');
                            shippingZoneList.data('empty', 0);
                        }

                        shippingZoneForm.find('[name="name"]').val('');
                        shippingZoneForm.find('[name="locations"]').val('');

                        shippingZoneList.append(
                            '<div><input type="checkbox" id="shipping-zone-' + response.zone_id + '" value="' + response.zone_id + '"><label for="shipping-zone-' + response.zone_id + '">' + response.zone_name + '</label></div>'
                        );

                        getStepArea(6).find('.open-shipping-zone-form').show();
                        shippingZoneForm.slideUp();
                    } else {
                        alert(woodostavista.wizard.translations.create_zone_error);
                    }
                }
            );
        });
    });

    // Выбор способа оплаты
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
