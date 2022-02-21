<?php

/* *
 * Plugin Name: 	   	Cog Local Pickup Extender
 * Plugin URI:  	   	http://cogtests.codenug.com/cog-local-pickup-extender
 * Description: 	   	Extend the functionality of the local pickup plus plugin.
 * Version: 	   		1.8.0
 * Author: 	        	Amin Matola
 * Author URI:      		http://codenug.com
 * License:         		GPL v3 or later
 * License URI:     		https://www.gnu.org/licenses/gpl-3.0.html
 * */



class Cog_Local_Pickup_Master{
	
	#Plugin Data
	protected $version;

	protected $author;

	# Notice store
	protected $notices;

	# Store Current wordpress user
	protected $user;

	# Store the current customer
	protected $customer;

	# store the current customer id
	protected $customer_id;

 	# Store orders for this customer
	protected $customer_orders;

	# Store orders for this customer
	protected $customer_last_order;

	# store customer's last order
	protected $customer_last_order_id;



		

	/**
	 * Set the scope variables and activation hooks
	 **/
	private function __construct(){
		# Plugin Data
		$this->version 		= "1.8.0";
		$this->author  		= "AMIN MATOLA";		

	
		# Check if the user is calling the file directly
		defined("ABSPATH") or die(cog_generate_forbidden());
		
		# load the plugin dependancies
		$this->load_dependancies();

		# initialize the view handler (CSS/JS) used in "wp_enqueue_scripts"
		$this->view_handler = new Cog_View_Handler();

		# Check if the environment is alright
		$this->fire_activation_hook();

		# fire up the hooks for this class
		$this->manage_actions();
		$this->manage_filters();
				

	}

	/**
	 * Check if the environment is correctly set for this plugin
	 * Load the necessary files if they're not available
	 **/
	private function load_dependancies(){
		if(! defined("COG_PLUGIN_URL")){
			require_once( dirname(__FILE__)."/includes/helpers.php" );
			require_once( dirname(__FILE__)."/includes/constants.php" );
			require_once( dirname(__FILE__)."/templates/local-pickup-admin-notices.php" );	
			require_once( dirname(__FILE__)."/includes/view-handler.php" );
			require_once( dirname(__FILE__)."/includes/template-controller.php" );
			require_once( dirname(__FILE__)."/includes/checkout.php" );
		}
	}

	/**
	 * Run the activation
	 **/
	private function fire_activation_hook(){
		# Confirm the User that plugin is activated
		cog_activation_hooks( __FILE__ );		
	}


	/* *
	 * Add necessary action hooks for this plugin, separate them from filter hooks
	 * */ 
	public function manage_actions(){
		
		# Activation hook when woocommerce starts
		add_action( "woocommerce_init", array( $this, "cog_woocommerce_init" ) );

		# call action for the function that adds css or javascript
		add_action( "wp_enqueue_scripts", array($this->view_handler, "load_css_and_js") );

		# Use thank you hook to process order after all.
		remove_action( 'woocommerce_thankyou', 'action_woocommerce_thankyou', 10, 1 ); 
		add_action( "woocommerce_thankyou", array($this, "process_order"), 10, 1 );

		# Process order when just fresh
		add_action( 'woocommerce_checkout_order_processed', array($this, 'changing_order_data_before_payment'), 10, 3 );  

		# Add ajax action
		//add_action( "wp_ajax_change_method", array($this, "change_shipping_method")); 
		//add_action( "wp_ajax_nopriv_change_method", array($this, "change_shipping_method")); 
		//add_action( "woocommerce_package_rates", array($this, "change_shipping_method"), 10,2); 
	}

	/* *
	 * Process ajax request
	 * */
	function change_shipping_method($rates, $package){
		
		return $rates;

	}

	/* *
	 * This function will hold all the filter hooks, seperating them from action hooks
	 * */
	public function manage_filters(){

		# hook for woocommerce manipulation
		add_filter( 'woocommerce_locate_template', 'locate_cog_template', 1, 3 );

	}


	/* *
	 * This function has been included in the blueprint, it may be used in the future releases of this plugin
	 * */
	public function cog_woocommerce_init(){
		global $wpdb;
			
		# Initialize the customer class's necessary functions, as they require woocommerce to be active.
		$this->init();

		# Set out the checkout hooks
		Extender_Checkout_Handler::set_checkout_hooks();
			
	}

