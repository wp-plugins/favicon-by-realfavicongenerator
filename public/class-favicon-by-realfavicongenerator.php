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
		// Nothing to do
	}

}
