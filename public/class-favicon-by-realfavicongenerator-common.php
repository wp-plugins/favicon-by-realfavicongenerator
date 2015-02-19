<?php
// Copyright 2014 RealFaviconGenerator

class Favicon_By_RealFaviconGenerator_Common {
	
	const PLUGIN_PREFIX = 'fbrfg';

	const OPTION_FAVICON_CONFIGURED = 'fbrfg_favicon_configured';
	const OPTION_FAVICON_IN_ROOT    = 'fbrfg_favicon_in_root';
	const OPTION_PREVIEW_FILE_NAME  = 'fbrfg_preview_file_name';
	const OPTION_HTML_CODE          = 'fbrfg_html_code';
	const OPTION_FAVICON_VERSION    = 'fbrfg_favicon_version';
	const OPTION_UPDATE_AVAILABLE   = 'fbrfg_update_available';
	const OPTION_LATEST_VERSION     = 'fbrfg_latest_version';

	const META_NO_UPDATE_NOTICE_FOR_VERSION = 'fbrfg_ignore_update_notice_';
	const META_NO_UPDATE_NOTICE = 'fbrfg_no_update_notice';

	public static function get_options_list() {
		return array(
			Favicon_By_RealFaviconGenerator_Common::OPTION_FAVICON_CONFIGURED, 
			Favicon_By_RealFaviconGenerator_Common::OPTION_FAVICON_IN_ROOT, 
			Favicon_By_RealFaviconGenerator_Common::OPTION_PREVIEW_FILE_NAME,
			Favicon_By_RealFaviconGenerator_Common::OPTION_HTML_CODE,
			Favicon_By_RealFaviconGenerator_Common::OPTION_FAVICON_VERSION,
			Favicon_By_RealFaviconGenerator_Common::OPTION_UPDATE_AVAILABLE,
			Favicon_By_RealFaviconGenerator_Common::OPTION_LATEST_VERSION );
	}

	const PLUGIN_SLUG = 'favicon-by-realfavicongenerator';

	const ACTION_CHECK_FOR_UPDATE = 'fbrfg_check_for_updates';

	public function get_latest_version_available() {
		return get_option( Favicon_By_RealFaviconGenerator_Common::OPTION_LATEST_VERSION );
	}

	public function set_latest_version_available( $version ) {
		update_option( Favicon_By_RealFaviconGenerator_Common::OPTION_LATEST_VERSION,
			$version );
	}

	/**
	 * Indicate if a favicon was configured.
	 */
	public function is_favicon_configured() {
		$opt = get_option( Favicon_By_RealFaviconGenerator_Common::OPTION_FAVICON_CONFIGURED );
		return ( $opt == 1 );
	}

	/**
	 * Indicate if the configured favicon is in the root directory of the web site.
	 */
	public function is_favicon_in_root() {
		$opt = get_option( Favicon_By_RealFaviconGenerator_Common::OPTION_FAVICON_IN_ROOT );
		return ( $opt == 1 ) && $this->is_favicon_configured();
	}

	/**
	 * Indicate if an update is available (ie. need to re-generate the favicon).
	 */
	public function is_update_available() {
		$opt = get_option( Favicon_By_RealFaviconGenerator_Common::OPTION_UPDATE_AVAILABLE );
		// "Update" only makes sense when a favicon is already configured
		return ( $opt == 1 ) && $this->is_favicon_configured();
	}

	/**
	 * Set the "update" indicator
	 */
	public function set_update_available( $update_available ) {
		update_option( Favicon_By_RealFaviconGenerator_Common::OPTION_UPDATE_AVAILABLE,
			$update_available ? 1 : 0 );
	}

	public function get_favicon_version() {
		// Before the "version" feature was implemented, all favicons generated by the plugin
		// were generated with RFG v0.7
		return get_option( Favicon_By_RealFaviconGenerator_Common::OPTION_FAVICON_VERSION, "0.7" );
	}

	public function set_favicon_configured( $configured = true, $favicon_in_root = false, $version = NULL ) {
		update_option( Favicon_By_RealFaviconGenerator_Common::OPTION_FAVICON_CONFIGURED,
			$configured ? 1 : 0 );
		update_option( Favicon_By_RealFaviconGenerator_Common::OPTION_FAVICON_IN_ROOT,
			$favicon_in_root ? 1 : 0 );

		if ( $version != NULL ) {
			update_option( Favicon_By_RealFaviconGenerator_Common::OPTION_FAVICON_VERSION,
				$version );
		}

		// We've just configured a favicon with the latest version of RFG so...
		$this->set_update_available( false );
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

	public function remove_genesis_favicon() {
		// See http://dreamwhisperdesigns.com/genesis-tutorials/change-default-genesis-favicon/
		// However, I didn't find the right hook to trigger this code in time to deactivate Genesis hooks.
		// As a consequence, this function is not used and mostly here as a reference.
		remove_action( 'genesis_meta', 'genesis_load_favicon' );
		remove_action( 'wp_head', 'genesis_load_favicon' );
	}

	public function return_empty_favicon_for_genesis( $param ) {
		$code = get_option( Favicon_By_RealFaviconGenerator_Common::OPTION_HTML_CODE );
		if ( $code ) {
			// Why NULL?
			// - It is not false (ie. the exact boolean value 'false')
			// - When tested with 'if ($value)', the condition fails.
			// See function genesis_load_favicon for more details
			return NULL;
		}
		else {
			// Return the value as is, no interference with the rest of WordPress
			return $param;
		}
	}

	/**
	 * Returns /www/wordpress/wp-content/uploaded/fbrfg
	 */
	public static function get_files_dir() {
		$up_dir = wp_upload_dir();
		return $up_dir['basedir'] . '/' . Favicon_By_RealFaviconGenerator_Common::PLUGIN_PREFIX . '/';
	}

	/**
	 * Returns http//somesite.com/blog/wp-content/upload/fbrfg/
	 */
	public static function get_files_url() {
		$up_dir = wp_upload_dir();
		return $up_dir['baseurl'] . '/' . Favicon_By_RealFaviconGenerator_Common::PLUGIN_PREFIX . '/';
	}

	public static function get_tmp_dir() {
		return Favicon_By_RealFaviconGenerator_Common::get_files_dir() . '/tmp/';
	}

	public static function remove_directory($directory) {
		foreach( scandir( $directory ) as $v ) {
			if ( is_dir( $directory . '/' . $v ) ) {
				if ( $v != '.' && $v != '..' ) {
					Favicon_By_RealFaviconGenerator_Common::remove_directory( $directory . '/' . $v );
				}
			}
			else {
				unlink( $directory . '/' . $v );
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

	// See http://webcheatsheet.com/php/get_current_page_url.php
	public function current_page_url() {
		$pageURL = 'http';
		if ( $_SERVER["HTTPS"] == "on" ) {
			$pageURL .= "s";
		}
		$pageURL .= "://";
		if ( $_SERVER["SERVER_PORT"] != "80" ) {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		}
		else {
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		return $pageURL;
	}

	public function add_parameter_to_current_url( $param_and_value ) {
		$url = $this->current_page_url();
		if ( strpos( $url, '?') !== false) {
			return $url . '&' . $param_and_value;
		}
		else {
			return $url . '?' . $param_and_value;
		}
	}
}

// Shortcut
define('FBRFG_PLUGIN_SLUG', Favicon_By_RealFaviconGenerator_Common::PLUGIN_SLUG);
