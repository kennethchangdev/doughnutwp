if (typeof(woodostavista) === 'undefined') {
    var woodostavista = {};
}

woodostavista.wcOrdersController = {
    'order_form_url' : '',
};

jQuery(function () {
    var $ = jQuery;

    var orderListArea = $('.post-type-shop_order');
    if (!orderListArea.length) {
        return;
    }

    var ordersTable = orderListArea.find('.wp-list-table');
    function getCheckedOrderIds() {
        var ids = [];
        ordersTable.find('tbody input[type="checkbox"]').each(function () {
            var jObj = $(this);
            if (jObj.prop('checked')) {
                ids.push(jObj.val());
            }
        });

        return ids;
    }

    var orderCreationButton = $('<button class="button button-dostavista">'+ woodostavista.wcOrder.translations.send_to_dostavista +'</button>');
    orderCreationButton.css({
        'margin': '1px 8px 0 0',
        'height': '32px'
    });

    var dostavistaActions = $('<div class="alignleft actions"></div>');
    dostavistaActions.append(orderCreationButton);
    orderListArea.find('.tablenav.top .alignleft.actions:last').after(dostavistaActions);

    orderCreationButton.click(function (e) {
        e.preventDefault();

        var ids = getCheckedOrderIds();
        if (ids.length <= 0) {
            alert(woodostavista.wcOrder.translations.check_orders_required);
            return;
        }

        window.location.href = woodostavista.wcOrdersController.order_form_url + '&' + $.param({'ids': ids});
    });
});
