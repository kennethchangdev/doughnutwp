<?php
/**
 * UnderStrap functions and definitions
 *
 * @package UnderStrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$understrap_includes = array(
	'/theme-settings.php',                  // Initialize theme default settings.
	'/setup.php',                           // Theme setup and custom theme supports.
	'/widgets.php',                         // Register widget area.
	'/enqueue.php',                         // Enqueue scripts and styles.
	'/template-tags.php',                   // Custom template tags for this theme.
	'/pagination.php',                      // Custom pagination for this theme.
	'/hooks.php',                           // Custom hooks.
	'/extras.php',                          // Custom functions that act independently of the theme templates.
	'/customizer.php',                      // Customizer additions.
	'/custom-comments.php',                 // Custom Comments file.
	'/jetpack.php',                         // Load Jetpack compatibility file.
	'/class-wp-bootstrap-navwalker.php',    // Load custom WordPress nav walker. Trying to get deeper navigation? Check out: https://github.com/understrap/understrap/issues/567.
	'/woocommerce.php',                     // Load WooCommerce functions.
	'/editor.php',                          // Load Editor functions.
	'/deprecated.php',                      // Load deprecated functions.
);

foreach ( $understrap_includes as $file ) {
	require_once get_template_directory() . '/inc' . $file;
}


add_action('wp_enqueue_scripts', 'bootstrap_input_spinner');

function bootstrap_input_spinner(){
    if(is_page('home'))
    {
        wp_enqueue_script('bootstrap_input_spinner_script', get_stylesheet_directory_uri().'/node_modules/bootstrap-input-spinner/src/bootstrap-input-spinner.js', 
        array('jquery'), false, true);
    }
}

add_action('wp_enqueue_scripts', 'doughnut_shop_js');

function doughnut_shop_js() {
        wp_enqueue_script('doughnut_shop_script', get_stylesheet_directory_uri().'/js/doughnut_shop.js', 
        array('jquery'), false, true);
}

add_action('wp_enqueue_scripts', 'change_page_styles_js');

function change_page_styles_js() {
    if(!is_page('home'))
    {
        wp_enqueue_script('change_page_styles_script', get_stylesheet_directory_uri().'/js/change_page_elements.js', 
        array('jquery'), false, true);
    }
}

add_action('wp_enqueue_scripts', 'checkout_page_elements');

function checkout_page_elements() {
    if(is_page('checkout'))
    {
        wp_enqueue_script('checkout_page_elements_script', get_stylesheet_directory_uri().'/js/checkout_page_elements.js', 
        array('jquery'), false, true);
    }
}

function jquery_timepicker_stylesheet() {
    wp_register_style('jquery_timepicker_style', get_template_directory_uri().'/jquery_timepicker/jquery.timepicker.min.css');
    
    wp_enqueue_style( 'jquery_timepicker_style' );
}
add_action( 'wp_enqueue_scripts', 'jquery_timepicker_stylesheet' );

add_action('wp_enqueue_scripts', 'jquery_timepicker_js');
function jquery_timepicker_js() {
    wp_enqueue_script(' jquery_timepicker_script', get_stylesheet_directory_uri().'/jquery_timepicker/jquery.timepicker.min.js', 
    array('jquery'), false, true);
}

// Display custom cart item meta data (in cart and checkout)
add_filter( 'woocommerce_get_item_data', 'display_cart_item_custom_meta_data', 10, 2 );
function display_cart_item_custom_meta_data( $item_data, $cart_item ) {
    $meta_key = 'Flavors';
    if ( isset($cart_item['doughnut_flavors']) && isset($cart_item['doughnut_flavors'][$meta_key]) ) {
        $item_data[] = array(
            'key'       => $meta_key,
            'value'     => $cart_item['doughnut_flavors'][$meta_key],
        );
    }
    return $item_data;
}

// Save cart item custom meta as order item meta data and display it everywhere on orders and email notifications.
add_action( 'woocommerce_checkout_create_order_line_item', 'save_cart_item_custom_meta_as_order_item_meta', 10, 4 );
function save_cart_item_custom_meta_as_order_item_meta( $item, $cart_item_key, $values, $order ) {
    $meta_key = 'Flavors';
    if ( isset($values['doughnut_flavors']) && isset($values['doughnut_flavors'][$meta_key]) ) {
        $item->update_meta_data( $meta_key, $values['doughnut_flavors'][$meta_key] );
    }
}

/**
 * @snippet       WooCommerce Show Product Image @ Checkout Page
 * @author        Sandesh Jangam
 * @donate $9     https://www.paypal.me/SandeshJangam/9
 */
 
add_filter( 'woocommerce_cart_item_name', 'ts_product_image_on_checkout', 10, 3 );
 
