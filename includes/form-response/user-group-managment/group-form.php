<?php // Group Form

// Create Group
function wnus_create_group() {


  	// check if nonce is valid
  	if ( isset( $_POST['wnus-nonce'] ) && wp_verify_nonce( $_POST['wnus-nonce'], 'wnus-nonce' ) ) {

  		// check if user is allowed
  		// if ( ! is_user_logged_in() ) wp_die();

      if ( isset( $_POST['unit-select-list']) && !empty( $_POST['unit-select-list'] )){
        $new_role = $_POST['unit-select-list'];
      } else {
        $new_role = '';
      }

      // Get Role
      if ( isset( $_POST['create-group'] ) && ! empty( $_POST['create-group'] ) ) {

  			$new_role .= $_POST['create-group'];
  		} else {

  			$new_role = '';
  		}


  		// Get Database options
  		$options = get_option( 'wnus_options', wnus_options_default() );

  		// Get details from Database
  		$nextcloud_server_url = isset( $options['nextcloud_server_url']) ? sanitize_text_field( $options['nextcloud_server_url'] ) : '';
  		$nextcloud_server_username = isset( $options['nextcloud_server_username']) ? sanitize_text_field( $options['nextcloud_server_username'] ) : '';
  		$nextcloud_server_pass = isset( $options['nextcloud_server_pass']) ? sanitize_text_field( $options['nextcloud_server_pass'] ) : '';

  		// HTTP request details
  		$nc_user_login = $user_login;

  		// URLs for updating email and display name
  		$url = 'https://'. $nextcloud_server_username .':'. $nextcloud_server_pass .'@'. $nextcloud_server_url .'/ocs/v1.php/cloud/groups?groupid='. $new_role;

  		// Args for HTTP Request
  		$args = array( 'method' => 'POST',
  			              'timeout' => 40,
  			              'headers' => array(
  			                'OCS-APIRequest' => 'true',
  			                'Content-Type' => 'application/x-www-form-urlencoded',),
  			            );



  			// Update NextCloud User
  					$nc_response = wp_remote_request( $url, $args );

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



  				// Create Wordpress Group If The HTTP response == 100 (Success)  $nc_response == '100'
  			  	if ($nc_response == '100') {
              // Add role
              $user_id = add_role($new_role, $new_role);

              if (get_role($new_role)){
              $user_id = 'Group Created';
            }else {
              $user_id = 'Create Group Failed';
            }

  					}


  		// Check if HTTP request was successfull for update email and update displayname
      if ($nc_response != '100') {
        $user_id = esc_html__( 'Operation failed, Please check NextCloud URL, username, and password in Nextcloud settings page.' , 'wnus' );
      }

  		// Set a message $user_id if its numeric
  		if (is_numeric($user_id)){
  			$user_id = $user_login. ' Has been added to group '. $wp_role;
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

  		exit;

  	}

  }
add_action( 'admin_post_wnus_group_managment_create_form_response', 'wnus_create_group' );


// Delete Group
function wnus_delete_group() {


  function get_checked_roles(){
  // Get array of wordpress roles
  $roles_s = '';
  $wnus_editable_roles = get_editable_roles();
  foreach ($wnus_editable_roles as $role => $details) {
      $roles_s .= $role .'|';
  }
  $roles_s = rtrim($roles_s, '|');
  $roles_a = explode('|', $roles_s);


  // Returns an array with IDs which where checked in user table
    $roles_checked_s = '';
    foreach ($roles_a as $role) {
      if ( $_POST['group-id-'. $role] != '') {
        $roles_checked_s .= $role .'|';
      }
    }
    // Trim last pipe from string
    $roles_checked_s = rtrim($roles_checked_s, '|');
    $roles_checked_a = explode('|', $roles_checked_s);
    return $roles_checked_a;
  }

  // Check what action was selected values: add-to-group, remove-from-group, delete-user
  $select_action = $_POST['select-action'];

  // Get checked users
    $checked_roles = get_checked_roles();






	// check if nonce is valid
	if ( isset( $_POST['wnus-nonce'] ) && wp_verify_nonce( $_POST['wnus-nonce'], 'wnus-nonce' ) ) {

  	// check if user is allowed
  	// if ( ! is_user_logged_in() ) wp_die();

  foreach ($checked_roles as $wp_role) {


    if ($wp_role != 'Administrator') {


  		// Get Database options
  		$options = get_option( 'wnus_options', wnus_options_default() );

  		// Get details from Database
  		$nextcloud_server_url = isset( $options['nextcloud_server_url']) ? sanitize_text_field( $options['nextcloud_server_url'] ) : '';
  		$nextcloud_server_username = isset( $options['nextcloud_server_username']) ? sanitize_text_field( $options['nextcloud_server_username'] ) : '';
  		$nextcloud_server_pass = isset( $options['nextcloud_server_pass']) ? sanitize_text_field( $options['nextcloud_server_pass'] ) : '';

  		// HTTP request details
  		$nc_user_login = $user_login;

  		// URLs for updating email and display name
  		$url = 'https://'. $nextcloud_server_username .':'. $nextcloud_server_pass .'@'. $nextcloud_server_url .'/ocs/v1.php/cloud/groups/'. $wp_role;

  		// Args for HTTP Request
  		$args = array( 'method' => 'DELETE',
  			              'timeout' => 40,
  			              'headers' => array(
  			                'OCS-APIRequest' => 'true',
  			                'Content-Type' => 'application/x-www-form-urlencoded',),
  			            );



  			// Update NextCloud User
  					$nc_response = wp_remote_request( $url, $args );

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



  				// Delete Wordpress Group If The HTTP response == 100 (Success)  $nc_response == '100'
  			  	if ($nc_response == '100') {
              // Add role
              remove_role($wp_role, $wp_role);

              if (!get_role($wp_role)){
              $user_id = 'Group/s Deleted';
            }else {
              $user_id = 'Delete Group/s Failed';
            }
          }

    		// Check if HTTP request was successfull for update email and update displayname
        if ($nc_response != '100') {
          $user_id = esc_html__( 'Operation failed, Please check NextCloud URL, username, and password in Nextcloud settings page.' , 'wnus' );
        }
    } if ($wp_role == 'Administrator') {
      $user_id = "Administrator Cannot be Deleted";
      $nc_response = $user_id;
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
  }
add_action( 'admin_post_wnus_group_managment_delete_form_response', 'wnus_delete_group' );
