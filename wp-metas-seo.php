<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.motionweb.cl
 * @since             1.0.0
 * @package           Wp_Metas_Seo
 *
 * @wordpress-plugin
 * Plugin Name:       WP Metas SEO
 * Plugin URI:        https://github.com/MartinPereiraP
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Martin Pereira
 * Author URI:        https://www.motionweb.cl
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-metas-seo
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('PLUGIN_NAME_VERSION', '1.0.0');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-metas-seo-activator.php
 */
function activate_wp_metas_seo()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-wp-metas-seo-activator.php';
	Wp_Metas_Seo_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-metas-seo-deactivator.php
 */
function deactivate_wp_metas_seo()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-wp-metas-seo-deactivator.php';
	Wp_Metas_Seo_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_wp_metas_seo');
register_deactivation_hook(__FILE__, 'deactivate_wp_metas_seo');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-wp-metas-seo.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_metas_seo()
{

	$plugin = new Wp_Metas_Seo();
	$plugin->run();

}

// SEO WordPress
///////////////////////////////////////////////////////
function add_meta_tags()
{
	global $page, $paged, $post;
	$default_keywords = 'Diseño Web, Desarrollo Web, Pagina Web, Desing Website, Developers Website'; // personaliza esto
	$output = '';

  // títulos
	$title_custom = get_post_meta($post->ID, 'wp_seo_title', true);
	$url = ltrim(esc_url($_SERVER['REQUEST_URI']), '/');
	$name = get_bloginfo('name', 'display');
	$title = trim(wp_title('', false));
	$cat = single_cat_title('', false);
	$tag = single_tag_title('', false);
	$search = get_search_query();

	if (!empty($title_custom)) $title = $title_custom;
	if ($paged >= 2 || $page >= 2) $page_number = ' | ' . sprintf('Página %s', max($paged, $page));
	else $page_number = '';

	if (is_home() || is_front_page()) $seo_title = $name . ' | Diseño y Desarrollo Web' . $description;
	elseif (is_singular()) $seo_title = $title;
	elseif (is_tag()) $seo_title = 'Archivo de la etiqueta: ' . $tag . ' | ' . $name;
	elseif (is_category()) $seo_title = 'Archivo de la categoría: ' . $cat . ' | ' . $name;
	elseif (is_archive()) $seo_title = '' . $title . ' | ' . $name;
	elseif (is_search()) $seo_title = 'Búsqueda: ' . $search . ' | ' . $name;
	elseif (is_404()) $seo_title = '404 - No encontrado: ' . $url . ' | ' . $name;
	else $seo_title = $name . ' | ' . $description;

	{
		echo '<title>' . esc_attr($seo_title . $page_number) . '</title>' . "\n";
	}

	// descripción
	$seo_desc = get_post_meta($post->ID, 'wp_seo_desc', true);
	$description = get_bloginfo('description', 'display');
	$pagedata = get_post($post->ID);
	if (is_singular()) {
		if (!empty($seo_desc)) {
			$content = $seo_desc;
		} else if (!empty($pagedata)) {
			$content = apply_filters('the_excerpt_rss', $pagedata->post_content);
			$content = substr(trim(strip_tags($content)), 0, 155);
			$content = preg_replace('#\n#', ' ', $content);
			$content = preg_replace('#\s{2,}#', ' ', $content);
			$content = trim($content);
		}
	} else {
		$content = $description;
	}
	{
		echo "\t" . '<meta name="description" content="' . esc_attr($content) . '">' . "\n";
	}

	// palabras clave
	$keys = get_post_meta($post->ID, 'wp_seo_keywords', true);
	$cats = get_the_category();
	$tags = get_the_tags();
	if (empty($keys)) {
		if (!empty($cats)) foreach ($cats as $cat) $keys .= $cat->name . ', ';
		if (!empty($tags)) foreach ($tags as $tag) $keys .= $tag->name . ', ';
		$keys .= $default_keywords;
	}

	if (is_singular()) {
		echo "\t" . '<meta name="keywords" content="' . esc_attr($keys) . '">' . "\n";
	} else {
		echo "\t" . '<meta name="keywords" content="' . $default_keywords . '">' . "\n";
	}

	// robots
	if (is_category() || is_tag()) {
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		if ($paged > 1) {
			echo "\t" . '<meta name="robots" content="noindex,follow">' . "\n";
		} else {
			echo "\t" . '<meta name="robots" content="index,follow">' . "\n";
		}
	} else if (is_home() || is_singular()) {
		echo "\t" . '<meta name="robots" content="index,follow">' . "\n";
	} else {
		echo "\t" . '<meta name="robots" content="noindex,follow">' . "\n";
	}
}
add_action('wp_head', 'add_meta_tags', '');

