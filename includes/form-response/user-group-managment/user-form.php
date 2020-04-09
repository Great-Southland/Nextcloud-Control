<?php // Update User form response

// update user
function wnus_user_form() {

	// check if nonce is valid
	if ( isset( $_POST['wnus-nonce'] ) && wp_verify_nonce( $_POST['wnus-nonce'], 'wnus-nonce' ) ) {

		// check if user is allowed
		if ( ! is_user_logged_in() ) wp_die();

// Get users which are checked users form User/Group Manager
		function get_checked_users(){
		// Get array of wordpress user's IDs
			$wp_users = get_users( array( 'fields' => array('ID' ) ) );
			$user_ids_s = '|';

			foreach ($wp_users as $user) {
				$user_id = $user->ID;
				$user_ids_s .= $user_id .'|';
			}
			// Trim last pipe from string
			$user_ids_s = rtrim($user_ids_s, '|');
			$user_ids_a = explode('|', $user_ids_s);


		// Returns an array with IDs which where checked in user table
			$IDs_checked_s = '';
			foreach ($user_ids_a as $ID) {
				if ( $_POST['user-id-'. $ID] != '') {
					$IDs_checked_s .= $ID .'|';
				}
			}
			// Trim last pipe from string
			$IDs_checked_s = rtrim($IDs_checked_s, '|');
			$IDs_checked_a = explode('|', $IDs_checked_s);
			return $IDs_checked_a;
    }

		// Check what action was selected values: add-to-group, remove-from-group, delete-user
		$select_action = $_POST['select-action'];

		// Get checked users
			$checked_users = get_checked_users();
			$checked_users_s = explode('|', $checked_users);

			// Add user to group
		if ($select_action == 'add-to-group') {

		foreach ($checked_users as $id) {

			$user_meta = get_userdata($id);

			// Get User's Login username
			$user_login = $user_meta->user_login;
			$user_id = $id;
  			$user_email = $user_meta->user_email;

      // Get Role
      		$wp_role = $_POST['wp-roles'];
			$nc_group = $_POST['wp-roles'];
			if ($nc_group == 'administrator'){
				$nc_group = 'admin';
			}
			// HTTP request details
			$nc_user_login = $user_login;

  	  // get the user id
  			$user_id = email_exists( $user_email );


  		//  If user id exists
  		if ( is_numeric( $user_id ) ) {

  			// Update NextCloud User
					$nc_response = nc_request('POST', '/ocs/v1.php/cloud/users/'. $nc_user_login .'/groups?groupid='. $nc_group);

  				 // Get Response code and message if HTTP request did not return an error
  				 if (!is_wp_error($nc_response)) {

  						$nc_code_wp = wp_remote_retrieve_response_code( $nc_response );
  						$nc_message_wp = wp_remote_retrieve_response_message( $nc_response );

  						$nc_xml = $nc_response['body'];
  						$nc_ob= simplexml_load_string($nc_xml);
  						$nc_json  = json_encode($nc_ob);
  						$nc_configData = json_decode($nc_json, true);

  						$nc_code_nc = $nc_configData['meta']['statuscode'];
  						$nc_message_nc = $nc_configData['meta']['message'];

  						$nc_code    = isset( $nc_code_nc )   ? $nc_code_nc  : $nc_code_wp;
  						$nc_message    = isset( $nc_message_nc )   ? $nc_message_nc  : $nc_message_wp;
  					}

  						//  Check if HTTP request returned an error
  						if (is_wp_error($nc_response)) {

  							$nc_response = $nc_response->get_error_message();
  					// Otherwise set $nc_response_email equal to response code
  						} else {

  							$nc_response = $nc_code;
  						}



  				// Update Wordpress User If  The HTTP responses == 100 (Success)
  			  	if ($nc_response == '100') {

              // Create an instance of WP_user Class
              $u = new WP_User($user_id);

              // Add role
              $u->add_role( $wp_role );
  					}
  		} else {
  			// user not found
  			$user_id = 'User not found.';
  			$nc_response = $user_id;
  		}

  		// Check if HTTP request was successfull for update email and update displayname
      if (!is_numeric($nc_response) || is_numeric($nc_response) & $nc_response != '100') {
        $user_id = esc_html__( 'Operation failed, Please check NextCloud URL, username, and password in Nextcloud settings page.' , 'wnus' );
      }

  		// Set a message $user_id if its numeric
  		if (is_numeric($user_id)){
  			$user_id ='User/s Have Been Added to Group '. $wp_role;
  		}

      $nc_code = '<br>Response Code: '. $nc_code;
      $nc_message = '<br>Response Message: '. $nc_message;

			$redirect_url = $_POST['redirect-url'];
			$front_back_end = $_POST['front-back-end'];

			if ($front_back_end == 'frontend') {
				// set the redirect url
					$location = 					 $redirect_url
																.'?result-wp='. urlencode( $user_id )
																.'&result-nc='. urlencode($nc_response)
																.'&$nc_response-code='. urlencode($nc_code)
																.'&$nc_response-message='. urlencode($nc_message);
			} if ($front_back_end == 'backend') {
				// set the redirect url
				$location = 					 $redirect_url
															.'?page=user-group-manager'
															.'&result-wp='. urlencode( $user_id )
															.'&result-nc='. urlencode($nc_response)
															.'&$nc_response-code='. urlencode($nc_code)
															.'&$nc_response-message='. urlencode($nc_message);
			}

  		// redirect
  		wp_redirect( $location );

     }
		exit;
	}






	if ($select_action == 'remove-from-group') {

	foreach ($checked_users as $id) {

		$user_meta = get_userdata($id);

		// Get User's Login username
		$user_login = $user_meta->user_login;
		$user_id = $id;
		$user_email = $user_meta->user_email;

		// Get Role
		$wp_role = $_POST['wp-roles'];
		$nc_group = $_POST['wp-roles'];
		if ($nc_group == 'administrator'){
			$nc_group = 'admin';
		}

		// HTTP request details
		$nc_user_login = $user_login;

		// get the user id
		$user_id = email_exists( $user_email );


		//  If user id exists
		if ( is_numeric( $user_id ) ) {

			// Update NextCloud User
				$nc_response = nc_request('DELETE', '/ocs/v1.php/cloud/users/'. $nc_user_login .'/groups?groupid='. $nc_group);

				 // Get Response code and message if HTTP request did not return an error
				 if (!is_wp_error($nc_response)) {

						$nc_code_wp = wp_remote_retrieve_response_code( $nc_response );
						$nc_message_wp = wp_remote_retrieve_response_message( $nc_response );

						$nc_xml = $nc_response['body'];
						$nc_ob= simplexml_load_string($nc_xml);
						$nc_json  = json_encode($nc_ob);
						$nc_configData = json_decode($nc_json, true);

						$nc_code_nc = $nc_configData['meta']['statuscode'];
						$nc_message_nc = $nc_configData['meta']['message'];

						$nc_code    = isset( $nc_code_nc )   ? $nc_code_nc  : $nc_code_wp;
						$nc_message    = isset( $nc_message_nc )   ? $nc_message_nc  : $nc_message_wp;
					}

						//  Check if HTTP request returned an error
						if (is_wp_error($nc_response)) {

							$nc_response = $nc_response->get_error_message();
					// Otherwise set $nc_response_email equal to response code
						} else {

							$nc_response = $nc_code;
						}



				// Update Wordpress User If  The HTTP responses == 100 (Success)
					if ($nc_response == '100') {

						// Create an instance of WP_user Class
						$u = new WP_User($user_id);

						// Add role
						$u->remove_role( $wp_role );
					}
		} else {
			// user not found
			$user_id = 'User not found.';
			$nc_response = $user_id;
		}

		// Check if HTTP request was successfull for update email and update displayname
		if ($nc_response != '100') {
			$user_id = esc_html__( 'Operation failed, Please check NextCloud URL, username, and password in Nextcloud settings page.' , 'wnus' );
		}

		// Set a message $user_id if its numeric
		if (is_numeric($user_id)){
			$user_id = 'User/s Have Been Removed from Group '. $wp_role;
		}

		$nc_code = '<br>Response Code: '. $nc_code;
		$nc_message = '<br>Response Message: '. $nc_message;

		$redirect_url = $_POST['redirect-url'];
		$front_back_end = $_POST['front-back-end'];

		if ($front_back_end == 'frontend') {
			// set the redirect url
				$location = 					 $redirect_url
															.'?result-wp='. urlencode( $user_id )
															.'&result-nc='. urlencode($nc_response)
															.'&$nc_response-code='. urlencode($nc_code)
															.'&$nc_response-message='. urlencode($nc_message);
		} if ($front_back_end == 'backend') {
			// set the redirect url
			$location = 					 $redirect_url
														.'?page=user-group-manager'
														.'&result-wp='. urlencode( $user_id )
														.'&result-nc='. urlencode($nc_response)
														.'&$nc_response-code='. urlencode($nc_code)
														.'&$nc_response-message='. urlencode($nc_message);
		}

		// redirect
		wp_redirect( $location );

	 }
	exit;
}













// Delete user
if ($select_action == 'delete-user') {

	// Run code to delete users
		foreach ($checked_users as $id) {

			$user_meta = get_userdata($id);

			// Get User's Login username
			$user_login = $user_meta->user_login;
			$user_id = $id;

			// HTTP request details
			$nc_user_login = $user_login;

			//  If user id exists
			if ( is_numeric( $user_id ) ) {

				// Delete NextCloud User
				$nc_response = nc_request('DELETE', '/ocs/v1.php/cloud/users/'. $nc_user_login);

					 // Get Response code and message if HTTP request did not return an error
					 if (!is_wp_error($nc_response)) {

							$nc_code_wp = wp_remote_retrieve_response_code( $nc_response );
							$nc_message_wp = wp_remote_retrieve_response_message( $nc_response );

							$nc_xml = $nc_response['body'];
							$nc_ob= simplexml_load_string($nc_xml);
							$nc_json  = json_encode($nc_ob);
							$nc_configData = json_decode($nc_json, true);

							$nc_code_nc = $nc_configData['meta']['statuscode'];
							$nc_message_nc = $nc_configData['meta']['message'];

							$nc_code    = isset( $nc_code_nc )   ? $nc_code_nc  : $nc_code_wp;
							$nc_message    = isset( $nc_message_nc )   ? $nc_message_nc  : $nc_message_wp;
						}

							//  Check if HTTP request returned an error
							if (is_wp_error($nc_response)) {

								$nc_response = $nc_response->get_error_message();
						// Otherwise set $nc_response equal to response code
							} else {

								$nc_response = $nc_code;
							}



					// Delete Wordpress User If  The HTTP responses == 100 (Success)
				  	if ($nc_response == '100') {

							// delete the user
							$user_id = wp_delete_user( $user_id );
							// check for errors
							if ( is_wp_error( $user_id ) ) {

								// get the error message
								$user_dd = $user_id->get_error_message();
							}
						}
			} else {
				// user not found
				$user_id = 'User not found.';
				$nc_response = $user_id;
			}

			// Check if HTTP request was successfull for delete user
	    if (!is_numeric($nc_response) || is_numeric($nc_response) & $nc_response != '100') {
	      $user_id = esc_html__( 'Operation failed, Please check NextCloud URL, username, and password in Nextcloud settings page.' , 'wnus' );
	    }

			// Set a message $user_id if its numeric
			if ($user_id == '1'){
				$user_id = 'WordPress Delete User/s Succesfully';
			}
	    if ($user_id == '2'){
	      $user_id = 'WordPress Delete User/s Failed';
	    }
		}

		// $user_id = $IDs_checked;

    $nc_code = '<br>Response Code: '. $nc_code;
    $nc_message = '<br>Response Message: '. $nc_message;

		$redirect_url = $_POST['redirect-url'];
		$front_back_end = $_POST['front-back-end'];

		if ($front_back_end == 'frontend') {
			// set the redirect url
				$location = 					 $redirect_url
															.'?result-wp='. urlencode( $user_id )
															.'&result-nc='. urlencode($nc_response)
															.'&$nc_response-code='. urlencode($nc_code)
															.'&$nc_response-message='. urlencode($nc_message);
		} if ($front_back_end == 'backend') {
			// set the redirect url
			$location = 					 $redirect_url
														.'?page=user-group-manager'
														.'&result-wp='. urlencode( $user_id )
														.'&result-nc='. urlencode($nc_response)
														.'&$nc_response-code='. urlencode($nc_code)
														.'&$nc_response-message='. urlencode($nc_message);
		}

		// redirect
		wp_redirect( $location );

		exit;


  	}
	}
}
add_action( 'admin_post_wnus_user_form_response', 'wnus_user_form' );

