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

do_action( 'woocommerce_before_checkout_form', $checkout );


// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}



?>


<div class="col-10 cog-order-top-summary">
		<div class="cog-order-pointer" onclick="show_summary()">
				<span id="cog-order-pointer">
					Show Order Summary <i class="fa fa-chevron-down"></i> &nbsp;
				</span>
				<span class="review-cost">
					<?php  echo wc_price(WC()->cart->get_cart_contents_total()); ?>
				</span>

		</div>
		<div class="cog-order-review" style="display: none;">
			<!-- <table>
				<thead>
					<th>Product</th><th>Subtotal</th>
				</thead>
			</table> -->
			<?php do_action( 'woocommerce_checkout_order_review' ); ?>
				
		</div>

</div>

<div class="col-10 cog-shop-header">
 	<div class="col-5">
 		<h4>Shipping / Pickup</h4>
 			<hr>
		<h5>Standard Shipping and Pickup is <span class="text-danger2">Free</span></h5>
 	</div>
 	<div class="col-4 ml-auto">
 		<h4>Your Order</h4>
 		<hr>
 	</div>

</div>



<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">
	

	<?php if ( $checkout->get_checkout_fields() ) : ?>

		<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>
		<h6 id="error-show"></h6>

		<div class="col2-set" id="customer_details">
			<div class="col-1 cog-shipping-controls" class='cog-shipping-controls'>
				<div class="cog-control-item" id="cog-btn-shipping" onblur="this.style.background='initial'">


				<svg id="Group_31" data-name="Group 31" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="28.988" height="19" viewBox="0 0 28.988 19">
					  <defs>
					    <clipPath id="clip-path">
					      <!--rect id="Rectangle_71" data-name="Rectangle 71" width="28.988" height="19" fill="#01c5b8"/-->
					      <rect id="Rectangle_71" class="ship-icon" data-name="Rectangle 71" width="28.988" height="19" fill="#fff" id="cog-shipping-icon"/>
					    </clipPath>
					  </defs>
					  <g id="Group_30" data-name="Group 30" clip-path="url(#clip-path)">
					    <path id="Path_11" data-name="Path 11" d="M28.877,10.311,24.6,3.652a.7.7,0,0,0-.59-.322h-4.53V.7a.7.7,0,0,0-.7-.7H.7A.7.7,0,0,0,0,.7V15.922a.7.7,0,0,0,.7.7H3.887a3.079,3.079,0,0,0,6,0H20.058a3.079,3.079,0,0,0,6,0h2.234a.7.7,0,0,0,.7-.7V10.69a.7.7,0,0,0-.111-.379M23.056,17.6a1.677,1.677,0,1,1,1.677-1.677A1.679,1.679,0,0,1,23.056,17.6M22.689,9.59V6.981h2.38L26.746,9.59Zm-.608-3.934a.638.638,0,0,0-.608.663v3.934a.638.638,0,0,0,.608.663h5.5v4.3H26.053a3.079,3.079,0,0,0-6,0h-.581V4.733h4.147l.593.923Zm-15.2,8.589a1.677,1.677,0,1,1-1.677,1.677,1.679,1.679,0,0,1,1.677-1.677M18.074,4.031V15.22H9.883a3.079,3.079,0,0,0-6,0H1.4V1.4H18.074Z" class="ship-icon" transform="translate(-0.001 -0.001)" fill="#fff"/>
					  </g>
				</svg>


				 	Deliver To Me
				 </div>
				<div class="cog-control-item" id='cog-btn-local-pickup'>
					<svg id="Group_34" data-name="Group 34" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="23.266" height="23.265" viewBox="0 0 23.266 23.265">
					  <defs>
					    <clipPath id="clip-path">
					      <rect id="Rectangle_72" class="pickup-icon" data-name="Rectangle 72" width="23.266" height="23.265" fill="#01c5b8"/>
					    </clipPath>
					  </defs>
					  <g id="Group_33" data-name="Group 33" clip-path="url(#clip-path)">
					    <path id="Path_12" class="pickup-icon" data-name="Path 12" d="M200.482,40.739h0l0,.007,0-.007" transform="translate(-178.697 -36.312)" fill="#01c5b8"/>
					    <path id="Path_13" class="pickup-icon" data-name="Path 13" d="M200.439,40.672l0,.005h0l0-.006" transform="translate(-178.658 -36.252)" fill="#01c5b8"/>
					    <path id="Path_14" class="pickup-icon" data-name="Path 14" d="M23.167,15.016A2.246,2.246,0,0,0,21.933,13.6V4.917a.935.935,0,0,0-.147-.49h0L19.113.417A.936.936,0,0,0,18.333,0H6.263a.936.936,0,0,0-.78.417L2.828,4.4l-.007.009-.013.022a.931.931,0,0,0-.144.482V13.4H.937A.938.938,0,0,0,0,14.339v7.988a.938.938,0,0,0,.937.937H3.6a2.268,2.268,0,0,0,1.585-.648,15.727,15.727,0,0,0,4.453.648h6.033a4.955,4.955,0,0,0,3.945-1.973l3.2-4.261a2.245,2.245,0,0,0,.355-2.015M17.832,1.875l1.413,2.119H15.232V1.875ZM13.358,3.994H11.238V1.875h2.119ZM9.364,1.875V3.994H5.351L6.764,1.875Zm10.7,11.732a2.275,2.275,0,0,0-.723.518l-1.671,1.794a2.257,2.257,0,0,0-1.994-1.185H12.507a1.5,1.5,0,0,1-.99-.373,4.957,4.957,0,0,0-6.245-.22,2.255,2.255,0,0,0-.735-.535V5.869H20.059ZM5.86,16.1l.381-.329a3.063,3.063,0,0,1,4.039,0,3.37,3.37,0,0,0,2.228.841h3.164a.394.394,0,0,1,0,.788H10.878a.937.937,0,1,0,0,1.875h5.239a2.276,2.276,0,0,0,1.66-.722L20.708,15.4a.394.394,0,0,1,.6.5l-3.2,4.261a3.071,3.071,0,0,1-2.445,1.223H9.638a13.683,13.683,0,0,1-3.778-.529c0-.23,0-.729,0-1.843Zm-1.866-.425V21a.394.394,0,0,1-.394.394H1.875V15.277H3.6a.4.4,0,0,1,.394.394" fill="#01c5b8" id="cog-pickup-icon"/>
					  </g>
					</svg>
					 &nbsp;Will Collect
				</div>
				<div class="cog-no-show">
				</div>

				<div class="cog-no-show">
				</div>
			</div>
			<div class="col-1 cog-toggle-form-area cog-btn-shipping">
				<h3 style="font-style: 24px; font-weight: 600">Shipping Details</h3>
							

				<?php //do_action("cog_show_shipping_field", cog_get_shipping_methods()); ?>

				<?php do_action( 'woocommerce_checkout_shipping' ); ?>

			<h3><span id="bill" style="font-style: 24px;font-weight:600">Billing Details</span></h3>

				<?php do_action( 'woocommerce_checkout_billing' ); ?>


		<h3>Billing Details</h3>
		<h3 id="ship-to-different-address">
			<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
				<input id="ship-to-different-address-checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" <?php checked( apply_filters( 'woocommerce_ship_to_different_address_checked', 'shipping' === get_option( 'woocommerce_ship_to_destination' ) ? 1 : 0 ), 1 ); ?> type="checkbox" name="ship_to_different_address" value="1" /> 
				<?php esc_html_e( 'Use my shipping details for billing', 'woocommerce' ); ?>
			</label>
		</h3>
		

		</div>

			<div class="col-1 cog-toggle-form-area-2 cog-btn-local-pickup">
				<h3>Pickup Details</h3>
				<p class="form-row selectpicker select2 pickup-location-field update_totals_on_change pickup-location-lookup" id="pickup_point_field" data-priority="">
					<span class="woocommerce-input-wrapper">
						<select 
						name="pickup_point" 
						id="pickup_point" class="select " 
						data-live-search="1" 
						data-allow_clear="true" 
						data-placeholder="select pickup point">
							<option value="" selected="selected">select pickup point</option>
				<?php foreach (apply_filters("cog_get_local_addresses", "") as $key => $value): ?>
							
							<option value="<?= $key ?>"><?= $value ?></option>
				<?php endforeach; ?>
				
					</select>
				</span>
			</p>


				<p class="form-row arrival_date cog_arrival_date_field" id="arrival_date_field" data-priority="">
					<label for="arrival_date" class="">Opening Times: Monday - Sunday: 9am - 5:30pm&nbsp;<span class="optional">(optional)</span>
					</label>
					<span class="woocommerce-input-wrapper">
						<input type="text" value="<?= date("Y-m-d") ?>" class="datepicker input-text " name="arrival_date" id="arrival_date" placeholder="DD-MM-YYYY" value="" data-date-format="dd-mm-YYYY">
					</span>
				</p>


				<h3>Billing Details</h3>
				<?php do_action( 'woocommerce_checkout_billing' ); ?>
			</div>

			
		</div>

		<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

	<?php endif; ?>
	
	<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>
	
	<h3 id="order_review_heading"><?php esc_html_e( 'Your order', 'woocommerce' ); ?></h3>
	
	<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

	<div id="order_review" class="woocommerce-checkout-review-order">
		<?php do_action( 'woocommerce_checkout_order_review' ); ?>
	</div>

	<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>

