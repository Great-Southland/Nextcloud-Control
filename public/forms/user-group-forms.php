<?php // Frontend user/group manager

// Add Create User Page
function wnus_create_user_form_frontend() {
//******************** Do not continue loading document if user does not have a leader role
	$current_user_id = get_current_user_id();
	$current_user_roles = get_user_roles($current_user_id);
	//Get all roles of current user with word Leader
	$current_user_roles_leader = array_contains($current_user_roles, 'Leader');
	if ($current_user_roles_leader){

	// Form Vars
		$form_action = esc_url( admin_url( 'admin-post.php' ) );
		$form_nonce = wp_create_nonce( 'wnus-nonce' );
		$redirect_url = strtok($_SERVER["REQUEST_URI"],'?');

		// Notice Code
		if (!isset($_GET['result-wp']) && !isset($_GET['result-nc']) && !isset($_GET['nc_response-code']) && !isset($_GET['nc_response-message'])) {
			$result_wp = '';
			$result_nc = '';
			$nc_response_code = '';
			$nc_response_message = '';
			$notice = '';
		} else {
			$result_wp = $_GET['result-wp'];
			$result_nc = $_GET['result-nc'];
			$nc_response_code = $_GET['nc_response-code'];
			$nc_response_message = $_GET['nc_response-message'];
			$notice = '';

			$notice .= '
				<div class="wnus-notice wnus-notice-success wnus-is-dismissible">
					<p><strong>'. $result_wp .'</strong></p>
				</div>';

			if (is_numeric( $result_nc )) {
				$notice .= '
				<div class="wnus-notice wnus-notice-success wnus-is-dismissible">
					<p><strong>NextCloud: '. $nc_response_code . $nc_response_message .'</strong></p>
				</div>';

			 } else {
			 $notice .= '
				<div class="wnus-notice wnus-notice-warning wnus-is-dismissible">
					<p><strong>NextCloud: '. $result_nc .'</strong></p>
				</div>';
			}
		}

	// Output Code
		$output = $notice
			.'<div class="wrap">
					<form action="'. $form_action .'" method="post">
						<h3>Add New User</h3>
						<p>
							<label for="username">Username</label><br />
							<input class="regular-text" type="text" size="40" name="username" placeholder="Username" id="username">
						</p>
						<p>
							<label for="email">Email</label><br />
							<input class="regular-text" type="text" size="40" name="email" placeholder="Email" id="email">
						</p>
						<p>
							<label for="password">Password</label><br />
							<input class="regular-text" type="text" size="40" name="password" placeholder="Password" id="password">
						</p>
						<p>The user will receive this information via email.</p>
						<input type="hidden" name="action" value="wnus_create_user_form_response">
						<input type="hidden" name="front-back-end" value="frontend">
						<input type="hidden" name="redirect-url" value="'. $redirect_url .'">
						<input type="hidden" name="wnus-nonce" value="'. $form_nonce .'">
						<input type="submit" class="button button-primary" value="Add User">
					</form>
				</div>';
	} else {
		$output = '
		<div class="section-inner thin error404-content">
			<h1 class="entry-title">Page Not Found</h1>
			<div class="intro-text"><p>The page you were looking for could not be found. It might have been removed, renamed, or did not exist in the first place.</p></div>
			<form role="search" aria-label="404 not found" method="get" class="search-form" action="http://localhost/~davidforg/SitesWordpress/wordpress/">
				<label for="search-form-2">
					<span class="screen-reader-text">Search for:</span>
					<input type="search" id="search-form-2" class="search-field" placeholder="Search …" value="" name="s">
				</label>
				<input type="submit" class="search-submit" value="Search">
			</form>
		</div>';
	}
		return $output;
 }
