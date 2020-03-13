<?php //Folder Manager Functions
function wnus_folder_manager() {

//---------------------------------- Security Checks --------------------------
// check if nonce is valid
	if ( isset( $_POST['wnus-nonce'] ) && wp_verify_nonce( $_POST['wnus-nonce'], 'wnus-nonce' ) ) {

//--------------------------------- Get Data -------------------------------




	 	



  	// Get checked users
  	$checked_folders = get_checked_folders();
  	//Get selected Action
  	$select_action = $_POST['action-select'];
  	$group_select = isset($_POST['group-select']) ? $_POST['group-select'] : '';
  	$text_input = isset($_POST['text-input']) ? $_POST['text-input'] : '';



  foreach ($checked_folders as $folder) {
    	//---------------------------------------- Proccess HTTP Request --------------------------------
    	$http_request = nc_request('GET', 'apps/groupfolders/folders');

    	// ************** Convert XML NC Response to json and then to and array ***************
    	// Convet XML String into an onject
    	$ob = simplexml_load_string($http_request['body']);
    	// Convet XML Object to json
    	$json  = json_encode($ob);
    	// Convet json to PHP Array
    	$configData = json_decode($json, true);

    	// ************* Get Data *****************
    	// HTTP Response Code and Message from NC
    	$nc_code = $configData['meta']['statuscode'];
    	$nc_message = $configData['meta']['message'];
    	// HTTP Response Code and Message from WP
    	$wp_code = wp_remote_retrieve_response_code( $nc_response );
    	$wp_message = wp_remote_retrieve_response_message( $nc_response );
    	// Set HTTP Response Code and Message
  		$http_code    = isset( $nc_code )   ? $nc_code  : $wp_code;
  		$http_message    = isset( $nc_message )   ? $nc_message  : $wp_message;
//--------------------------------- Proccess Data ---------------------------------------












// ------------------------------ Check for Errors and Set Redirect URL ----------------------------------
			//  Check if HTTP request returned an error
			if (!is_wp_error($http_request)) {
  			$http_request = $http_code;
			}

// Check if HTTP request was successfull
			if ($http_request != '100') {
  			$wp = esc_html__( 'Operation failed, Please check NextCloud URL, username, and password in Nextcloud settings page.' , 'wnus' );
			}


  		$http_code = '<br>Response Code: '. $http_code;
  		$http_message = '<br>Response Message: '. $http_message;

  		$redirect_url = $_POST['redirect-url'];
  		$front_back_end = $_POST['front-back-end'];

  		if ($front_back_end == 'frontend') {
  			// set the redirect url
  			$location = 					 $redirect_url
  														.'&result-nc='. urlencode($http_request)
  														.'&$nc_response-code='. urlencode($http_code)
  														.'&$nc_response-message='. urlencode($http_message);
			}
			if ($front_back_end == 'backend') {
  			// set the redirect url
  			$location = 				 $redirect_url
  														.'?page=user-group-manager'
  														.'&result-nc='. urlencode($http_request)
  														.'&$nc_response-code='. urlencode($http_code)
  														.'&$nc_response-message='. urlencode($http_message);
			}
		}
		// redirect
		wp_redirect( $location );
  	exit;
	}
}
add_action( 'admin_post_wnus_folder_manager_form_response', 'wnus_folder_manager' );
