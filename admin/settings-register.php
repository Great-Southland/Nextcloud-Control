<?php // wnus - Register Settings



// disable direct file access
if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

function wnus_register_settings() {

	/*

	register_setting(
		string   $option_group,
		string   $option_name,
		callable $sanitize_callback = ''
	);

	*/

	register_setting(
		'wnus_options',
		'wnus_options',
		'wnus_callback_validate_options'
	);

// ------------ Register Sections ------------
	/*

	add_settings_section(
		string   $id,
		string   $title,
		callable $callback,
		string   $page
	);

	*/
 
	add_settings_section(
		'wnus_section_nextcloud_settings',
		esc_html__('NextCloud Settings', 'wnus'),
		'wnus_callback_section_nextcloud_settings',
		'wnus'
	);

// ----------- Register Fields ----------------

/*

add_settings_field(
		string   $id,
	string   $title,
	callable $callback,
	string   $page,
	string   $section = 'default',
	array    $args = []
);

*/

	add_settings_field(
		'nextcloud_server_url',
		'Server URL',
		'wnus_callback_field_text',
		'wnus',
		'wnus_section_nextcloud_settings',
		[ 'id' => 'nextcloud_server_url', 'label' => 'NextCloud Server URL', 'placeholder' => 'example.com']
	);

	add_settings_field(
		'nextcloud_server_username',
		'Admin Username',
		'wnus_callback_field_text',
		'wnus',
		'wnus_section_nextcloud_settings',
		[ 'id' => 'nextcloud_server_username', 'label' => 'NextCloud Server Admin Username', 'placeholder' => 'Admin Username']
	);

	add_settings_field(
		'nextcloud_server_pass',
		'Password',
		'wnus_callback_field_pass',
		'wnus',
		'wnus_section_nextcloud_settings',
		[ 'id' => 'nextcloud_server_pass', 'label' => 'NextCloud Server Password', 'placeholder' => 'Admin Password']
	);

	add_settings_field(
		'edit_user_page_url',
		'Edit User Page URL',
		'wnus_callback_field_text',
		'wnus',
		'wnus_section_nextcloud_settings',
		[ 'id' => 'edit_user_page_url', 'label' => 'Edit User Page URL', 'placeholder' => 'https://example.com/page']
	);

	add_settings_field(
		'create_user_page_url',
		'Create User Page URL',
		'wnus_callback_field_text',
		'wnus',
		'wnus_section_nextcloud_settings',
		[ 'id' => 'create_user_page_url', 'label' => 'Create User Page URL', 'placeholder' => 'https://example.com/page']
	);

}
add_action( 'admin_init', 'wnus_register_settings' );
