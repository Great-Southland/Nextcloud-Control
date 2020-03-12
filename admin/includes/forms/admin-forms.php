<?php //Forms

// Add Edit User Page
function wnus_edit_user_form() {
	$user_roles = get_user_roles($_GET['user-id']); //Returns array
	// Create string to store roles
	$roles = '';
	// Get Roles
	foreach ($user_roles as $role) {
		$roles .= ucfirst($role) .', ';
	}
	$redirect_url = strtok($_SERVER["REQUEST_URI"],'?');

?>
	<div class="wrap">
		<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
			<h3><?php esc_html_e( 'Edit User', 'wnus' ); ?></h3>
			<input type="hidden" name="action" value="wnus_update_user_form_response">
			<input type="hidden" name="email-login" value="<?php echo $_GET['email-login']; ?>">
			<p>
				<label for="display-name">
					<?php esc_html_e( 'Enter a new Display Name for this user:', 'wnus' ); ?>
				</label><br />
				<input class="regular-text" type="text" size="40" name="display-name" placeholder="<?php echo $_GET['display-name']; ?>" id="display-name">
			</p>
			<p>
				<label for="email">
					<?php esc_html_e( 'Enter a new Email for this user:', 'wnus' ); ?>
				</label><br />
				<input class="regular-text" type="text" size="40" name="email" placeholder="<?php echo $_GET['user-email']; ?>" id="email">
			</p>
			<p>
				<label for="password">
					<?php esc_html_e( 'Enter a new Password for this user:', 'wnus' ); ?>
				</label><br />
				<input class="regular-text" type="text" size="40" name="password" placeholder="************" id="password">
			</p>
			<p>
					<h4 style="margin-bottom:5px;">Groups You Are In:</h4>
					<span><?php echo $roles; ?></span>
		  </p>
			<input type="hidden" name="wnus-nonce" value="<?php echo wp_create_nonce( 'wnus-nonce' ); ?>">
			<input type="hidden" name="front-back-end" value="backend">
			<input type="hidden" name="redirect-url" value="<?php echo $redirect_url; ?>">
			<input type="submit" name="update-user-submit" class="button button-primary" value="<?php esc_html_e( 'Update User', 'wnus' ); ?>">
		</form>
	</div>
<?php }

// Add Create User Page
function wnus_create_user_form() {
	$redirect_url = strtok($_SERVER["REQUEST_URI"],'?');
?>
<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
		<h3><?php esc_html_e( 'Add New User', 'wnus' ); ?></h3>
		<input type="hidden" name="action" value="wnus_create_user_form_response">
		<p>
			<label for="username"><?php esc_html_e( 'Username', 'wnus' ); ?></label><br />
			<input class="regular-text" type="text" size="40" name="username" placeholder="Username" id="username">
		</p>
		<p>
			<label for="email"><?php esc_html_e( 'Email', 'wnus' ); ?></label><br />
			<input class="regular-text" type="text" size="40" name="email" placeholder="Email" id="email">
		</p>
		<p>
			<label for="password"><?php esc_html_e( 'Password', 'wnus' ); ?></label><br />
			<input class="regular-text" type="text" size="40" name="password" placeholder="Password" id="password">
		</p>

		<p><?php esc_html_e( 'The user will receive this information via email.', 'wnus' ); ?></p>

		<input type="hidden" name="wnus-nonce" value="<?php echo wp_create_nonce( 'wnus-nonce' ); ?>">
		<input type="hidden" name="front-back-end" value="backend">
		<input type="hidden" name="redirect-url" value="<?php echo $redirect_url; ?>">
		<input type="submit" class="button button-primary" value="<?php esc_html_e( 'Add User', 'wnus' ); ?>">
	</form>
</div>
<?php }