function motionweb_favicons()
{
	$color = "000000";
	$url_favicon = get_stylesheet_directory_uri() . '/assets/img/favicons/';

	echo "\t" . '<!-- Apple Touch icons -->' . "\n";
	echo "\t" . '<meta name="mobile-web-app-capable" content="yes">' . "\n";
	echo "\t" . '<meta name="apple-mobile-web-app-capable" content="yes">' . "\n";
	echo "\t" . '<meta name="apple-mobile-web-app-title" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
	echo "\t" . '<link rel="apple-touch-icon" sizes="57x57" href="' . $url_favicon . 'apple-touch-icon-57x57.png">' . "\n";
	echo "\t" . '<link rel="apple-touch-icon" sizes="72x72" href="' . $url_favicon . 'apple-touch-icon-72x72.png">' . "\n";
	echo "\t" . '<link rel="apple-touch-icon" sizes="76x76" href="' . $url_favicon . 'apple-touch-icon-76x76.png">' . "\n";
	echo "\t" . '<link rel="apple-touch-icon" sizes="114x114" href="' . $url_favicon . 'apple-touch-icon-114x114.png">' . "\n";
	echo "\t" . '<link rel="apple-touch-icon" sizes="120x120" href="' . $url_favicon . 'apple-touch-icon-120x120.png">' . "\n";
	echo "\t" . '<link rel="apple-touch-icon" sizes="144x144" href="' . $url_favicon . 'apple-touch-icon-144x144.png">' . "\n";
	echo "\t" . '<link rel="apple-touch-icon" sizes="152x152" href="' . $url_favicon . 'apple-touch-icon-152x152.png">' . "\n";
	echo "\t" . '<link rel="apple-touch-icon" sizes="180x180" href="' . $url_favicon . 'apple-touch-icon-180x180.png">' . "\n";
	echo "\t" . '<link rel="apple-touch-icon" sizes="167x167" href="' . $url_favicon . 'apple-touch-icon-167x167.png">' . "\n";

	echo "\t" . '<!-- Favicons -->' . "\n";
	echo "\t" . '<link rel="shortcut icon" type="image/x-icon" href="' . $url_favicon . 'favicon.ico">' . "\n";
	echo "\t" . '<link rel="icon" type="image/icon" href="' . $url_favicon . 'favicon.ico">' . "\n";
	echo "\t" . '<link rel="icon" type="image/png" href="' . $url_favicon . 'favicon.png" />' . "\n";
	echo "\t" . '<link rel="icon" type="image/vnd.microsoft.icon" href="' . $url_favicon . 'favicon.ico" />' . "\n";
	echo "\t" . '<link rel="icon" type="image/png" sizes="16x16" href="' . $url_favicon . 'favicon-16x16.png">' . "\n";
	echo "\t" . '<link rel="icon" type="image/png" sizes="32x32" href="' . $url_favicon . 'favicon-32x32.png">' . "\n";
	echo "\t" . '<link rel="icon" type="image/png" sizes="48x48" href="' . $url_favicon . 'favicon-48x48.png">' . "\n";
	echo "\t" . '<link rel="icon" type="image/png" sizes="192x192" href="' . $url_favicon . 'android-chrome-192x192.png">' . "\n";
	echo "\t" . '<link rel="icon" type="image/png" sizes="194x194" href="' . $url_favicon . 'favicon-194x194.png">' . "\n";
	echo "\t" . '<link rel="manifest" href="' . $url_favicon . 'site.webmanifest">' . "\n";
	echo "\t" . '<link rel="mask-icon" href="' . $url_favicon . 'safari-pinned-tab.svg" color="#' . $color . '">' . "\n";

	echo "\t" . '<!-- Windows 8 icon and RSS feed -->' . "\n";
	echo "\t" . '<meta name="application-name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
	echo "\t" . '<meta name="msapplication-config" content="' . $url_favicon . 'browserconfig.xml">' . "\n";
	echo "\t" . '<meta name="msapplication-TileColor" content="#' . $color . '">' . "\n";
	echo "\t" . '<meta name="theme-color" content="#' . $color . '">' . "\n";
	echo "\t" . '<meta name="msapplication-TileImage" content="' . $url_favicon . 'mstile-144x144.png">' . "\n";
	echo "\t" . '<meta name="theme-color" content="#' . $color . '">' . "\n";
}
add_action('wp_head', 'motionweb_favicons', '');


