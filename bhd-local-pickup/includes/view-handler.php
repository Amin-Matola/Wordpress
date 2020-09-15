<?php

/* *
 * This class handles all the CSS and Javascript 
 * */

defined("ABSPATH") or die("No cheating please...");

class Cog_View_Handler{

		public function __construct(){
          $this->sep 		= DIRECTORY_SEPARATOR;
          $this->asset_dir = "assets".$this->sep;
          $this->asset_url = str_replace($this->sep, "/", $this->asset_dir);

          # Stylesheet dir
          $this->style_dir = $this->asset_dir."css".$this->sep;
          $this->style_url = str_replace($this->sep, "/", $this->style_dir);

          # Javascript paths
          $this->script_dir = $this->asset_dir."js".$this->sep;
          $this->script_url = str_replace($this->sep, "/", $this->script_dir);
		}

		/* *
		 * Load  the styling
		 * */
		public function load_cog_styles(){
          wp_enqueue_style( "cog_main_style", COG_PLUGIN_DIR_URL.$this->style_url."main.css" );
          wp_enqueue_style( "cog_checkout_style", COG_PLUGIN_DIR_URL.$this->style_url."checkout.css" );
		}

		/* *
		 * Load the javascript
		 * */
		public function load_cog_scripts(){
          wp_enqueue_script( "cog_main_script", COG_PLUGIN_DIR_URL.$this->script_url."main.js", [], false, true );
	
		}

		/* *
		 * Call the functions that add css and js together
		 * */
		public function load_css_and_js(){
          $this->load_cog_styles();
          $this->load_cog_scripts();
		}

}
