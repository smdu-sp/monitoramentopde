<?php

namespace Roots\Sage\Setup;

use Roots\Sage\Assets;

/**
 * Theme setup
 */
function setup() {
  // Enable features from Soil when plugin is activated
  // https://roots.io/plugins/soil/
  /*add_theme_support('soil-clean-up');
  add_theme_support('soil-nav-walker');
  add_theme_support('soil-nice-search');
  add_theme_support('soil-jquery-cdn');
  add_theme_support('soil-relative-urls');*/

  // Make theme available for translation
  // Community translations can be found at https://github.com/roots/sage-translations
  load_theme_textdomain('sage', get_template_directory() . '/lang');

  // Enable plugins to manage the document title
  // http://codex.wordpress.org/Function_Reference/add_theme_support#Title_Tag
  add_theme_support('title-tag');

  // Register wp_nav_menu() menus
  // http://codex.wordpress.org/Function_Reference/register_nav_menus
  register_nav_menus([
    'secoes' => __('Navegação por seções', 'sage'),
		'formas' => __('Formas de visualização de indicadores', 'sage')
  ]);

  // Enable post thumbnails
  // http://codex.wordpress.org/Post_Thumbnails
  // http://codex.wordpress.org/Function_Reference/set_post_thumbnail_size
  // http://codex.wordpress.org/Function_Reference/add_image_size
  add_theme_support('post-thumbnails');

  // Enable post formats
  // http://codex.wordpress.org/Post_Formats
  add_theme_support('post-formats', ['aside', 'gallery', 'link', 'image', 'quote', 'video', 'audio']);

  // Enable HTML5 markup support
  // http://codex.wordpress.org/Function_Reference/add_theme_support#HTML5
  add_theme_support('html5', ['caption', 'comment-form', 'comment-list', 'gallery', 'search-form']);

  // Use main stylesheet for visual editor
  // To add custom styles edit /assets/styles/layouts/_tinymce.scss
  add_editor_style(Assets\asset_path('styles/main.css'));
  
	global $ApiConfig;
	$ApiConfig  = array(
		'application' => 'monitoramento_pde',
		'version' => '1'
	);

	global $DbConfig;
	$DbConfig = array(
		'host'     => '10.75.19.221',
		'port'     => '5432',
		'user'     => 'smdu',
		'dbname'   => 'MonitoramentoPDE',
		'password' => 'O&6zlfbN'
	);
	
}
add_action('after_setup_theme', __NAMESPACE__ . '\\setup');

/**
 * Register sidebars
 */
function widgets_init() {
  register_sidebar([
    'name'          => __('Primary', 'sage'),
    'id'            => 'sidebar-primary',
    'before_widget' => '<section class="widget %1$s %2$s">',
    'after_widget'  => '</section>',
    'before_title'  => '<h3>',
    'after_title'   => '</h3>'
  ]);

  register_sidebar([
    'name'          => __('Footer', 'sage'),
    'id'            => 'sidebar-footer',
    'before_widget' => '<section class="widget %1$s %2$s">',
    'after_widget'  => '</section>',
    'before_title'  => '<h3>',
    'after_title'   => '</h3>'
  ]);
}
add_action('widgets_init', __NAMESPACE__ . '\\widgets_init');

/**
 * Determine which pages should NOT display the sidebar
 */
function display_sidebar() {
  static $display;

  isset($display) || $display = !in_array(true, [
    // The sidebar will NOT be displayed if ANY of the following return true.
    // @link https://codex.wordpress.org/Conditional_Tags
    is_404(),
    is_front_page(),
    is_page_template('template-custom.php'),
  ]);

  return apply_filters('sage/display_sidebar', $display);
}

/**
 * Theme assets
 */