// User Group Manager Page
function wnus_user_group_manager_form() {


	$wp_users = get_users( array( 'fields' => array( 'user_login', 'display_name', 'user_email', 'ID' ) ) );


	// --------------- Get array of Roles ---------------
		$roles_s = '';
	  $wnus_editable_roles = get_editable_roles();
	  foreach ($wnus_editable_roles as $role => $details) {
	      $roles_s .= $role .'|';
	  }
		$roles_s = rtrim($roles_s, '|');
		$roles_a = explode('|', $roles_s);


 // -------------- Create Select list -----------------
 		$roles_drop_down = '<option value="">Select Group</option>';
 		foreach($roles_a as $wnus_role) {
			$wnus_role_capital = ucfirst($wnus_role);
			$roles_drop_down .= '<option value="' . $wnus_role .'">' . $wnus_role_capital .'</option>' . "\n";
		}

// ----------------- Create Group Table Row Data -----------------
	// Strings to hold table row data
	$group_tr = '';

	foreach ( $roles_a as $group_role ) {
  $group_role_users = get_users_by_role($group_role, 'user_nicename', 'ASC');
	$group_users = '';

	foreach ($group_role_users as $user) {
		$display_name = $user->display_name;
		$group_users .= $display_name .', ';
	}
	if ($group_users == '') {
		$group_users = '(No Users)';
	}

	$group_tr .= '<tr>
							<td class="group-checkbox"><input type="checkbox" name="group-id-'. $group_role .'"></td>
							<td class="group-name">'. ucfirst($group_role) .'</td>
							<td class="users">'. $group_users .'</td>
							</tr>';
}

// ----------------- Create User Table Row Data ------------------
		// Strings to hold table row data
		$user_tr = '';

		foreach ( $wp_users as $user ) {
			$user_name = esc_html( $user->user_login );
			$user_display_name = esc_html( $user->display_name );
			$user_email = esc_html( $user->user_email);
			$user_id = esc_html( $user->ID);
			$user_roles = get_user_roles($user_id); //Returns array
			// Reset Roles every loop
			$roles = '';

			// Get Roles
			foreach ($user_roles as $role) {
				$roles .= ucfirst($role) .', ';
			}
			$edit_user_link = admin_url( 'admin.php?page=edit-user&email-login=' . $user_email .'|'. $user_name .'&user-id='. $user_id .'&display-name='. $user_display_name .'&user-email='. $user_email .'&user-password=********');
			$user_tr .= '<tr>
									<td class="user-checkbox"><input type="checkbox" name="user-id-'. $user_id .'"></td>
									<td class="name">'. $user_display_name .' ('. $user_name .')</td>
									<td class="email">'. $user_email .'</td>
									<td class="groups">'. $roles .'</td>
									<td class="edit-user"><a href="'. $edit_user_link .'" class="edit-user-link">Edit User</a></td>
									</tr>';
		}

		$redirect_url = strtok($_SERVER["REQUEST_URI"],'?');
?>



<div class="wrap">
	<div class="users" style="padding-top: 30px;">
		<span class="users-header"><h1 style="display:inline-block;padding-right:10px;">Users <a href="<?php echo admin_url( 'admin.php?page=create-user' ); ?>" class="page-title-action">Add New</a></h1></span>
		<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
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
				<tbody>
					<?php echo $user_tr; ?>
				</tbody>
			</table>
			<select required style="margin:10px;display:inline-block;" name="select-action" id="select-action" onchange="optionCheck(this);">
				<option value="">Select Action</option>
				<option value="add-to-group">Add User/s to Group</option>
				<option value="remove-from-group">Remove User/s from Group</option>
				<option value="delete-user">Delete User/s</option>
			</select>
	  	<select id="wp-roles" style="display: none;" name="wp-roles">
				<?php echo $roles_drop_down; ?>
			</select>
			<input type="hidden" name="action" value="wnus_user_form_response">
				<input type="hidden" name="front-back-end" value="backend">
			<input type="hidden" name="redirect-url" value="<?php echo $redirect_url; ?>">
			<input type="hidden" name="wnus-nonce" value="<?php echo wp_create_nonce( 'wnus-nonce' ); ?>">

			<input type="submit" name="form-submit" class="button button-primary" style="margin:10px;" value="<?php esc_html_e( 'Apply', 'wnus' ); ?>">
		</form>
	</div>

	<div class="groups" style="padding-top:30px;">
		<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
			<span class="groups-header"><h1 style="display:inline-block;padding-right:10px;">Groups</h1>
				<input class="regular-text" style="width:160px;" placeholder="Enter Name for Group" type="text" size="40" name="create-group" id="create-group">
			</span>
			<input type="hidden" name="action" value="wnus_group_managment_create_form_response">
			<input type="hidden" name="front-back-end" value="backend">
		  <input type="hidden" name="redirect-url" value="<?php echo $redirect_url; ?>">
			<input type="hidden" name="wnus-nonce" value="<?php echo wp_create_nonce( 'wnus-nonce' ); ?>">
			<input type="submit" name="create-group-submit" class="button button-primary" style="margin-top:12px;" value="<?php esc_html_e( 'Create', 'wnus' ); ?>">
		</form>

		<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">

			<table class="group-table" style="width:100%;border:1px solid black;margin-top:35px;">
				<thead style="background-color:white;">
					<tr>
						<th style="text-align:left;width:15px;"><input type="checkbox" name="group-checkbox"></th>
						<th style="text-align:left;">Group Name</th>
						<th style="text-align:left;">Users in Group</th>
					</tr>
				</thead>
				<tbody>
					<?php echo $group_tr; ?>
				</tbody>
			</table>

			<input type="hidden" name="action" value="wnus_group_managment_delete_form_response">
			<input type="hidden" name="front-back-end" value="backend">
		  <input type="hidden" name="redirect-url" value="<?php echo $redirect_url; ?>">
			<input type="hidden" name="wnus-nonce" value="<?php echo wp_create_nonce( 'wnus-nonce' ); ?>">
			<input type="submit" name="update-user-submit" class="button button-primary" style="margin:10px;" value="<?php esc_html_e( 'Delete Checked Groups', 'wnus' ); ?>">
		</form>

	</div>
</div>



<?php }

