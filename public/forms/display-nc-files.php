<?php // Get/Create share link for all files/folders in a nc folder and display them on a table with preview

function display_nc_share_links($shortcode_atts) {

	$nc_file_username = 'Benjamin';
	$nc_file_pass = 'benjaminforg';


	// $options = get_option( 'wnus_options', wnus_options_default() );
	// $nc_file_username = isset( $options['file_display_username']) ? sanitize_text_field( $options['file_display_username'] ) : 'username';
	// $nc_file_pass = isset( $options['file_display_pass']) ? sanitize_text_field( $options['file_display_pass'] ) : 'pass';

	$shortcode_att = shortcode_atts(array('folder-path'=>'',
																				'hide-file'=>'Readme.md',
																				),$shortcode_atts);
	$folder_path = $shortcode_att['folder-path'];
	$hide_file = $shortcode_att['hide-file'];

	$url = 'remote.php/dav/files/'. $nc_file_username .'/'. $folder_path;
	$raw_xml_response = nc_request('PROPFIND', $url, $nc_file_username, $nc_file_pass);

	$xml_response = str_replace('d:', '', $raw_xml_response['body']);
	$xml_array = xml_to_array($xml_response);
	$result = '';

	foreach($xml_array['response'] as $response){
		$url_path = str_replace('/cloud/remote.php/dav/files/'. $nc_file_username .'/', '', $response['href']);
		$file_name = str_replace($folder_path .'/', '', $url_path);
		$decoded_file_name = urldecode($file_name);

		if($decoded_file_name == $hide_file){continue;}

		$share_url = get_nc_share_link($url_path, $nc_file_username, $nc_file_pass);
		$result .= '<a href="'. $share_url .'">'. $decoded_file_name .'</a><br>';
	}
	return $result;
} add_shortcode('list-nc-files', 'display_nc_share_links');
