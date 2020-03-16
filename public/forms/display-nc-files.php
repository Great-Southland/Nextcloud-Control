<?php // Get/Create share link for all files/folders in a nc folder and display them on a table with preview

function display_nc_share_links($shortcode_atts) {
	$shortcode_att = shortcode_atts(array('folder-path'=>'',
																				'hide-file'=>'Readme.md',
																				),$shortcode_atts);
	$folder_path = $shortcode_att['folder-path'];
	$hide_file = $shortcode_att['hide-file'];

	$url = 'https://Benjamin:benjaminforg@salones-portal.ddns.net/cloud/remote.php/dav/files/Benjamin/'. $folder_path;
	$args = array( 'method' => 'PROPFIND',
								 'timeout' => 40000,
							 );

	$raw_xml_response = wp_remote_request( $url, $args );
	$xml_response = str_replace('d:', '', $raw_xml_response['body']);
	$xml_array = xml_to_array($xml_response);
	$result = '';



	foreach($xml_array['response'] as $response){
		$url_path = str_replace('/cloud/remote.php/dav/files/Benjamin/', '', $response['href']);
		$file_name = str_replace($folder_path .'/', '', $url_path);
		$decoded_file_name = urldecode($file_name);

		if($decoded_file_name == $hide_file){continue;}

		$share_url = get_nc_share_link($url_path);
		$result .= '<a href="'. $share_url .'">'. $decoded_file_name .'</a><br>';
	}



	return $result;
} add_shortcode('display-nc-share-link', 'display_nc_share_links');
