<?php // Enqueue admin Styles and Scripts

function wnus_attach_styles() {
  wp_enqueue_style( 'wnus', plugin_dir_url( dirname( __FILE__ ) ) . 'public/css/user-group-manager.css', array(), null, 'all' );
  wp_enqueue_script( 'wnus', plugin_dir_url( dirname( __FILE__ ) ) . 'public/js/user-group-manager.js', array(), null, true );
}
add_action( 'wp_enqueue_scripts', 'wnus_attach_styles' );
