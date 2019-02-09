<?php
/**
 * AutoUploadCSV setup
 *
 * @package  AutoUploadCSV
 * @since    1.0.0
 */

final class AutoUploadCSV {

  function __construct() {
    // init = initialization
    $this->plugin = plugin_basename( __FILE__ );
  }

  function register() {
    // on the front end
    add_action( 'wp_enqueue_scripts', array( $this, 'enqueue') );

  }

  function activate() {
    // genereated a custom post type
    // flush rewrite rules
    flush_rewrite_rules();
  }

  function deactivate() {
    // flush rewrite rules
    flush_rewrite_rules();
  }


  function enqueue() {
    // enqueue all our scripts
    // wp_enqueue_style( 'mypluginstyle', plugins_url( '../assets/style.css', __FILE__ ) );
    // wp_enqueue_style( 'mypluginscript', plugins_url( '../assets/script.js', __FILE__ ) );
  }


}
