jQuery(document).ready(function(){

    jQuery('#form_timepicker').timepicker({
        timeFormat: 'h:mm p',
        interval: 1,
        minTime: '10',
        maxTime: '6:00pm',
        defaultTime: 'now',
        startTime: '10:00',
        dynamic: false,
        dropdown: true,
        scrollbar: true
    });

    //for customer
    jQuery('.woocommerce-form-login').find('p:nth-child(1)').remove();
    jQuery('.woocommerce-form-login').find('.woocommerce-form__label-for-checkbox:nth-child(1)').hide();
    jQuery('.lost_password').hide();
    jQuery('<button id="cancel-login" class="btn btn-warning">CANCEL</button>').insertAfter('.woocommerce-form-login__submit');

    var login_flag = false;

    jQuery('#returning-customer-radio').click(function() {
        if(login_flag === false){
            //jQuery('.woocommerce-form-login').css('display', 'block');
            jQuery('.woocommerce-form-login').fadeIn().insertAfter('.returning-customer-label');
            login_flag = true;
        }
        else
        {
            jQuery('.woocommerce-form-login').fadeIn();
        }

        jQuery('#customer_details').fadeOut();
        jQuery('.ywsl-box').fadeOut();
        jQuery('.wc-social-login').hide();
        jQuery('#createaccount').prop('checked', false); 

        console.log(login_flag);
    });

    jQuery('#cancel-login').click(function(e) {
        e.preventDefault();
        jQuery('.woocommerce-form-login').fadeOut();
    });

    jQuery('#new-customer-radio').click(function() {
        //jQuery('.woocommerce-form-login').css('display', 'none');
        jQuery('.woocommerce-form-login').fadeOut();
        jQuery('#customer_details').fadeIn().insertAfter('.new-customer-label');
        //jQuery('#billing_address_1_field').hide();
        jQuery('.ywsl-box').fadeOut();
        console.log(login_flag);
    });

    jQuery('#fb-customer-radio').click(function(){
        jQuery('.ywsl-box').fadeIn().insertAfter('.fb-customer-label');
        jQuery('.woocommerce-form-login').fadeOut();
        jQuery('#customer_details').fadeOut();
        jQuery('#createaccount').prop('checked', false); 
    });

    jQuery('.returning-cancel-login').click(function(){
        jQuery('.returning-customer-row').hide();
    });

    jQuery('.woocommerce-billing-fields').find('h3:nth-child(1)').hide();

    jQuery('#customer_details').hide();

    jQuery('.custom-checkout-address-field').on("change paste keyup", function(){
        var customAddressValue =  jQuery('.custom-checkout-address-field').val();
        jQuery('#billing_address_1').val(customAddressValue);
    });

    jQuery('#delivery-tab-link').click(function() {
        var testlel = jQuery('#datepicker-wrapper').html();
        jQuery('#delivery-tab').append(testlel);
    });

})