</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>


<script type="text/javascript">

	// function remove_class(class_name, item){
	// 	var classes = item.getAttribute("class").split(" ");

	// 	var results = classes.filter(function(v, i, a){ return v != "update_totals_on_change";});
	// 	item.setAttribute("class", results.join(" "));

	// }

	// function set_billing_country(country = ""){
	// 		if(country.length < 1){
	// 			country = document.querySelector("#customer_details > div.col-1.cog-toggle-form-area.cog-btn-shipping > div.woocommerce-shipping-fields > div > div > #shipping_country_field > #shipping_country").value;
	// 		}
	// 		try{
	// 			var billing_country = document.querySelectorAll("#billing_country_field > #billing_country");
	// 			if(billing_country.length > 1){
	// 				billing_country[0].value = country;
	// 				remove_class("update_totals_on_change", billing_country[0].parentNode);
	// 				remove_class("update_totals_on_change", billing_country[1].parentNode);
	// 			}else{
	// 				billing_country.value = country;
	// 			}
	// 		}catch(e){
	// 			alert(e.message);
	// 		}
	// }

	

	// document.querySelector("#customer_details > div.col-1.cog-toggle-form-area.cog-btn-shipping > div.woocommerce-shipping-fields > div > div > #shipping_country_field > #shipping_country").onchange = function(){
	//  	set_billing_country(this.value);
	 	
	//  }
	// set_billing_country();	

</script>







