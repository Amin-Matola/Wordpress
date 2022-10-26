<?php


/* *
 * Plugin Name: 	Beehive Woo Checkout
 * Plugin URI:  	https://codeblush.com
 * Description: 	Woocommerce Checkout Development and Advanced Search Engine Optimization
 * Author: 		Amin Matola
 * Author URI:  	https://beehivedigital.co
 * Version:     	2.0.1
 * License:     	GPLv2 or later
 * */

//////////// Helper Functions ////////////////
function bhd_get_filter($which){

	global $wp_filters; 
	return $wp_filters[$which];

}

function bh_style_object($w){
	global $wp_styles;

	$styles = $wp_styles->registered;
	foreach ($styles as $style) {
		if($style->handle == $w){
			return $style;
		}
		
	}
	return false;
}


function bh_script_object($w){
	global $wp_scripts;

	$scripts = $wp_scripts->registered;
	foreach ($scripts as $script) {
		if($script->handle == $w){
			return $script;
		}
	}
	return false;
}

function bh_all_script_objects($w){

	$scripts = $w->registered;
	$res 	 = [];
	foreach ($scripts as $script) {
		if( strpos($script->handle, "load"))
			$res[] = $script->handle;
	}
	return $res;
}

function bh_all_handlers($src){
	return $src->queue;
}

function bh_all_srcs($w){
	$items = [];
	foreach($w->registered as $i){
	   $items[$i->handle] = $i->src;
	}
	return $items;
}

/****************
 * Woo Checkout Merged with WP SEO
 * **************/

class Bhd_Checkout{

	public function __construct(){
		$this->init();
	}

	public function init(){
		$this->add_hooks();
	}


