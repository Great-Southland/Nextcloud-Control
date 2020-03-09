<?php // MyPlugin - Core Functionality
// /*


// disable direct file access
if ( ! defined( 'ABSPATH' ) ) {

	exit;

}



// FOR TESTING ONLY Database values shortcode
function wnus_nextcloud_server_url( $atts ) {
	$redirect_url = strtok($_SERVER["REQUEST_URI"],'?');
	$options = get_option( 'wnus_options', wnus_options_default() );

	$nextcloud_server_url = isset( $options['nextcloud_server_url']) ? sanitize_text_field( $options['nextcloud_server_url'] ) : '';
	$nextcloud_server_username = isset( $options['nextcloud_server_username']) ? sanitize_text_field( $options['nextcloud_server_username'] ) : '';
	$nextcloud_server_pass = isset( $options['nextcloud_server_pass']) ? sanitize_text_field( $options['nextcloud_server_pass'] ) : '';

	$form_action = plugin_dir_path( __FILE__ );
	$output = '<p>NextCloud Server Url: ' . $nextcloud_server_url . '</p>';
	$output .= '<p>NextCloud Server UserName: ' . $nextcloud_server_username . '</p>';
	$output .= '<p>NextCloud Server Password: ' . $nextcloud_server_pass . '</p>';
	$output .= '<p>Form Path: ' . $form_action . '</p>';
	$output .= '<p>Current URL: '. $redirect_url .'</p>';



	return $output;

}
add_shortcode('wnus', 'wnus_nextcloud_server_url');
