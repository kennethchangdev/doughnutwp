jQuery(function () {
    var $ = jQuery;

    var form = $('#woo-dostavista-warehouse-edit form:first');

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

        if (form.data('id')) {
            data['id'] = form.data('id');
        }

        woodostavista.frontendHttpClient.storeWarehouse(data, function(success, error, parameterErrors, response) {
            if (success) {
                window.location.href = response.redirect_url;
            } else {
                licode.preloader.off(form);
                alert(woodostavista.warehouse.translation.save_error);
            }
        });
    });
});
