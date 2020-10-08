if (typeof(woodostavista) === 'undefined') {
    var woodostavista = {};
}

/**
 * @type {woodostavista.frontendHttpClient}
 *
 * Интерфейс колбеков ответа:
 * responseCallback(success, error, parameterErrors, rawResponse);
 *
 */
woodostavista.frontendHttpClient = new (function() {
    /** @type {woodostavista.frontendHttpClient} */
    var _self = this;
    var _postUrl;

    this.setPostUrl = function (url) {
        _postUrl = url;
    };

    function sendPostRequest(action, postData, responseCallback) {
        var handler = function(response, textStatus, jqXHR) {
            var parameterErrors = null;
            var error = null;
            var isSuccessful = true;
            var responseJson = (response && typeof(response.responseJSON) !== 'undefined') ? response.responseJSON : response;

            if (responseJson === null) {
                responseJson = {};
            }

            if (typeof(responseJson.error) !== 'undefined' || jqXHR.status != 200) {
                error = response.error;
                isSuccessful = false;
            }

            if (typeof(responseJson.parameter_errors) !== 'undefined') {
                parameterErrors = responseJson.parameter_errors;
            }

            if (responseCallback && typeof(responseCallback) !== 'undefined') {
                responseCallback(isSuccessful, error, parameterErrors, responseJson);
            }
        };

        jQuery.ajax(
            {
                type: 'POST',
                jsonp: false,
                url: _postUrl + '?action=woo_dostavista/' + encodeURIComponent(action),
                data: JSON.stringify(postData),
                dataType: 'json'
            }
        ).always(handler);
    }

    this.storeSettings = function(data, responseCallback) {
        sendPostRequest('store_settings', data, responseCallback);
    };

    this.storeWarehouse = function(data, responseCallback) {
        sendPostRequest('store_warehouse', data, responseCallback);
    };

    this.deleteWarehouse = function(warehouseId, responseCallback) {
        sendPostRequest('delete_warehouse', {"id" : warehouseId}, responseCallback);
    };

    this.storeBackpaymentDetail = function(data, responseCallback) {
        sendPostRequest('store_backpayment_detail', data, responseCallback);
    };

    this.deleteBackpaymentDetail = function(id, responseCallback) {
        sendPostRequest('delete_backpayment_detail', {"id" : id}, responseCallback);
    };

    this.calculateOrder = function(data, responseCallback) {
        sendPostRequest('calculate_order', data, responseCallback);
    };

    this.createOrder = function(data, responseCallback) {
        sendPostRequest('create_order', data, responseCallback);
    };

    this.createAuthToken = function(data, responseCallback) {
        sendPostRequest('create_auth_token', data, responseCallback);
    };

    this.installShippingMethod = function(zoneIds, responseCallback) {
        sendPostRequest('install_shipping_method', {'zone_ids': zoneIds}, responseCallback);
    };

    this.addShippingZone = function(name, locations, responseCallback) {
        sendPostRequest('add_shipping_zone', {'name': name, 'locations': locations}, responseCallback);
    };

    this.getPaymentTypes = function(responseCallback) {
        sendPostRequest('get_payment_types', {}, responseCallback);
    };

    this.setLastFinishedWizardStep = function(data, responseCallback) {
        sendPostRequest('set-wizard-last-finished-step', data, responseCallback);
    };

    // Логаут с доставистой
    this.dostavistaClientLogout = function(responseCallback) {
        sendPostRequest('dostavista-logout', {}, responseCallback);
    };
});

