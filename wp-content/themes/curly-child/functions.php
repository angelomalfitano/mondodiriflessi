<?php

/*** Child Theme Function  ***/
if ( ! function_exists('curly_mkdf_child_theme_enqueue_scripts') ) {
	
	function curly_mkdf_child_theme_enqueue_scripts() {
		
		$parent_style = 'curly-mkdf-default-style';
		
		wp_enqueue_style( 'curly-mkdf-child-style', get_stylesheet_directory_uri() . '/style.css', array( $parent_style ) );
	}
	
	add_action( 'wp_enqueue_scripts', 'curly_mkdf_child_theme_enqueue_scripts' );
}

add_action('admin_enqueue_scripts', 'ds_admin_theme_style');
add_action('login_enqueue_scripts', 'ds_admin_theme_style');
function ds_admin_theme_style() {
	if (!current_user_can( 'manage_options' )) {
		echo '<style>.sln-notice.sln-notice--bold.sln-notice--subscription-cancelled {display: none;}</style>';
	}
}