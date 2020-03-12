<?php
/*
Plugin Name:  WP-NC-User-Sync
Description:  Automatically Update NextCloud Users And Groups When Users and Roles are changed in Wordpress
Plugin URI:   WP-NC-User-Sync
Author:       David Forg
Version:      0.1
Text Domain:  WP-NC-User-Sync
License:      GPL v2 or later
License URI:  https://www.gnu.org/licenses/gpl-2.0.txt
*/



// disable direct file access
if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

// ----------------- include plugin dependencies: admin and public ---------------
	require_once plugin_dir_path( __FILE__ ) . 'includes/core-functions.php';
	require_once plugin_dir_path( __FILE__ ) . 'includes/form-response/user-group-managment/user-form.php';
	require_once plugin_dir_path( __FILE__ ) . 'includes/form-response/user-group-managment/group-form.php';
	require_once plugin_dir_path( __FILE__ ) . 'includes/forms/frontend-forms.php';

	// enqueue styles frontend
		require_once plugin_dir_path( __FILE__ ) . 'public/enqueue-public.php';

// ----------------- include plugin dependencies: admin only ----------------
if ( is_admin() ) {

// register menus
	require_once plugin_dir_path( __FILE__ ) . 'admin/admin-menu.php';

// Settings pages and related
 	require_once plugin_dir_path( __FILE__ ) . 'admin/settings-page.php';
	require_once plugin_dir_path( __FILE__ ) . 'admin/settings-register.php';
	require_once plugin_dir_path( __FILE__ ) . 'admin/settings-callbacks.php';
	require_once plugin_dir_path( __FILE__ ) . 'admin/settings-validate.php';

// enqueue styles admin
	require_once plugin_dir_path( __FILE__ ) . 'admin/enqueue-admin.php';

// Forms
  require_once plugin_dir_path( __FILE__ ) . 'admin/includes/forms/admin-forms.php';
	require_once plugin_dir_path( __FILE__ ) . 'admin/includes/admin-notices/admin-notices.php';
}







// default plugin options
function wnus_options_default() {

	return array(
		'nextcloud_server_url'     => 'example.com',
		'nextcloud_server_username'     => '',
		'nextcloud_server_pass'     => '',
		'edit_user_page_url'     => '',
		'create_user_page_url'     => '',
	);

}
