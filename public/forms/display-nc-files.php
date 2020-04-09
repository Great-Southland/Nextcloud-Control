<?php // Get/Create share link for all files/folders in a nc folder and display them on a table with preview

function display_nc_share_links($shortcode_atts) {
if(false === ($display_nextcloud_files = get_transient('display_nextcloud_files'))){
	/* -------------------- Get Credentials Defined in Nextcloud Settings Page -----------------------*/
		$options = get_option( 'wnus_options', wnus_options_default() );
		$nc_file_username = isset( $options['file_display_username']) ? sanitize_text_field( $options['file_display_username'] ) : '';
		$nc_file_pass = isset( $options['file_display_pass']) ? sanitize_text_field( $options['file_display_pass'] ) : '';

	/*-------------------- Define Shortcode Attributes -------------------*/
		$shortcode_att = shortcode_atts(array('folder-path'=>'',
																					'hide-files'=>'Readme.md',
																					),$shortcode_atts);
		$folder_path = $shortcode_att['folder-path'];
		$hide_file_a = explode(',', $shortcode_att['hide-files']);

		foreach($hide_file_a as $hide_file_s){
			$hide_file_a[$hide_file_s] = $hide_file_s;
		}

	/*--------------------------- Get a List of Files -----------------------*/
		$url = 'remote.php/dav/files/'. $nc_file_username .'/'. $folder_path;
		$raw_xml_response = nc_request('PROPFIND', $url, $nc_file_username, $nc_file_pass);

		$xml_response = str_replace('d:', '', $raw_xml_response['body']);
		$xml_array = xml_to_array($xml_response);
		$result = '';
		$share_link = '';
		$link_preview = '';
		$user_tr = '';

	/*------------------------ Print a Grid item for each response -----------------------*/
		foreach($xml_array['response'] as $response){
	// define the file path, name and url decode the file name
			$url_path = str_replace('/cloud/remote.php/dav/files/'. $nc_file_username .'/', '', $response['href']);
			$file_name = str_replace($folder_path .'/', '', $url_path);
			$decoded_file_name = urldecode($file_name);
	// variable that leaves te / in front of a file name
			$current_folder_name = str_replace($folder_path, '', $url_path);

	// do not continue if it is trying to list current folder or specied in the shortcode atts
			if(array_key_exists($decoded_file_name, $hide_file_a)){continue;}
			if($current_folder_name == '/'){continue;}
	// Create variables for for the share url preview link and hrefs
			$share_url = get_nc_share_link($url_path, $nc_file_username, $nc_file_pass);
			$share_link_tag = '<a class="display-files-item-name" href="'. $share_url .'">'. $decoded_file_name .'</a>';
			$link_preview = $share_url . '/preview';
			$link_preview_img = '<a href="'. $share_url .'"><img class="nc-file-display-img" src="'. $link_preview . '"></a>';
	// create a div for each file with preview image and share link
			$user_tr .= '<div class="display-files-grid-item">'. $link_preview_img . $share_link_tag .'</div>';

		}
		// wrap for the elments to be displayed in
		$result = '<div class="display-files-wrap">
								<div class="display-files-grid-container">'
									. $user_tr .
								'</div>
							 </div>	';

		//Set Transient
		set_transient('display_nextcloud_files', $result, YEAR_IN_SECONDS);
	} else {

// Get all the variables for the xml_array for PROPFIND
		$options = get_option( 'wnus_options', wnus_options_default() );
		$nc_file_username = isset( $options['file_display_username']) ? sanitize_text_field( $options['file_display_username'] ) : '';
		$nc_file_pass = isset( $options['file_display_pass']) ? sanitize_text_field( $options['file_display_pass'] ) : '';
		$shortcode_att = shortcode_atts(array('folder-path'=>'',
																					'hide-files'=>'Readme.md',),$shortcode_atts);
		$folder_path = $shortcode_att['folder-path'];
		$url = 'remote.php/dav/files/'. $nc_file_username .'/'. $folder_path;
		$raw_xml_response = nc_request('PROPFIND', $url, $nc_file_username, $nc_file_pass);
		$xml_response = str_replace('d:', '', $raw_xml_response['body']);
		$xml_array = xml_to_array($xml_response);

//if xml_array is not equal to the transiant of the PROPFIND list set PROPFIND to xml_array, delete the file display transiant and restart function from beginning
		if ($xml_array != get_transient('display_nextcloud_files_list')) {

			set_transient('display_nextcloud_files_list', $xml_array, YEAR_IN_SECONDS);
			delete_transient('display_nextcloud_files');
			return display_nc_share_links($shortcode_atts);
		}

		$result =  get_transient('display_nextcloud_files');
	}
	return $result;
} add_shortcode('list-nc-files', 'display_nc_share_links');
