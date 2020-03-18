<?php // WNUS - Admin Menu



// disable direct file access
if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

// add top-level administrative menu
function wnus_add_menus() {

	/*
	add_menu_page(
		string   $page_title,
		string   $menu_title,
		string   $capability,
		string   $menu_slug,
		callable $function = '',
		string   $icon_url = '',
		int      $position = null
	)
	*/

	/*
	add_submenu_page(
		string   $parent_slug,
		string   $page_title,
		string   $menu_title,
		string   $capability,
		string   $menu_slug,
		callable $function = ''
	);
	*/

// -------------- Add Menus ---------------

// Add Main Menu Page
	add_menu_page(
		'WordPress NextCloud User Sync Settings',
		'NextCloud',
		'manage_options',
		'wnus',
		'wnus_display_settings_page',
		'dashicons-admin-generic',
		null
	);

	// Add user manager submenu
		add_submenu_page(
			'wnus',
			'User/Group Manager',
			'User/Group Manager',
			'manage_options',
			'user-group-manager',
			'wnus_user_group_manager_form'
		);

	// Add Edit User Page (set parent slug to null to make page not apear in admin menu)
	 add_submenu_page(
	 	null,
		'Edit User',
	 	'Edit User',
	 	'manage_options',
	 	'edit-user',
		'wnus_edit_user_form'
	);

	// Add user group submenu (set parent slug to null to make page not apear in admin menu)
	add_submenu_page(
		null,
		'Create User',
		'Create User',
		'manage_options',
		'create-user',
		'wnus_create_user_form'
	);

	// Add folder manager submenu
	// add_submenu_page(
	// 	'wnus',
	// 	'Folder Manager',
	// 	'Folder Manager',
	// 	'manage_options',
	// 	'folder-manager',
	// 	'wnus_folder_manager_form'
	// );



}
add_action( 'admin_menu', 'wnus_add_menus' );
