<?php
// Copyright 2014 RealFaviconGenerator

class Favicon_By_RealFaviconGenerator_Common {
	
	const PLUGIN_PREFIX = 'fbrfg';

	const OPTION_FAVICON_CONFIGURED = 'fbrfg_favicon_configured';
	const OPTION_PREVIEW_FILE_NAME = 'fbrfg_preview_file_name';
	const OPTION_HTML_CODE = 'fbrfg_html_code';

	public static function get_options_list() {
		return array(
			Favicon_By_RealFaviconGenerator_Common::OPTION_FAVICON_CONFIGURED, 
			Favicon_By_RealFaviconGenerator_Common::OPTION_PREVIEW_FILE_NAME,
			Favicon_By_RealFaviconGenerator_Common::OPTION_HTML_CODE );
	}

	const PLUGIN_SLUG = 'favicon-by-realfavicongenerator';

	/**
	 * Indicate if a favicon was configured.
	 */
	public function is_favicon_configured() {
		$opt = get_option( Favicon_By_RealFaviconGenerator_Common::OPTION_FAVICON_CONFIGURED );
		return ( $opt == 1 );
	}

	public function set_favicon_configured( $configured = true ) {
		update_option( Favicon_By_RealFaviconGenerator_Common::OPTION_FAVICON_CONFIGURED,
			$configured ? 1 : 0 );
	}

	public function is_preview_available() {
		$opt = get_option( Favicon_By_RealFaviconGenerator_Common::OPTION_PREVIEW_FILE_NAME );
		return ( ( $opt != NULL ) && ( $opt != false ) );
	}

	public function get_preview_file_name() {
		return get_option( Favicon_By_RealFaviconGenerator_Common::OPTION_PREVIEW_FILE_NAME );
	}

	public function set_preview_file_name($preview_file_name) {
		update_option( Favicon_By_RealFaviconGenerator_Common::OPTION_PREVIEW_FILE_NAME, 
			$preview_file_name);
	}

	public function add_favicon_markups() {
		$code = get_option( Favicon_By_RealFaviconGenerator_Common::OPTION_HTML_CODE );
		if ( $code ) {
			echo $code;
		}
	}

	/**
	 * Returns /www/wordpress/wp-content/uploaded/fbrfg
	 */
	public static function get_files_dir() {
		$up_dir = wp_upload_dir();
		return $up_dir['basedir'] . DIRECTORY_SEPARATOR . Favicon_By_RealFaviconGenerator_Common::PLUGIN_PREFIX . DIRECTORY_SEPARATOR;
	}

	/**
	 * Returns http//somesite.com/blog/wp-content/upload/fbrfg/
	 */
	public static function get_files_url() {
		$up_dir = wp_upload_dir();
		return $up_dir['baseurl'] . DIRECTORY_SEPARATOR . Favicon_By_RealFaviconGenerator_Common::PLUGIN_PREFIX . DIRECTORY_SEPARATOR;
	}

	public static function get_tmp_dir() {
		return Favicon_By_RealFaviconGenerator_Common::get_files_dir() . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
	}

	public static function remove_directory($directory) {
		foreach( scandir( $directory ) as $v ) {
			if ( is_dir( $directory . DIRECTORY_SEPARATOR . $v ) ) {
				if ( $v != '.' && $v != '..' ) {
					Favicon_By_RealFaviconGenerator_Common::remove_directory( $directory . DIRECTORY_SEPARATOR . $v );
				}
			}
			else {
				unlink( $directory . DIRECTORY_SEPARATOR . $v );
			}
		}
		rmdir( $directory );
	}


	/**
	 * Load the plugin text domain for translation.
	 */
	public function load_plugin_textdomain() {

		$domain = Favicon_By_RealFaviconGenerator_Common::PLUGIN_SLUG;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

	}

}

// Shortcut
define('FBRFG_PLUGIN_SLUG', Favicon_By_RealFaviconGenerator_Common::PLUGIN_SLUG);
