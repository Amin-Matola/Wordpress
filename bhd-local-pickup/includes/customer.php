<?php

/*
 * This class handles the customer data, this class has been added in the main class for ease.
 */

defined("ABSPATH") or die("<h1 style='color:red'>No cheating please...</h1>");

class Cog_Customer{

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

	# Initialize all necessary variables 
	public function init( $customer_id = null ){

		# Set the user to a current user
		$this->user 	= wp_get_current_user();//$current_user;

		# start by constructing/initializing this customer's instance
		$this->init_customer( $customer_id );

		# Then set his/her orders 
		$this->calculate_orders();

	}


	public function init_customer($customer_id){
		if(!is_null($customer_id) && !is_bool($customer_id)){

			$this->customer_id 		= $customer_id;
			$this->customer 		= new WC_Customer( $customer_id );

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
		$this->customer_orders 	= 	$this->customer->get_order_count();

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
		$this->customer_last_order 		= $this->get_customer_last_order();
		$this->customer_last_order_id 	= $this->customer_last_order->get_id();
		return $this->customer_last_order_id;
	}


}
