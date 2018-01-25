<?php
/**
 * Plugin Name: AgriLife Extension Research
 * Plugin URI: https://github.com/AgriLife/agrilife-extension-research
 * Description: Functionality for AgriLife Extension Research sites using AgriFlex 3
 * Version: 1.0
 * Author: Zach Watkins
 * Author URI: http://github.com/ZachWatkins
 * Author Email: zachary.watkins@ag.tamu.edu
 * License: GPL2+
 */

require 'vendor/autoload.php';

define( 'AG_EXTRES_DIRNAME', 'agrilife-extension-research' );
define( 'AG_EXTRES_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'AG_EXTRES_DIR_FILE', __FILE__ );
define( 'AG_EXTRES_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'AG_EXTRES_TEMPLATE_PATH', AG_EXTRES_DIR_PATH . 'view' );

$extres_required_dom = new \AgriLife\ExtensionResearch\RequiredDOM();

$extres_asset = new \AgriLife\ExtensionResearch\Asset();

$extres_widget_areas = new \AgriLife\ExtensionResearch\WidgetAreas();

$extres_custom_fields = new \AgriLife\ExtensionResearch\CustomFields();

// Load custom post type
add_action( 'init', function(){

  if ( class_exists( 'Acf' ) ) {
    require_once(AG_EXTRES_DIR_PATH . 'fields/research-project-details.php');
  }

  $extres_project = new \AgriLife\ExtensionResearch\PostType( 'Research Project' );

});

add_action( 'agrilife_core_init', function() {
  $extres_home_template = new \AgriLife\Core\PageTemplate();
  $extres_home_template->with_path( AG_EXTRES_TEMPLATE_PATH )->with_file( 'home' )->with_name( 'Home' );
  $extres_home_template->register();
});