function assets() {
  wp_enqueue_style('sage/css', Assets\asset_path('styles/main.css'), false, null);

  if (is_single() && comments_open() && get_option('thread_comments')) {
    wp_enqueue_script('comment-reply');
  }

  wp_enqueue_script('sage/js', Assets\asset_path('scripts/main.js'), ['jquery'], null, true);
  
  wp_enqueue_script( 'highcharts', get_template_directory_uri() . '/js/Highcharts-4.2.5/js/highcharts.js', false );
	wp_enqueue_script( 'highcharts_exporting', get_template_directory_uri() . '/js/Highcharts-4.2.5/js/modules/exporting.js', false );
	wp_enqueue_script( 'highcharts_offline_exporting', get_template_directory_uri() . '/js/Highcharts-4.2.5/js/modules/offline-exporting.js', false );
  wp_enqueue_script( 'highcharts_more', get_template_directory_uri() . '/js/Highcharts-4.2.5/js/highcharts-more.js', false );
  wp_enqueue_script( 'angular', get_template_directory_uri() . '/js/angular-1.5.8/angular.min.js', false );
  wp_enqueue_script( 'angular_resource', get_template_directory_uri() . '/js/angular-1.5.8/angular-resource.min.js', false );
	wp_enqueue_script( 'angular_sanitize', get_template_directory_uri() . '/js/angular-1.5.8/angular-sanitize.min.js', false );
  wp_enqueue_script( 'angular_animate', get_template_directory_uri() . '/js/angular-1.5.8/angular-animate.min.js', false );
  wp_enqueue_script( 'angular_touch', get_template_directory_uri() . '/js/angular-1.5.8/angular-touch.min.js', false );
  wp_enqueue_script( 'angular_ptbr', get_template_directory_uri() . '/js/angular-1.5.8/angular-locale_pt-br.js',false );
  wp_enqueue_script( 'open_layers', get_template_directory_uri() . '/js/OpenLayers-3.17.1/build/ol.js',false );
  wp_enqueue_script( 'angularui-bootstrap', get_template_directory_uri() . '/js/angularui_bootstrap-2.0.2/ui-bootstrap-tpls-2.0.2.min.js',false );
  wp_enqueue_script( 'bootstrap-js', get_template_directory_uri() . '/js/Bootstrap-3.3.7/bootstrap.min.js',false );
  wp_enqueue_script( 'angular-filter', get_template_directory_uri() . '/js/angular-filter.js',false );
	wp_enqueue_script( 'angular-route', get_template_directory_uri() . '/js/angular-route.js',false );
	wp_enqueue_script( 'blob', get_template_directory_uri() . '/js/js-xlsx/Blob.js',false );
	wp_enqueue_script( 'file_saver', get_template_directory_uri() . '/js/js-xlsx/FileSaver.js',false );
	wp_enqueue_script( 'jszip', get_template_directory_uri() . '/js/js-xlsx/jszip.js',false );
	wp_enqueue_script( 'js-xlsx', get_template_directory_uri() . '/js/js-xlsx/xlsx.js',false );
	wp_enqueue_script( 'cpexcel', get_template_directory_uri() . '/js/js-xlsx/cpexcel.js',false );
	wp_enqueue_script( 'filesaver', get_template_directory_uri() . '/js/FileSaver.min.js',false );
	wp_enqueue_script( 'sortable', get_template_directory_uri() . '/js/ng-sortable.min.js',false );
	wp_enqueue_script( 'dropdown-multiselect', get_template_directory_uri() . '/js/dropdown-multiselect/angularjs-dropdown-multiselect.min.js',false );
	
  wp_enqueue_style( 'open_layers', get_template_directory_uri() . '/css/ol.css' );
	wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/css/font-awesome.min.css' );
  wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/css/bootstrap-3.3.7/css/bootstrap.min.css' );
  wp_enqueue_style( 'sortable-required', get_template_directory_uri() . '/css/ng-sortable.css' );
  wp_enqueue_style( 'sortable-style', get_template_directory_uri() . '/css/ng-sortable.style.css' );
  wp_enqueue_style( 'monitoramento_pde', get_template_directory_uri() . '/style.css' );
}
add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\assets', 100);