add_shortcode('create-user', 'wnus_create_user_form_frontend');
// Add Edit User Page
function wnus_edit_user_form_frontend() {
	$current_user_id = get_current_user_id();
	$current_user_roles = get_user_roles($current_user_id);
	//Get all roles of current user with word Leader
	$current_user_roles_leader = array_contains($current_user_roles, 'Leader');
	if ($current_user_roles_leader){
	// Set variables
			if (!isset($_GET['email-login']) && !isset($_GET['display-name']) && !isset($_GET['user-email']) && !isset($_GET['user-password']) && !isset($_GET['user-id'])) {
				$user_info = wp_get_current_user();
				$user_name = $user_info->user_login;
				$display_name = $user_info->display_name;
				$user_email = $user_info->user_email;
				$user_password = '************';
				$user_id = $user_info->ID;
				$email_login = $user_email .'|'. $user_name;

			} else {
				$email_login = $_GET['email-login'];
				$display_name = $_GET['display-name'];
				$user_email = $_GET['user-email'];
				$user_password = $_GET['user-password'];
				$user_id = $_GET['user-id'];
			}
	// Get Users roles from ID
		$user_roles = get_user_roles_edit($user_id); //Returns array
		// Create string to store roles
		$roles = '';
		// Get Roles
		foreach ($user_roles as $role) {
			$roles .= ucfirst($role) .', ';
		}
	// Form vars
	 	$redirect_url = strtok($_SERVER["REQUEST_URI"],'?');
		$form_action = esc_url( admin_url( 'admin-post.php' ) );
		$form_nonce = wp_create_nonce( 'wnus-nonce' );

		// Notice Code
		if (!isset($_GET['result-wp']) && !isset($_GET['result-nc']) && !isset($_GET['nc_response-code']) && !isset($_GET['nc_response-message'])) {
			$result_wp = '';
			$result_nc = '';
			$nc_response_code = '';
			$nc_response_message = '';
			$notice = '';
		} else {
			$result_wp = $_GET['result-wp'];
			$result_nc = $_GET['result-nc'];
			$nc_response_code = $_GET['nc_response-code'];
			$nc_response_message = $_GET['nc_response-message'];
			$notice = '';

			$notice .= '
				<div class="wnus-notice wnus-notice-success wnus-is-dismissible">
					<p><strong>'. $result_wp .'</strong></p>
				</div>';

			if (is_numeric( $result_nc )) {
				$notice .= '
				<div class="wnus-notice wnus-notice-success wnus-is-dismissible">
					<p><strong>NextCloud: '. $nc_response_code . $nc_response_message .'</strong></p>
				</div>';

			 } else {
			 $notice .= '
				<div class="wnus-notice wnus-notice-warning wnus-is-dismissible">
					<p><strong>NextCloud: '. $result_nc .'</strong></p>
				</div>';
			}
		}

	$output = $notice .'
		<div class="wrap">
			<form action="'. $form_action .'" method="post">
				<h3>Edit User</h3>
				<p>
					<label for="display-name">
						Enter a new Display Name for this user:
					</label><br />
					<input class="regular-text" type="text" size="40" name="display-name" placeholder="'. $display_name .'" id="display-name">
				</p>
				<p>
					<label for="email">
						Enter a new Email for this user:
					</label><br />
					<input class="regular-text" type="text" size="40" name="email" placeholder="'. $user_email .'" id="email">
				</p>
				<p>
					<label for="password">
						Enter a new Password for this user:
					</label><br />
					<input class="regular-text" type="text" size="40" name="password" placeholder="'. $user_password .'" id="password">
				</p>
				<p>
						<h4 style="margin-bottom:5px;">Groups You Are In:</h4>
						<span>'. $roles .'</span>
			  </p>
				<input type="hidden" name="action" value="wnus_update_user_form_response">
				<input type="hidden" name="email-login" value="'. $email_login .'">
				<input type="hidden" name="front-back-end" value="frontend">
				<input type="hidden" name="redirect-url" value="'. $redirect_url .'">
				<input type="hidden" name="wnus-nonce" value="'. $form_nonce .'">
				<input type="submit" name="update-user-submit" class="button button-primary" value="Update">
			</form>
		</div>';
	}else{
		$output = '
		<div class="section-inner thin error404-content">
			<h1 class="entry-title">Page Not Found</h1>
			<div class="intro-text"><p>The page you were looking for could not be found. It might have been removed, renamed, or did not exist in the first place.</p></div>
			<form role="search" aria-label="404 not found" method="get" class="search-form" action="http://localhost/~davidforg/SitesWordpress/wordpress/">
				<label for="search-form-2">
					<span class="screen-reader-text">Search for:</span>
					<input type="search" id="search-form-2" class="search-field" placeholder="Search …" value="" name="s">
				</label>
				<input type="submit" class="search-submit" value="Search">
			</form>
		</div>';
	}
	return $output;
 }