	/* *
	 * Link all necessary hooks
	 * */
	public function add_hooks(){
		add_action( "wp_enqueue_scripts", array($this, "add_styles") );
		add_filter( 'woocommerce_locate_template', array($this, 'bhd_woo_templates'), 1, 3 );
		add_filter( "woocommerce_checkout_fields", array($this, "bhd_slack_billing"), 999, 1 );
		add_filter( "woocommerce_billing_fields", array($this, "bhd_further_slack_fields"), 999, 1);
		add_filter( "woocommerce_get_checkout_url", function(){return get_site_url()."/checkout";});
		add_filter( 'woocommerce_cart_needs_shipping_address', '__return_true');
		add_filter( 'woocommerce_ship_to_different_address_checked', '__return_true' );
		add_filter( "bhd_methods", array($this, "bhd_payment_methods" ));
		add_filter( 'woocommerce_coupons_enabled', '__return_true' );
		add_filter( 'woocommerce_checkout_coupon_message', function($m){return "";});
		add_filter( 'woocommerce_form_field' , array($this, 'remove_optionals'), 10, 4 );
		add_filter( 'woocommerce_checkout_posted_data', array( $this,'bhd_get_posted_data' ));
		
		add_action( 'woocommerce_checkout_before_customer_details', array($this, 'my_add_notice_free_shipping' ));

		/******** New Changes ********
		 * 22/06/2020
		 ****************************/

		add_action( 'woocommerce_checkout_order_processed', array($this, 'changing_order_data_before_payment'), 10, 3 );

		/****************** Optimizations *****************/
		add_action( "wp_print_styles", array($this, "remove_similar_resources"));
		add_action( "wp_print_scripts", array($this, "bhd_do_scripts"));
		add_action( "init", array($this, "bh_compress_files"));
		add_filter( 'script_loader_tag', array($this, "bh_defer_scripts"), 10);
		add_action( "wp_head", array($this, "disable_imojs"));
		// add_action( "wp_footer", function(){
		// 	wp_add_inline_script(
		// 		"images_not_loaded", 
		// 		"(function(_j){_j(document).ready(function(){try{var l=new LazyLoad({elements_selector: '[loading=lazy]',use_native: true});return true; }catch(e){ _j('img').each(function(){_j(this).attr('src', _j(this).attr('data-src')); });}})})(jQuery)"
		// 		);
		// });
		add_filter( "get_filter", function($which){global $wp_filters; return $wp_filters[$which]; });
	}
	
	
	/* *********************** 
	 *Add necessary styles 
	 **************/ 
	public function add_styles(){
		global $wp_scripts;

		if( !is_admin() ){
			wp_register_script("bhd-lazy-load", null, [], "1.0.1", true);
			wp_add_inline_script( 'bhd-lazy-load',
				"
				/* Let the doc load first */
				window.onload = function(){
					try{var l=new LazyLoad({elements_selector: '[loading=lazy]',use_native: true}); return true;}
					catch(e){ 
						document.querySelectorAll('img').forEach(function(i){
							var _src = i.getAttribute('data-src');if(Boolean(_src)) i.setAttribute('src', _src); 
					});
				}};
			" );
			$wp_scripts->enqueue('bhd-lazy-load');
		}
		if( is_checkout() && ! is_wc_endpoint_url() ) {
			wp_enqueue_script( "bhd_cscript", plugin_dir_url( __FILE__ )."assets/js/bhd-woo.js", [], "1.1.0", true );
		}
		if(is_cart() && !is_checkout()){
			add_action( 'woocommerce_before_cart_contents', array( $this,'my_add_notice_free_shipping' ));
		}
		wp_enqueue_style( "bhd_combined_styles", plugin_dir_url( __FILE__ )."assets/bhd_combined_styles.min.css", [], "1.0", "all" );
		//wp_enqueue_style( "bhd_bootstrap_glyphicons", plugin_dir_url( __FILE__ )."assets/bootstrap-glyphicons.css", [], "1.0", "all" );
	}
	

	/* *
	 * Remove unnecessary scripts
	 * */
	public function bhd_do_scripts(){
		global $wp_scripts;
		
		if(!is_admin()){
			
			$wp_scripts->dequeue("bootstrap");
			$wp_scripts->dequeue("jquery-migrate");
			$wp_scripts->add("sbp-ins-page", "group", 1);
			$wp_scripts->dequeue("sbp-ins-page");
			$wp_scripts->dequeue("wp-embed");
		
		}
			
	}

	/******
	 * Load scripts in a defered way
	 * *****/
	function bh_defer_scripts($link){
		global $wp_filter;
		if ( is_admin() || FALSE === strpos( $link, '.js' ) || strpos( $link, 'jquery.js' ) || strpos($link, "load") || strpos($link, "slick")){
			return $link; //don't break WP Admin
		}
	
		return str_replace( 'src', 'defer src', $link );
	}

	
	/* ****************
	 * Remove unused emojis and scripts that may slow site
	 * ****/
	public function disable_imojs(){
		global $wp_styles;
		if(!in_array("administrator", wp_get_current_user()->roles)){
			$wp_styles->dequeue("heading-font");
			$wp_styles->dequeue("admin-bar");
		}
		if(!is_admin()){
			remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
			remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
			remove_action( 'wp_print_styles', 'print_emoji_styles' );
			remove_action( 'admin_print_styles', 'print_emoji_styles' );   
			remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
			remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );     
			remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
			//remove_action( 'wp_print_scripts', 'oxygen_print_custom_styles' );
			add_filter( 'tiny_mce_plugins', array($this, 'disable_emojis_tinymce') );
		}
	}

	/* ***************
	 * Remove google fonts to reduce requests
	 *************/
	public function remove_g_fonts(){
		global $wp_styles;
		foreach($wp_styles as $st){
			if(is_object($st) && strpos($st->src, "fonts.google")){
				$wp_styles->dequeue($st->handle);
				$wp_styles->remove($st->handle);
			}
		
		}
	}

	/******
	 * Remove emojis
	 * ****/
	public function disable_emojis_tinymce( $plugins ) {
        if ( is_array( $plugins ) ) {
            return array_diff( $plugins, array( 'wpemoji' ) );
        } else {
            return array();
        }
    }

	/* *
	 * Beehive Digital Compression algroithms
	 * */
	public function bh_compress_files() {
        global $concatenate_scripts, $compress_scripts, $compress_css;
        if(!is_admin()){
            $concatenate_scripts = true;
            $compress_scripts = true;
            $compress_css = true;
        }
    
	}


