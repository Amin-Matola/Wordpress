<?php


/* *
 * This is he collection of functions that makeup the plugin
 *
 * First,  No direct access is allowed... Restrict it!
 * */
function cog_generate_forbidden(){
      $ff = $_SERVER["REQUEST_URI"];
      $fp = explode("/", $ff)[1];
      $pt = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
      $mp = $pt.$_SERVER["HTTP_HOST"]."/".$fp;

      # Return html data
      $html 	= "<h1 class='error' style='color:red; text-align:center'>Sorry, That is not Allowed</h1>";
      $html  .= "<p style='margin-top:50px; text-align:center;font-size:1.2em'>Please go to the <a href='".$mp."'>Home Page</a> to get started and have what you are looking for.</p>";

      return $html;
}


/* *
 * Load templates custom way
 * */
function cog_shipping_calculator( $button_text = '', $icon = false ) {
        if ( 'no' === get_option( 'woocommerce_enable_shipping_calc' ) || ! WC()->cart->needs_shipping() ) {
            return;
        }
        wp_enqueue_script( 'wc-country-select' );
        wc_get_template(
            'cart/shipping-calculator.php',
            array(
                'cog_text' => $button_text,
                'cog_icon' => $icon
            )
        );
}




/* *
 * Check if local pickup plus is available
 * */


function cog_is_local_pickup_plus_available($order_id){
			$order 		= wc_get_order($order_id);
			$local_pickup_available = false;
			foreach( $order->get_shipping_methods() as $item_id => $item ){
					$method_id 		= explode(":", $item->get_method_id())[0];
					if(strtolower($method_id) == "local_pickup_plus"){
							$local_pickup_available = $item;
							break;
					}
			}

			return $local_pickup_available;

}

/* *
 * Get the order item and process it according to the needs
 * */
function prepare_local_pickup_data($order_id = ""){

      $method 		     = wc_local_pickup_plus()->get_shipping_method_instance();

      #  Get Current Shipping Customer
      $customer 		   = new WC_Customer(WC()->session->get_customer_id());


      # Get an order for this customer in the session
      $c_order 		     = empty($order_id)? $customer->get_last_order() : wc_get_order($order_id);

      # Get the shipping line for this order
      $c_shipping 	   = $c_order->data["shipping_lines"];

      # Create a new rate for the local pickup
      // $local_pick_rate = new WC_Shipping_Rate($method->get_rate_id());
      // $local_pick_rate->set_label( $method->get_method_title() );
      // $local_pick_rate->set_method_id($method->get_method_id());

      $found 			= 0;


      foreach($c_shipping as $id=>$c_method){
          $c_shipping[$id]->set_method_title($method->get_method_title());
          $c_shipping[$id]->set_method_id($method->get_method_id());
          $c_shipping[$id]->set_shipping_rate($method->get_rates_for_package(0)["local_pickup_plus"]);
          $c_shipping[$id]->save();
          $found      = $id;
          break;
      }
      $c_order->save();
      return $c_shipping[$found];

}


/* *
 * get the local pickup country
 * */
function cog_get_country($code = ""){
		return !empty($code)? WC()->countries->countries[$code] : false;
}

/* *
 * Get all the products from order items.
 * */
function cog_get_order_products($order){
		$all_products 		= [];

		// Big Oh rule.
		foreach ($order->get_items() as $item) {
			$all_products[] = !empty($item->get_product())? $item->get_product() : $order->get_product_from_item($item);
		}
		return $all_products;
}

/* *
 * If local Pickup is available but location is not set
 * */
function cog_set_local_pickup_data($order_item, $order_id = 0, $location_data = []){
      if(!empty($order_item)){

        $location_data 	 = !empty($location_data)? $location_data : get_option( "cog_pickup_point", "" );
        $location_id  	 = $location_data["location"];
        $pick_date 		 = $location_data["date"];

        $order_object    = $order_item->get_order();
        $object_items 	 = cog_get_order_products($order_object);

        /* *
         * Local Pickup Item Class
         * */
        $pickup_handler	 = new WC_Local_Pickup_Plus_Order_Items();


        if(empty($location_id)){
          return false;
        }

        $location 		 = wc_local_pickup_plus()->get_pickup_locations_instance()->get_pickup_location( $location_id );


        if(empty($location)){
          return false;
        }

        $order_item->update_meta_data("_pickup_location_id", $location_id);

        $order_item->update_meta_data("_pickup_location_address", $location->get_address()->get_array() );
        $order_item->update_meta_data("_pickup_location_name", $location->get_address()->get_name() );
        $order_item->update_meta_data("_pickup_date", $pick_date);
        //$order_item->update_meta_data("pickup_date", $pick_date);

        //$items 		= (array)$order_item->get_meta_data()[0]->get_data()["value"]
        $pickup_handler->set_order_item_pickup_items($order_item, $object_items);
        $order_item->save();
        $order_item->update_meta_data("_pickup_items", $pickup_handler->get_order_item_pickup_items($order_item));
        $order_item->update_meta_data("pickup_items", $pickup_handler->get_order_item_pickup_items($order_item));
        $order_item->save_meta_data();

        $order_item->save();

        return $order_item;


	}
}



/* *
 * Get all class methods
 * */
function cog_class_methods($item){
	  print_r(get_class_methods(get_class($item)));
}


