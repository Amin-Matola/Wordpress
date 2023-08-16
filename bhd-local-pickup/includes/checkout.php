<?php

/* *
 * Handling the checkout data and hooks
 * */
class Extender_Checkout_Handler{

        /* *
         * Custom fields to be used to specify where a shipping method is not a select drop down
         *
         * Return filtered shipping method field.
         * */

        public static function get_customer_country(){
              /*$ip_address 	= WC_Geolocation::get_ip_address();
              if(count(explode(":", (string)$ip_address)) > 1){
              	$ip_address = WC_Geolocation::get_external_ip_address();
              }*/
              $location 		= WC_Geolocation::geolocate_ip("", true, true);

              return $location["country"];
        }


        public static function custom_billing_fields($methods = [1]){
            if(count($methods) > 1){
                $shipping_method 	= array(
                            "shipping_method[0]" => [
                      '				name' 		   => 'cog_shipping_method',
                                'type' 		 => "select",
                              'label' 	   => 'Shipping Method',
                              'title' 	   => 'Shipping Method',
                              'required' 	 => false,
                              'options' 	 => get_zone_shipping_methods(),
                              "id" 		     => "shipping_method_0",
                              "custom_attributes"=>array("data-index"=>"0"),
                              "class"      => ["shipping_method"],
                              "default"    => array_keys(get_zone_shipping_methods())[0]
                            ]);
              }else{

                $shipping_method 	= array(
                            "shipping_method" => [
                      '				name' 		=> 'cog_shipping_method',
                              'type' 		=> "text",
                              'label' 	=> 'Shipping Method',
                              'title' 	=> 'Shipping Method',
                              'required' 	=> false,
                              "class"     => array("shipping_method", "select2","shipping-method"),
                              "custom_attributes" => ["disabled" => true],
                              "default"   => array_values(get_zone_shipping_methods())[0]
                            ]);
              }
              return $shipping_method;

        }


        /* *
         * Inserts a fields on a specified position
         * 
         * @param $what 	A field to be inserted
         * @param $position A Location where to add the field.
         * @param &$array 	A reference to the array to be modified
         * */
        public static function push_field( $what, $position, &$array){
                if (is_int($position)) {
                    array_splice($array, $position, 0, $what);
                } else {
                    $pos   = array_search($position, array_keys($array));
                    $array = array_merge(
                        array_slice($array, 0, $pos+1),
                        $what,
                        array_slice($array, $pos)
                    );
                }
        }

        /* *
         * Modifies checkout fields
         *
         * @param $fields woocommerce checkout fields to be modified
         * Return Modified woocommerce fields
         * */

        public static function modify_checkout_additional_fields($fields){

                $shipping_method  	= self::custom_billing_fields(cog_get_shipping_methods());
                self::push_field($shipping_method, "shipping_country", $fields["shipping"]);



                foreach( $fields["shipping"] as $k => $v ){

                  if($k != "shipping_country" && $k != "shipping_address_1" && $k != "shipping_address_2" && $k != "shipping_method"){
                    $fields["shipping"][$k]["label"] 			= "";
                    if($k != "shipping_method")
                    $fields["shipping"][$k]["placeholder"]= $v["title"];
                    $fields["shipping"][$k]["title"] 			= "";
                  }

                }


                foreach( $fields["billing"] as $k => $v ){

                    if($k != "billing_country" && $k != "billing_address_1" && $k != "billing_address_2"){
                      $fields["billing"][$k]["label"] 			= "";
                      $fields["billing"][$k]["placeholder"] 		= $v["title"];
                      $fields["billing"][$k]["title"] 			= "";
                    }


                    if($k == "billing_phone"){
                      $fields["billing"][$k]["label"] 			= "Contact Information";
                    }

                    $fields["billing"][$k]["required"] 				= false;

            }


            $contacts			= ["shipping_phone"=>[
                              "name" 			=> "mobile",
                              "type" 			=> "text",
                              "placeholder" 	=> "Phone * Your phone number will be used for order related queries",
                              "label" 		=> "Contact Information",
                              "required" 		=> 1,
                            ],
                            "shipping_email"=>[
                              "name" 			=> "email",
                              "type" 			=> "email",
                              "placeholder" 	=> "Email *",
                              "required" 		=> 1
                          ]];

            $pickup_data 	= [
                              "pickup_point" => [
                              "name" => "pickup_point",
                              "type" => "select",
                              "options" => array(""=>"select pickup point")+apply_filters( "cog_get_local_addresses", "" ),
                              "class" => array("selectpicker", "select2", "pickup-location-field", "pickup-location-lookup"),
                              "custom_attributes" => ["data-live-search" => true]
                            ]];

                        /*"arrival_date" => [
                          "label"  => "Opening Times: Monday - Sunday: 9am - 5:30pm",
                          "name"   => "arrival_date",
                          "type"   => "date",
                          "class"  => array("arrival_date", "cog_arrival_date_field"), //pickup-location-appointment-date-alt", "pickup-date", "hasDatePicker", "pickup-location-calendar-icon"),
                          "placeholder"=> "DD-MM-YYYY",
                          "default" => date("Y-m-d"),
                          "custom_attributes" => array(
                            "format"  => "DD-MM-YYYY")
                        ]]; */

            $fields["shipping"] += $contacts;
          
            return $fields;
          }


