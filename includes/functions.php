<?php

//////////////////// API ////////////////////

// https://mytoolbox.jcb-tools.co.uk/wp-json/csv/download
add_action( 'rest_api_init', function () {
        register_rest_route( 'csv', '/download/', array(
                'methods' => 'GET',
                'callback' => 'download_csv'
        ) );
} );

function download_csv( $request ) {
  // if(getUserIp() == '95.151.234.157' || getUserIp() == '167.98.89.236'){
      export_csv();
      return "";
  // }
}



//////////////////// FUNCTION ////////////////////

function export_csv(){
  $page = $_REQUEST["page"];

  if($page == "submissions_fm"){
    echo "correct";
  }

  $form_id = $_REQUEST["current_id"];

  global $wpdb;
  // $table_header = $wpdb->get_var("SELECT label_order FROM wp_formmaker WHERE id = $form_id");
  $table_header = $wpdb->get_var("SELECT label_order_current FROM wp_formmaker WHERE id = 3");
  $results = $wpdb->get_results("SELECT * FROM wp_formmaker_submits WHERE form_id = 3");


  // seperate header string into array
  $header_array = explode('#****#', $table_header);
  $fixed_header_array = array();

  foreach ($header_array as $value) {
    $key = substr($value, 0, strpos($value, '#'));
    $value = cleanHeader($value);
    $fixed_header_array[$key] = $value;
    // echo $header.'<br>';
  }

  $form_data = array();
  $headers = array();
  $headers['ID'] = "ID";
  $headers['Submit date'] = "Submit date";
  $headers["Submitter's IP"] = "Submitter's IP";

  $group_id = 0;
  foreach ( $results as $row ){

    if($group_id != $row->group_id){
      $group_id = $row->group_id;
      $date = $row->date;
      $ip = $row->ip;

      $row_data = array();
      $row_data['ID'] = $group_id;
      $row_data['Submit date'] = $date;
      $row_data["Submitter's IP"] = $ip;
    }

     $element_label = $row->element_label;
     $element_value = $row->element_value;

     $row_data[$element_label] = $element_value;

     // add row to total form data
     $form_data[$group_id] = $row_data;

     // add header value to array
     $headers[$element_label] = $fixed_header_array[$element_label];
  }

  // remove duplicate header values from array
  $headers = array_unique($headers);

  $csv = getHeadersForCsv($headers);

  $csv = getDataForCsv($csv, $form_data, $headers);
  echo $csv;

}


function cleanHeader($header){
  $header = substr($header, strpos($header, '#'));
  $header = substr($header, strpos($header, '#', 2));
  $header = substr($header, 1);
  $header = substr($header, 0, strpos($header, '#'));

  return $header;
}

function getHeadersForCsv($headers){
  foreach ( $headers as $head ){
    if(strpos($head, ' ') !== false){
      $csv = "$csv,\"$head\"";
    } else {
      $csv = "$csv,$head";
    }
  }

  return $csv = substr($csv, 1).'<br>';
}

function getDataForCsv($csv, $form_data, $headers){
  foreach ( $form_data as $data ){
    $line = "";
    foreach ( $headers as $key => $head ){
      if(strpos($data[$key], ' ') !== false){
        $line = "$line,\"$data[$key]\"";
      } else {
        $line = "$line,$data[$key]";
      }
    }

    $csv = $csv.substr($line, 1).'<br>';
  }

  return str_replace('&quot;', '\'', $csv);
}






// delete soon
function getUserIp(){
  foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
    if (array_key_exists($key, $_SERVER) === true){
      foreach (array_map('trim', explode(',', $_SERVER[$key])) as $ip){
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
          return $ip;
        }
      }
    }
  }
}

?>
