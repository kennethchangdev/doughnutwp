jQuery(function () {
    var $ = jQuery;

    var orderFormArea = $('#woo-dostavista-order-form');
    var orderForm = orderFormArea.find('form');
    var orderSummaryBlock = orderFormArea.find('.summary-block');
    var orderSummaryErrorBlock = orderSummaryBlock.find('.errors');

    var isOrderCreated = false;
    var _isCalculationPrevented = false;

    function calculateOrder() {
        if (!getInputElements().length) {
            return;
        }

        if (_isCalculationPrevented) {
            return;
        }

        licode.preloader.on(orderSummaryBlock);

        var data = {};
        getInputElements().each(function () {
            var jInput = $(this);
            var name = jInput.attr('name');
            if (typeof (name) !== 'undefined') {
                data[name] = jInput.is(':checkbox') ? jInput.prop('checked') : jInput.val();
            }
        });

        woodostavista.frontendHttpClient.calculateOrder(data, function (success, error, parameterErrors, response) {
            licode.preloader.off(orderSummaryBlock);
            orderSummaryErrorBlock.hide();

            renderParameterErrors(response);

            var calculationValues = [
                'delivery_fee_amount',
                'weight_fee_amount',
                'insurance_fee_amount',
                'money_transfer_fee_amount',
                'loading_fee_amount',
                'payment_amount'
            ];

            if (typeof (response.order_calculation_result) === 'undefined') {
                orderSummaryErrorBlock.text(woodostavista.order.translation.calculate_error);
                orderSummaryErrorBlock.show();

                for (var i in calculationValues) {
                    orderSummaryBlock
                        .find('[data-calculation="' + calculationValues[i] + '"]')
                        .text(0);
                }

                return false;
            }

            for (var i in calculationValues) {
                orderSummaryBlock
                    .find('[data-calculation="' + calculationValues[i] + '"]')
                    .text(response.order_calculation_result[calculationValues[i]]);
            }

            // После расчета стоимости тип транспортного средства подставим тот, который пришел в ответе
            var vehichleTypeSelect = orderForm.find('select[name="vehicle_type_id"]:first');
            vehichleTypeSelect.val(response.order_calculation_result.vehicle_type_id);
        });
    }

    orderForm.submit(function (e) {
        e.preventDefault();

        if (!getInputElements().length) {
            return;
        }

        licode.preloader.on(orderSummaryBlock);

        var data = {};
        getInputElements().each(function () {
            var jInput = $(this);
            var name = jInput.attr('name');
            if (typeof (name) !== 'undefined') {
                data[name] = jInput.is(':checkbox') ? jInput.prop('checked') : jInput.val();
            }
        });

        woodostavista.frontendHttpClient.createOrder(data, function (success, error, parameterErrors, response) {
            licode.preloader.off(orderSummaryBlock);

            orderSummaryErrorBlock.hide();

            if (!success || typeof (response.order_id) === 'undefined' || !response.order_id) {
                if (typeof(response.error) !== 'undefined' && typeof( woodostavista.order.errorTranslations[response.error]) !== 'undefined') {
                    orderSummaryErrorBlock.text(woodostavista.order.errorTranslations[response.error]);
                } else {
                    orderSummaryErrorBlock.text(woodostavista.order.translation.create_order_error_text);
                }
                orderSummaryErrorBlock.removeClass('d-none');
                orderSummaryErrorBlock.show();
            } else {
                isOrderCreated = true;
                orderFormArea.find('.content-container').html(
                    '<div class="woo-dostavista-success-block"> '+ woodostavista.order.translation.success_message.replace("{id}", '<strong>' + response.order_id + '</strong>') +' </div>'
                );
                orderSummaryBlock.stop().css({'margin-top' : '0'});
            }
        });
    });

    function getInputElements() {
        return orderForm.find(':input').not('#pickup-warehouse-select');
    }

    function renderParameterErrors(response) {
        orderForm.find('div.invalid-feedback').remove();
        orderForm.find('.is-invalid').removeClass('is-invalid');

        if (typeof (response.form_parameter_errors) === 'undefined') {
            return;
        }

        var parameterErrors = response.form_parameter_errors;

        if (parameterErrors) {
            var errorsCount = 0;
            for (var fieldName in parameterErrors) {
                errorsCount++;

                var inputElement = orderForm.find(':input[name="' + fieldName + '"]');
                inputElement.after(
                    '<div class="invalid-feedback">' +  woodostavista.order.parameterErrorTranslations[parameterErrors[fieldName]] + '</div>'
                );
                inputElement.addClass('is-invalid');
            }

            if (errorsCount) {
                orderSummaryErrorBlock.text(woodostavista.order.translation.field_error_text);
                orderSummaryErrorBlock.show();
            }
        }
    }

    // Summary block floating
    (function () {
        var offset = orderSummaryBlock.offset();
        var topPadding = 40;
        $(window).scroll(function () {
            alignSummaryBlock();
        });

        function alignSummaryBlock() {
            if (isOrderCreated) {
                return;
            }

            if ($(window).scrollTop() > offset.top) {
                orderSummaryBlock.stop().animate({marginTop: $(window).scrollTop() - offset.top + topPadding}, 200);
            } else {
                orderSummaryBlock.stop().animate({marginTop: 0}, 200);
            }
        }

        alignSummaryBlock();
    })();

    if (getInputElements().length) {
        calculateOrder();
    }

    orderForm.on('change', ':input:not(#pickup-warehouse-select, .delivery-warehouse-select)', function (e) {
        calculateOrder();
    });

    $('#pickup-warehouse-select').change(function (e) {
        var option = $(this).find('option:selected');

        if (option.length) {
            _isCalculationPrevented = true;

            orderForm.find(':input[name="pickup_address"]').val(option.data('address'));
            orderForm.find(':input[name="pickup_required_start_time"]').val(option.data('work-start-time'));
            orderForm.find(':input[name="pickup_required_finish_time"]').val(option.data('work-finish-time'));
            orderForm.find(':input[name="pickup_contact_name"]').val(option.data('contact-name'));
            orderForm.find(':input[name="pickup_contact_phone"]').val(option.data('contact-phone'));
            orderForm.find(':input[name="pickup_note"]').val(option.data('note'));

            _isCalculationPrevented = false;
            calculateOrder();
        }
    });

    // Backpayment detail control
    orderForm.find(':input[name="backpayment_detail_id"]').change(function () {
        orderForm.find(':input[name="backpayment_details"]').val($(this).find('option:selected').data('description'));
    });

    // Pickup time control
    function restrictPickupTime() {
        var pickupDateFirstOption = orderForm.find(':input[name="pickup_required_date"]').find('option:first');
        var pickupRequiredStartTime = orderForm.find(':input[name="pickup_required_start_time"]');
        if (pickupDateFirstOption.is(':selected') && pickupDateFirstOption.data('today')) {
            var minTodayTimeValue = parseInt(pickupRequiredStartTime.data('minTodayTime').replace(':', ''));
            pickupRequiredStartTime.find('option').each(function () {
                if (parseInt($(this).val().replace(':', '')) < minTodayTimeValue) {
                    $(this).prop('disabled', true);
                }
            });
        } else {
            pickupRequiredStartTime.find('option').prop('disabled', false);
        }
    }

    orderForm.find(':input[name="pickup_required_date"]').change(function () {
        restrictPickupTime();
    });
    orderForm.find(':input[name="pickup_required_start_time"]').change(function () {
        restrictPickupTime();
    });

    restrictPickupTime();

    // Контроллер управления точками доставки
    $(function() {
        function getDeliveryPointForm(pointIndex) {
            return orderForm.find('div.point-form[data-point-index="' + pointIndex + '"]');
        }

        function changePointFormIndex(pointForm, newPointIndex) {
            var currentPointIndex = parseInt(pointForm.attr('data-point-index'));

            pointForm.find(':input').each(function () {
                var jObj = $(this);
                var name = jObj.attr('name');
                if (typeof(name) !== 'undefined' && name.length) {
                    if (name.indexOf('sdek') >= 0 && name.indexOf('_package_') >= 0) {
                        name = name.replace(currentPointIndex + '_package', newPointIndex +'_package');
                    } else {
                        name = name.replace(currentPointIndex, newPointIndex);
                    }
                    jObj.attr('name', name);
                }
            });

            pointForm.find('h2.form-section > span > span').text(newPointIndex + 1);
            pointForm.attr('data-point-index', newPointIndex);
        }

        function swapSiblingPoints(sourcePointForm, destinationPointForm) {
            if (sourcePointForm.length <= 0) {
                return;
            }

            if (destinationPointForm.length <= 0) {
                return;
            }

            sourcePointForm.fadeOut(300);
            destinationPointForm.fadeOut(300);

            var sourcePointFormIndex = parseInt(sourcePointForm.attr('data-point-index'));
            var destinationPointFormIndex = parseInt(destinationPointForm.attr('data-point-index'));

            var sourcePointFormAddDeliveryPointButton = sourcePointForm.prev('.add-delivery-point:first');
            var destinationPointFormAddDeliveryPointButton = destinationPointForm.prev('.add-delivery-point:first');

            sourcePointFormAddDeliveryPointButton.after(destinationPointForm);
            destinationPointFormAddDeliveryPointButton.after(sourcePointForm);

            changePointFormIndex(sourcePointForm, destinationPointFormIndex);
            changePointFormIndex(destinationPointForm, sourcePointFormIndex);

            calculateOrder();

            sourcePointForm.fadeIn(300);
            destinationPointForm.fadeIn(300);

            scrollToPoint(sourcePointForm);
        }

        function scrollToPoint(pointForm) {
            $('html, body').animate({
                scrollTop: pointForm.offset().top
            }, 300);
        }

        orderForm.on('click', '.point-up', function (e) {
            e.preventDefault();
            var pointForm = $(this).parents('.point-form:first');
            var pointIndex = parseInt(pointForm.attr('data-point-index'));
            swapSiblingPoints(pointForm, getDeliveryPointForm(pointIndex - 1));
        });

        orderForm.on('click', '.point-down', function (e) {
            e.preventDefault();
            var pointForm = $(this).parents('.point-form:first');
            var pointIndex = parseInt(pointForm.attr('data-point-index'));
            swapSiblingPoints(pointForm, getDeliveryPointForm(pointIndex + 1));
        });

        orderForm.on('click', '.add-delivery-point', function (e) {
            e.preventDefault();

            var newPointButton = $(this);

            var pointForm = newPointButton.next('.point-form:first');
            var isLastPoint = false;
            if (pointForm.length <= 0) {
                pointForm = newPointButton.prev('.point-form:first');
                isLastPoint = true;
            }

            if (pointForm.length <= 0) {
                return;
            }

            _isCalculationPrevented = true;

            var clonedPointForm = pointForm.clone();
            clonedPointForm.find(':input').val('');
            clonedPointForm.hide();

            var newPointFormIndex = parseInt(pointForm.attr('data-point-index'));
            if (isLastPoint) {
                newPointFormIndex = newPointFormIndex + 1;
                changePointFormIndex(clonedPointForm, newPointFormIndex);
            }
            clonedPointForm.find(':input[name="point_type_' + newPointFormIndex + '"]').val('plain');

            // Пересчитаем индексы всех точек после текущей
            if (!isLastPoint) {
                var secondPointFormList = newPointButton.nextAll('.point-form');
                secondPointFormList.each(function () {
                    var eachPointForm = $(this);
                    var pointIndex = parseInt(eachPointForm.attr('data-point-index'));
                    changePointFormIndex(eachPointForm, pointIndex + 1);
                });
            }

            newPointButton.after(clonedPointForm);
            clonedPointForm.after(orderForm.find('.add-delivery-point:first').clone());
            clonedPointForm.fadeIn(500);
            scrollToPoint(clonedPointForm);

            _isCalculationPrevented = false;
            calculateOrder();
        });

        orderForm.on('click', '.point-remove', function (e) {
            e.preventDefault();

            if (orderForm.find('.point-form').length <= 1) {
                return;
            }

            _isCalculationPrevented = true;

            var pointForm = $(this).parents('.point-form:first');
            var secondPointFormList = pointForm.nextAll('.point-form');
            secondPointFormList.each(function () {
                var eachPointForm = $(this);
                var pointIndex = parseInt(eachPointForm.attr('data-point-index'));
                changePointFormIndex(eachPointForm, pointIndex - 1);
            });

            pointForm.fadeOut(300, function() {
                pointForm.next('.add-delivery-point:first').remove();
                pointForm.remove();
            });

            _isCalculationPrevented = false;
            calculateOrder();
        });
    });

    orderForm.on('change', '.delivery-warehouse-select', function (e) {
        var jObj = $(this);
        var pointForm = jObj.parents('.point-form:first');
        var pointFormIndex = parseInt(pointForm.attr('data-point-index'));
        var option = jObj.find('option:selected');

        if (option.length) {
            _isCalculationPrevented = true;

            pointForm.find(':input[name="plain_point_address_' + pointFormIndex + '"]').val(option.data('address'));
            pointForm.find(':input[name="plain_point_required_start_time_' + pointFormIndex + '"]').val(option.data('work-start-time'));
            pointForm.find(':input[name="plain_point_required_finish_time_' + pointFormIndex + '"]').val(option.data('work-finish-time'));
            pointForm.find(':input[name="plain_point_recipient_name_' + pointFormIndex + '"]').val(option.data('contact-name'));
            pointForm.find(':input[name="plain_point_recipient_phone_' + pointFormIndex + '"]').val(option.data('contact-phone'));
            pointForm.find(':input[name="plain_point_note_' + pointFormIndex + '"]').val(option.data('note'));

            _isCalculationPrevented = false;
            calculateOrder();
        }
    });

    $('select[name="payment_type"]:first').on('change', paymentTypeChanged);
    paymentTypeChanged();
    function paymentTypeChanged()
    {
        if ($("select[name='payment_type'] option:selected").data('is-card')) {
            $('select[name="bank_card_id"]:first').parents('tr:first').show();
        } else {
            $('select[name="bank_card_id"]:first').parents('tr:first').hide();
        }
    }
});
