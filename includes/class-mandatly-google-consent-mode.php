<?php
/**
 * Google Consent Mode v2 Integration for Mandatly Cookie Compliance
 *
 * @link       https://www.mandatly.com
 * @since      1.3.0
 *
 * @package    Mandatly_Cookie_Compliance
 * @subpackage Mandatly_Cookie_Compliance/includes
 */

/**
 * Google Consent Mode v2 Integration Class
 *
 * @since      1.3.0
 * @package    Mandatly_Cookie_Compliance
 * @subpackage Mandatly_Cookie_Compliance/includes
 * @author     Mandatly <info@mandatly.com>
 */
class Mandatly_Google_Consent_Mode {

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
	 * Insert Google Consent Mode script in head with priority 0 (before banner)
	 *
	 * @since    1.3.0
	 */
	public function insert_google_consent_script() {
		// Get settings
		$google_consent_enabled = get_option( 'mdt_google_consent_enabled', 'false' );
		$tags_before_consent = get_option( 'mdt_google_tags_before_consent', 'false' );

		// Always set MandatlyScriptData, but only add consent commands if enabled
		?>
		<!-- Google Consent Mode v2 - Mandatly Integration -->
		<script>
		(function() {
			// Helper function to get cookie value
			function getCookie(name) {
				const value = '; ' + document.cookie;
				const parts = value.split('; ' + name + '=');
				if (parts.length === 2) {
					return parts.pop().split(';').shift();
				}
				return null;
			}

			// Initialize Google Tag Manager dataLayer if not exists
			window.dataLayer = window.dataLayer || [];
			function gtag() {
				dataLayer.push(arguments);
			}

			<?php if ( $google_consent_enabled === 'true' ) : ?>
			// Google Consent Mode is enabled
			const mdtConsentCookie = getCookie('Mdt_Consent');

			if (mdtConsentCookie) {
				// Cookie exists - execute UPDATE command
				try {
					const consentData = JSON.parse(decodeURIComponent(mdtConsentCookie));
					if (consentData.googleconsent) {
						gtag('consent', 'update', consentData.googleconsent);
					}
				} catch (e) {
					console.error('[Mandatly GCM] Error parsing consent cookie:', e);
				}
			} else {
				// Cookie doesn't exist - check if we should set defaults
				<?php if ( $tags_before_consent === 'true' ) : ?>
				gtag('consent', 'default', {
					'ad_storage': 'denied',
					'analytics_storage': 'denied',
					'ad_user_data': 'denied',
					'ad_personalization': 'denied',
					'functionality_storage': 'denied',
					'personalization_storage': 'denied',
					'security_storage': 'granted',
					'wait_for_update': 500
				});
				<?php endif; ?>
			}
			<?php endif; ?>

			// Always set MandatlyScriptData
			window.MandatlyScriptData = {
				useGTagTemplate: true,
				useGCM: <?php echo $google_consent_enabled === 'true' ? 'true' : 'false'; ?>,
				useGTagAdvanceMode: <?php echo $tags_before_consent === 'true' ? 'true' : 'false'; ?>
			};
		})();
		</script>
		<!-- End Google Consent Mode v2 -->
		<?php
	}
}