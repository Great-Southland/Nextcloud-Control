<?php // wnus - Validate Settings



// disable direct file access
if ( ! defined( 'ABSPATH' ) ) {

	exit;

}



// callback: validate options
function wnus_callback_validate_options( $input ) {

	// custom url
	if ( isset( $input['nextcloud_server_url'] ) ) {

		$input['nextcloud_server_url'] = $input['nextcloud_server_url'];

	}

	return $input;

}
