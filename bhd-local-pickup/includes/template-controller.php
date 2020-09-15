<?php


/**
* Plugin dir
*/

/**
* Override WooCommerce templates from your plugin with child theme way
*/
function locate_cog_template( $template, $template_name, $template_path ) {
        global $woocommerce;

        $_template = $template;

        if ( ! $template_path ) {
          $template_path = $woocommerce->template_url;
        }

        $plugin_path = COG_PLUGIN_DIR_PATH . '/woocommerce/';

        $template = locate_template(

          array(

            $template_path . $template_name,
            $template_name,
          )
        );

        // Modification: Get the template from this plugin, if it exists
        if ( ! $template && file_exists( $plugin_path . $template_name ) ) {
          $template = $plugin_path . $template_name;
        }

        // Use default template.
        if ( ! $template ) {
          $template = $_template;
        }
        // Return what we found
        return $template;
 }
