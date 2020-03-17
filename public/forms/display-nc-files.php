<?php // Get/Create share link for all files/folders in a nc folder and display them on a table with preview

function display_nc_share_links($shortcode_atts) {

	$nc_file_username = 'Benjamin';
	$nc_file_pass = 'benjaminforg';


	// $options = get_option( 'wnus_options', wnus_options_default() );
	// $ncnc_file_username = 'username';
	// $ncnc_file_pass = 'pass';

	$shortcode_att = shortcode_atts(array('folder-path'=>'',
																				'hide-files'=>'Readme.md',
																				),$shortcode_atts);
	$folder_path = $shortcode_att['folder-path'];
	$hide_file_a = explode(',', $shortcode_att['hide-files']);

	foreach($hide_file_a as $hide_file_s){
		$hide_file_a[$hide_file_s] = $hide_file_s;
	}

	$url = 'remote.php/dav/files/'. $nc_file_username .'/'. $folder_path;
	$raw_xml_response = nc_request('PROPFIND', $url, $nc_file_username, $nc_file_pass);

	$xml_response = str_replace('d:', '', $raw_xml_response['body']);
	$xml_array = xml_to_array($xml_response);
	$result = '';

	foreach($xml_array['response'] as $response){
		$url_path = str_replace('/cloud/remote.php/dav/files/'. $nc_file_username .'/', '', $response['href']);
		$file_name = str_replace($folder_path .'/', '', $url_path);
		$decoded_file_name = urldecode($file_name);

		if(array_key_exists($decoded_file_name, $hide_file_a)){continue;}

		$share_url = get_nc_share_link($url_path, $nc_file_username, $nc_file_pass);
		$result .= '<a href="'. $share_url .'">'. $decoded_file_name .'</a><br>';
	}

	return $result;
} add_shortcode('list-nc-files', 'display_nc_share_links');
