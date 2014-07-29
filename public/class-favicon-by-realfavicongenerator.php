<?php
// Copyright 2014 RealFaviconGenerator

require_once plugin_dir_path( __FILE__ ) . '../public/class-favicon-by-realfavicongenerator-common.php';

class Favicon_By_RealFaviconGenerator extends Favicon_By_RealFaviconGenerator_Common {

	protected static $instance = null;

	private function __construct() {
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'wp_head', array( $this, 'add_favicon_markups' ) );

		// Deactivate Genesis default favicon
		add_filter( 'genesis_pre_load_favicon', array( $this, 'return_empty_favicon_for_genesis' ) );

		// Check for updates
		add_action( Favicon_By_RealFaviconGenerator_Common::ACTION_CHECK_FOR_UPDATE, array( $this, 'check_for_updates' )  );
	}

	public function check_for_updates() {
		if ( ! $this->is_favicon_configured() ) {
			// No favicon so nothing to update
			error_log("RFG update checking: no favicon configured");
			return;
		}

		$version = $this->get_favicon_version();

		if ( $version == NULL ) {
			// No version for some reason. Let's leave.
			error_log("RFG update checking: current version not available");
			return;
		}

		$checkUrl = 'http://realfavicongenerator.net/api/versions?since=' . $version;
		$resp = wp_remote_get( $checkUrl );
		if ( ( $resp == NULL ) || ( $resp == false ) || ( is_wp_error( $resp ) )  || 
			 ( $resp['response'] == NULL ) || ( $resp['response']['code'] == NULL ) || ( $resp['response']['code'] != 200 ) ) {
			// Error of some kind? Return
			error_log("RFG update checking: cannot get latest version from RealFaviconGenerator" . 
				( is_wp_error( $resp ) ? ': ' . $resp->get_error_message() : '' ) . ' (URL was ' . $checkUrl . ')' );
			return;
		}

		$json = json_decode( $resp['body'], true );
		if ( empty( $json ) ) {
			error_log('RFG update checking: No change since version ' . $version . ' or cannot parse JSON (JSON parsing error code is ' . json_last_error() . ')' );
			return;
		}

		// We only note the latest available version.
		// For example, if we receive version 0.8, 0.9 and 0.10 (in this order), we only note 0.10
		$last = $json[count( $json ) - 1];
		$latestVersion = $last['version'];

		// Save the fact that we should update
		error_log( 'RFG update checking: we should update to ' . $latestVersion . ' (version of current favicon is ' . $version . ')');
		$this->set_update_available( true );
		$this->set_latest_version_available( $latestVersion );
	}

	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}


	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	private static function single_activate() {
		// Nothing to do
	}

	private static function single_deactivate() {
		wp_clear_scheduled_hook( Favicon_By_RealFaviconGenerator_Common::ACTION_CHECK_FOR_UPDATE );
	}

}
