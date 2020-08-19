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
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}

?>

<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

	<!-------------------  Payments ------------------------------------>
	<ul class="wc_payment_methods payment_methods methods">
				<?php
				$available_gateways 	= apply_filters("bhd_methods", "");
				if ( ! empty( $available_gateways ) ) {
					foreach ( $available_gateways as $gateway ) {
						wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
					}
				} else {
					echo '<li class="woocommerce-notice woocommerce-notice--info woocommerce-info">' . apply_filters( 'woocommerce_no_available_payment_methods_message', esc_html__( 'Sorry, it seems that there are no available payment methods for your location. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce' ) ) . '</li>'; // @codingStandardsIgnoreLine
				}
				
				?>

	</ul>

	<!-------------- Payments ------------------------------->

	<?php if ( $checkout->get_checkout_fields() ) : ?>

		<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

		<div class="col2-set" id="customer_details">
		    <div class="shipping-holder">
			   <?php wc_cart_totals_shipping_html(); ?>
			   <?php 
                	 // @codingStandardsIgnoreLine 
                	$order_button_text = isset($order_button_text)?$order_button_text : __("Submit Order", "woocommerce");
                				echo apply_filters( 'woocommerce_order_button_html', '<button type="submit" id="proceed" class="button alt" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '">' . esc_html( $order_button_text ) . '</button>' );
                	//do_action( 'woocommerce_checkout_before_order_review_heading' ); 
	         ?>
			</div>
			<div class="col-1">
			    
				<?php do_action( 'woocommerce_checkout_billing' ); 

				do_action( 'woocommerce_checkout_shipping' );?>
				<?php 
	 // @codingStandardsIgnoreLine 
	$order_button_text = "Proceed To Shipping";
				echo apply_filters( 'woocommerce_order_button_html', '<button type="button" id="bhd-ship" class="button alt" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '">' . esc_html( $order_button_text ) . '</button>' );
	//do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>
				
			</div>

			<div class="col-2">
				<?php do_action( 'woocommerce_checkout_order_review' );
				
				
				//do_action( 'woocommerce_checkout_after_customer_details' ); ?>
				
			</div>
		</div>

		<?php //shipping fields  ?>

	<?php endif; ?>
	
	
	
	<h3 id="order_review_heading"><?php esc_html_e( 'Your order', 'woocommerce' ); ?></h3>
	
	<?php //do_action( 'woocommerce_checkout_before_order_review' ); ?>

	<div id="order_review" class="woocommerce-checkout-review-order">
		<?php //do_action( 'woocommerce_checkout_order_review' ); ?>
	</div>

	<?php //do_action( 'woocommerce_checkout_after_order_review' ); ?>

</form>

<?php //do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