// Create User
function create_user_add_user() {

	// check if nonce is valid
	if ( isset( $_POST['wnus-nonce'] ) && wp_verify_nonce( $_POST['wnus-nonce'], 'wnus-nonce' ) ) {

		// check if user is allowed
		// if ( ! is_user_logged_in() ) wp_die();

		// get submitted username
		if ( isset( $_POST['username'] ) && ! empty( $_POST['username'] ) ) {

			$username = sanitize_user( $_POST['username'] );

		} else {

			$username = '';

		}

		// get submitted email
		if ( isset( $_POST['email'] ) && ! empty( $_POST['email'] ) ) {

			$email = sanitize_email( $_POST['email'] );

		} else {

			$email = '';

		}

		// get submitted password
		if ( isset( $_POST['password'] ) && ! empty( $_POST['password'] ) ) {

			$password = $_POST['password']; // sanitized by wp_create_user()

		} else {

			$password = wp_generate_password();

		}

		// set user_id variable
		$user_id = '';

		// check if user exists
		$username_exists = username_exists( $username );
		$email_exists = email_exists( $email );

		if ( $username_exists || $email_exists ) {

			$user_id = esc_html__( 'The user already exists.', 'wnus' );

		}

		// check non-empty values
		if ( empty( $username ) || empty( $email ) ) {

			$user_id = esc_html__( 'Required: username and email.', 'wnus' );

		}

		// HTTP request details
		$nc_create_user_username    = isset( $username )   ? '&userid='. urlencode($username)  : '';
		$nc_create_user_password    = isset( $password )   ? '&password='. urlencode($password)  : '';
		$nc_create_user_email    = isset( $email )   ? '&email='. urlencode($email)  : '';

    // Creat Nextcloud user
    if(empty( $user_id )){

      // Create NextCloud User
		$nc_create_user_response = nc_request('POST', '/ocs/v1.php/cloud/users?'. $nc_create_user_username . $nc_create_user_password . $nc_create_user_email);


		 // Get Response code and message if HTTP request did not return an error
		 if (!is_wp_error($nc_create_user_response)) {

        $nc_create_user_code_r = wp_remote_retrieve_response_code( $nc_create_user_response );
        $nc_create_user_message_r = wp_remote_retrieve_response_message( $nc_create_user_response );


				$xml = $nc_create_user_response['body'];
				$ob= simplexml_load_string($xml);
				$json  = json_encode($ob);
				$configData = json_decode($json, true);

				$nc_create_user_code_nc = $configData['meta']['statuscode'];
				$nc_create_user_message_nc = $configData['meta']['message'];

				$nc_create_user_code    = isset( $nc_create_user_code_nc )   ? $nc_create_user_code_nc  : $nc_create_user_code_r;
        $nc_create_user_message    = isset( $nc_create_user_message_nc )   ? $nc_create_user_message_nc  : $nc_create_user_message_r;
			}


        //  Check if HTTP request returned an error
        if (is_wp_error($nc_create_user_response)) {

          $nc_create_user_response = $nc_create_user_response->get_error_message();
      // Otherwise set $nc_create_user_response equal to response code
        } else {

          $nc_create_user_response = $nc_create_user_code;
        }

    } else {
      $nc_create_user_response = $user_id;
    }

		// Create Wordpress user
		if ( empty( $user_id ) & $nc_create_user_response == '100') {


   // Create WordPress User
			$user_id = wp_create_user( $username, $password, $email );


    // check if WP returned with an error while creating user
      if ( is_wp_error( $user_id ) ) {

				$user_id = $user_id->get_error_message();

			}else {
				//************ Add User to Group Member *************
				//Add user to group NextCloud
				$user_group = 'Member';
				$nc_response = nc_request('POST', '/ocs/v1.php/cloud/users/'. $username .'/groups?groupid='. $user_group);
				//Add user to group WordPress
				// Create an instance of WP_user Class
				$u = new WP_User($user_id);
				// Add role
				$u->add_role( $user_group );

				//************ Send Welcome Email **************
				// // email password
				// $subject = 'Welcome to Lones!';
				// $message = 'You can log in using username: '. $username .' and password: ' . $password;
				//
				// wp_mail( $email, $subject, $message );
			}

		}

	// Check if HTTP request was successfull if not numeric == WP_Error
    if (empty( $user_id ) & !is_numeric($nc_create_user_response) || empty( $user_id ) & is_numeric($nc_create_user_response) & $nc_create_user_response != '100') {
      $user_id = esc_html__( 'Operation failed, Please check NextCloud URL, username, and password in Nextcloud settings page.' , 'wnus' );
    }


		if (is_numeric($user_id)){
			$user_id = 'Wordpress User added succesfully';
		}

		$nc_code = '<br>Response Code: '. $nc_create_user_code;
		$nc_message = '<br>Response Message: '. $nc_create_user_message;
		$nc_response = $nc_create_user_code;

		$redirect_url = $_POST['redirect-url'];
		$front_back_end = $_POST['front-back-end'];

		if ($front_back_end == 'frontend') {
			// set the redirect url
				$location = 					 $redirect_url
															.'?result-wp='. urlencode( $user_id )
															.'&result-nc='. urlencode($nc_response)
															.'&$nc_response-code='. urlencode($nc_code)
															.'&$nc_response-message='. urlencode($nc_message);
		} if ($front_back_end == 'backend') {
			// set the redirect url
			$location = 					 $redirect_url
														.'?page=user-group-manager'
														.'&result-wp='. urlencode( $user_id )
														.'&result-nc='. urlencode($nc_response)
														.'&$nc_response-code='. urlencode($nc_code)
														.'&$nc_response-message='. urlencode($nc_message);
		}

		wp_redirect( $location );

		exit;

	}

}
add_action( 'admin_post_wnus_create_user_form_response', 'create_user_add_user' );