// Folder Mangaer Form backend
function wnus_folder_manager_form() {
$redirect_url = strtok($_SERVER["REQUEST_URI"],'?');

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
// HTTP Response Code from NC
$nc_code = $configData['meta']['statuscode'];
$nc_message = $configData['meta']['message'];
// HTTP Response Message from NC
$folders = $configData['data']['element'];

//---------------------------------------- Create Folder Rows for Folder-Manager Table --------------------------------------
// String to hold Table Row HTML
$folder_tr = '';
//If there are multiple folders
if(array_key_exists(0, $folders)){
	//Cycle through Folders
	foreach($folders as $folder){
		$folder_id = $folder['id'];
		$folder_name = $folder['mount_point'];

		//************* Folder Quota *************
		$folder_quota = $folder['quota'];
		//Calculat KiloByte, MegaByte, and GigaByte
		$folder_quota_kb = ceil($folder_quota * 0.000977);
		$folder_quota_mb = ceil($folder_quota * 0.0000009537);
		$folder_quota_gb = ceil($folder_quota * 0.000000000931);
		// Use the appropriate unit for displaying Storage Used
		if ($folder_quota == '-3'){
				$folder_quota = 'UNLIMITED';
		} elseif ($folder_quota_kb < '999'){
				$folder_quota = $folder_quota_kb .' KB';
		} elseif ($folder_quota_mb < '999'){
				$folder_quota = $folder_quota_mb .' MB';
		} else {
				$folder_quota = $folder_quota_gb .' GB';
		}

		//************ Folder Sizes **************
		$folder_size = $folder['size'];
		//Calculat KiloByte, MegaByte, and GigaByte
		$folder_size_kb = ceil($folder_size * 0.000977);
		$folder_size_mb = ceil($folder_size * 0.0000009537);
		$folder_size_gb = ceil($folder_size * 0.000000000931);
		// Use the appropriate unit for displaying Storage Used
		if ($folder_size_kb < '999'){
			$folder_size = $folder_size_kb .' KB';
		} elseif ($folder_size_mb < '999'){
			$folder_size = $folder_size_mb .' MB';
		} else {
			$folder_size = $folder_size_gb .' GB';
		}

		//************ Get Folder Group names and Permissions ******************
		//Array and String to hold folder ID
		$folder_group_id = [];
		$folder_group_id_str = '';
		//Array and String to hold folder Permissions
		$folder_group_permissions = [];
		$folder_group_permissions_str = '';
		//Iteration Counter for array keys
		$iter_count = 0;
		// If Folder is owned by multiple groups
		if(array_key_exists(0, $folder['groups']['element'])){
			//Cylce through Groups
			foreach($folder['groups']['element'] as $item){
				//Increment Iteration Counter by 1
				$iter_count = $iter_count + 1;
				//Get Group Ids and Permissions
				$group_id = $item['@attributes']['group_id'];
				$permissions = $item['@attributes']['permissions'];
				//Add group ids and permissions to arrays
				$folder_group_id[$folder_name .'_group_id_'. $iter_count] = $group_id;
				$folder_group_permissions[$folder_name .'_permissions_'. $iter_count] = $permissions;
			}
		} else {
			//If folder is owned by one group
			//Get Group Ids and Permissions
			$group_id = $folder['groups']['element']['@attributes']['group_id'];
			$permissions = $folder['groups']['element']['@attributes']['permissions'];
			//Add group ids and permissions to arrays
			$folder_group_id[$folder_name .'_group_id'] = $group_id;
			$folder_group_permissions[$folder_name .'_permissions'] = $permissions;
		}
		//Create String from Group_ID arrays
		foreach($folder_group_id as $id){
				$folder_group_id_str .= ucfirst($id) .', ';
		}
		//Create String from Permission arrays
		foreach($folder_group_permissions as $permissions){
				$folder_group_permissions_str .= ucfirst($permissions) .', ';
		}
		$folder_tr .= '<tr>
								<td class="folder-checkbox"><input type="checkbox" name="folder-id-|'. $folder_id .'"></td>
								<td class="folder_name">'. $folder_name .'</td>
								<td class="folder_group">'. $folder_group_id_str .'</td>
								<td class="folder_quota">'. $folder_quota .'</td>
								<td class="folder_storage">'. $folder_size .'</td>
								</tr>';
	}
} else {
	//If there is only one folder
	$folder_id = $folders['id'];
	$folder_name = $folders['mount_point'];

	//************* Folder Quota *************
	$folder_quota = $folders['quota'];
	//Calculat KiloByte, MegaByte, and GigaByte
	$folder_quota_kb = ceil($folder_quota * 0.000977);
	$folder_quota_mb = ceil($folder_quota * 0.0000009537);
	$folder_quota_gb = ceil($folder_quota * 0.000000000931);
	// Use the appropriate unit for displaying Storage Used
	if ($folder_quota == '-3'){
			$folder_quota = 'UNLIMITED';
	} elseif ($folder_quota_kb < '999'){
			$folder_quota = $folder_quota_kb .' KB';
	} elseif ($folder_quota_mb < '999'){
			$folder_quota = $folder_quota_mb .' MB';
	} else {
			$folder_quota = $folder_quota_gb .' GB';
	}

	//************ Folder Sizes **************
	$folder_size = $folders['size'];
	//Calculat KiloByte, MegaByte, and GigaByte
	$folder_size_kb = ceil($folder_size * 0.000977);
	$folder_size_mb = ceil($folder_size * 0.0000009537);
	$folder_size_gb = ceil($folder_size * 0.000000000931);
	// Use the appropriate unit for displaying Storage Used
	if ($folder_size_kb < '999'){
		$folder_size = $folder_size_kb .' KB';
	} elseif ($folder_size_mb < '999'){
		$folder_size = $folder_size_mb .' MB';
	} else {
		$folder_size = $folder_size_gb .' GB';
	}

	//************ Get Folder Group names and Permissions ******************
	//Array and String to hold folder ID
	$folder_group_id = [];
	$folder_group_id_str = '';
	//Array and String to hold folder Permissions
	$folder_group_permissions = [];
	$folder_group_permissions_str = '';
	//Iteration Counter for array keys
	$iter_count = 0;
	// If Folder is owned by multiple groups
	if(array_key_exists(0, $folders['groups']['element'])){
		//Cylce through Groups
		foreach($folders['groups']['element'] as $item){
			//Increment Iteration Counter by 1
			$iter_count = $iter_count + 1;
			//Get Group Ids and Permissions
			$group_id = $item['@attributes']['group_id'];
			$permissions = $item['@attributes']['permissions'];
			//Add group ids and permissions to arrays
			$folder_group_id[$folder_name .'_group_id_'. $iter_count] = $group_id;
			$folder_group_permissions[$folder_name .'_permissions_'. $iter_count] = $permissions;
		}
	} else {
		//If folder is owned by one group
		//Get Group Ids and Permissions
		$group_id = $folders['groups']['element']['@attributes']['group_id'];
		$permissions = $folders['groups']['element']['@attributes']['permissions'];
		//Add group ids and permissions to arrays
		$folder_group_id[$folder_name .'_group_id'] = $group_id;
		$folder_group_permissions[$folder_name .'_permissions'] = $permissions;
	}
	//Create String from Group_ID arrays
	foreach($folder_group_id as $id){
			$folder_group_id_str .= ucfirst($id) .', ';
	}
	//Create String from Permission arrays
	foreach($folder_group_permissions as $permissions){
			$folder_group_permissions_str .= ucfirst($permissions) .', ';
	}
	$folder_tr .= '<tr>
							<td class="folder-checkbox"><input type="checkbox" name="folder-id-|'. $folder_id .'"></td>
							<td class="folder_name">'. $folder_name .'</td>
							<td class="folder_group">'. $folder_group_id_str .'</td>
							<td class="folder_quota">'. $folder_quota .'</td>
							<td class="folder_storage">'. $folder_size .'</td>
							</tr>';
}



//---------------------------------------- HTML Code --------------------------------------------
?>
	<div class="wrap">
		<h3><?php esc_html_e( 'Folder Manager', 'wnus' ); ?></h3>
		<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">


			<table class="folder-table" style="width:100%;border:1px solid black;margin-top:35px;">
				<thead style="background-color:white;">
					<tr>
						<th style="text-align:left;width:15px;"><input type="checkbox" name="folder-checkbox"></th>
						<th style="text-align:left;">Folder Name</th>
						<th style="text-align:left;">Group</th>
						<th style="text-align:left;">Quota</th>
						<th style="text-align:left;">Storage Used</th>
					</tr>
				</thead>
				<tbody>
					<?php echo $folder_tr; ?>
				</tbody>
			</table>
			<select required style="margin:10px;display:inline-block;" name="select-action" id="select-action" onchange="optionCheck(this);">
				<option value="">Select Action</option>
				<option value="delete-folder">Delete Folder/s</option>
				<option value="give-access">Add Group to Folder</option>
				<option value="remove-access">Remove A Group From Folder</option>
				<option value="set-quota">Set Quota Limit For Folder</option>
				<option value="rename-folder">Rename Folder</option>
			</select>
			<select id="wp-roles" style="display: none;" name="wp-roles">
				<?php echo $roles_drop_down; ?>
			</select>


			<input type="hidden" name="action" value="wnus_folder_manager_form_response">
			<input type="hidden" name="redirect-url" value="<?php echo $redirect_url; ?>">
			<input type="hidden" name="front-back-end" value="backend">
			<input type="hidden" name="wnus-nonce" value="<?php echo wp_create_nonce( 'wnus-nonce' ); ?>">
			<input type="submit" name="update-user-submit" class="button button-primary" value="<?php esc_html_e( 'Delete Folder', 'wnus' ); ?>">
		</form>
	</div>
<?php }
