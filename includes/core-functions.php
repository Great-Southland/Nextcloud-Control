<?php // MyPlugin - Core Functionality
// /*


// disable direct file access
if ( ! defined( 'ABSPATH' ) ) {

	exit;

}


// --------------------------- Function to send a HTTP request to NC using Credentials/URL stored in Database ----------------------------------
function nc_request($method, $link){
	// Get details from Database
	$options = get_option( 'wnus_options', wnus_options_default() );
	$nextcloud_server_url = isset( $options['nextcloud_server_url']) ? sanitize_text_field( $options['nextcloud_server_url'] ) : '';
	$nextcloud_server_username = isset( $options['nextcloud_server_username']) ? sanitize_text_field( $options['nextcloud_server_username'] ) : '';
	$nextcloud_server_pass = isset( $options['nextcloud_server_pass']) ? sanitize_text_field( $options['nextcloud_server_pass'] ) : '';


	$url = 'https://'. $nextcloud_server_username .':'. $nextcloud_server_pass .'@'. $nextcloud_server_url .'/'. $link;
	$args = array( 'method' => $method,
								 'timeout' => 40,
								 'headers' => array(
									 'OCS-APIRequest' => 'true',
									 'Content-Type' => 'application/x-www-form-urlencoded',),
							 );
  // Send HTTP Request with URL and Args
  $nc_response = wp_remote_request( $url, $args );

	// Checks if HTTP Request failed on Wordpress Side
	if ( is_wp_error( $nc_response ) ) {
		$nc_response = $response->get_error_message();
	}

	// Return HTTP Request Response
	return $nc_response;
}

// --------------------- Function to Get user roles by id --------------------
	function get_user_roles($user_id) {
		$user_meta=get_userdata($user_id);
		$user_roles=$user_meta->roles;
		return $user_roles;
	}

// --------------------- Get users by Roles-----------------------
function get_users_by_role($role, $orderby, $order) {
	   $args = array(
	       'role'    => $role,
	       'orderby' => $orderby,
	       'order'   => $order
	   );
	   $users = get_users( $args );
	   return $users;
}
//------------------------- Function to Get user roles by id ----------------
function get_user_roles_edit($user_id) {
	$user_meta=get_userdata($user_id);
	$user_roles=$user_meta->roles;
	return $user_roles;
}

// ----------------------- Function to see if Array contains String -------------------------------
function array_contains($array, $filter){
	// Get current users Leader Roles
	$filtered_array = array_filter($array, function($array_element) use($filter){
			if (strpos($array_element, $filter) !== false) {
				return true;
			} else {
				return false;
			}
	});
	return $filtered_array;
}

// ----------------------------- Get array of wp roles -------------------------
function get_roles(){
	if (!function_exists('get_editable_roles')) {
		require_once(ABSPATH . '/wp-admin/includes/user.php');
	}
		$roles_s = '';
		$wnus_editable_roles = get_editable_roles();
		foreach ($wnus_editable_roles as $role => $details) {
				$roles_s .= $role .'|';
		}
		$roles_s = rtrim($roles_s, '|');
		$roles_a = explode('|', $roles_s);
		return $roles_a;
}

/* ---------------------------- Convert XML data in array -------------------*/
function xml_to_array($raw_xml_data) {
	$raw_xml_string = simplexml_load_string($raw_xml_data);
		$encode_json  = json_encode($raw_xml_string);
		$xml_array = json_decode($encode_json, true);
	return $xml_array;
}

/* ----------------------------  Get/Create a Nextcloud Share link ----------------------------*/
function get_create_nextcloud_share_link($method, $file_path, $shareType, $publicUpload, $permissions, $shareWith) {
	//******************* Send API Request *************************
	$url = 'https://Benjamin:benjaminforg@salones-portal.ddns.net/cloud/ocs/v2.php/apps/files_sharing/api/v1/shares?shareWith='. $shareWith .'&shareType='. $shareType .'&publicUpload='. $publicUpload .'&permissions='. $permissions .'&path='. $file_path;
	$args = array( 'method' => $method,
							   'timeout' => 40000,
								 'headers' => array(
									 'OCS-APIRequest' => 'true',
									 'Content-Type' => 'application/x-www-form-urlencoded',),
							 );
	$xml_response = wp_remote_request( $url, $args );
  $xml_in_array = xml_to_array($xml_response['body']);
// ************** check if share link is in the "element" or "data" array (If it created the link its in the "data" array)******************
	if (isset($xml_in_array['data']['element']['url'])) {
		$result = $xml_in_array['data']['element']['url'];
	}	elseif (isset($xml_in_array['data']['url'])) {
		$result = $xml_in_array['data']['url'];
	// Return false if the method is GET
	}	elseif ($method = 'GET') {
		$result = false;
	}
return $result;
}
