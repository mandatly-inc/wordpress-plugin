<?php
/**
 * WP Consent API Integration for Mandatly Cookie Compliance
 *
 * @link       https://www.mandatly.com
 * @since      1.3.0
 *
 * @package    Mandatly_Cookie_Compliance
 * @subpackage Mandatly_Cookie_Compliance/includes
 */

/**
 * WP Consent API Integration Class
 *
 * @since      1.3.0
 * @package    Mandatly_Cookie_Compliance
 * @subpackage Mandatly_Cookie_Compliance/includes
 * @author     Mandatly <info@mandatly.com>
 */
class Mandatly_WP_Consent_API {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.3.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.3.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Mapping between Mandatly categories and WP Consent API categories
	 *
	 * @since    1.3.0
	 * @access   private
	 * @var      array    $category_mapping
	 */
	private $category_mapping = array(
		'Essential'    => 'functional',
		'Marketing'    => 'marketing',
		'Analytics'    => 'statistics',
		'Functional'   => 'preferences',
		'Unclassified' => 'statistics',
	);

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.3.0
	 * @param    string    $plugin_name    The name of this plugin.
	 * @param    string    $version        The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register plugin compliance with WP Consent API
	 *
	 * @since    1.3.0
	 */
	public function register_compliance() {
		$plugin = plugin_basename( dirname( dirname( __FILE__ ) ) . '/mandatly-cookie-compliance-and-consent-solution.php' );
		add_filter( "wp_consent_api_registered_{$plugin}", '__return_true' );
	}

	/**
	 * Set the consent type based on configuration
	 *
	 * @since    1.3.0
	 * @param    string    $consent_type    The consent type (opt-in or opt-out)
	 * @return   string
	 */
	public function set_consent_type( $consent_type ) {
		// Default to opt-in for GDPR compliance
		return 'opt-in';
	}

	/**
	 * Check if WP Consent API plugin is active
	 *
	 * @since    1.3.0
	 * @return   boolean
	 */
	public function is_wp_consent_api_active() {
		return function_exists( 'wp_has_consent' ) && function_exists( 'wp_set_consent' );
	}

	/**
	 * Enqueue the consent synchronization scripts
	 *
	 * @since    1.3.0
	 */
	public function enqueue_consent_sync_scripts() {
		// Check if WP Consent API integration is enabled
		$wp_consent_enabled = get_option( 'mdt_wp_consent_api_enabled', 'false' );
		if ( $wp_consent_enabled !== 'true' ) {
			return;
		}

		// Only enqueue if WP Consent API is active and banner is enabled
		if ( ! $this->is_wp_consent_api_active() ) {
			return;
		}

		$banner_status = get_option( 'mdt_cookie_banner_status', 'true' );
		if ( $banner_status !== 'true' ) {
			return;
		}

		// Enqueue the sync script
		wp_enqueue_script(
			$this->plugin_name . '-wp-consent-sync',
			plugin_dir_url( dirname( __FILE__ ) ) . 'public/js/mandatly-wp-consent-sync.js',
			array(),
			$this->version,
			true // Load in footer
		);

		// Localize script with necessary data
		wp_localize_script(
			$this->plugin_name . '-wp-consent-sync',
			'mandatly_consent_api',
			array(
				'category_mapping' => $this->category_mapping,
				'is_api_active'   => $this->is_wp_consent_api_active(),
			)
		);
	}
}