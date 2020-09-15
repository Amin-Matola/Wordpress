<?php
/**
 * Review order table
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/review-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.8.0
 */

defined( 'ABSPATH' ) || exit;
?>
<table class="shop_table woocommerce-checkout-review-order-table">
	<thead>
		<tr>
			<th class="product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
			<th class="product-total"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		do_action( 'woocommerce_review_order_before_cart_contents' );

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				?>
				<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
					<td class="product-name">
						<?php echo apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php echo apply_filters( 'woocommerce_checkout_cart_item_quantity', ' <strong class="product-quantity">' . sprintf( '&times;&nbsp;%s', $cart_item['quantity'] ) . '</strong>', $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</td>
					<td class="product-total">
						<?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</td>
				</tr>
				<?php
			}
		}


		//do_action( 'woocommerce_review_order_after_cart_contents' );
		?>
	</tbody>
	<tfoot>

		<tr class="cart-subtotal">
			<th><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
			<td><?php wc_cart_totals_subtotal_html(); ?></td>
		</tr>

		<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
				<th><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
				<td><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
			</tr>
		<?php endforeach; ?>

		<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>

			<?php do_action( 'woocommerce_review_order_before_shipping' ); ?>

		<tr>
			<td>
				<?php $method = array_keys(get_chosen_methods(false, false))[0] ?>

				<h5 class="text-danger pickup_location_current">
					<?php if(explode(":", $method)[0] != "local_pickup_plus"): ?>
						<?=  array_values(get_chosen_methods(false, false))[0] ?>
					<?php else: ?>
							<script type="text/javascript">
								var source, target;

								source = document.querySelector("#customer_details > div.col-1.cog-toggle-form-area-2.cog-btn-local-pickup > #pickup_point_field > span > #pickup_point");
								target = document.querySelector("#order_review > table > tfoot > tr:nth-child(2) > td:nth-child(1) > h5");


								if(source && source.value){
									
									target.innerText = source.options[source.selectedIndex].innerText;
								}
							</script>
					<?php endif ?>
		
				</h5>
				<p style="height:0px !important; overflow:hidden !important">
						<select>
							<?php foreach(get_zone_shipping_methods() as $m => $label): ?>
								<option value="<?= $m ?>"><?= $label ?></option>
							<?php endforeach; ?>
						</select>


				</p>
				<?php  
						//$p = wc_local_pickup_plus()->get_shipping_instance();
						//print_r(WC()->session->get("chosen_shipping_methods"));
						 ?>
		</td>
		<td>

			<h5 class="text-danger pickup_location_current_fee">
				<?= 
				
					(get_chosen_methods(true, true)[0] > 0)? wc_price(get_chosen_methods(true, true)[0]) : "FREE"; 
				?>

			</h5>
			<h5 id="free-local">FREE</h5>
		</td>

		</tr>

			<?php do_action( 'woocommerce_review_order_after_shipping' ); ?>

		<?php endif; ?>

		<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<tr class="fee">
				<th><?php echo esc_html( $fee->name ); ?></th>
				<td><?php wc_cart_totals_fee_html( $fee ); ?></td>
			</tr>
		<?php endforeach; ?>

		<?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
			<?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
				<?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited ?>
					<tr class="tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
						<th><?php echo esc_html( $tax->label ); ?></th>
						<td><?php echo wp_kses_post( $tax->formatted_amount ); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr class="tax-total">
					<th><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></th>
					<td><?php wc_cart_totals_taxes_total_html(); ?></td>
				</tr>
			<?php endif; ?>
		<?php endif; ?>

		<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

		<tr class="order-total">
			<th><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
			<td><?php wc_cart_totals_order_total_html(); ?></td>
		</tr>


		<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>

	</tfoot>
</table>

<script type="text/javascript">
					function create_input_field(placeholder = "My field"){
								var tfield;

								tfield = document.createElement("input");
								tfield.setAttribute("type", "text");
								tfield.setAttribute("disabled", "true");
								tfield.setAttribute("placeholder", placeholder);
								return tfield;
					}

					function set_shipping_options(options){
							var target, firstChild, parent, input;
					

							target 	= document.querySelector("#order_review > table > tfoot > tr:nth-child(2) > td:nth-child(1) > p > select");
							if(!target){
								return false;
							}

							firstChild = target.options[target.selectedIndex].innerText;

							input 		 = create_input_field(firstChild);

							parent 	= document.querySelector("#shipping_method_0_field > span > #shipping_method");
							if(!parent){
								parent = document.querySelector("#shipping_method_0_field > span > #shipping_method_0");
							}

							if(!parent){
								return false;
							}

							if(!parent.classList.contains("shipping_method")){
								parent.classList.add("class", "shipping_method");
							}

							if(options > 1){
								if(parent.parentNode.childNodes.length > 1){
									parent.parentNode.removeChild(parent.parentNode.childNodes[1]);
								}
								parent.style     = "display: block";
								parent.innerHTML = target.innerHTML;
								
							}else{
								if(parent.parentNode.childNodes.length < 2){
									parent.style = "display: none";
									parent.parentNode.appendChild(input);
								}

							}

					}

					set_shipping_options(<?= count(array_keys(cog_get_shipping_methods(false, false))) ?>);



					


</script>