<?php


/* *
 * Handle admin menus and the likes
 * */

class Bike_Admin{

	public $main_page_content;

	/* *
	 * Set the content at the initial opening
	 * */
	private function __construct(){
		$this->set_main_page_content();
	}

	/* *
	 * Add the page for the bike menu
	 * */
	public function add_admin_page(){
	
		add_menu_page( "Bike Appearance", 
						"Elephant Bike", 
						"manage_options", 
						"elephant-bike", 
						array($this, "add_settings"), 
						"dashicons-sos", 
						3.76 );
	}


	/* *
	 * Add the submenu page(s) for the admin
	 * */
	public function add_admin_subpages(){
		
		add_submenu_page(
						"elephant-bike",  
						"Bike Documentation", 
						"Documentation", 
						"manage_options", 
						"documentation", 
						array($this, "bike_documentation"), 
						1 );
		add_submenu_page(
						"elephant-bike",  
						"Set Bike", 
						"Configure", 
						"manage_options", 
						"configure-bike", 
						array($this, "add_settings"), 
						1 );
	}

	/* *
	 * Get the available colors
	 * */
	public function get_colors(){
		$opts = [
		"#F3E03B"=> "Zinc Yellow",
		"#F0CA00"=> "Traffic Yellow",
		"#DD7907"=> "Yellow Orange",
		"#E75B12"=> "Pure Orange",
		"#E1A6AD"=> "Light Pink",
		"#5E2028"=> "Wine Red",
		"#D15B8F"=> "Heather Violet",
		"#E9E5CE"=> "Oyster White",
		"#FDF4E3"=> "Cream",
		"#FFFFFF"=> "Pure White",
		"#83639D"=> "Blue Lilac",
		"#384C70"=> "Violet Blue",
		"#13447C"=> "Gentian Blue",
		"#3481B8"=> "Light Blue",
		"#26392F"=> "Fir Green",
		"#48A43F"=> "Yellow Green",
		"#008754"=> "Traffic Grey",
		"#596163"=> "Basalt Grey",
		"#6F4F28"=> "Olive Brown",
		"#633A34"=> "Chesnut Brown"
		];

		return $opts;

	}


	/* *
	 * Get the posted data
	 * */
	public function get_posted_color($data = []){
	    if(empty($data) || empty($data["initial_color"])){
	        return false;
	    }
		$colors 	= $this->get_colors();

		$posted 	= $data["initial_color"];
		return $colors[$posted];

	}

	/* *
	 * Append the color to the query
	 * */
	public function get_additional_string(){
		return "?initial=".get_option("initial_colour");
	}

	/* *
	 * Get the required file and pass the color
	 * */
	public function get_template($name, $data = ''){

		return wp_remote_retrieve_body( 
			wp_remote_get( 
				dirname(plugin_dir_url( __FILE__ ))."/templates/".$name."?initial=".$data
			)); 
	}


	/* *
	 * Open the required file and set the main page content
	 * */
	public function set_main_page_content(){
	    $initial        =   "";
	    $color          =   "";
	    
		if(array_key_exists("initial_color", $_POST)){
            
            $initial    =   $_POST["initial_color"];
            
            if(!empty($initial)) {
			    update_option( "initial_colour", $initial, true );
			    $color  =   "Initial Color Changed To ".$this->get_posted_color($_POST);
            }
			if(!$this->bike_settings() && !empty($_POST["bike_sku"])){
			    $color  = "Sorry, the bike with Stock Keep Unit ".$_POST["bike_sku"]." is not available. Please make sure you add a bike with a defined SKU.";
			}
		}
		

		$options_file 			 = "admin/options.php";
		$this->main_page_content = $this->get_template($options_file, $color);
	}


	/* *
	 * Create Bike Admin Settings
	 * */
	public function add_settings(){

		echo $this->main_page_content;
	}


	/* *
	 * Add the bike documentation to the admin menu
	 * */
	public function bike_documentation(){
		echo $this->get_template("admin/documentation.php");
	}

	/* *
	 * Fire admin hooks
	 * */
	public function admin_hooks(){
		add_action("init", array($this, "set_main_page_content"), 10);
		add_action("admin_menu", array($this, "add_admin_page"), 10);
		add_action("admin_menu", array($this, "add_admin_subpages"),20);
	}

	/* *
	 * Get the instance of this class
	 * */
	public static function get_instance(){
		return new self();
	}


	/* *
	 * Set the configurations from the admin
	 * */
	public function bike_settings(){
		if(!is_admin()){
			return false;
		}
		$name 		= $_POST["bike_name"];
		$slug 		= $_POST["bike_slug"];
		$sku 		= $_POST["bike_sku"];
		$initial 	= $_POST["initial_color"];
		
		if(empty($sku)){
		    $bike_data  = get_option("bike_data","");
		    if(!empty($bike_data)){
		        $sku    = $bike_data["sku"];
		    }
		    
		}

		$items 		= new WC_Product_Query(["sku" => $sku??"elephant_bike"]);

		if(count($items->get_products())){
			
			$item 		= $items->get_products()[0];
			$item->get_sku() == $sku?$item->set_sku( $sku ):"";
			!empty($name)?$item->set_name($name): "";
			!empty($slug)?$item->update_meta_data("_bike_slug", $slug, true):"";
			
			$item->save();

			$bike_data 	= ["name"=>$name??$item->get_name(),
							"slug"=>$slug?? get_metadata("post", $item->get_id(), "_bike_slug", true), 
							"sku"=>$sku??$item->get_sku(), 
							"initial"=>$initial??get_option("initial_colour", "")
						  ];
						  

            update_option("elephant_bike_id", $item->get_id());
			update_option("bike_data", $bike_data, true);

			return true;
		}

		return false;
	}
}
