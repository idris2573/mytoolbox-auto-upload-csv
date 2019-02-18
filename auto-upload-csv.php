<?php
/*
 * @package AutoUploadCSV
 */
/*
Plugin Name: Auto Upload CSV
Description: Auto Upload CSV
Version: 1.0.0
License:
Text Domain: auto-upload-csv.php
*/


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Include the main WooCommerce class.
if ( ! class_exists( 'AutoUploadCSV' ) ) {
	include_once dirname( __FILE__ ) . '/includes/class-auto-upload-csv.php';
	$autoUploadCSV = new AutoUploadCSV();
	$autoUploadCSV->register();
}

// activate plugin
register_activation_hook( __FILE__, array( $autoUploadCSV, 'activate' ) );

function installer(){
    include('installer.php');
}
register_activation_hook( __file__, 'installer' );


include('includes/functions.php');
include('includes/cron.php');

// deactivate plugin
register_deactivation_hook( __FILE__, array( $autoUploadCSV, 'deactivate' ) );

// unschedule event upon plugin deactivation
function cronstarter_deactivate() {
	// find out when the last event was scheduled
	$timestamp = wp_next_scheduled ('csv_upload');
	// unschedule previous event if any
	wp_unschedule_event ($timestamp, 'csv_upload');
}
register_deactivation_hook (__FILE__, 'cronstarter_deactivate');
