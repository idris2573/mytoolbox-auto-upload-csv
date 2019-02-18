<?php

//////////////////// CRON ////////////////////
// create a scheduled event (if it does not exist already)
function cronstarter_activation() {
	if( !wp_next_scheduled( 'csv_upload' ) ) {
	   wp_schedule_event( time(), 'daily', 'csv_upload' );
	 }
}
// and make sure it's called whenever WordPress loads
add_action('wp', 'cronstarter_activation');

function my_repeat_function() {

	$recepients = 'idris2573@gmail.com';
	$subject = 'Hello from your Cron Job';
	$message = 'This is a test mail sent by WordPress automatically as per your schedule.';

	mail($recepients, $subject, $message);
}

add_action ('csv_upload', 'upload_file_via_ftp');

// add custom interval
function cron_add_minute( $schedules ) {
	// Adds once every minute to the existing schedules.
    $schedules['everyminute'] = array(
	    'interval' => 60,
	    'display' => __( 'Once Every Minute' )
    );
    return $schedules;
}
add_filter( 'cron_schedules', 'cron_add_minute' );


function upload_file_via_ftp(){

  $date = date("d-m-Y");

  $temp = tmpfile();
  fwrite($temp, export_csv());
  fseek($temp, 0);
  // echo fread($temp, 1024);
  $path = stream_get_meta_data($temp)['uri'];

  $connection = ssh2_connect('mytrafficcentral.com', 22);
  if($connection === FALSE) {
      die('Failed to connect');
  }

  $state = ssh2_auth_password($connection, 'root', 'daniel2573');
  if($state === FALSE) {
      die('Failed to authenticate');
  }

  $state = ssh2_scp_send($connection, $path, "/gse-server/ftp/csv-$date.csv", 0644);
  if($state === FALSE) {
      die('Failed to transfer the file');
  }

  fclose($temp);

}

?>
