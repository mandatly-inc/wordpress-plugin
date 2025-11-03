<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 *
 * @link       https://www.mandatly.com
 *
 * @package    Mandatly_Cookie_Compliance
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die();
}
delete_option( 'mdt_cookie_banner_status' );
delete_option( 'mdt_cookie_demo_status' );
delete_option( 'mdt_cookie_banner_guid' );
delete_option( 'mdt_cookie_demo_url' );
delete_option( 'mdt_cookie_baseURL' );
delete_option( 'mdt_cookie_live_url' );
delete_option( 'mdt_cookie_demoSettingFileName' );
delete_option( 'mdt_cookie_bannerFolder' );
