<?php
/**
 * The admin functionality of the plugin.
 *
 * @link       https://www.mandatly.com
 *
 * @package    Mandatly_Cookie_Compliance
 * @subpackage Mandatly_Cookie_Compliance/admin
 */

/**
 * The admin functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin stylesheet and JavaScript.
 *
 * @package    Mandatly_Cookie_Compliance
 * @subpackage Mandatly_Cookie_Compliance/admin
 * @author     Mandatly Inc. <supportmail@mandatly.com>
 */
class Mandatly_Cookie_Compliance_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.2.1
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.2.1
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		/*Ajax calling function define in hook*/
		add_action( 'wp_ajax_banner_slider_status', array( $this, 'banner_slider_status' ) );
		add_action( 'wp_ajax_banner_getslider_status', array( $this, 'banner_getslider_status' ) );
		add_action( 'wp_ajax_save_banner_cookie_settings', array( $this, 'save_banner_cookie_settings' ) );
	}

	/**
	 * Register the stylesheets for the admin area.
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Mandatly_Cookie_Compliance_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mandatly_Cookie_Compliance_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		global $parent_file;
		if ( 'cooking-settings' != $parent_file ) {
			return;
		}
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mandatly-cookie-compliance-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Mandatly_Cookie_Compliance_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mandatly_Cookie_Compliance_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		global $parent_file;
		if ( 'cooking-settings' != $parent_file ) {
			return;
		}
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/mandatly-cookie-compliance-admin.js', array( 'jquery' ), $this->version, false );
	}
	/** Purpose: admin menu function.  */
	public function mcc_set_cookie_menu() {
		add_menu_page( esc_html__( 'Banner cookie setting', 'mandatly-cookie-compliance' ), esc_html__( 'Mandatly', 'mandatly-cookie-compliance' ), 8, 'cooking-settings', array( $this, 'mcc_display_cookie_form' ), plugin_dir_url( __FILE__ ) . 'assets/icon-mandatly.png' );
	}
	/** Purpose: Display function for cookies form.  */
	public function mcc_display_cookie_form() {
		$mcc_server_mode = get_option( 'mdt_cookie_demo_status' );
		$banner_guid     = get_option( 'mdt_cookie_banner_guid' );
		$banner_status   = get_option( 'mdt_cookie_banner_status' );
		require_once 'partials/mcc-admin-display.php';
	}
	/** Purpose: save serever mode - ajax call on click of save button  */
	public function save_server_mode() {
		$get_val = wp_unslash( $_GET );
		update_option( 'mdt_cookie_demo_status', $get_val['mcc_server_mode'] );
		wp_die();
	}
	/** Purpose: save option value - ajax call on click of save button  */
	public function save_banner_cookie_settings() {
		$get_val           = wp_unslash( $_GET );
		$banner_status     = '';
		$validated_pattern = '/^[a-zA-Z0-9-]+$/';

		if ( ! preg_match( $validated_pattern, $get_val['banner_guid'] ) ) {
			echo 'not_validate_guid';
		} else {
			// Validate banner file exists on CDN using WordPress HTTP API
			$banner_url = get_option( 'mdt_cookie_live_url' ) . $get_val['banner_guid'] . '.js';
			$response = wp_remote_head( $banner_url, array( 'timeout' => 10 ) );

			if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
				echo 'not_exists_guid';
			} else {
				update_option( 'mdt_cookie_banner_guid', $get_val['banner_guid'] );
				update_option( 'mdt_cookie_banner_status', $get_val['slider_status'] );
				update_option( 'mdt_cookie_demo_status', 'false' );

				// Integration settings are now saved via AJAX (v1.3.0+)
				// No need to save them here

				echo 'validate_guid';
			}
		}
		wp_die();

	}

	/**
	 * Save WP Consent API setting - AJAX handler
	 * @since 1.3.0
	 */
	public function save_wp_consent_api_setting() {
		$post_val = wp_unslash( $_GET );
		$setting_value = isset( $post_val['value'] ) ? sanitize_text_field( $post_val['value'] ) : 'false';
		update_option( 'mdt_wp_consent_api_enabled', $setting_value );
		echo 'success';
		wp_die();
	}

	/**
	 * Save Google Consent Mode setting - AJAX handler
	 * @since 1.3.0
	 */
	public function save_google_consent_mode_setting() {
		$post_val = wp_unslash( $_GET );
		$setting_value = isset( $post_val['value'] ) ? sanitize_text_field( $post_val['value'] ) : 'false';
		update_option( 'mdt_google_consent_enabled', $setting_value );
		echo 'success';
		wp_die();
	}

	/**
	 * Save Google Tags Before Consent setting - AJAX handler
	 * @since 1.3.0
	 */
	public function save_google_tags_before_setting() {
		$post_val = wp_unslash( $_GET );
		$setting_value = isset( $post_val['value'] ) ? sanitize_text_field( $post_val['value'] ) : 'false';
		update_option( 'mdt_google_tags_before_consent', $setting_value );
		echo 'success';
		wp_die();
	}

	/** Purpose: update slider status true/false - ajax call on click of slider button.  */
	public function banner_slider_status() {
		$post_val      = wp_unslash( $_GET );
		$slider_status = '';
		$slider_status = sanitize_text_field( $post_val['banner_slider_status_val'] );
		if ( isset( $slider_status ) && '' != $slider_status ) {
			if ( 'false' == $post_val['server_mode'] ) {
				update_option( 'mdt_cookie_demo_status', 'false' );
			} else {
				update_option( 'mdt_cookie_demo_status', 'true' );
			}
			update_option( 'mdt_cookie_banner_status', $slider_status );
		}
		wp_die();
	}
	/** Purpose: generate script and add in to wp_head(). */
	public function banner_script_insert() {
		if ( 'true' == get_option( 'mdt_cookie_banner_status' ) ) {
			if ( 'true' == get_option( 'mdt_cookie_demo_status' ) ) {
				if ( '' != get_option( 'mdt_cookie_demo_url' ) ) {
					wp_enqueue_script( 'mandatlycookie', get_option( 'mdt_cookie_demo_url' ), array(), uniqid() );
				}
			} else {
				if ( '' != get_option( 'mdt_cookie_banner_guid' ) ) {
					$live_guid_url = get_option( 'mdt_cookie_live_url' ) . get_option( 'mdt_cookie_banner_guid' ) . '.js';
					wp_enqueue_script( 'mandatlycookie', $live_guid_url, array(), uniqid() );
				}
			}
		}
	}

}
