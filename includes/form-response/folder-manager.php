<?php //Folder Manager Functions
function wnus_folder_manager() {

//---------------------------------- Security Checks --------------------------
// check if nonce is valid
	if ( isset( $_POST['wnus-nonce'] ) && wp_verify_nonce( $_POST['wnus-nonce'], 'wnus-nonce' ) ) {
			//--------------------------------- Get Data -------------------------------
			// ************** Make Get request and Convert XML NC Response ***************
		$http_request = nc_request('GET', 'apps/groupfolders/folders');
		if(!is_wp_error($http_request)){
			// Convet XML String into an onject
			$ob = simplexml_load_string($http_request['body']);
			// Convet XML Object to json
			$json  = json_encode($ob);
			// Convet json to PHP Array
			$configData = json_decode($json, true);
			$folders = $configData['data']['element'];

			// *************** Get Folder Ids ******************
			// Array to hold all NC Folder Ids
			$folder_ids = [];
			if(array_key_exists(0, $folders)){
			 //Cycle through Folders
			 foreach($folders as $folder){
				 $folder_id = $folder['id'];
				 $folder_ids[] = $folder_id;
			 }

			} else {
			 //If there is only one folder
			 $folder_id = $folders['id'];
			 $folder_ids[] = $folder_id;

			}
			// Check which IDs are checked
			$checked_folders = [];
			foreach ($folder_ids as $id) {
				if (isset($_POST[$id]) && $_POST[$id] == true) {
					$checked_folders[] = $id;
				}
			}

	  	//********************** Get other Form Data *******************
	  	$select_action = isset($_POST['action-select']) ? $_POST['action-select'] : '';
	  	$group_select = isset($_POST['group-select']) ? $_POST['group-select'] : '';
	  	$text_input = isset($_POST['text-input']) ? $_POST['text-input'] : '';


		if(!empty($checked_folders)){
		  foreach ($checked_folders as $folder) {
















// ================================ Procces a Delete request ================================
				if($select_action == 'delete-folder'){
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
				}

// ================================ Procces a Give Access request ================================
				if($select_action == 'give-access'){
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
					$http_message = '<br>Response Messag: '. $http_message;
				}

// ================================ Procces a Remove Access request ================================
				if($select_action == 'remove-access'){
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
				}

// ================================ Procces a Set Quota request ================================
				if($select_action == 'set-quota'){
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
				}

//  ================================  Procces a Rename Folder request  ================================
				if($select_action == 'rename-folder'){
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
				}












				}
			} else {
				//Set Error Messages
				$http_request = 'Operation Failed, Please Ensure at Least One Checkbox is Checkd';
				$http_code = '<br>Please Ensure at Least One Checkbox is Checked';
				$http_message = '<br>Please Ensure at Least One Checkbox is Checked';
			}
		} else {
			// If HTTP Returned an error
			$http_request = $http_request->get_error_message();
			//Set Error Messages
			$http_request = 'NextCloud: Operation Failed, Please Ensure You Have Entered the Correct Settings in the Settings Page<br>Error Message: '. $http_request;
			$http_code = '<br>NextCloud: Operation Failed, Please Ensure You Have Entered the Correct Settings in the Settings Page';
			$http_message = '<br>NextCloud: Operation Failed, Please Ensure You Have Entered the Correct Settings in the Settings Page';
		}
		//*********************** Set Redirect URL *******************
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
														.'?page=folder-manager'
														.'&result-nc='. urlencode($http_request)
														.'&$nc_response-code='. urlencode($http_code)
														.'&$nc_response-message='. urlencode($http_message);
		}
		// **************** redirect ****************
		wp_redirect( $location );
  	exit;
	}
}
add_action( 'admin_post_wnus_folder_manager_form_response', 'wnus_folder_manager' );