          /* *
           * Get and print the posted data for adminstrator
           * */
          public function print_posted_data( $data ){
                $all_ 	   = "";
                foreach($data as $key => $value){
                  $all_    .= "$key = $value<br>";
                }
                wc_add_notice($all_, "error");
          }


          /* *
           * Checks the billing fields if no data is available on some fields
           * */
          public function check_billing_fields( &$data ){
                $errs 			            = "";
                foreach ( $data as $key => $value ) {
                  if(strtolower( $key ) != "billing_address_2" && 
                    strtolower( $key ) != "billing_state" && 
                    strtolower( $key ) != "shipping_state" &&
                    strtolower( $key ) != "billing_address_2"){
                    if( empty(trim( $data[$key])) && in_array("billing", explode("_", $key)) ){
                      $errs		          .= "<b>".str_replace("_", " ", $key)."</b> is a required field.<br>";
                    }
                  }else{
                      $data[$key]["required"] = false;
                    }

                }

                return $errs;

          }

          public static function set_custom_validation($fields, $errors){

          }

          public static function before_order_notes($checkout){

          }

          /* *
           * Get the addresses for the pickup
           * 
           * Return all available pickup location addresses
           * */
          public function get_addresses(){
                $all_locs 		      = [];
                $all_loc_objects    = [];
                if(class_exists("WC_Local_Pickup_Plus")){

                  $pick_loc 	      = wc_local_pickup_plus()->get_pickup_locations_instance()->get_pickup_locations();
                  foreach ($pick_loc as $key => $value) {

                    $all_locs[$key] = strip_tags($value->get_address()->get_address_line_1()." ".$value->get_address()->get_address_line_2());
                  }
                }

                return $all_locs;

          }

          /* *
           * Custom validation, not used because it delays... 
           * instead, 
           * used process_posted_data() 
           * */
          public function manage_custom_validation($posted, $errors){
                #$errors->add("validation", "message")
          }

          /* *
           * Custom starts with check, rather that substr etc to reduce execution processes.
           * Checks if a given parent string starts with what string.
           * @param $what: The string to be checked with
           * @param $parent The parent string to check from
           * Return true if the $parent string connected with _ starts with $what
           * */
          public static function startswith($what, $parent){
                return (strpos(strtolower($parent), strtolower($what)) === 0);

          }


          /* *
           * If the local pickup is available, then there is no need to validate shipping data,
           * Simply set it to the current billing details
           * @param &fields reference to the $fields location
           * Return N/A
           * */
          public static function set_shipping_data(&$fields){

                foreach($fields as $key => $value){
                  if(self::startswith("shipping", $key)){
                    $key_data 	 = explode("_", $key);
                    $key_data[0] = "billing";

                    $fields[$key]= $fields[implode("_", $key_data)];
                    
                  }
                }
          }

          public static function get_valid_date($date_str){
                return date("Y-m-d", strtotime($date_str));
          }