function ts_product_image_on_checkout( $name, $cart_item, $cart_item_key ) {
     
    /* Return if not checkout page */
    if ( ! is_checkout() ) {
        return $name;
    }
     
    /* Get product object */
    $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
 
    /* Get product thumbnail */
    $thumbnail = $_product->get_image();
 
    /* Add wrapper to image and add some css */
    $image = '<div class="ts-product-image" style="width: 52px; height: 45px; display: inline-block; padding-right: 7px; vertical-align: middle;">'
                . $thumbnail .
            '</div>'; 
 
    /* Prepend image to name and return it */
    return $image . $name;
}

add_filter( 'woocommerce_cart_item_quantity', 'wc_cart_item_quantity', 10, 3 );
function wc_cart_item_quantity( $product_quantity, $cart_item_key, $cart_item ){
    if( is_cart() ){
        $product_quantity = sprintf( '%2$s <input type="hidden" name="cart[%1$s][qty]" value="%2$s" />', $cart_item_key, $cart_item['quantity'] );
    }
    return $product_quantity;
}

 /**
  * Remove all possible fields
 **/
function wc_remove_checkout_fields( $fields ) {
    // Order fields
    unset( $fields['order']['order_comments'] );

    return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'wc_remove_checkout_fields' );

/*add_filter( 'woocommerce_default_address_fields' , 'rename_checkout_field_labels', 9999 );
 
function rename_checkout_field_labels( $fields ) {
    $fields['address_1']['label'] = 'Address';
    return $fields;
}*/

// Register main datepicker jQuery plugin script
add_action( 'wp_enqueue_scripts', 'enabling_date_picker' );
function enabling_date_picker() {
    // Only on front-end and checkout page
    if( is_admin() || ! is_checkout() ) return;

    // Load the datepicker jQuery-ui plugin script
    wp_enqueue_script( 'jquery-ui-datepicker' );
    wp_enqueue_style('jquery-ui', "http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/smoothness/jquery-ui.css", '', '', false);
}

// Add custom checkout datepicker field
add_action( 'woocommerce_before_order_notes', 'datepicker_custom_field' );
function datepicker_custom_field($checkout) {
    $datepicker_slug = 'form_datepicker';

    echo '<div id="datepicker-wrapper">';

    woocommerce_form_field($datepicker_slug, array(
        'type' => 'text',
        'class'=> array( 'form-row-first form-datepicker'),
        'label' => __('Date'),
        'required' => true, // Or false
    ), '' );

    echo '<br clear="all"></div>';


    // Jquery: Enable the Datepicker
    ?>
    <script language="javascript">
    jQuery( function($){
        var a = '#<?php echo $datepicker_slug ?>';
        $(a).datepicker({
            dateFormat: 'yy-mm-dd', // ISO formatting date
        });
    });
    </script>
    <?php
}

// Add custom checkout datepicker field
add_action( 'woocommerce_before_order_notes', 'timepicker_custom_field' );
function timepicker_custom_field($checkout) {
    $timepicker_slug = 'form_timepicker';

    woocommerce_form_field($timepicker_slug, array(
        'type' => 'text',
        'class'=> array( 'form-row-first form-timepicker'),
        'label' => __('Time'),
        'required' => true, // Or false
    ), '' );
}

// Display the product thumbnail in order view pages
add_filter( 'woocommerce_order_item_name', 'display_product_image_in_order_item', 20, 3 );
function display_product_image_in_order_item( $item_name, $item, $is_visible ) {
    // Targeting view order pages only
    if( is_wc_endpoint_url( 'view-order' ) ) {
        $product   = $item->get_product(); // Get the WC_Product object (from order item)
        $thumbnail = $product->get_image(array( 36, 36)); // Get the product thumbnail (from product object)
        if( $product->get_image_id() > 0 )
            $item_name = '<div class="item-thumbnail">' . $thumbnail . '</div>' . $item_name;
    }
    return $item_name;
}

/**
 * @snippet       Hide Hidden Products from Cart, Checkout, Order - WooCommerce
 * @how-to        Get CustomizeWoo.com FREE
 * @author        Rodolfo Melogli
 * @testedwith    WooCommerce 4.1
 * @donate $9     https://businessbloomer.com/bloomer-armada/
 */
   
add_filter( 'woocommerce_cart_item_visible', 'bbloomer_hide_hidden_product_from_cart' , 10, 3 );
add_filter( 'woocommerce_widget_cart_item_visible', 'bbloomer_hide_hidden_product_from_cart', 10, 3 );
add_filter( 'woocommerce_checkout_cart_item_visible', 'bbloomer_hide_hidden_product_from_cart', 10, 3 );
add_filter( 'woocommerce_order_item_visible', 'bbloomer_hide_hidden_product_from_order_woo333', 10, 2 );
    
function bbloomer_hide_hidden_product_from_cart( $visible, $cart_item, $cart_item_key ) {
    $product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
    if ( $product->get_catalog_visibility() == 'hidden' ) {
        $visible = false;
    }
    return $visible;
}
    
function bbloomer_hide_hidden_product_from_order_woo333( $visible, $order_item ) {
    
    $product = $order_item->get_product();
    if ( $product->get_catalog_visibility() == 'hidden' ) {
        $visible = false;
    }

    return $visible;
}