add_shortcode('edit-user', 'wnus_edit_user_form_frontend');

function wnus_user_form_frontend() {
	$current_user_id = get_current_user_id();
	$current_user_roles = get_user_roles($current_user_id);
	//Get all roles of current user with word Leader
	$current_user_roles_leader = array_contains($current_user_roles, 'Leader');
	if ($current_user_roles_leader){
		//-------------------- Get Current User Info ------------------------
			// Get Current Users Roles
			$current_user_info = wp_get_current_user();
			$current_user_id = $current_user_info->ID;
			$current_user_roles = get_user_roles($current_user_id);
			// Get Current Users Leader Roles Example: Leader-Venturer
			$current_user_roles_leader = array_contains($current_user_roles, 'Leader');

			// -------------------------- Get Database options and wp users -------------------------
			$options = get_option( 'wnus_options', wnus_options_default() );
			$wp_users = get_users( array( 'fields' => array( 'user_login', 'display_name', 'user_email', 'ID' ) ) );

		 // --------------------------- Create Select list ----------------------------
		 	if (current_user_can( 'list_users' )){
				// Show all role if current user has list_users capability
				$select_list_roles = get_roles();
				$roles_drop_down = '<option value="">- Select Group -</option>';
				foreach($select_list_roles as $wnus_role) {
					$wnus_role_capital = ucfirst($wnus_role);
					$roles_drop_down .= '<option value="' . $wnus_role .'">' . $wnus_role_capital .'</option>' . "\n";
				}

			} else {
					// Get current users Leader Roles
					$current_user_roles_leader = array_contains($current_user_roles, 'Leader');

					// Initialize $select_list_roles_result array
					$select_list_roles_result = [];
					// Loop through leader roles
					foreach($current_user_roles_leader as $role){
						// Get group current Leader is leader of; Example if role is Leader-Venturer $leader_role would equal Veturer
						$seperate_leader = explode('-', $role);
						$leader_role = $seperate_leader[1];
						// Get wp_roles
						$wnus_wp_roles = get_roles();
						// Get roles with word $leader_role in it
						$leader_role_roles = array_contains($wnus_wp_roles, $leader_role);
						// Cycle throuch $leader_role_roles
						foreach($leader_role_roles as $leader_role_role){
							$select_list_roles_result[] = $leader_role_role;
						}
					}


			 		$roles_drop_down = '<option value="">- Select Group -</option>';
			 		foreach($select_list_roles_result as $wnus_role) {
						$wnus_role_capital = ucfirst($wnus_role);
						$roles_drop_down .= '<option value="' . $wnus_role .'">' . $wnus_role_capital .'</option>' . "\n";
					}
				}


		// ----------------------------- User Table Row Data ----------------------------
			// generate table row data

				$user_tr = '';
				$users_printed = [];
			foreach ($current_user_roles_leader as $leader_roles){
				$seperate_leader_roles = explode('-', $leader_roles);
				$leader_role = $seperate_leader_roles[1];

				foreach ( $wp_users as $user ) {
					$user_name = esc_html( $user->user_login );
					$user_display_name = esc_html( $user->display_name );
					$user_email = esc_html( $user->user_email);
					$user_id = esc_html( $user->ID);
					$user_roles = get_user_roles($user_id); //Returns array

						// Check if the user is in group current user is leader of
						$roles_section_filter = array_contains($user_roles, $leader_role);

						if ($roles_section_filter || count($user_roles) == '1'){

							// Check if $user_id's table has already been created
							if (!array_key_exists($user_id, $users_printed)){
								// Add $user_id to $users_printed array to show user has already been printed
								$users_printed[$user_id] = $user_id;

							// Reset Roles every loop
							$roles = '';

							// Get Roles
							foreach ($user_roles as $role) {
								$roles .= ucfirst($role) .', ';
							}
							$edit_user_page_url = isset( $options['edit_user_page_url']) ? sanitize_text_field( $options['edit_user_page_url'] ) : '';
							$edit_user_link =  $edit_user_page_url .'?email-login=' . $user_email .'|'. $user_name .'&user-id='. $user_id .'&display-name='. $user_display_name .'&user-email='. $user_email .'&user-password=********';

							$user_tr .= '<tr>
													<td class="user-checkbox"><input type="checkbox" name="user-id-'. $user_id .'"></td>
													<td class="name">'. $user_display_name .' ('. $user_name .')</td>
													<td class="email">'. $user_email .'</td>
													<td class="groups">'. $roles .'</td>
													<td class="edit-user"><a href="'. $edit_user_link .'" class="edit-user-link">Edit User</a></td>
													</tr>';
						}
					}
				}
			}

				// Form vars
				 	$redirect_url = strtok($_SERVER["REQUEST_URI"],'?');
					$form_action = esc_url( admin_url( 'admin-post.php' ) );
					$form_nonce = wp_create_nonce( 'wnus-nonce' );
					$create_user_page_url = isset( $options['create_user_page_url']) ? sanitize_text_field( $options['create_user_page_url'] ) : '';

				// Notice Code
				if (!isset($_GET['result-wp']) && !isset($_GET['result-nc']) && !isset($_GET['nc_response-code']) && !isset($_GET['nc_response-message'])) {
					$result_wp = '';
					$result_nc = '';
					$nc_response_code = '';
					$nc_response_message = '';
					$notice = '';
				} else {
					$result_wp = $_GET['result-wp'];
					$result_nc = $_GET['result-nc'];
					$nc_response_code = $_GET['nc_response-code'];
					$nc_response_message = $_GET['nc_response-message'];
					$notice = '';

					$notice .= '
						<div class="wnus-notice wnus-notice-success wnus-is-dismissible">
							<p><strong>'. $result_wp .'</strong></p>
						</div>';

					if (is_numeric( $result_nc )) {
						$notice .= '
						<div class="wnus-notice wnus-notice-success wnus-is-dismissible">
							<p><strong>NextCloud: '. $nc_response_code . $nc_response_message .'</strong></p>
						</div>';

					 } else {
					 $notice .= '
						<div class="wnus-notice wnus-notice-warning wnus-is-dismissible">
							<p><strong>NextCloud: '. $result_nc .'</strong></p>
						</div>';
					}
				}



	$output = $notice
			.'<div class="user-group-manager-wrap">
					<div class="users" style="padding-top: 30px;">
						<span class="users-header"><h2 style="display:inline-block;padding-right:10px;">Users <a href="'. $create_user_page_url .'" class="page-title-action">Add New</a></h2></span>
						<form action="'. $form_action .'" method="post">
							<table class="user-table" style="width:100%;border:1px solid black;margin-top:35px;">
								<thead style="background-color:white;">
									<tr>
										<th style="text-align:left;width:15px;"><input type="checkbox" name="user-checkbox"></th>
										<th style="text-align:left;">Name</th>
										<th style="text-align:left;">Email</th>
										<th style="text-align:left;">Groups</th>
										<th style="text-align:left;">Edit User</th>
								  </tr>
								</thead>
								<tbody>'.
									$user_tr
							.'</tbody>
							</table>
							<select required style="margin:10px;display:inline-block;" name="select-action" id="select-action" onchange="optionCheck(this);">
								<option value="">Select Action</option>
								<option value="add-to-group">Add User/s to Group</option>
								<option value="remove-from-group">Remove User/s from Group</option>
								<option value="delete-user">Delete User/s</option>
							</select>
					  	<select id="wp-roles" style="display: none;" name="wp-roles">'.
								$roles_drop_down
						.'</select>
							<input type="hidden" name="action" value="wnus_user_form_response">
							<input type="hidden" name="front-back-end" value="frontend">
							<input type="hidden" name="redirect-url" value="'. $redirect_url .'">
							<input type="hidden" name="wnus-nonce" value="'. $form_nonce .'">

							<input type="submit" name="form-submit" class="button button-primary" style="margin:10px;" value="Apply">
						</form>
					</div>
				</div>';
		}else{
			$output = '
			<div class="section-inner thin error404-content">
				<h1 class="entry-title">Page Not Found</h1>
				<div class="intro-text"><p>The page you were looking for could not be found. It might have been removed, renamed, or did not exist in the first place.</p></div>
				<form role="search" aria-label="404 not found" method="get" class="search-form" action="http://localhost/~davidforg/SitesWordpress/wordpress/">
					<label for="search-form-2">
						<span class="screen-reader-text">Search for:</span>
						<input type="search" id="search-form-2" class="search-field" placeholder="Search …" value="" name="s">
					</label>
					<input type="submit" class="search-submit" value="Search">
				</form>
			</div>';
		}
		return $output;
	}
