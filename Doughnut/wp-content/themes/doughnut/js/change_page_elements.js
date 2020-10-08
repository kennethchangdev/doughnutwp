jQuery(document).ready(function(){

    //change button styles on login page
    jQuery("button[name='login']").attr("class", "woocommerce-form-login__submit btn btn-warning");
    jQuery("button[name='register']").attr("class", "woocommerce-form-login__submit btn btn-warning");

    jQuery('.return-to-shop').find('a').attr('class', 'btn btn-warning');
    jQuery('.return-to-shop').find('a').attr('href', '/');
})

