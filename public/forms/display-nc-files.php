<?php // Get/Create share link for all files/folders in a nc folder and display them on a table with preview

function display_nc_share_links() {
	$url = 'https://Benjamin:benjaminforg@salones-portal.ddns.net/cloud/remote.php/dav/files/Benjamin/testgroupfolder';
	$args = array( 'method' => 'PROPFIND',
								 'timeout' => 40000,
							 );

	$raw_xml_response = wp_remote_request( $url, $args );
	$xml_response = str_replace('d:', '', $raw_xml_response['body']);
	$xml_array = xml_to_array($xml_response);
	$result = '';

	foreach($xml_array['response'] as $response){
		$folder_path = 'testgroupfolder/';
		$url_path = str_replace('/cloud/remote.php/dav/files/Benjamin/', '', $response['href']);
		$file_name = str_replace($folder_path, '', $url_path);
		$decoded_file_name = urldecode($file_name);

		$share_url = get_nc_share_link($url_path);
		$result .= '<a href="'. $share_url .'">'. $decoded_file_name .'</a><br>';
	}

	return $result;
} add_shortcode('display_nc_share_link', 'display_nc_share_links');