function opengraph_protocol()
{
    // ID SDK Facebook
	$fbAppId = 'xxxxxxxxxxx';
	$fbAdmins = 'xxxxxxxxxxxxxxx';
    // ID Twitter
	$twitterSite = 'twitterSite';
	$twitterCreator = "twitterCreator";
    // defaults
	$title = get_bloginfo('title');
	$img_src = get_stylesheet_directory_uri() . '/screenshot.png';
	$excerpt = get_bloginfo('description');
    // for non posts/pages, like /blog, just use the current URL
	$permalink = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	if (is_single() || is_page()) {
		global $post;
		setup_postdata($post);
		$title = get_the_title();
		$permalink = get_the_permalink();
		if (has_post_thumbnail($post->ID)) {
			$img_src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'featured-opengraph')[0];
		}
		$excerpt = get_the_excerpt();
		if ($excerpt) {
			$excerpt = strip_tags($excerpt);
			$excerpt = str_replace("", "'", $excerpt);
		}
	}
	echo "\t" . '<!-- Protocol Opengraph -->' . "\n";
	echo "\t" . '<meta property="fb:app_id" content="' . $fbAppId . '"/>' . "\n";
	echo "\t" . '<meta property="fb:admins" content="' . $fbAdmins . '" />' . "\n";
	echo "\t" . '<meta property="og:locale" content="es_ES" />' . "\n";
	echo "\t" . '<meta property="og:locale:alternate" content="en_US" />' . "\n";
	echo "\t" . '<meta property="og:title" content="' . $title . '" />' . "\n";
	if (is_home()) {
		echo "\t" . '<meta property="og:description" content="' . esc_attr( get_bloginfo( 'description' ) ) . '" />' . "\n";
		echo "\t" . '<meta property="og:url" content="' . home_url('/') . '" />' . "\n";
	} else {
		echo "\t" . '<meta property="og:description" content="' . excerpt('50') . '" />' . "\n";
		echo "\t" . '<meta property="og:url" content="' . $permalink . '" />' . "\n";
	}
	echo "\t" . '<meta property="og:image" content="' . $img_src . '" />' . "\n";
	echo "\t" . '<meta property="og:image:type" content="image/jpeg" />' . "\n";
	echo "\t" . '<meta property="og:image:width" content="1200" />' . "\n";
	echo "\t" . '<meta property="og:image:height" content="630" />' . "\n";
	if (is_single()) {
		$og_type = 'article';
	} else {
		$og_type = 'website';
	}
	echo "\t" . '<meta property="og:type" content="' . esc_attr(apply_filters('wog_type', $og_type)) . '"/>' . "\n";
	'<meta property="og:site_name" content="' . get_bloginfo() . '" />' . "\n";

	echo "\t" . '<!--Protocol Twitter Card -->' . "\n";
	echo "\t" . '<meta name="twitter:title" content="' . $title . '" />' . "\n";
	if (is_home()) {
		echo "\t" . '<meta name="twitter:description" content="' . esc_attr( get_bloginfo( 'description' ) ) . '" />' . "\n";
	} else {
		echo "\t" . '<meta name="twitter:description" content="' . excerpt('50') . '" />' . "\n";
	}
	echo "\t" . '<meta name="twitter:card" content="summary_large_image" />' . "\n";
	echo "\t" . '<meta name="twitter:site" content="@' . $twitterSite . '" />' . "\n";
	echo "\t" . '<meta name="twitter:creator" content="@' . $twitterCreator . '" />' . "\n";

}
add_action('wp_head', 'opengraph_protocol', '');



// Remove Title Head.
////////////////////////////////////////////////////////////////////////////
remove_action('wp_head', '_wp_render_title_tag', 1);

// Remove Head Security.
////////////////////////////////////////////////////////////////////////////
if (!function_exists('motionweb_start_cleanup')) :
  function motionweb_start_cleanup()
{
  add_action('init', 'motionweb_cleanup_head'); // Launching operation cleanup.
  add_filter('the_generator', 'motionweb_remove_rss_version'); // Remove WP version from RSS.
  add_filter('wp_head', 'motionweb_remove_wp_widget_recent_comments_style', 1); // Remove pesky injected css for recent comments widget.
  add_action('wp_head', 'motionweb_remove_recent_comments_style', 1); // Clean up comment styles in the head.
  add_filter('img_caption_shortcode', 'motionweb_remove_figure_inline_style', 10, 3); // Remove inline width attribute from figure tag

}
add_action('after_setup_theme', 'motionweb_start_cleanup');
endif;

