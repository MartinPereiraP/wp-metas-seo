<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.motionweb.cl
 * @since      1.0.0
 *
 * @package    Wp_Metas_Seo
 * @subpackage Wp_Metas_Seo/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Metas_Seo
 * @subpackage Wp_Metas_Seo/admin
 * @author     Martin Pereira <martinpereirap@gmail.com>
 */
class Wp_Metas_Seo_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Metas_Seo_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Metas_Seo_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-metas-seo-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Metas_Seo_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Metas_Seo_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-metas-seo-admin.js', array( 'jquery' ), $this->version, false );

	}
	function social_plugin_menu()
{
	add_menu_page(
		'Ajuste Sociales',
		'ID Facebook',
		'administrador',
		'social-max-length-content-settings',
		'social-max-length_content_page_settings',
		'dashicons-admin-generic'
	);
}
add_action('admin_menu', 'social_plugin_menu');
}

