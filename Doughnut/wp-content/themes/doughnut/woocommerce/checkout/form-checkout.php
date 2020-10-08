<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_checkout_form', $checkout );

remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );

// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'understrap' ) ) );
	return;
}

?>
<br/>
<div class="row customer-row">
	<div class="col-lg-6">
		<h3>1&nbsp;&nbsp;&nbsp;Customer</h3>
		<i class="fa fa-minus" aria-hidden="true"></i><br/>


		<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

			<?php if ( $checkout->get_checkout_fields() ) : ?>

			
		<?php 
			if(!is_user_logged_in()){
		?>
			<input type="radio" id="returning-customer-radio" name="customer-type-radio" value="returning customer">&nbsp;<label class="returning-customer-label" for="returning-customer-radio">Returning Customer</label>
			<br/>
			<input type="radio" id="new-customer-radio" name="customer-type-radio" value="new customer">&nbsp;<label class="new-customer-label" for="new-customer-radio">New Customer</label>
			<br/>
			<input type="radio" id="fb-customer-radio" name="customer-type-radio" value="fb login">&nbsp;<label class="fb-customer-label" for="fb-customer-radio"><button class="btn btn-info">Login Using Facebook</button></label>
			
		<?php
			} 
		?>

			<br/>
			<br/>

				<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

				<div class="row checkout-section" id="customer_details">
					<!--<div class="col-12 col-sm-7">-->
					<div class="col-md-12">
						<?php do_action( 'woocommerce_checkout_billing' ); ?>
						<?php do_action( 'woocommerce_checkout_shipping' ); ?>
					</div>
				</div>

				<div class="row checkout-section" id="delivery-details">
					<div class="col-md-12">
						<h3>2&nbsp;&nbsp;&nbsp;Delivery</h3>
						<i class="fa fa-minus" aria-hidden="true"></i>
						<ul class="nav nav-tabs" id="myTab" role="tablist">
							<li class="nav-item">
								<a class="nav-link active" id="pickup-tab-link" data-toggle="tab" href="#pickup" role="tab" aria-controls="pickup" aria-selected="true">Pickup</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" id="delivery-tab-link" data-toggle="tab" href="#delivery" role="tab" aria-controls="delivery" aria-selected="false">Delivery</a>
							</li>
						</ul>

						<div class="tab-content" id="myTabContent">

							<div class="tab-pane fade show active" id="pickup-tab" role="tabpanel" aria-labelledby="pickup-tab">
								
							</div>

							<div class="tab-pane fade" id="delivery-tab" role="tabpanel" aria-labelledby="delivery-tab">
							</div>
						</div>
					</div>
				</div>

				<div class="row checkout-section" id="payment-details">
					<div class="col-md-12">
						<h3>3&nbsp;&nbsp;&nbsp;Payment</h3>
						<i class="fa fa-minus" aria-hidden="true"></i>
						<?php woocommerce_checkout_payment() ?>
								<!--</div>-->

								<!--<div class="col-12 col-sm-5 hidden-order-summary">
									<?php do_action( 'woocommerce_checkout_shipping' ); ?>
									<h3 id="order_review_heading"><?php esc_html_e( 'ORDER SUMMARY', 'understrap' ); ?></h3>

									<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

									<div id="order_review" class="woocommerce-checkout-review-order">
										<?php do_action( 'woocommerce_checkout_order_review' ); ?>
									</div>
								</div>-->

						<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

						<?php endif; ?>

						<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
					</div>
				</div>

		</form>

	</div> <!--End Column -->

	<div class="col-lg-6">
			<h3 id="order_review_heading"><?php esc_html_e( 'Order Summary', 'understrap' ); ?></h3>

			<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

			<div id="order_review" class="woocommerce-checkout-review-order">
				<?php do_action( 'woocommerce_checkout_order_review' ); ?>
			</div>
	</div>

</div> <!-- Row End -->

<?php
do_action( 'woocommerce_after_checkout_form', $checkout );
