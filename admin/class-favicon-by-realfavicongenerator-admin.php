<?php
// Copyright 2014 RealFaviconGenerator

require_once plugin_dir_path( __FILE__ ) . '../public/class-favicon-by-realfavicongenerator-common.php';
require_once plugin_dir_path( __FILE__ ) . 'class-favicon-by-realfavicongenerator-api-response.php';

class Favicon_By_RealFaviconGenerator_Admin extends Favicon_By_RealFaviconGenerator_Common {

	protected static $instance = null;

	private function __construct() {
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		add_action( 'admin_head', array( $this, 'add_favicon_markups' ) );

		// Deactivate Genesis default favicon
		add_filter( 'genesis_pre_load_favicon', array( $this, 'return_empty_favicon_for_genesis' ) );
		
		// Except for the headers, everything is accessible only to the admin
		if ( ! is_super_admin() ) {
			return;
		}

		add_action( 'admin_menu',
			array( $this, 'create_favicon_settings_menu' ) );

		add_action('wp_ajax_' . Favicon_By_RealFaviconGenerator_Common::PLUGIN_PREFIX . '_install_new_favicon',
			array( $this, 'install_new_favicon' ) );
		add_action('wp_ajax_nopriv_' . Favicon_By_RealFaviconGenerator_Common::PLUGIN_PREFIX . '_install_new_favicon',
			array( $this, 'install_new_favicon' ) );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function create_favicon_settings_menu() {
		add_theme_page( __( 'Favicon Settings', Favicon_By_RealFaviconGenerator_Common::PLUGIN_SLUG ), 
			__( 'Favicon', Favicon_By_RealFaviconGenerator_Common::PLUGIN_SLUG ), 'manage_options', __FILE__ . 'favicon_settings_menu', 
			array( $this, 'create_favicon_settings_page' ) );
	}

	public function create_favicon_settings_page() {
		$result = NULL;

		// Prepare settings page

		// Option to allow user to not use the Rewrite API: display it only when the Rewrite API is available
		$can_rewrite = $this->can_access_pics_with_url_rewrite();
		$pic_path = $this->get_full_picture_path();

		$favicon_configured = $this->is_favicon_configured();
		$favicon_in_root = $this->is_favicon_in_root();

		$preview_url = $this->is_preview_available() ? $this->get_preview_url() : NULL;

		if ( isset( $_REQUEST['json_result_url'] ) ) {
			// New favicon to install:
			// Parameters will be processed with an Ajax call

			$new_favicon_params_url = $_REQUEST['json_result_url'];
			$ajax_url = admin_url( 'admin-ajax.php', isset( $_SERVER['HTTPS'] ) ? 'https://' : 'http://' );
		}
		else {
			// No new favicon, simply display the settings page
			$new_favicon_params_url = NULL;
		}

		// External files
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui' );
		wp_enqueue_media();
		wp_enqueue_style( 'fbrfg_admin_style', plugins_url( 'assets/css/admin.css', __FILE__ ) );

		// Template time!
		include_once( plugin_dir_path(__FILE__) . 'views/settings.php' );
	}

	private function download_result_json( $url ) {
		$resp = wp_remote_get( $url );
		if ( is_wp_error( $resp )) {
			throw new InvalidArgumentException( "Cannot download JSON file at " . $url . ": " . $resp->get_error_message() );
		}

		$json = wp_remote_retrieve_body( $resp );
		if ( empty( $json ) ) {
			throw new InvalidArgumentException( "Empty JSON document at " . $url );
		}

		return $json;
	}

	public function install_new_favicon() {
		header("Content-type: application/json");

		try {
			// URL is explicitely decoded to compensate the extra encoding performed while generating the settings page
			$url = $_REQUEST['json_result_url'];

			$result = $this->download_result_json( $url );

			$response = new Favicon_By_RealFaviconGenerator_Api_Response( $result );

			$zip_path = Favicon_By_RealFaviconGenerator_Common::get_tmp_dir();
			if ( ! file_exists( $zip_path ) ) {
				mkdir( $zip_path, 0777, true );
			}
			$response->downloadAndUnpack( $zip_path );

			$this->store_pictures( $response );

			$this->store_preview( $response->getPreviewPath() );

			Favicon_By_RealFaviconGenerator_Common::remove_directory( $zip_path );

			update_option( Favicon_By_RealFaviconGenerator_Common::OPTION_HTML_CODE, $response->getHtmlCode() );
			
			$this->set_favicon_configured( true, $response->isFilesInRoot() );
?>
{
	"status": "success",
	"preview_url": <?php echo json_encode( $this->get_preview_url() ) ?>,
	"favicon_in_root": <?php echo json_encode( $this->is_favicon_in_root() ) ?>
}
<?php
		}
		catch(Exception $e) {
?>
{
	"status": "error",
	"message": <?php echo json_encode( $e->getMessage() ) ?>
}
<?php
		}

		die();
	}

	public function get_picture_dir() {
		return Favicon_By_RealFaviconGenerator_Common::get_files_dir();
	}

	/**
	 * Returns http//somesite.com/blog/wp-content/upload/fbrfg/
	 */
	public function get_picture_url() {
		return Favicon_By_RealFaviconGenerator_Common::get_files_url();
	}

	/**
	 * Returns /blog/wp-content/upload/fbrfg/
	 */
	public function get_full_picture_path() {
		return parse_url( $this->get_picture_url(), PHP_URL_PATH );
	}

	/**
	 * Returns wp-content/upload/fbrfg/
	 */
	public function get_picture_path() {
		return substr( $this->get_picture_url(), strlen( home_url() ) );
	}

	public function get_preview_path( $preview_file_name = NULL ) {
		if ( ! $preview_file_name ) {
			$preview_file_name = $this->get_preview_file_name();
		}
		return $this->get_picture_dir() . 'preview' . DIRECTORY_SEPARATOR . $preview_file_name;
	}

	public function get_preview_url( $preview_file_name = NULL ) {
		if ( ! $preview_file_name ) {
			$preview_file_name = $this->get_preview_file_name();
		}
		return $this->get_picture_url() . '/preview/' . $preview_file_name;
	}

	public function store_preview( $preview_path ) {
		// Remove previous preview, if any
		$previous_preview = $this->get_preview_file_name();
		if ( $previous_preview != NULL && ( file_exists( $this->get_preview_path( $previous_preview ) ) ) ) {
			unlink( $this->get_preview_path( $previous_preview ) );
		}

		if ( $preview_path == NULL ) {
			// "Unregister" previous preview, if any
			$this->set_preview_file_name( NULL );
			return NULL;
		}
		else {
			$preview_file_name = 'preview_' . md5( 'RFB stuff here ' . rand() . microtime() ) . '.png';
		}

		if ( ! file_exists( dirname( $this->get_preview_path( $preview_file_name ) ) ) ) {
			mkdir( dirname( $this->get_preview_path( $preview_file_name ) ) );
		}

		rename( $preview_path, $this->get_preview_path( $preview_file_name ) );

		$this->set_preview_file_name( $preview_file_name );
	}

	public function store_pictures( $rfg_response ) {
		$working_dir = $this->get_picture_dir();

		// Move pictures to production directory
		$files = glob( $working_dir . '*' );
		foreach( $files as $file ) {
			if ( is_file( $file ) ) {
			    unlink( $file );
			}
		}
		$files = glob( $rfg_response->getProductionPackagePath() . DIRECTORY_SEPARATOR . '*' );
		foreach( $files as $file ) {
			if ( is_file( $file ) ) {
			    rename( $file, $working_dir . basename( $file ) );
			}
		}

		if ( $rfg_response->isFilesInRoot() ) {
			$this->rewrite_pictures_url( $working_dir );
			flush_rewrite_rules();
		}
	}

	public function rewrite_pictures_url( $pic_dir ) {
		foreach ( scandir($pic_dir) as $file ) {
			if ( ! is_dir( $pic_dir . DIRECTORY_SEPARATOR . $file ) ) {
				add_rewrite_rule( str_replace( '.', '\.', $file ), 
					trim( $this->get_picture_path(), '/') . '/' . $file );
			}
		}
	}

	/**
	 * Indicate if it is possible to create URLs such as /favicon.ico
	 */
	public function can_access_pics_with_url_rewrite() {
		global $wp_rewrite;

		// If blog is in root AND rewriting is available (http://wordpress.stackexchange.com/questions/142273/checking-that-the-rewrite-api-is-available),
		// we can produce URLs such as /favicon.ico
		$rewrite = ( $this->wp_in_root() && $wp_rewrite->using_permalinks() );
		if ( ! $rewrite ) {
			return false;
		}

		// See http://wordpress.org/support/topic/fbrfg-not-updating-htaccess-rewrite-rules
		$htaccess = get_home_path() . DIRECTORY_SEPARATOR . '.htaccess';
		// Two cases:
		//   - There is no .htaccess. Either we are not using Apache (so the Rewrite API is supposed to handle
		//     the rewriting differently) or there is a problem with Apache/WordPress config, but this is not our job.
		//   - .htaccess is present. If so, it should be writable.
		return ( ( ! file_exists( $htaccess ) ) || is_writable( $htaccess ) );
	}

	/**
	 * Indicate if WP is installed in the root of the web site (eg. http://mysite.com) or not (eg. http://mysite.com/blog).
	 */
	public function wp_in_root() {
		$path = parse_url( home_url(), PHP_URL_PATH );
		return ( ($path == NULL) || (strlen( $path ) == 0) );
	}
}
?>