// Update User
function wnus_update_user() {

	// check if nonce is valid
	if ( isset( $_POST['wnus-nonce'] ) && wp_verify_nonce( $_POST['wnus-nonce'], 'wnus-nonce' ) ) {

		// check if user is allowed
		// if ( ! is_user_logged_in() ) wp_die();


		// Seperate email and user_login from User select list
		$email_login = $_POST['email-login'];
		$seperate_email_login = explode('|', $email_login);

		// Get User's current email and Login username_exists
		$email = $seperate_email_login[0];
		$user_login = $seperate_email_login[1];

		// get new display name
		if ( isset( $_POST['display-name'] ) && ! empty( $_POST['display-name'] ) ) {

			$new_displayname = sanitize_user( $_POST['display-name'] );

		} else {

			$new_displayname = '';
		}

		// get new email
		if ( isset( $_POST['email'] ) && ! empty( $_POST['email'] ) ) {

			$new_email = sanitize_email( $_POST['email'] );

		} else {

			$new_email = '';
		}

		// get new passwird
		if ( isset( $_POST['password'] ) && ! empty( $_POST['password'] ) ) {

			$new_password = $_POST['password'];

		} else {

			$new_password = '';
		}


				// HTTP request details
				$nc_user_login    = ( $user_login != '' )   ? urlencode($user_login) . '?'  : '';
				$nc_new_displayname    = ( $new_displayname != '' )   ? '&key=displayname&value='. urlencode($new_displayname)  : '';
				$nc_new_email    = ( $new_email != '' )   ? '&key=email&value='. urlencode($new_email)  : '';
				$nc_new_password    = ( $new_password != '' )   ? '&key=password&value='. urlencode($new_password)  : '';

				// URLs for updating email and display name
				$nc_displayname_url = '/ocs/v1.php/cloud/users/'. $nc_user_login . $nc_new_displayname;
				$nc_email_url = '/ocs/v1.php/cloud/users/'. $nc_user_login . $nc_new_email;
				$nc_password_url = '/ocs/v1.php/cloud/users/'. $nc_user_login . $nc_new_password;

	  // get the user id
		$user_id = email_exists( $email );

		// Check if forms are filled out
		if (is_numeric($user_id) && $new_email == '' && $new_displayname == '' && $new_password == ''){
			$form_empty = 'true';
		} else {
			$form_empty = 'false';
		}

		//  If user id exists
		if ( is_numeric( $user_id ) && $form_empty == 'false' ) {

			// Update NextCloud User displayname
			if (!$nc_new_displayname == '') {
					$nc_response_displayname = nc_request('PUT', $nc_displayname_url);

				 // Get Response code and message if HTTP request did not return an error
				 if (!is_wp_error($nc_response_displayname)) {

						$nc_displayname_code_wp = wp_remote_retrieve_response_code( $nc_response_displayname );
						$nc_displayname_message_wp = wp_remote_retrieve_response_message( $nc_response_displayname );

						$nc_displayname_xml = $nc_response_displayname['body'];
						$nc_displayname_ob= simplexml_load_string($nc_displayname_xml);
						$nc_displayname_json  = json_encode($nc_displayname_ob);
						$nc_displayname_configData = json_decode($nc_displayname_json, true);

						$nc_displayname_code_nc = $nc_displayname_configData['meta']['statuscode'];
						$nc_displayname_message_nc = $nc_displayname_configData['meta']['message'];

						$nc_displayname_code    = isset( $nc_displayname_code_nc )   ? $nc_displayname_code_nc  : $nc_displayname_code_wp;
						$nc_displayname_message    = isset( $nc_displayname_message_nc )   ? $nc_displayname_message_nc  : $nc_displayname_message_wp;
					}

						//  Check if HTTP request returned an error
						if (is_wp_error($nc_response_displayname)) {

							$nc_response_displayname = $nc_response_displayname->get_error_message();
					// Otherwise set $nc_response_email equal to response code
						} else {

							$nc_response_displayname = $nc_displayname_code;
						}
					}

			// Update Nextcloud User email
			if (!$nc_new_email == '') {
					$nc_response_email = nc_request('PUT', $nc_email_url);

				 // Get Response code and message if HTTP request did not return an error
				 if (!is_wp_error($nc_response_email)) {

						$nc_email_code_wp = wp_remote_retrieve_response_code( $nc_response_email );
						$nc_email_message_wp = wp_remote_retrieve_response_message( $nc_response_email );

						$nc_email_xml = $nc_response_email['body'];
						$nc_email_ob= simplexml_load_string($nc_email_xml);
						$nc_email_json  = json_encode($nc_email_ob);
						$nc_email_configData = json_decode($nc_email_json, true);

						$nc_email_code_nc = $nc_email_configData['meta']['statuscode'];
						$nc_email_message_nc = $nc_email_configData['meta']['message'];

						$nc_email_code    = isset( $nc_email_code_nc )   ? $nc_email_code_nc  : $nc_email_code_wp;
						$nc_email_message    = isset( $nc_email_message_nc )   ? $nc_email_message_nc  : $nc_email_message_wp;
					}

						//  Check if HTTP request returned an error
						if (is_wp_error($nc_response_email)) {

							$nc_response_email = $nc_response_email->get_error_message();
					// Otherwise set $nc_response_email equal to response code
						} else {

							$nc_response_email = $nc_email_code;
						}
					}

					// Update Nextcloud User password
					if (!$nc_new_password == '') {
							$nc_response_password = nc_request('PUT', $nc_password_url);

						 // Get Response code and message if HTTP request did not return an error
						 if (!is_wp_error($nc_response_password)) {

								$nc_password_code_wp = wp_remote_retrieve_response_code( $nc_response_password );
								$nc_password_message_wp = wp_remote_retrieve_response_message( $nc_response_password );

								$nc_password_xml = $nc_response_password['body'];
								$nc_password_ob= simplexml_load_string($nc_password_xml);
								$nc_password_json  = json_encode($nc_password_ob);
								$nc_password_configData = json_decode($nc_password_json, true);

								$nc_password_code_nc = $nc_password_configData['meta']['statuscode'];
								$nc_password_message_nc = $nc_password_configData['meta']['message'];

								$nc_password_code    = isset( $nc_password_code_nc )   ? $nc_password_code_nc  : $nc_password_code_wp;
								$nc_password_message    = isset( $nc_password_message_nc )   ? $nc_password_message_nc  : $nc_password_message_wp;
							}

								//  Check if HTTP request returned an error
								if (is_wp_error($nc_response_password)) {

									$nc_response_password = $nc_response_email->get_error_message();
							// Otherwise set $nc_response_email equal to response code
								} else {

									$nc_response_password = $nc_password_code;
								}
							}

				// Create Wordpress User If  The HTTP responses == 100 (Success)
			  	if (
						$new_email == '' && $new_password == '' && $nc_response_displayname == '100'
						|| $new_password == '' && $new_displayname == '' && $nc_response_email == '100'
						|| $new_displayname == '' && $new_email == '' && $nc_response_password == '100'
						|| $new_displayname == '' && $nc_response_email == '100' && $nc_response_password == '100'
						|| $new_email == '' && $nc_response_password == '100' && $nc_response_displayname == '100'
						|| $new_password == '' && $nc_response_displayname == '100' && $nc_response_email == '100'
						|| $nc_response_displayname == '100' && $nc_response_email == '100' && $nc_response_password == '100') {

						// define the parameters. check if email is set otherwise it would set email to none
						if ( $new_email != '') {
							$userdata = array( 'ID' => $user_id, 'user_email' => $new_email, 'display_name' => $new_displayname, 'user_pass' => $new_password);
					 }
					 	if ( $new_email == '') {
						  $userdata = array( 'ID' => $user_id, 'display_name' => $new_displayname, 'user_pass' => $new_password);
					 }
						// update the user
						$user_id = wp_update_user( $userdata );

						// check for errors
						if ( is_wp_error( $user_id ) ) {

							// get the error message
							$user_id = $user_id->get_error_message();
						}
					}
		} else {
			// user not found
			$user_id = 'User not found.';
			$nc_response = $user_id;
		}

		// If form is empty Set Message
		if ($form_empty == 'true' ){
			$user_id = 'Please fill out Form';
		}

// Set Nextcloud Response, Code, and Message
	  // If only one field is populated
	 	if (!$nc_new_displayname == '' && is_numeric( $user_id )){
			$nc_response = $nc_response_displayname;
			$nc_code = '<br>Display Name Response Code: '. $nc_displayname_code;
			$nc_message = '<br>Display Name Response Message: '. $nc_displayname_message;
		}
		if (!$nc_new_email == '' && is_numeric( $user_id )){
			$nc_response = $nc_response_email;
			$nc_code = '<br> Email Response Code: '. $nc_email_code;
			$nc_message = '<br>Email Response Message: '. $nc_email_message;
		}
		if (!$nc_new_password == '' && is_numeric( $user_id )){
			$nc_response = $nc_response_password;
			$nc_code = '<br>Password Name Response Code: '. $nc_password_code;
			$nc_message = '<br>Password Name Response Message: '. $nc_password_message;
		}

		// If 2 fields are populated
		if (!$nc_new_displayname == '' && !$nc_new_email == '' && is_numeric( $user_id )){
			$nc_response = $nc_response_displayname . $nc_email_code;
			$nc_code = '<p><br>Display Name Response Code: '. $nc_displayname_code .'<br>Email Response Code: '. $nc_email_code .'</p><br>';
			$nc_message = '<br>Display Name Response Message: '. $nc_displayname_message .'<br>Email Response Message: '. $nc_email_message .'</p>';
		}
		if (!$nc_new_displayname == '' && !$nc_new_password == '' && is_numeric( $user_id )){
			$nc_response = $nc_response_displayname . $nc_password_code;
			$nc_code = '<p><br>Display Name Response Code: '. $nc_displayname_code .'<br>Password Response Code: '. $nc_password_code .'</p><br>';
			$nc_message = '<br>Display Name Response Message: '. $nc_displayname_message .'<br>Password Response Message: '. $nc_password_message .'</p>';
		}
		if (!$nc_new_email == '' && !$nc_new_password == '' && is_numeric( $user_id )){
			$nc_response = $nc_response_email . $nc_password_code;
			$nc_code = '<p><br>Email Response Code: '. $nc_email_code .'<br>Password Response Code: '. $nc_password_code .'</p><br>';
			$nc_message = '<br>Email Response Message: '. $nc_email_message .'<br>Password Response Message: '. $nc_password_message .'</p>';
		}

		// If all fields are populated
		if (!$nc_new_displayname == '' && !$nc_new_email == '' && !$nc_new_password == '' && is_numeric( $user_id )){
			$nc_response = $nc_email_code . $nc_displayname_code . $nc_password_code;
			$nc_code = '<p>Display Name Response Code: '. $nc_displayname_code .'<br>Email Response Code: '. $nc_email_code .'<br>Password Response Code: '. $nc_password_code .'</p><br>';
			$nc_message = '<p>Display Name Response Message: '. $nc_displayname_message .'<br>Email Response Message: '. $nc_email_message .'<br>Password Response Message: '. $nc_password_message .'</p>';
		}

		// Check if HTTP request was successfull for update email and update displayname
		if (!$nc_new_displayname == '' && !is_numeric($nc_response_displayname) | !$nc_new_displayname == '' && is_numeric($nc_response_displayname) && $nc_response_displayname != '100') {
			$user_id = esc_html__( 'Update DisplayName Operation failed, Please check NextCloud URL, username, and password in Nextcloud settings page.   '.$url.'' , 'wnus' );
		}
	  if (!$nc_new_email == '' && !is_numeric($nc_response_email) | !$nc_new_email == '' && is_numeric($nc_response_email) && $nc_response_email != '100') {
	    $user_id = esc_html__( 'Update Email Operation failed, Please check NextCloud URL, username, and password in Nextcloud settings page.   '.$url.'' , 'wnus' );
	  }
	 if (!$nc_new_password == '' && !is_numeric($nc_response_password) | !$nc_new_password == '' && is_numeric($nc_response_password) && $nc_response_password != '100') {
		 $user_id = esc_html__( 'Update Password Operation failed, Please check NextCloud URL, username, and password in Nextcloud settings page.   '.$url.'' , 'wnus' );
	 }

		// Set a message $user_id if its numeric
		if (is_numeric($user_id)){
			$user_id = 'WordPress User updated succesfully';
			// $user_id = $url;
		}


		$redirect_url = $_POST['redirect-url'];
		$front_back_end = $_POST['front-back-end'];

		if ($front_back_end == 'frontend') {
			// set the redirect url
				$location = 					 $redirect_url
															.'?result-wp='. urlencode( $user_id )
															.'&result-nc='. urlencode($nc_response)
															.'&$nc_response-code='. urlencode($nc_code)
															.'&$nc_response-message='. urlencode($nc_message);
		} if ($front_back_end == 'backend') {
			// set the redirect url
			$location = 					 $redirect_url
														.'?page=user-group-manager'
														.'&result-wp='. urlencode( $user_id )
														.'&result-nc='. urlencode($nc_response)
														.'&$nc_response-code='. urlencode($nc_code)
														.'&$nc_response-message='. urlencode($nc_message);
		}

		// redirect
		wp_redirect( $location );

		exit;

	}

}
add_action( 'admin_post_wnus_update_user_form_response', 'wnus_update_user' );
