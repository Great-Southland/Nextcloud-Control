<?php // MyPlugin - Core Functionality
// /*


// disable direct file access
if ( ! defined( 'ABSPATH' ) ) {

	exit;

}


// --------------------------- Function to send a HTTP request to NC using Credentials/URL stored in Database ----------------------------------
function nc_request($method, $link, $nc_server_username = 'username', $nc_server_pass = 'pass', $array_merge = 0, $custom_args = array()){

	// Get details from Database
	$options = get_option( 'wnus_options', wnus_options_default() );
	$nextcloud_server_url = isset( $options['nextcloud_server_url']) ? sanitize_text_field( $options['nextcloud_server_url'] ) : '';
	$nextcloud_server_username = isset( $options['nextcloud_server_username']) ? sanitize_text_field( $options['nextcloud_server_username'] ) : '';
	$nextcloud_server_pass = isset( $options['nextcloud_server_pass']) ? sanitize_text_field( $options['nextcloud_server_pass'] ) : '';

	$nextcloud_sever_username = $nc_server_username != 'username' ? $nc_server_username : $nextcloud_server_username;
	$nextcloud_sever_pass = $nc_server_pass != 'pass' ? $nc_server_pass : $nextcloud_server_pass;


	$url = 'https://'. $nextcloud_server_username .':'. $nextcloud_server_pass .'@'. $nextcloud_server_url .'/'. $link;

	if($array_merge == 2){
		$args = $custom_args;

	} elseif($array_merge == 1) {
		$default_args = array( 'method' => $method,
									 				 'timeout' => 40,
									 			 	 'headers' => array(
										 			 		'OCS-APIRequest' => 'true',
										 		 	 		'Content-Type' => 'application/x-www-form-urlencoded',),);
    $args = array_merge($default_args, $custom_args);
	} else {
		$args = array( 'method' => $method,
									 'timeout' => 40,
									 'headers' => array(
										 'OCS-APIRequest' => 'true',
										 'Content-Type' => 'application/x-www-form-urlencoded',),);
	}
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
	$url = 'ocs/v2.php/apps/files_sharing/api/v1/shares?shareWith='. $shareWith .'&shareType='. $shareType .'&publicUpload='. $publicUpload .'&permissions='. $permissions .'&path='. $file_path;
	$xml_response = nc_request($method, $url);
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

/* =========================== Get share link and if not created yet create and return the link ======================*/
function get_nc_share_link($file_path, $shareType = 3, $publicUpload = false, $permissions = 1, $shareWith = '') {
	$get_create_nc_share_link = get_create_nextcloud_share_link_1('GET', $file_path, $shareType, $publicUpload, $permissions, $shareWith);
// **************** Check if GET link returns false, if it does Create link ***************
	if ($get_create_nc_share_link != false) {
		$result = $get_create_nc_share_link;
	}	else {
		$result = get_create_nextcloud_share_link_1('POST', $file_path, $shareType, $publicUpload, $permissions, $shareWith);
	}
// if it couldn't Get or create link return message
  if (empty($result)) {
		$result = "Couldn't Create/Get Share Link";
	}
	return $result;
}

function display_nc_share_links() {
	$url = 'https://Benjamin:benjaminforg@salones-portal.ddns.net/cloud/remote.php/dav/files/Benjamin/testgroupfolder';
	$args = array( 'method' => 'PROPFIND',
								 'timeout' => 40000,
							 );


	$raw_xml_response = wp_remote_request( $url, $args );
	$xml_response = str_replace('d:', '', $raw_xml_response['body']);
	$xml_array = xml_to_array($xml_response);
	$result = '';

	foreach($xml_array['response'] as $response){
		$folder_path = 'testgroupfolder/';
		$url_path = str_replace('/cloud/remote.php/dav/files/Benjamin/', '', $response['href']);
		$file_name = str_replace($folder_path, '', $url_path);
		$decoded_file_name = urldecode($file_name);

		$share_url = get_nc_share_link_1($url_path);
		$result .= '<a href="'. $share_url .'">'. $decoded_file_name .'</a><br>';
	}

	return $result;
} add_shortcode('display_nc_share_link', 'display_nc_share_links');