add_shortcode('user-form', 'wnus_user_form_frontend');

function wnus_group_form_frontend() {
	$current_user_id = get_current_user_id();
	$current_user_roles = get_user_roles($current_user_id);
	//Get all roles of current user with word Leader
	$current_user_roles_leader = array_contains($current_user_roles, 'Leader');
	if ($current_user_roles_leader){

		// -------------------------- Get Current User Info ------------------------
		// Get Current Users Roles
		$current_user_info = wp_get_current_user();
		$current_user_id = $current_user_info->ID;
		$current_user_roles = get_user_roles($current_user_id);
		// get wp_roles
		$wnus_wp_roles = get_roles();
		// Get Current Users Leader Roles Example: Leader-Venturer
		$current_user_roles_leader = array_contains($current_user_roles, 'Leader');

		// -------------------------- Get Database options and wp users -------------------------
		$options = get_option( 'wnus_options', wnus_options_default() );
		$wp_users = get_users( array( 'fields' => array( 'user_login', 'display_name', 'user_email', 'ID' ) ) );

		// ------------------------- Create Group Select List ---------------------
		$unit_drop_down = '<option value="">- Select Unit -</option>';
		foreach($current_user_roles_leader as $unit) {
			$seperate_leader = explode('-', $unit);
			$leader_role = $seperate_leader[1];
			$leader_role .= '-';
			$unit_drop_down .= '<option value="' . $leader_role .'">' . $leader_role .'</option>' . "\n";
		}



	// ---------------------------- Create Group Table Row Data ---------------------------
	// array to hold roles
	$group_table_roles = [];
	foreach($current_user_roles_leader as $role) {
			$seperate_leader = explode('-', $role);
			$leader_role = $seperate_leader[1];
			// get roles which contain word $leader_role
			$leader_roles = array_contains($wnus_wp_roles, $leader_role);
			foreach ($leader_roles as $leader_role){
				$group_table_roles[] = $leader_role;
			}
		}

		// Strings to hold table row data
		$group_tr = '';
		// Cycle create table row for each role
		foreach ( $group_table_roles as $group_role ) {
		// Get users in current role
	  $group_role_users = get_users_by_role($group_role, 'user_nicename', 'ASC');
		// string to hold users in current group
		$group_users = '';

		foreach ($group_role_users as $user) {
			$display_name = $user->display_name;
			$group_users .= $display_name .', ';
		}
		// if there are now users in current group set $group_users to (No Users)
		if ($group_users == '') {
			$group_users = '(No Users)';
		}
		// Create a Table row
		$group_tr .= '<tr>
								<td class="group-checkbox"><input type="checkbox" name="group-id-'. $group_role .'"></td>
								<td class="group-name">'. ucfirst($group_role) .'</td>
								<td class="users">'. $group_users .'</td>
								</tr>';
	}

			// ---------------------------- Form vars ------------------------------
			 	$redirect_url = strtok($_SERVER["REQUEST_URI"],'?');
				$form_action = esc_url( admin_url( 'admin-post.php' ) );
				$form_nonce = wp_create_nonce( 'wnus-nonce' );
				$create_user_page_url = isset( $options['create_user_page_url']) ? sanitize_text_field( $options['create_user_page_url'] ) : '';

			// ----------------------------- Notice Code ----------------------------
			if (!isset($_GET['result-wp']) && !isset($_GET['result-nc']) && !isset($_GET['nc_response-code']) && !isset($_GET['nc_response-message'])) {
				$result_wp = '';
				$result_nc = '';
				$nc_response_code = '';
				$nc_response_message = '';
				$notice = '';
			} else {
				$result_wp = $_GET['result-wp'];
				$result_nc = $_GET['result-nc'];
				$nc_response_code = $_GET['nc_response-code'];
				$nc_response_message = $_GET['nc_response-message'];
				$notice = '';

				$notice .= '
					<div class="wnus-notice wnus-notice-success wnus-is-dismissible">
						<p><strong>'. $result_wp .'</strong></p>
					</div>';

				if (is_numeric( $result_nc )) {
					$notice .= '
					<div class="wnus-notice wnus-notice-success wnus-is-dismissible">
						<p><strong>NextCloud: '. $nc_response_code . $nc_response_message .'</strong></p>
					</div>';

				 } else {
				 $notice .= '
					<div class="wnus-notice wnus-notice-warning wnus-is-dismissible">
						<p><strong>NextCloud: '. $result_nc .'</strong></p>
					</div>';
				}
			}


// ----------------------- html ------------------------
$output = $notice
		.'<div class="user-group-manager-wrap">
				<div class="groups" style="padding-top:30px;">
					<form action="'. $form_action .'" method="post">
						<span class="groups-header"><h2 style="display:block;padding-right:10px;">Groups</h2>
						<select required id="unit-select-list" name="unit-select-list">'.
							$unit_drop_down
						.'</select>
							<input class="regular-text" style="width:200px;display:inline-block;margin-right:10px;" placeholder="Enter Name for Group" type="text" size="40" name="create-group" id="create-group">
						</span>
						<input type="hidden" name="action" value="wnus_group_managment_create_form_response">
						<input type="hidden" name="front-back-end" value="frontend">
						<input type="hidden" name="redirect-url" value="'. $redirect_url .'">
						<input type="hidden" name="wnus-nonce" value="'. $form_nonce .'">
						<input type="submit" name="create-group-submit" class="button button-primary" style="margin-top:12px;" value="Create">
					</form>

					<form action="'. $form_action .'" method="post">

						<table class="group-table" style="width:100%;border:1px solid black;margin-top:35px;">
							<thead style="background-color:white;">
								<tr>
									<th style="text-align:left;width:15px;"><input type="checkbox" name="group-checkbox"></th>
									<th style="text-align:left;">Group Name</th>
									<th style="text-align:left;">Users in Group</th>
								</tr>
							</thead>
							<tbody>'.
								$group_tr
						.'</tbody>
						</table>

						<input type="hidden" name="action" value="wnus_group_managment_delete_form_response">
						<input type="hidden" name="front-back-end" value="frontend">
						<input type="hidden" name="redirect-url" value="'. $redirect_url .'">
						<input type="hidden" name="wnus-nonce" value="'. $form_nonce .'">
						<input type="submit" name="update-user-submit" class="button button-primary" style="margin:10px;" value="Delete Checked Groups">
					</form>

				</div>
			</div>';
		}else{
			$output = '
			<div class="section-inner thin error404-content">
				<h1 class="entry-title">Page Not Found</h1>
				<div class="intro-text"><p>The page you were looking for could not be found. It might have been removed, renamed, or did not exist in the first place.</p></div>
				<form role="search" aria-label="404 not found" method="get" class="search-form" action="http://localhost/~davidforg/SitesWordpress/wordpress/">
					<label for="search-form-2">
						<span class="screen-reader-text">Search for:</span>
						<input type="search" id="search-form-2" class="search-field" placeholder="Search …" value="" name="s">
					</label>
					<input type="submit" class="search-submit" value="Search">
				</form>
			</div>';
		}
		return $output;
	}
add_shortcode('group-form', 'wnus_group_form_frontend');
