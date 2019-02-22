<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.motionweb.cl
 * @since      1.0.0
 *
 * @package    Wp_Metas_Seo
 * @subpackage Wp_Metas_Seo/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Wp_Metas_Seo
 * @subpackage Wp_Metas_Seo/includes
 * @author     Martin Pereira <martinpereirap@gmail.com>
 */
class Wp_Metas_Seo_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wp-metas-seo',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
