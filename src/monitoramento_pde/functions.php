<?php
/**
 * Sage includes
 *
 * The $sage_includes array determines the code library included in your theme.
 * Add or remove files to the array as needed. Supports child theme overrides.
 *
 * Please note that missing files will produce a fatal error.
 *
 * @link https://github.com/roots/sage/pull/1042
 */
$sage_includes = [
  'lib/assets.php',    	// Scripts and stylesheets
  'lib/extras.php',    	// Custom functions
  'lib/setup.php',     	// Theme setup
  'lib/titles.php',    	// Page titles
  'lib/wrapper.php',   	// Theme wrapper class
  'lib/customizer.php', // Theme customizer
  'lib/api.php' 		// Custom api
];

foreach ($sage_includes as $file) {
  if (!$filepath = locate_template($file)) {
    trigger_error(sprintf(__('Error locating %s for inclusion', 'sage'), $file), E_USER_ERROR);
  }

  require_once $filepath;
}
unset($file, $filepath);

add_filter( 'wp_image_editors', 'change_graphic_lib' );

function change_graphic_lib($array) {
return array( 'WP_Image_Editor_GD', 'WP_Image_Editor_Imagick' );
}

// Funções para esconder os usuários 
function redirect_to_home_if_author_parameter() {
	$is_author_set = get_query_var( 'author', '' );

	if ( $is_author_set != '' && !is_admin()) {
		wp_redirect( home_url(), 301 );
		exit;
	}
}

add_action( 'template_redirect', 'redirect_to_home_if_author_parameter' );

function disable_rest_endpoints ( $endpoints ) {
    if ( isset( $endpoints['/wp/v2/users'] ) ) {
        unset( $endpoints['/wp/v2/users'] );
    }

    if ( isset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] ) ) {
        unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
    }

    return $endpoints;
}

add_filter( 'rest_endpoints', 'disable_rest_endpoints');

if (!function_exists('get_magic_quotes_gpc')) {
    function get_magic_quotes_gpc() {
        // Check if magic quotes GPC emulation is needed
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            return (bool) ini_get('magic_quotes_gpc');
        } else {
            return false; // Magic quotes GPC is deprecated and not available
        }
    }
}