	/********************************
	 * Function to deque some scripts | Toda's Modes
	 * */
	public function remove_similar_resources(){
			global $wp_styles, $wp_filter, $custom_styles;
			if(wp_style_is( 'yith-wcwl-font-awesome', "registered") || wp_style_is('yith-wcwl-font-awesome', "enqueued")){
					if( !is_admin() && (wp_style_is( 'yith-wcwl-font-awesome', "registered") || wp_style_is( 'yith-wcwl-font-awesome', "enqueued"))){
						$wp_styles->dequeue('yith-wcwl-font-awesome');

					}
			  }


			  if(!is_admin()){
				  $g_fonts = bh_all_srcs($wp_styles);
				  $wp_styles->dequeue('bootstrap');
				  $wp_styles->dequeue('entypo');
				  //$wp_styles->dequeue('style');
				  //$wp_styles->dequeue('slick');
				  $wp_styles->dequeue('wpdesk_wc_shipping_notices_ups');
				  $wp_styles->dequeue('wp-block-library');
				  $wp_styles->dequeue('wc-block-style');
				  $wp_styles->dequeue('jquery-selectBox');
				  $wp_styles->dequeue('wis_font-awesome');
				  $wp_styles->dequeue('custom-skin');
				  $wp_styles->dequeue('custom-style');
				  $wp_styles->dequeue('primary-font');
				  $wp_styles->dequeue('heading-font');

				  if(!in_array("administrator", wp_get_current_user()->roles)){
					  $wp_styles->dequeue("admin-bar");

				  }
				  foreach($g_fonts as $font=>$src){
					  $wp_styles->dequeue($font);
					  $wp_styles->remove($font);
				  }

				  remove_action( 'wp_enqueue_scripts', 'oxygen_wp_head', 100 );
				  add_action( 'wp_enqueue_scripts', function(){
					  _deprecated_oxygen_custom_css();
				  }, 100 );
				  $this->remove_g_fonts();
			  }
      }

	/* *
	 * End of Today's modifications
	 * Remove optional text at the end of the  subscribe
	 * */
	public function remove_optionals( $field, $key, $args, $value ) {
	    
	    if( is_checkout() && ! is_wc_endpoint_url() ) {
	        $optional = '&nbsp;<span class="optional">(' . esc_html__( 'optional', 'woocommerce' ) . ')</span>';
	        $field = str_replace( $optional, '', $field );
	    }
	    return $field;
	}

	/* *
	 * Get payment methods that are enabled
	 * */
	public function bhd_payment_methods(){
		$gateways_obj 		= new WC_Payment_Gateways(); 
		$enabled_gateways 	= $gateways_obj->get_available_payment_gateways();
		
		return $enabled_gateways;
	}

	/*******
	 * Change the order data
	 ******************************/
	 public function changing_order_data_before_payment( $order_id, $posted_data, $order ){
		 if( empty( $order ) ){
		 	$order 	= wc_get_order( $order_id );
		 }
		 update_post_meta( $order->get_id(), "shipping_phone", get_option("bhd_shipping_phone",""), true );
	 }

	/* *
	 * Woocommerce Redirection
	 * */
	public function bhd_woo_templates( $template, $template_name, $template_path ) {
	     global $woocommerce;
	     $_template = $template;
	     if ( ! $template_path ) 
	        $template_path = $woocommerce->template_url;
	 
	     $plugin_path  = untrailingslashit( plugin_dir_path( __FILE__ ) )  . '/woocommerce/';
	 
	    // Look within passed path within the theme - this is priority
	    $template = locate_template(
		    array(
		      $template_path . $template_name,
		      $template_name
		    )
	   	);
	 
	   if( ! $template && file_exists( $plugin_path . $template_name ) )
	    	$template = $plugin_path . $template_name;
	 
	   if ( ! $template )
	    $template = $_template;
		
	   return $template;
	}