	/* *
	 * Process and change order data before payments
	 * */
	public function changing_order_data_before_payment( $order_id, $posted_data, $order ){
		
 		$this->process_order($order_id);

	}
		
	/* *
	 * This function is just in the blueprint, perhaps it will be used in the coming versions of this plugin.
	 * */ 
	public function process_order( $order_id ){
		global $order, $processed, $post;


		$customer 			= new WC_Customer(WC()->session->get_customer_id());
		
		# get the order of the given id
		$ord 				= !empty($order_id)? wc_get_order( $order_id ): $customer->get_last_order_id();
		$option 			= get_option( "cog_pickup_point", "" );

		# Update order shipping method if local pickup is selected
		if(empty($option) || empty($ord)){
			return false;
		}
		
		# will hold the local pickup data
		$local_pickup_data 		= array();	

		# get the order item if it is available: This will no longer be required
		#$found_local_pickup_plus 	= cog_is_local_pickup_plus_available( $order_id );
		$prepared_order_shipping_data= prepare_local_pickup_data( $ord->get_id() ); 

		# get local pickup data if local pickup method is available
		if($prepared_order_shipping_data){
				
			//$tomorrow  = mktime(0, 0, 0, date("m")  , date("d")+1, date("Y"));
			$current_shipping_item 	= cog_set_local_pickup_data( $prepared_order_shipping_data, $option );
				
			# Now get the shipping item to be returned incase the method didnt work
			$data 			= cog_get_local_pickup_data( $current_shipping_item ); 
		}

			# set the shipping data only if the "local pickup data" is available
			!empty( $data )? cog_set_shipping_data( $ord, $data ) : "";
				
	}

	/* *
	 * Process order if payment method is paypal
	 * */
	public function process_paypal_order( $order_data ){
			
		# Then call the process order function for this paypal order
		process_order($this->get_customer_last_order_id());

	}

	/* *
	 * Get the instance of this very class to initialize the plugin.
	 * */
	public static function initialize(){
		return new self();
	}

	/* *
	 * Customer part, this deals with the customer functionality
	 * */

	# Initialize all necessary variables 
	public function init( $customer_id = null ){
		global $current_user;

		# Set the user to a current user
		$this->user 	= $current_user;

		# start by constructing/initializing this customer's instance
		$this->init_customer( $customer_id );

		# Then set his/her orders 
		$this->calculate_orders();

	}

	# This function will initialize all the necessary customer variables
	public function init_customer( $customer_id ){
		if(!is_null($customer_id) && !is_bool($customer_id)){
			$this->customer_id 			= $customer_id;
			$this->customer 			= new WC_Customer( $customer_id );

		}
		elseif(isset(WC()->session)){
			if(!WC()->session->has_session())
				WC()->session->set_customer_session_cookie(true);
			$this->customer_id 			= WC()->session->get_customer_id();
			$this->customer 			= new WC_Customer($this->customer_id);
		}
		else{
			$this->customer 			= new WC_Customer($this->user);
			$this->customer_id 			= $this->customer->get_id();
		}	
	}

	/* *
	 * This function checks if the current customer has orders
	 * */
	public function has_orders(){
		return !empty($this->customer_orders);
	}

	/* *
	 * Set the orders for the current customer
	 * */
	public function calculate_orders(){
		$this->customer_orders 			= 	$this->customer->get_order_count();

	}

	/* *
	 * Get all the orders this customer has
	 * */
	public function get_customer_orders(){
		return $this->customer_orders;
	}

	/* *
	 * Retrieve the last order for this customer
	 * */
	public function get_customer_last_order(){
		return !empty($this->customer)?$this->customer->get_last_order():null;
	}

	/* *
	 * Retrieve customer's last order id
	 * */
	public function get_customer_last_order_id(){
		$this->customer_last_order 	= $this->get_customer_last_order();
		$this->customer_last_order_id 	= $this->customer_last_order->get_id();
		return $this->customer_last_order_id;
	}

}



/* *
 * Set the plugin ablaze
 * */
Cog_Local_Pickup_Master::initialize();