function cog_get_location_instance($location_id){
		return wc_local_pickup_plus()->get_pickup_locations_instance()->get_pickup_location( $location_id );
}

/* *
 * Check functions attached to a specific filter
 * */
function get_filters($name){
    global $wp_filter;
    return $wp_filter[$name];
}


/* *
 * get local pickup data
 * */
function cog_get_local_pickup_data($order = 0){
		$data 					= array();
		if($order != 0){
			$item 				= $order;
	   		$location_id        = $item->get_meta('_pickup_location_id');
	   		$location_name      = $item->get_meta('_pickup_location_name');
	
		    $pick_address   	= $item->get_meta('_pickup_location_address'); // Array

		    //return false;
		    $address_1 			= $pick_address['address_1'];
		    $address_2 			= $pick_address['address_2'];
		    $postcode  			= $pick_address['postcode'];
		    $city      			= $pick_address['city'];
		    $state     			= $pick_address['state'];
		    $country   			= cog_get_country($pick_address['country']);
		    $phone     			= $item->get_meta('_pickup_location_phone');

			// add all the necessary pickup data to the array
		    $data 				= compact("address_1", "address_2", "city", "state", "country", "postcode", "phone");
			
			// Additional pickup date
			# $pickup_date        = $item->get_meta('_pickup_date');
			# $pickup_min_hours   = $item->get_meta('_pickup_minimum_hours');
			}
			
			return !empty($data)? $data : false;
	}


/* *
 * The internet can't be trusted, prevention is better than cure.
 * */
function cog_sanitize_data($data){
      $result_data 	= [];

      if(is_array($data)){
        foreach ( $data as $key => $value ) {
          if(is_string( $value )){
            $result_data[ sanitize_key( $key ) ] 	= sanitize_text_field( $value );
          }
        }
      }else{
        $result_data 	= is_string($data) ? sanitize_text_field( $data ) : $data;
      }

      return $result_data;
}

/* *
 * set local pickup data
 * */
function cog_set_shipping_data($order, $data){

      $data 	= cog_sanitize_data( $data );

      if( empty($order) || empty( $data ) ){

        return false;
      }

      $order->set_shipping_address_1(@$data["address_1"]);
      $order->set_shipping_address_2(@$data["address_2"]);

      $order->set_shipping_country(@$data["country"]);
      $order->set_shipping_city(@$data["city"]);
      $order->set_shipping_state(@$data["state"]);
      $order->set_shipping_postcode(@$data["postcode"]);

      add_post_meta( $order->get_id(), "_shipping_phone", @$data["phone"], true );
      $order->save();

      delete_option( "cog_pickup_point" );

      return $order;
}

/* *
 * Used for searching an erray to get index
 * */
function cog_get_index( $needle, $haystack ){
	    return array_search( $needle, $haystack );
}

function get_chosen_methods($instances, $costs){
      $all_methods 		= [];
      foreach( WC()->session->get('shipping_for_package_0')['rates'] as $method_id => $rate ){
        if( WC()->session->get('chosen_shipping_methods' )[0] == $method_id ){
              if($instances){
                if($costs){
                  $all_methods[]= $rate->cost;
                }else{
                $all_methods[$method_id]    = $rate;
              }
              }else{
              $all_methods[$method_id] 		= $rate->label;
            }

       }
      }

      return $all_methods;
}

/* *
 * Set the cost of the shipping rate
 * */
function cog_set_cost($cost){
    $session_rates =& WC()->session->get('shipping_for_package_0')['rates'];

    foreach( $session_rates as $method_id => $rate ){
        if( in_array($method_id, WC()->session->get('chosen_shipping_methods' )) ){
            $session_rates[$method_id]->set_cost($cost);  
       }
      }
}


/* *
 * get all the shipping methods 
 * */
function cog_get_shipping_methods($instances = false, $costs = false){
      $all_methods 		= [];
      foreach( WC()->session->get('shipping_for_package_0')['rates'] as $method_id => $rate ){
  
          if($method_id != wc_local_pickup_plus()->get_shipping_method_instance()->id){
            if($instances){
                if($costs){
                  $all_methods[$method_id]= $rate->cost;
                }else{
                $all_methods[$method_id]    = $rate;
              }
              }else{
              $all_methods[$method_id] 		= $rate->label;
            }
          }
      }

      return $all_methods;
}

/* *
 * Get shipping methods based on the zone
 * */
function get_zone_shipping_methods($instances= false, $costs=false){
    $all_methods    = [];
    $packages       = WC()->cart->get_shipping_packages();
    $zone           = WC_Shipping_Zones::get_zone_matching_package($packages[0]);
    $zone_methods   = $zone->get_shipping_methods();
    $local_pickup   = wc_local_pickup_plus()->get_shipping_method_instance()->id;

    foreach( $zone_methods as $method_id => $method ){
  
        if($method->id != $local_pickup && $method->id != "local_pickup"){
           
           if($instances){
            $all_methods[$method->id] = $method->get_rate_id();
           }
            else{
              $all_methods[$method->get_rate_id()] = $method->title;
            }
        }
      }

      return $all_methods;
}



/* *
 * Catch if, it is being accessed directly
 * */
defined("ABSPATH") or die(cog_generate_forbidden());
