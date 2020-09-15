<?php

/* *
 * All the constants used in this plugin are defined here
 * */

defined("ABSPATH") or die("No cheating please...");

define( "COG_PLUGIN_BNAME", plugin_basename(dirname(dirname(__FILE__))."/cog-local-pickup-extender.php") );
define( "COG_MASTER_CLASS", "Cog_Local_Pickup_Master" );
define( "COG_PLUGIN_URL",  plugin_dir_url(dirname(__FILE__))."cog-local-pickup-extender.php" );
define( "COG_PLUGIN_DIR_URL",  plugin_dir_path(plugin_dir_url(dirname(__FILE__))."cog-local-pickup-extender.php" ));

define( "COG_PLUGIN_PATH",  rtrim(plugin_dir_path(dirname(__FILE__)), "/").DIRECTORY_SEPARATOR."cog-local-pickup-extender.php" );
define( "COG_PLUGIN_DIR_PATH",  dirname(rtrim(plugin_dir_path(dirname(__FILE__)), "/").DIRECTORY_SEPARATOR."cog-local-pickup-extender.php" ));
define( "COG_ACTIVE_PLUGINS", get_option("active_plugins", []));