// Clean up head.+
////////////////////////////////////////////////////////////////////////////
if (!function_exists('motionweb_cleanup_head')) :
  function motionweb_cleanup_head()
{
  remove_action('wp_head', 'rsd_link'); // EditURI link.
  remove_action('wp_head', 'feed_links_extra', 3); // Category feed links.
  remove_action('wp_head', 'feed_links', 2); // Post and comment feed links.
  remove_action('wp_head', 'wlwmanifest_link'); // Windows Live Writer.
  remove_action('wp_head', 'index_rel_link'); // Index link.
  remove_action('wp_head', 'parent_post_rel_link', 10, 0); // Previous link.
  remove_action('wp_head', 'start_post_rel_link', 10, 0); // Start link.
  //remove_action('wp_head', 'rel_canonical', 10, 0); // Canonical.
  remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0); // Shortlink.
  remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0); // Links for adjacent posts.
  remove_action('wp_head', 'wp_generator'); // WP version.
  remove_action('wp_head', 'print_emoji_detection_script', 7); // Emoji detection script.
  remove_action('wp_print_styles', 'print_emoji_styles'); // Emoji styles.
}
endif;

// Remove WP version from RSS.
////////////////////////////////////////////////////////////////////////////
if (!function_exists('motionweb_remove_rss_version')) :
  function motionweb_remove_rss_version()
{
  return '';
}
endif;

// Remove injected CSS for recent comments widget.
////////////////////////////////////////////////////////////////////////////
if (!function_exists('motionweb_remove_wp_widget_recent_comments_style')) :
  function motionweb_remove_wp_widget_recent_comments_style()
{
  if (has_filter('wp_head', 'wp_widget_recent_comments_style')) {
    remove_filter('wp_head', 'wp_widget_recent_comments_style');
  }
}
endif;

// Remove injected CSS from recent comments widget.
////////////////////////////////////////////////////////////////////////////
if (!function_exists('motionweb_remove_recent_comments_style')) :
  function motionweb_remove_recent_comments_style()
{
  global $wp_widget_factory;
  if (isset($wp_widget_factory->widgets['WP_Widget_Recent_Comments'])) {
    remove_action('wp_head', array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style'));
  }
}
endif;

// Remove inline width attribute from figure tag causing images wider than 100% of its container
////////////////////////////////////////////////////////////////////////////
if (!function_exists('motionweb_remove_figure_inline_style')) :
  function motionweb_remove_figure_inline_style($output, $attr, $content)
{
  $atts = shortcode_atts(array(
    'id' => '',
    'align' => 'alignnone',
    'width' => '',
    'caption' => '',
    'class' => '',
  ), $attr, 'caption');

  $atts['width'] = (int)$atts['width'];
  if ($atts['width'] < 1 || empty($atts['caption'])) {
    return $content;
  }

  if (!empty($atts['id'])) {
    $atts['id'] = 'id="' . esc_attr($atts['id']) . '" ';
  }

  $class = trim('wp-caption ' . $atts['align'] . ' ' . $atts['class']);

  if (current_theme_supports('html5', 'caption')) {
    return '<figure ' . $atts['id'] . ' class="' . esc_attr($class) . '">'
      . do_shortcode($content) . '<figcaption class="wp-caption-text">' . $atts['caption'] . '</figcaption></figure>';
  }

}
endif;

// Eliminar las versiones de los parámetros en las URLs
////////////////////////////////////////////////////////////////////////////
function remove_version_params($src)
{
  $parts = explode('?ver', $src);

  return $parts[0];
}
add_filter('script_loader_src', 'remove_version_params', 15, 1);
add_filter('style_loader_src', 'remove_version_params', 15, 1);

//Remove all classes and IDs from Nav Menu
////////////////////////////////////////////////////////////////////////////
function wp_nav_menu_remove($var) {
  return is_array($var) ? array_intersect($var, array('nav-item', 'active')) : '';
}
add_filter('page_css_class', 'wp_nav_menu_remove', 100, 1);
add_filter('nav_menu_item_id', 'wp_nav_menu_remove', 100, 1);
add_filter('nav_menu_css_class', 'wp_nav_menu_remove', 100, 1);

// Eliminar el control 'CSS Adicional' del personalizador
////////////////////////////////////////////////////////////////////////////
add_action('customize_register', 'jgc_remove_additional_css_control', 11);
function jgc_remove_additional_css_control($wp_customize)
{
  $wp_customize->remove_control('custom_css');
}

// Remove scripts from head.
////////////////////////////////////////////////////////
function move_scripts_from_head_to_footer()
{
    remove_action('wp_head', 'wp_print_scripts');
    remove_action('wp_head', 'wp_print_head_scripts', 9);
    remove_action('wp_head', 'wp_enqueue_scripts', 1);

    add_action('wp_footer', 'wp_print_scripts', 5);
    add_action('wp_footer', 'wp_enqueue_scripts', 5);
    add_action('wp_footer', 'wp_print_head_scripts', 5);
}
add_action('wp_enqueue_scripts', 'move_scripts_from_head_to_footer');


run_wp_metas_seo();