	/* *
	 * Remove unnecessary billing fields
	 * */
	public function bhd_slack_billing($fields){
		$billing_email = $fields["billing"]["billing_email"];
		$billing_phone = @$fields["billing"]["billing_phone"];

		
		$fields["billing"] 					= [];

		$fields["billing"]["billing_email"]  = $billing_email;
		$fields["shipping"]["billing_phone"] = $billing_phone;
		

		foreach ($fields["shipping"] as $key => $value) {
			$old_label 	= @$fields["shipping"][$key]["label"];
			$fields["shipping"][$key]["label"] = "";
			
			if( in_array( $key, ["shipping_first_name", "shipping_last_name", "shipping_company"])){
				$fields["shipping"][$key]["placeholder"] = $old_label;
			}else{
				if( $key == "billing_phone" ){
					$fields["shipping"][$key]["placeholder"] = __("Shipping phone", "woocommerce");
				}
			}	
		}
		$fields["billing"]["subscribe"]= array("label"=>"Subscribe to our newsletter for offers and new launches. By signing up to our newsletter you are agreeing to our privacy policy", "name"=>"subscribe", "type"=>"checkbox");
		
		return $fields;
	}
	
	/* *
	 *  Some fields are being post-added, so undo them
	 * */
	public function bhd_further_slack_fields( $fields ){
		return $fields;
	}
	
	/* *
	 * Validations here
	 * */
	public function validate_data( $data ){
		foreach( $data as $key => $value ){
			if( is_string($value)){
				$data[$key] = sanitize_text_field( $value );
			}
			elseif( is_email( $value )){
				$data[$key] = sanitize_email( $value );
			}
			else{
				$data[$key] = wc_clean( $value );
			}
		}
		return $data;
	}
	
	
	/* ******************
	 * Capture data when it is posted
	 ************/
	public function bhd_get_posted_data( $data ){
		
		if( !array_key_exists("shipping_country", $data) || empty( $data["shipping_country"]) ){
			$data                   = $this->validate_data( $_POST );
		}

		if( array_key_exists("billing_phone", $data) || !empty( $data["billing_phone"]) ){
			update_option("bhd_shipping_phone", $data["billing_phone"]);
		}
		
		$data["billing_first_name"] = $data["shipping_first_name"];
		$data["billing_last_name"]  = $data["shipping_last_name"];
		
		$billing_email              = $data["billing_email"];
		$subscription               = $data["subscribe"];
		
		if( $subscription ){
			$user = register_new_user( $billing_email, $billing_email );
			
			if( !is_wp_error( $user ) ){
				wc_add_notice( "Thanks for subscribing to our newsletter", "notice");
			}
		}
		
		return $data;
	}

	/* **********
	* Get shipping methods based on the zone
	* */
	public function get_zone_free_instance(){

		# Get zone as associated to the package
		$packages       = WC()->cart->get_shipping_packages();
		$zone           = WC_Shipping_Zones::get_zone_matching_package( $packages[0] );

		# Get necessary data from the zone methods array
		$zone_methods   = array_flip(
								array_unique(
										wp_list_pluck( $zone->get_shipping_methods(), "id", "instance_id" )
									)
							);

		# Check if we have "free_shipping" set, or set it false
		$free 			= array_key_exists( "free_shipping", $zone_methods )?
						"free_shipping:".$zone_methods["free_shipping"]:false;

		# Give the results back
		return $free;

	}

	/* *****************************
	 * Notify customer of free shipping purchase qualification
	 ***********/
	function my_add_notice_free_shipping() {

		$free_instance 			  = $this->get_zone_free_instance();

		# Return if we have no free shipping instance
		if( !$free_instance ){
			return false;
		}

		
		$free_shipping_settings   = get_option('woocommerce_'. str_replace(":", "_", $free_instance).'_settings', []);

		$amount_for_free_shipping = $free_shipping_settings['min_amount'];

		$cart 					  = WC()->cart->get_subtotal();

		$remaining                = $amount_for_free_shipping - $cart;

		

		if( $amount_for_free_shipping > $cart ){

			$notice = sprintf(
						"Ajoutez &nbsp; %s &nbsp;&mdash; produits supplémentaires pour obtenir la livraison gratuite&nbsp;%s",
						wc_price($remaining),
						"<i style='font-size:2em' class='fa fa-truck' aria-hidden='true'></i>"
						);
		
			//wc_add_notice( $notice , 'notice' );
			wc_print_notice( $notice, "notice");
		}

		return true;

	}


}


/* *
 * Now fire it out
 * */
new Bhd_Checkout;
