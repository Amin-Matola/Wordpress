<?php

/* *
 * This functions deal with the notifications on the admin side
 * */


defined("ABSPATH") or die("No cheating please...");

function tell_plugin_active(){
    if(get_option( "cog_local_pickup_active", false )){
      add_action( "admin_notices",function(){
        if( !(function_exists("is_woocommerce_active") && is_woocommerce_active()) || !class_exists("WC_Local_Pickup_Plus_Loader")){
          ?>
        <div class="notice notice-error wrap" style="font-size:1.1em;padding:15px;">
          The plugin <a href="http://cycleofgood.com" style="text-decoration:none;color:orange; background:#f7f7f7;text-shadow:0px 2px 2px black">Cog Local Pickup Extender</a>

          only works with <strong>Woocommerce</strong>
           and 
           <strong>Woocommerce Local Pickup Plus</strong>, please install all of them and come back again!</div>

          <?php


          cog_deactivate_local_pickup();
            }else{
           ?>
            <div class="notice notice-success wrap aligncenter" style="font-size:1.1em; padding:15px;">Thanks for using 
          <a href="http://cycleofgood.com" style="text-decoration:none;color:orange; background:#f7f7f7;text-shadow:0px 2px 2px black">Cog Local Pickup Extender</a> auto shipping plugin. Enjoy your shipping!</div>
        <?php
      }
        delete_option( "cog_local_pickup_active" );

      } );

    }
}

/*
 * Called when the plugin is deactivated
 */
function tell_plugin_inactive(){
    if( get_option( "cog_local_pickup_inactive", false )){
      add_action( "admin_notices",function(){
        ?>
          <div class="notice notice-success">
            Thanks for using our plugin, explore us soon as we bring new more changes!
          </div>
        <?php

      } );
      delete_option( "cog_local_pickup_inactive" );
    }
}

/* *
 * Perform the actual deactivations here
 * */
function cog_deactivate_local_pickup( ){
    if(empty(COG_PLUGIN_BNAME) || empty(COG_ACTIVE_PLUGINS)){
        define( "COG_PLUGIN_BNAME", plugin_basename(dirname(dirname(__FILE__))."/cog-local-pickup-extender.php") );
        define("COG_ACTIVE_PLUGINS", get_option( "active_plugins", array()));
      }
      $plugin_index 		= cog_get_index(COG_PLUGIN_BNAME, COG_ACTIVE_PLUGINS, true);
      $all_plugins 		= COG_ACTIVE_PLUGINS;

      do_action("deactivate_plugin", COG_PLUGIN_BNAME);
      unset($all_plugins[ $plugin_index ] );
      update_option( "active_plugins", $all_plugins, true );
}

/* *
* Check if woocommerce is available, otherwise exit;
* */
function check_environment(){

		if( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || !class_exists("WC_Local_Pickup_Plus_Loader") ){
			cog_deactivate_local_pickup();
			return false;
		}
		return true;
}



add_action( "admin_init", "tell_plugin_active" );
add_action( "admin_init", "tell_plugin_inactive" );

function cog_local_pickup_activate(){
    check_environment();
    add_option( "cog_local_pickup_active", "true" );
}

function cog_local_pickup_deactivate(){
	  add_option( "cog_local_pickup_inactive", "true" );
}





/*
 * Register hooks called when activation is affected
 */
function cog_activation_hooks($file){
		register_activation_hook( $file, "cog_local_pickup_activate" );
		//register_deactivation_hook( $file, "cog_local_pickup_deactivate");
	}
	
