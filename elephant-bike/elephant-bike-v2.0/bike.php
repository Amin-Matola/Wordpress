<?php

/* *
 * Plugin Name: 		Elephant Bike
 * Plugin Version: 		2.0
 * Description:			Plugin for Elephant Bike Generation
 * Author: 			Amin Matola
 * Author URI: 			http://codenug.com/webmaster
 * Plugin URI: 			http://cogtests.codenug.com/bike-plugin.zip
 * */

class Bike_Generator{

	public $bike_name;

	public $bike_data;

	public $bike;

	public $bike_uri;

	public function __construct(){
		
		$this->load_admin();
		$this->add_hooks();
		$this->add_codes();
		
	}

	
	/* *
	 * Load the styles and scripts to the admin page
	 * */
	public function load_admin_views(){
		wp_enqueue_script( "bike_scripts", plugin_dir_url( __FILE__ )."assets/admin/bike.js", [], "1.0", true );
		wp_enqueue_style( "bike_styles", plugin_dir_url( __FILE__ )."assets/admin/bike.css", [] );
	}
	
	/* *
	 * Initialize the admin page
	 * */
	public function load_admin(){
		require_once(plugin_dir_path( __FILE__ )."admin/admin.php");
	}
	
	/* *
	 * Load views for this client
	 * */ 
	public function load_client_views(){
		wp_enqueue_script( "elephant_bike", plugin_dir_url( __FILE__ )."assets/bike.js", [], "1.0", true );
		wp_enqueue_script( "elephant_bike_ajax", plugin_dir_url( __FILE__ )."assets/bike-ajax.js", [], "1.0", true );

		$initial 		= get_option("bike_data", false)?get_option("bike_data", false)["initial"]:"#F3E03B";

		wp_localize_script( 'elephant_bike_ajax', 'elephant_ajax', 
			array( 'ajax_url' => admin_url( 'admin-ajax.php' ), "site_url"=>get_site_url(), "initial" => $initial));

		wp_localize_script( 'elephant_bike', 'elephant_initial',  array( "initial" => $initial));
	}
	
	/* *
	 * Load elephant bike to this class
	 * */
	public function load_bike($bike = []){
	    $ops            = [];
		if(empty($bike)){
			$bike 		= $this->bike;
		}
	
		if(is_object($bike)){
    		$attrib 			= 	$bike->get_attributes();
    		$option 			= 	trim(get_option("initial_colour", ""), "#");
    
    		$ops 				=   $bike->get_available_variations();
		}
    	
    	$this->bike_data 	    =	$this->prepare_template("bike.php",	["bike"=>$this->map_variation_to_id($ops)]);

		

	}

	public function show_data($data){
		foreach ($data as $key => $value) {
			# code...
		}
	}


	/* *
	 * Set the hooks up and running
	 * */
	public function add_hooks(){
		register_activation_hook( __FILE__, array($this, "set_bike_data") );
		register_deactivation_hook( __FILE__, array($this, "unset_bike_data") );

		add_action( "wp_enqueue_scripts", array($this, "load_client_views" ));
		add_action( "init", array($this, "init" ), 10);
		add_action( "init", array($this, "set_bike_data" ), 20);
		add_action( "woocommerce_thankyou", array($this, "bikejs" ));

		add_action( "wp_ajax_add_bike", array($this, "bikejs" ));
		add_action( "wp_ajax_nopriv_add_bike", array($this, "bikejs" ));
		add_action( "woocommerce_loop_product_link", array($this, "generate_custom_bike_link" ), 10, 2);
		add_action( "woocommerce_loop_add_to_cart_link", array($this, "add_to_cart_link" ), 10, 2);

	}


	/* *
	 * Initialize the whole thing
	 * */
	public function init(){
	    
		$this->get_admin_instance()->admin_hooks();
		$this->set_bike();

		$bike 		= $this->get_bike();

		if(!get_option("elephant_bike_id", "")){
			if(is_object($bike)){
			    update_option("elephant_bike_id", $bike->get_id());
			}
		}
        
        if(!is_admin()) {
		    $this->load_bike($this->bike);
        }

	}

	

	/* *
	 * Add the bike to the cart
	 * */
	public function add_to_cart($id, $qty, $variation_id, $variation_data){
		
		$item_key 		= WC()->cart->add_to_cart($id, $qty, $variation_id, $variation_data );
		
		if(is_wp_error( $item_key ) || $item_key == false){
			return "We were unable to add <span style='colour:orange'>{$_POST['colour']} ".wc_get_product( $id )->get_name()."</span> to the cart. Please try different combination.";
		}
		return true;
	}


