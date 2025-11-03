<?php
/**
 * Fired during plugin activation
 *
 * @link       https://www.mandatly.com
 *
 * @package    Mandatly_Cookie_Compliance
 * @subpackage Mandatly_Cookie_Compliance/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @package    Mandatly_Cookie_Compliance
 * @subpackage Mandatly_Cookie_Compliance/includes
 * @author     Mandatly Inc. <supportmail@mandatly.com>
 */
class Mandatly_Cookie_Compliance_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 */
	public static function activate() {
		if ( '' != get_option( 'mcc_banner_config' ) ) {
			$abanner_array_val = maybe_unserialize( get_option( 'mcc_banner_config' ) );
			if ( '' != $abanner_array_val['banner_guid'] ) {
				if ( 'active' == $abanner_array_val['banner_status'] ) {
					$banner_status = 'true';
				} else {
					$banner_status = 'false';
				}
				update_option( 'mdt_cookie_demo_status', 'false' );
				update_option( 'mdt_cookie_banner_guid', $abanner_array_val['banner_guid'] );
				update_option( 'mdt_cookie_banner_status', $banner_status );
				delete_option( 'mcc_banner_config' );
			}
		} else {
			update_option( 'mdt_cookie_demo_status', 'true' );
			update_option( 'mdt_cookie_banner_status', 'true' );
		}

		// Set default values for integration settings (v1.3.0+)
		if ( get_option( 'mdt_wp_consent_api_enabled' ) === false ) {
			update_option( 'mdt_wp_consent_api_enabled', 'true' );
		}
		if ( get_option( 'mdt_google_consent_enabled' ) === false ) {
			update_option( 'mdt_google_consent_enabled', 'true' );
		}
		if ( get_option( 'mdt_google_tags_before_consent' ) === false ) {
			update_option( 'mdt_google_tags_before_consent', 'true' );
		}
	}

}
