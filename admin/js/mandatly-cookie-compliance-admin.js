(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
       
    $(document).ready(function () {
		$("#activation-notice").hide();
		$('#wpbody-content').children().filter(':not(#mdt-settings-area)').hide();
		$("#dismiss").click(function () {
			$("#activation-notice").hide();
		});
		if($('#server_mode').is(':checked')) {
			$('.mcc_live_content').hide();
			$('.mcc_demo_content').show();
		} else {
			$('.mcc_live_content').show();
			$('.mcc_demo_content').hide();
		}
		
		
		var activated_msg_load = $('.activate_msg_live_load').html();
		var deactivated_msg_load = $('.deactivate_msg_live_load').html();
		
		var banner_guid = $("#gidbox").val();
		if($('#server_mode').is(':checked')) {	
			$('#server_mode').attr('checked','checked');
			var server_mode = 'true';
		} else{
			$('#server_mode').removeAttr('checked');
			var server_mode = 'false';
		}
		if($('#slider-status').is(':checked')) {	
			$('#slider-status').attr('checked','checked');
			var banner_status = 'true';
		} else{
			$('#slider-status').removeAttr('checked');
			var banner_status = 'false';
		}
		if( server_mode == 'true' ) {
			if( banner_status == "true" ){    
				$("#activation-notice").hide();	
				$("#msg-notice").html('');
			} else {
				$("#msg-notice").html( $('.deactivate_msg_demo_load').html() );
				$("#activation-notice").show();	
			}
		} else {
			$("#server_mode").removeAttr("checked");
			if( banner_status == 'true' && banner_guid != "" ){  
				$("#msg-notice").html( $('.activate_msg_live_load').html() );
				$("#activation-notice").show();	
			} else if(banner_status == 'true' && banner_guid == ""){
				$("#msg-notice").html('');
				$("#activation-notice").hide();						
			} else{
				$("#activation-notice").hide();						
			}
		}
		
				
		$("#cookie").submit(function (event) {
			event.preventDefault();
            $("#activation-notice").hide();
			if($('#slider-status').is(':checked')) {	
				var save_banner_slider = 'true';
			} else {
				var save_banner_slider = 'false';
			}
			var save_gid = $("#cookie").find('input[name="gid"]').val();
			var not_validated_msg = $('.not_validated_msg').html();
			var not_exists_msg = $('.not_exists_msg').html();
			var save_activated_msg = $('.activate_msg').html();
			var save_deactivated_msg = $('.deactivate_msg').html();
			$.ajax({
				url: 'admin-ajax.php', 
				data:   {
					 'action': 'save_banner_cookie_settings',
					 'banner_guid': save_gid,     // We pass php values differently!
					 'slider_status' : save_banner_slider,
				},
				type: 'GET',
				success:function(response) {
					if(response == 'not_validate_guid') {
						$('.not_exists_msg').addClass('mcc_hide');
						$('.not_exists_msg').removeClass('mcc_show');
						$('.not_validated_msg').addClass('mcc_show');
						$('.not_validated_msg').removeClass('mcc_hide');
						$('#gidbox').addClass('error-input');
						
					} else if(response == 'not_exists_guid') {
						$('.not_validated_msg').addClass('mcc_hide');
						$('.not_validated_msg').removeClass('mcc_show');
						$('.not_exists_msg').addClass('mcc_show');
						$('.not_exists_msg').removeClass('mcc_hide');
						$('#gidbox').addClass('error-input');
					} else {
						$('.not_exists_msg').addClass('mcc_hide');
						$('.not_exists_msg').removeClass('mcc_show');
						$('.not_validated_msg').addClass('mcc_hide');
						$('.not_validated_msg').removeClass('mcc_show');
						$('#gidbox').removeClass('error-input');						
						$("#activation-notice").show();
						if(save_banner_slider == 'true'){
							$("#msg-notice").html(save_activated_msg);
						}
						else if(save_banner_slider == 'false'){
							$("#msg-notice").html(save_deactivated_msg);
						}
						else{
							$("#msg-notice").html('something went wrong');
						}
					}
				}
			});
		});
		
		
		$("#server_mode").on('click', function () {
			$("#activation-notice").hide();
			if($('#slider-status').is(':checked')) {	
				var chk_banner_slider = 'true';
			} else {
				var chk_banner_slider = 'false';
			}
			if($('#server_mode').is(':checked')) {	
				var server_mode = 'true';
				$('.mcc_live_content').hide();
				$('.mcc_demo_content').show();
				var activated_msg = $('.activate_msg_demo_opt').html();
				var deactivated_msg = $('.deactivate_msg_demo_opt').html();
			} else {
				var server_mode = 'false';
				$('.mcc_live_content').show();
				$('.mcc_demo_content').hide();
				var activated_msg = $('.activate_msg_live_opt').html();
				var deactivated_msg = $('.deactivate_msg_live_opt').html();
			}
			$.ajax({
                url: 'admin-ajax.php', 
                data:   {
                    'action': 'save_server_mode',
                    'mcc_server_mode': server_mode,     // We pass php values differently!
                                               
                },
                type: 'GET',
                
                // We can also pass the url value separately from ajaxurl for front end AJAX implementations
                success:function(response) {
					if(chk_banner_slider == 'true'){
						if($("#gidbox").val() != '' || server_mode == 'true' ){
							$("#activation-notice").show();
							$("#msg-notice").html(activated_msg);
						}
					} else{
						if($("#gidbox").val() != '' || server_mode == 'true'){
							$("#activation-notice").show();
							$("#msg-notice").html(deactivated_msg);
						}
					}
				}
			});
		});	
		
		$("#slider-status").click(function () {
			$("#activation-notice").hide();
			if($('#slider-status').is(':checked')) {	
				var chk_banner_slider = 'true';
			} else {
				var chk_banner_slider = 'false';
			}if($('#server_mode').is(':checked')) {	
				var server_mode_val = 'true';
				var activated_msg = $('.activate_msg_demo_opt').html();
				var deactivated_msg = $('.deactivate_msg_demo_opt').html();
			} else {
				var server_mode_val = 'false';
				var activated_msg = $('.activate_msg_live_opt').html();
				var deactivated_msg = $('.deactivate_msg_live_opt').html();
			}
			$.ajax({
				url: 'admin-ajax.php', 
				data:   {
							'action': 'banner_slider_status',
							'banner_slider_status_val': chk_banner_slider,     
							'server_mode' : server_mode_val,
						},
				type: 'GET',				
				success:function(response) {
				if(chk_banner_slider == 'true'){
					if($("#gidbox").val() != '' || server_mode_val == 'true'){
						$("#activation-notice").show();
						$("#msg-notice").html(activated_msg);
					}
					$("#slider-status").prop("checked", true);
				} else{
					if($("#gidbox").val() != '' || server_mode_val == 'true'){
						$("#activation-notice").show();
						$("#msg-notice").html(deactivated_msg);
					}
					$("#slider-status").removeAttr('checked');
					}
				},
				error:function(){
					$("#msg-notice").html('something went wrong on wordpress..');
				}
			});
        });

		// Initialize sub-option visibility on page load
		if ($('#mdt_google_consent_enabled').is(':checked')) {
			$('.mdt-google-consent-sub-option').show();
		} else {
			$('.mdt-google-consent-sub-option').hide();
		}

		// Auto-save WP Consent API setting on toggle
		$('#mdt_wp_consent_api_enabled').click(function() {
			var value = $(this).is(':checked') ? 'true' : 'false';
			$.ajax({
				url: 'admin-ajax.php',
				data: {
					'action': 'save_wp_consent_api',
					'value': value
				},
				type: 'GET',
				success: function(response) {
					console.log('WP Consent API setting saved:', value);
				},
				error: function() {
					console.error('Failed to save WP Consent API setting');
				}
			});
		});

		// Auto-save Google Consent Mode setting on toggle
		$('#mdt_google_consent_enabled').click(function() {
			var value = $(this).is(':checked') ? 'true' : 'false';

			// Show/hide sub-option
			if ($(this).is(':checked')) {
				$('.mdt-google-consent-sub-option').slideDown();
			} else {
				$('.mdt-google-consent-sub-option').slideUp();
				$('#mdt_google_tags_before_consent').prop('checked', false);
				// Also save the sub-option when parent is disabled
				saveGoogleTagsBeforeSetting('false');
			}

			// Save via AJAX
			$.ajax({
				url: 'admin-ajax.php',
				data: {
					'action': 'save_google_consent_mode',
					'value': value
				},
				type: 'GET',
				success: function(response) {
					console.log('Google Consent Mode setting saved:', value);
				},
				error: function() {
					console.error('Failed to save Google Consent Mode setting');
				}
			});
		});

		// Auto-save Google Tags Before Consent setting on toggle
		$('#mdt_google_tags_before_consent').click(function() {
			var value = $(this).is(':checked') ? 'true' : 'false';
			saveGoogleTagsBeforeSetting(value);
		});

		// Helper function to save Google Tags Before setting
		function saveGoogleTagsBeforeSetting(value) {
			$.ajax({
				url: 'admin-ajax.php',
				data: {
					'action': 'save_google_tags_before',
					'value': value
				},
				type: 'GET',
				success: function(response) {
					console.log('Google Tags Before Consent setting saved:', value);
				},
				error: function() {
					console.error('Failed to save Google Tags Before Consent setting');
				}
			});
		}
    });
	
})( jQuery );