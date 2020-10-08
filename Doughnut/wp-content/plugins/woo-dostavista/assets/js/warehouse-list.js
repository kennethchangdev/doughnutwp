jQuery(function () {
    var $ = jQuery;

    var area = $('#woo-dostavista-warehouse-list');

    area.on('click', 'a.warehouse-delete', function (e) {
        e.preventDefault();
        var _link = this;

        if (confirm(woodostavista.warehouse.translation.remove_confirm_message)) {
            var table = $(_link).parents('table:first');
            licode.preloader.on(table);

            var warehouseId = $(_link).parents('tr:first').data('warehouseId');
            woodostavista.frontendHttpClient.deleteWarehouse(warehouseId, function (success, error, parameterErrors, response) {
                if (success) {
                    window.location.reload();
                } else {
                    licode.preloader.off(table);
                    alert(woodostavista.warehouse.translation.remove_error);
                }
            });
        }
    });
});