          /* *
           * Process woocommerce posted data
           * @param $posted_data The data from $_POST to be processed.
           *
           * Return processed posted data.
           * */
          public function process_posted_data($posted_data){

                  $pick_point 		             = !empty($posted_data["pickup_point"])?$posted_data["pickup_point"] : $_POST["pickup_point"];
                  $arrival_date   	           = !empty($posted_data["arrival_date"])?$posted_data["arrival_date"] : $_POST["arrival_date"];

                  $posted_data["pickup_point"] = $pick_point;
                  $posted_data["arrival_date"] = self::get_valid_date($arrival_date);

                  if(!empty($pick_point)){

                    
                          

                          $errs = trim(self::check_billing_fields($posted_data));


                          if(!empty($errs)){
                              $errs .= "<hr>Please fill the fields above, or check <b>Use my shipping details for billing</b>";
                              wc_add_notice($errs, "error");
                          }

                          self::set_shipping_data($posted_data);
                          //self::print_posted_data($posted_data);

                          if(empty($posted_data["arrival_date"])){
                            wc_add_notice("Please set your arrival date in japan, it is required", "error");
                          }
                          else{
                            $posted_data["arrival_date_expedited"] 	= $posted_data["arrival_date"];
                            add_option( "cog_pickup_point", array("location"=>$pick_point, "date"=>$posted_data["arrival_date"] ));
                          }
                  }
                  else{
                        unset($posted_data["arrival_date"]);
                        unset($posted_data["arrival_date_expedited"]);
                  }



                if(empty($posted_data["ship_to_different_address"])){
                    $errs = trim(self::check_billing_fields($posted_data));


                    if(!empty($errs)){
                      $errs .= "<hr>Please fill the fields above, or check <b>Use my shipping details for billing</b>";
                      wc_add_notice($errs, "error");
                    }
                  }


                  //self::print_posted_data($posted_data);
                  return $posted_data;

          }


          /* *
           * Create custom shipping fields for woocommerce
           * */
          public function show_custom_billing_fields($options){
                  $field 		= self::custom_billing_fields($options);
                  echo woocommerce_form_field(array_keys($field)[0], array_values($field)[0]);
          }

          /* *
           * Tamper or Do manual shipping calculatons
           * */
          public static function add_custom_total($fee, $cart){
            $data = [];

            parse_str($_POST["post_data"], $data);

            if($data["pickup_point"]){
              WC()->session->set("chosen_shipping_methods", array(wc_local_pickup_plus()->get_shipping_method_instance()->get_method_id()));
              return $fee;
              //return $fee;
            }else{
              //$cart->set_shipping_total((string) get_chosen_methods(true, true)[0]);
              return $fee;
            }
          }


          public static function process_shipping_fields($fields){
                  return $fields;
          }


          /* *
           * Set custom hooks to process checkout
           * */
          public static function set_checkout_hooks(){
                  
                  # Get all available pickup locations
                  add_filter( "cog_get_local_addresses", array(get_called_class(), "get_addresses"));

                  # remove/add woocommerce checkout additional fields
                  add_filter( "woocommerce_checkout_fields", array(get_called_class(), "modify_checkout_additional_fields"), 10, 1 );

                  # Get data posted to woocommerce checkout page
                  add_filter( "woocommerce_checkout_posted_data", [get_called_class(), "process_posted_data"] );

                  # Return whether a "ship to different address" checkbox is checked
                  add_filter( 'woocommerce_ship_to_different_address_checked', "__return_true");

                  # Custom show shipping fields
                  add_action("cog_show_shipping_field", [get_called_class(), "show_custom_billing_fields"], 10, 1);

                  add_action("woocommerce_calculated_total", [get_called_class(), "add_custom_total"], 10, 2);
                  //add_action("woocommerce_cart_calculate_fee", [get_called_class(), "add_cart_total"], 10, 1);
          }
  
          /* *
           * This function is an archive for deprecated usage hooks
           * */
          public static function custom_deprecated_hooks(){

                  add_filter( 'default_shipping_fields', [get_called_class(), "process_shipping_fields"]);
            
                  add_action("woocommerce_checkout_create_order", array(get_called_class(), "create_initial_order_data"), 20, 2);

                  remove_action("woocommerce_after_checkout_validation", array(get_called_class(), "validate_checkout"), 2);

                  add_filter( 'woocommerce_package_rates', array(get_called_clas(), 'cog_woocommerce_package_rates'), 10, 2 );

                  add_action("woocommerce_checkout_update_order_meta", array(get_called_class(), "create_initial_order_data"), 20, 2);

                  add_action("woocommerce_after_checkout_validation", array(get_called_class(), "manage_custom_validation"), 1, 2);

                  add_action( "woocommerce_before_order_processed", array( COG_MASTER_CLASS, "process_order" ) );

                  # hook in when the payment is completed...
                  remove_filter( 'woocommerce_payment_complete_order_status', 'filter_woocommerce_payment_complete_order_status', 10, 3 ); 
                  add_filter( "woocommerce_payment_complete_order_status", array( COG_MASTER_CLASS, "change_order_details" ), 10, 2 );

          }

}
