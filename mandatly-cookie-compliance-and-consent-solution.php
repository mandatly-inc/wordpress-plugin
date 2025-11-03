<?php
/**
Plugin Name: Mandatly | Cookie Compliance and Consent solution
Plugin URI: https://www.mandatly.com
Description: Easily manage cookie consents and stay compliant with GDPR, LGPD, CCPA/CPRA, e-privacy directive and other regulations with our user-friendly cookie compliance solution.
Version: 1.3.0
Author: Mandatly Inc.
Author URI: https://www.mandatly.com
 *
 * @package    Mandatly_Cookie_Compliance
Text Domain: mandatly-cookie-compliance
Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
define( 'PLUGIN_URL', plugin_dir_path( __FILE__ ) );
require_once plugin_dir_path( __FILE__ ) . 'includes/mandatly-compliance-consents.php';
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-mandatly-cookie-compliance-activator.php
 */
function activate_mandatly_cookie_compliance() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mandatly-cookie-compliance-activator.php';
	Mandatly_Cookie_Compliance_Activator::activate();

}
/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-mandatly-cookie-compliance-deactivator.php
 */
function deactivate_mandatly_cookie_compliance() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mandatly-cookie-compliance-deactivator.php';
	Mandatly_Cookie_Compliance_Deactivator::deactivate();
}
register_activation_hook( __FILE__, 'activate_mandatly_cookie_compliance' );
register_deactivation_hook( __FILE__, 'deactivate_mandatly_cookie_compliance' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-mandatly-cookie-compliance.php';
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'cookie_settingslink' );
/** Purpose: add settings link under plugin name on plugin listing page
 *
@param string $links for getting existing links.
 */
function cookie_settingslink( $links ) {
	// Build and escape the URL.
	$setting_url = esc_url(
		add_query_arg(
			'page',
			'cooking-settings',
			get_admin_url() . 'admin.php'
		)
	);
	// Create the link.
	$settings_link = "<a href='" . $setting_url . "'>" . esc_html__( 'Settings', 'mandatly-cookie-compliance' ) . '</a>';
	// Adds the link to the end of the array.
	array_push(
		$links,
		$settings_link
	);
	return $links;
}
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 */
function run_mandatly_cookie_compliance() {
	$plugin = new Mandatly_Cookie_Compliance();
	$plugin->run();

}
run_mandatly_cookie_compliance();