	/* *
	 * add to cart link changing
	 * */
	public function add_to_cart_link( $link, $product ){
		if($product->get_id() == get_option( "elephant_bike_id", "" )){
			$site_url 	= get_site_url()."/";
			$slg 		= get_option("bike_data", []);
			
			$complete   = !empty($slg) ? $slg["slug"] : "elephant-bike-3";
			$site_url   .= $complete;

			$link = sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="button product_type_%s">%s</a>',
        		esc_url( $site_url ),
        		esc_attr( $product->get_id() ),
        		esc_attr( $product->get_sku() ),
        		esc_attr( isset( $quantity ) ? $quantity : 1 ),
        		esc_attr( $product->get_type() ),
        		esc_html( $product->add_to_cart_text() )
    				);
		}

		return $link;
	}

	public function build_custom_query($p){
		$r = "";
		foreach ($p as $key => $value) {
			$r .= "$key = $value<br>";
		}
		return $r;
	}

	/* *
	 * Load the admin class instance for use in this plugin setup
	 * */
	public function get_admin_instance(){
		return Bike_Admin::get_instance();
	}

	/* *
	 * Internal Use, get the methods connected to object
	 * */
	public function get_methods($ob){
		return get_class_methods(get_class($ob));
	}



	/* *
	 * Generate add to cart html
	 *
	 * @param Item - The name of item to be added to cart; Default Null
	 * @param colour- Bike colour to be added to the cart; Default Null
	 *
	 * Return Formatted html for the bike.
	 * */
	public function generate_cart_html($item = "", $colour = null){

		return "Item <b>$colour $item</b> added to cart, <a style='color:orange;cursor:pointer' href='".wc_get_cart_url()."'>View Cart</a>";
	}

	/* *
	 * Generate bike additional data
	 *
	 * @param Array from - Data to generate bike data from
	 * 
	 * Return Array Filtered data
	 * */
	public function generate_additional_bike_data($from, $current = "colour"){
		$additional_data 	= [];
		foreach ($from as $key => $value) {
			if(strpos(strtolower($key), strtolower("attribute_")) === 0){ // && $key != "attribute_$current") {
				$additional_data[$key] = $value;
			}
		}
		return $additional_data;
	}

	/* *
	 * Get the quantity of the specified variation
	 * */
	public function get_variation_stock_quantity($variation){
			$_v 	= new WC_Product_Variation($variation);
			return $_v->get_stock_quantity();
	}


	/* *
	 * Redirect the bike to the bike project page
	 * */
	public function generate_custom_bike_link($link, $product){
		
		if($product->get_id() == get_option( "elephant_bike_id", "" )){
			$link 		= get_site_url()."/";
			$slug 		= get_option("bike_data", []);
			
			$appendix 	= !empty( $slug ) && !empty( $slug["slug"] ) ? $slug["slug"] : "elephant-bike-3";
			
			$link .= $appendix;
		}

		return $link;
	}

	/* *
	 * Get the data of the bike
	 * */
	public function get_bike_data(){
		return $this->bike_data;
	}

	/* *
	 * Get the bike woocommerce product
	 * */
	public function get_bike(){
		
		return $this->bike;

	}
	
	/* *
	 * Get the id of the chosen bike colour
	 * */
	public function get_item_id($item, $what = "colour"){
		$bike 		= $this->get_bike();
		$varis 		= $bike->get_available_variations();
		$id 		= null;
		
		foreach ($varis as $key => $value) {
			$atts 	= $value["attributes"];

			if($atts["attribute_$what"] === $item || $value["variation_id"] === $item){
				if(is_int($colour)){
					$id = $atts["attribute_$what"];
				}
				else{
					$id = $value["variation_id"];
				}
				break;
			}
			
		}
		return $id;
	}

	/* *
	 * Process jquery ajax data
	 *
	 * @param Array Optional $posted_data - Data to be processed.
	 * 
	 * Type: Void
	 * */
	public function bikejs($posted_data = []){
		global $post;

		@$colour 				= $_POST["attribute_colour"];
		$bike 					= $this->get_bike();
		$item_name 				= $bike->get_name();
		
		if(isset($_POST["bike_colour"]) && empty($colour)){
			$c 					= $_POST["bike_colour"];
			$stock_quantity 	= $this->get_variation_stock_quantity($this->get_item_id($c));

			if($stock_quantity) {
				echo json_encode( ["bike_id" => $this->get_item_id($c), "message" => "Congrats! $stock_quantity $c {$item_name}s are available for sale."] );
				return true;
			}else{
				if($this->get_item_id($c)) {
					echo json_encode( ["bike_id" => "Sorry! We have inadequate ".$c." bikes in stock."] );
					return true;
				}else{
					echo json_encode( ["bike_id" => null] );
					return true;
				}
			}
			
		}
		
		if(empty($colour)){
			echo json_encode( ["bike_id" => "Please select colour for the bike."] );
			return false;
		}
		
		@$colour_id 			= $this->get_item_id($colour);

		$_POST["variation_id"] 	= $colour_id;
		$_POST["product_id"] 	= $bike->get_id();

		

		if($colour_id == false || !$this->get_variation_stock_quantity($colour_id)){
			echo json_encode(["cart_message" => "Sorry, $colour $item_name is not available."]);
			return true;
		}
		
		$results 				= $this->add_to_cart($bike->get_id(), 1, $colour_id, $this->generate_additional_bike_data($_POST));
		if(gettype($results) != "boolean"){
			echo json_encode(["cart_message" => $results]);
			return true;
		}

		echo json_encode(["cart_message" => $this->generate_cart_html($item_name, $colour)]);
	}


	/* *
	 * Add code for this elephant bike
	 * */
	public function add_codes(){
		add_shortcode( "elephant_bike", array($this, "get_bike_data") );
	}



	/* *
	 * Set the bike woocommerce product
	 * */
	public function set_bike(){
		$products 		= new WC_Product_Query(array(
			"sku" => empty(get_option("bike_data",[])["sku"])?"elephant_bike":get_option("bike_data",[])["sku"]
		));

        if(count($products->get_products()) < 1){
            return false;
        }
		$this->bike = $products->get_products()[0];
		if(is_object($this->bike) && count($this->bike->get_meta("_bike_slug",[])) < 1){
			$this->bike->update_meta_data("_bike_slug", "elephant-bike");
		}

	}

	/* *
	 * Set the initial colour for this bike
	 * */
	public function set_bike_data(){
		$bike 		  = $this->get_bike();


		delete_option( "bike_data" );
		$intersection = count(array_intersect(["initial", "slug"], array_keys(get_option("bike_data", []))));

		if(!is_object($bike) || count($bike->get_meta("_bike_slug",[])) < 1 || $intersection === 2){
			
			return false;
		}

		$bike_data 	= ["name" 	 	=> $bike->get_name(),
						"slug" 	 	=> get_metadata( "post", $bike->get_id(), "_bike_slug", true ), 
						"sku" 	 	=> $bike->get_sku(), 
						"initial"	=> get_option("initial_colour", ""),
						"bike_url"	=> site_url()."/".get_metadata( "post", $bike->get_id(), "_bike_slug", true )
						];
		update_option( "bike_data", $bike_data, true );
	}

	/* *
	 * Remove the data for this bike when being deactivated
	 * */
	public function unset_bike_data(){
		delete_option( "elephant_bike_id" );
		delete_option( "bike_data" );
		delete_option( "initial_color" );
	}


	/* *
	 * Prepare the template by passing options
	 *
	 * @param Text  name 	- Name of the template to prepare
	 * @param Array options - Options to be appended to the GET Request
	 *
	 * Return The Body of the requested page.
	 * */
	public function prepare_template($name, $options = []){

		$request 	= wp_remote_get( 
						plugin_dir_url( __FILE__ )."/templates/".$name,
						[
						    'body'        => $options,
						    'headers'     => [
						        'Content-Type' => 'application/json',
						    ],
						    'timeout'     => 60,
						    'redirection' => 5,
						    'blocking'    => true,
						    'httpversion' => '1.0',
						    'sslverify'   => false,
						    'data_format' => 'body',
						]
						);
		return wp_remote_retrieve_body( $request ); 
	}


	/* *
	 * Extract all id's of all available variations
	 * */
	public function map_variation_to_id($available_vars){
	    
	    if(empty($available_vars) || !is_object($this->bike)){
	        return [];
	    }
	    
		$attributes 	= array_keys($this->bike->get_attributes());
		
		$results 		= array_fill_keys(array_values($attributes), []);

		foreach ($available_vars as $key => $value) {

			$attrs 				= $available_vars[$key]["attributes"];
			//$id 				= $available_vars[$key]["variation_id"];

			foreach ($results as $k => $v) {
				if(!in_array($attrs["attribute_$k"], $results[$k])){
					$results[$k][] = $attrs["attribute_$k"];
				}
			}

		}

			
			

		return ['id'=>$this->bike->get_id()]+$results;
		
	}


}

new Bike_Generator();
