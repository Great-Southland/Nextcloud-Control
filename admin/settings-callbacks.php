<?php // wnus - Settings Callbacks



// disable direct file access
if ( ! defined( 'ABSPATH' ) ) {

	exit;

}



// -------------- callback NextCloud Settings section ---------------
function wnus_callback_section_nextcloud_settings() {

	echo '<p>These settings enable you to customize the NextCloud server settings.</p>';

}

function wnus_callback_section_nextcloud_file_display_settings() {

	echo '<p>Enter Username and Password for File Display. Display a Sharelink for Files/Folders in a Folder Using Shortcode: [list-nc-files] Ysing the folder-path"" Attribute to set Path and hide-files"" Hide 1 or More Files/Folders by Seperating With Comma.</p>';

}

// -------------- callback NextCloud Settings Fields --------------

// callback: text field
function wnus_callback_field_text( $args ) {

	$options = get_option( 'wnus_options', wnus_options_default() );

	$id    = isset( $args['id'] )    ? $args['id']    : '';
	$label = isset( $args['label'] ) ? $args['label'] : '';
	$placeholder = isset( $args['placeholder'] ) ? $args['placeholder'] : '';

	$value = isset( $options[$id] ) ? sanitize_text_field( $options[$id] ) : '';

	echo '<input id="wnus_options_'. $id .'" name="wnus_options['. $id .']" type="text" size="40" placeholder="'. $placeholder .'"value="'. $value .'"><br />';
	echo '<label for="wnus_options_'. $id .'">'. $label .'</label>';

}

// callback: Password Field
function wnus_callback_field_pass( $args ) {

	$options = get_option( 'wnus_options', wnus_options_default() );

	$id    = isset( $args['id'] )    ? $args['id']    : '';
	$label = isset( $args['label'] ) ? $args['label'] : '';
	$placeholder = isset( $args['placeholder'] ) ? $args['placeholder'] : '';

	$password = isset( $options[$id] ) ? sanitize_text_field( $options[$id] ) : '';


	echo '<div><input id="wnus_options_'. $id .'" name="wnus_options['. $id .']" type="password" size="40" placeholder="'. $placeholder .'"value="'. $password .'"><br>';
	echo '<label for="wnus_options_'. $id .'">'. $label .'</label></div><br>';

}



// radio field options
function wnus_options_radio() {

	return array(

		'enable'  => esc_html__('Enable custom styles', 'wnus'),
		'disable' => esc_html__('Disable custom styles', 'wnus')

	);

}



// callback: radio field
function wnus_callback_field_radio( $args ) {

	$options = get_option( 'wnus_options', wnus_options_default() );

	$id    = isset( $args['id'] )    ? $args['id']    : '';
	$label = isset( $args['label'] ) ? $args['label'] : '';

	$selected_option = isset( $options[$id] ) ? sanitize_text_field( $options[$id] ) : '';

	$radio_options = wnus_options_radio();

	foreach ( $radio_options as $value => $label ) {

		$checked = checked( $selected_option === $value, true, false );

		echo '<label><input name="wnus_options['. $id .']" type="radio" value="'. $value .'"'. $checked .'> ';
		echo '<span>'. $label .'</span></label><br />';

	}

}



// callback: textarea field
function wnus_callback_field_textarea( $args ) {

	$options = get_option( 'wnus_options', wnus_options_default() );

	$id    = isset( $args['id'] )    ? $args['id']    : '';
	$label = isset( $args['label'] ) ? $args['label'] : '';

	$allowed_tags = wp_kses_allowed_html( 'post' );

	$value = isset( $options[$id] ) ? wp_kses( stripslashes_deep( $options[$id] ), $allowed_tags ) : '';

	echo '<textarea id="wnus_options_'. $id .'" name="wnus_options['. $id .']" rows="5" cols="50">'. $value .'</textarea><br />';
	echo '<label for="wnus_options_'. $id .'">'. $label .'</label>';

}



// callback: checkbox field
function wnus_callback_field_checkbox( $args ) {

	$options = get_option( 'wnus_options', wnus_options_default() );

	$id    = isset( $args['id'] )    ? $args['id']    : '';
	$label = isset( $args['label'] ) ? $args['label'] : '';

	$checked = isset( $options[$id] ) ? checked( $options[$id], 1, false ) : '';

	echo '<input id="wnus_options_'. $id .'" name="wnus_options['. $id .']" type="checkbox" value="1"'. $checked .'> ';
	echo '<label for="wnus_options_'. $id .'">'. $label .'</label>';

}



// select field options
function wnus_options_select() {

	return array(

		'default'   => esc_html__('Default',   'wnus'),
		'light'     => esc_html__('Light',     'wnus'),
		'blue'      => esc_html__('Blue',      'wnus'),
		'coffee'    => esc_html__('Coffee',    'wnus'),
		'ectoplasm' => esc_html__('Ectoplasm', 'wnus'),
		'midnight'  => esc_html__('Midnight',  'wnus'),
		'ocean'     => esc_html__('Ocean',     'wnus'),
		'sunrise'   => esc_html__('Sunrise',   'wnus'),

	);

}



// callback: select field
function wnus_callback_field_select( $args ) {

	$options = get_option( 'wnus_options', wnus_options_default() );

	$id    = isset( $args['id'] )    ? $args['id']    : '';
	$label = isset( $args['label'] ) ? $args['label'] : '';

	$selected_option = isset( $options[$id] ) ? sanitize_text_field( $options[$id] ) : '';

	$select_options = wnus_options_select();

	echo '<select id="wnus_options_'. $id .'" name="wnus_options['. $id .']">';

	foreach ( $select_options as $value => $option ) {

		$selected = selected( $selected_option === $value, true, false );

		echo '<option value="'. $value .'"'. $selected .'>'. $option .'</option>';

	}

	echo '</select> <label for="wnus_options_'. $id .'">'. $label .'</label>';

}
