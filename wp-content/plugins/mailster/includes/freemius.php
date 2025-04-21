<?php

function mailster_freemius() {

	global $mailster_freemius;

	if ( ! isset( $mailster_freemius ) ) {
		// Include Freemius SDK.

		require_once MAILSTER_DIR . 'classes/license.class.php';
		$mailster_freemius = new MailsterLicense();
		if ( get_option( 'mailster_freemius' ) ) {
			$mailster_freemius->sdk();
		}
	}

	return $mailster_freemius;
}


mailster_freemius()->add_filter( 'plugin_icon', 'mailster_freemius_custom_icon' );
function mailster_freemius_custom_icon() {
	return MAILSTER_DIR . 'assets/img/opt-in.png';
}

add_action( 'load-toplevel_page_mailster', 'mailster_freemius_load_page' );
function mailster_freemius_load_page() {
	$suffix = SCRIPT_DEBUG ? '' : '.min';
	wp_enqueue_style( 'freemius-style', MAILSTER_URI . 'assets/css/freemius-style' . $suffix . '.css', array(), MAILSTER_VERSION );
}

mailster_freemius()->add_action( 'after_account_connection', 'mailster_freemius_install' );
function mailster_freemius_install() {
	mailster()->install();
}

mailster_freemius()->add_action( 'after_uninstall', 'mailster_on_uninstall' );


mailster_freemius()->add_action( 'hide_account_tabs', '__return_true' );
mailster_freemius()->add_action( 'hide_freemius_powered_by', '__return_true' );




mailster_freemius()->add_filter( 'license_key', 'mailster_legacy_license_key' );
function mailster_legacy_license_key( $key ) {

	$key = trim( $key );

	// Handle an Envato License
	if ( preg_match( '/[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}/', $key ) ) {
		$is_marketing_allowed = null;
		$email                = null;

		if ( isset( $_POST['is_marketing_allowed'] ) ) {
			$is_marketing_allowed = $_POST['is_marketing_allowed'] === 'true';
		}

		if ( isset( $_POST['fs_email'] ) ) {
			$email = $_POST['fs_email'];
		}

		$response = mailster( 'convert' )->convert( $email, $key, $is_marketing_allowed );

		if ( is_wp_error( $response ) ) {
			set_transient( 'mailster_last_legacy_key_error', $response, 10 );
		} else {
			$key = $response->data->secret_key;
		}
	}

	return $key;
}


mailster_freemius()->add_action( 'connect/after_license_input', 'mailster_add_link_for_envato' );
function mailster_add_link_for_envato() {

	$is_envato = get_option( 'mailster_envato' );

	$email = get_option( 'mailster_email' );
	if ( ! $email ) {
		$user  = wp_get_current_user();
		$email = $user->user_email;
	}

	?>
	<script>
		jQuery && jQuery(document).ready(function ($) {
			var is_envato = <?php echo $is_envato ? 'true' : 'false'; ?>;
			$('#fs_license_key').on('change', function(){
				if (is_envato || this.value.match(/[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}/)) {
					$('.is-envato').show();
					$('.show-license-resend-modal').hide();
				} else {
					$('.is-envato').hide();
					$('.show-license-resend-modal').show();
				}
			}).trigger('change');
			is_envato && $('#fs_license_key').attr('placeholder', '<?php esc_html_e( 'Envato Purchase Code', 'mailster' ); ?>' )
			$('#fs_email').on('change', function(){
				$.ajaxSetup({data:{fs_email:$(this).val()}});
			}).trigger('change');

		});
	</script>
	<style>.is-envato{display: none;}#fs_connect .fs-license-key-container{width: 330px}</style>
	<div class="fs-license-key-container is-envato">
		<span><?php printf( esc_html__( 'Please enter the email address for your %s', 'mailster' ), '<a href="' . mailster_url( 'https://mailster.co/account/' ) . '" target="_blank">' . esc_html__( 'Freemius Account', 'mailster' ) . '</a>' ); ?></span>
		<input id="fs_email" name="fs_email" type="email" required placeholder="<?php esc_attr_e( 'Email address', 'mailster' ); ?>" value="<?php echo esc_attr( $email ); ?>">
		<span>(<?php esc_html_e( 'will be used if no account is assigned to your license', 'mailster' ); ?>)</span>
	</div>
	<div class="fs-license-key-container is-envato">
		<a href="<?php echo mailster_url( 'https://kb.mailster.co/where-is-my-purchasecode/' ); ?>" target="_blank"><?php esc_html_e( "Can't find your license key?", 'mailster' ); ?></a>
		<?php if ( get_option( 'mailster_setup' ) ) : ?>
		<a class="alignright" href="<?php echo esc_url( add_query_arg( 'mailster_use_freemius', 0 ) ); ?>"><?php esc_html_e( 'Back to Migration', 'mailster' ); ?></a>
		<?php endif; ?>
	</div>
	<?php
}


mailster_freemius()->add_filter( 'permission_list', 'mailster_update_permission' );
function mailster_update_permission( $permissions ) {

	$permissions[] = array(
		'id'         => 'helpscout',
		'icon-class' => 'dashicons dashicons-sos',
		'tooltip'    => esc_html__( 'If you agree third-party scripts are loaded to provide you with help.', 'mailster' ),
		'label'      => 'Help Scout (' . esc_html__( 'optional', 'mailster' ) . ')',
		'desc'       => esc_html__( 'Loading Help Scout\'s beacon for easy support access', 'mailster' ),
		'optional'   => true,
		'priority'   => 20,
	);

	$list = wp_list_pluck( $permissions, 'id' );
	if ( $key = array_search( 'extensions', $list ) ) {
		$permissions[ $key ]['default'] = true;
	}

	return $permissions;
}


mailster_freemius()->add_filter( 'opt_in_error_message', 'mailster_freemius_opt_in_error_message' );
function mailster_freemius_opt_in_error_message( $error ) {

	$last_error = get_transient( 'mailster_last_legacy_key_error' );
	if ( $last_error ) {
		$error = $last_error->get_error_message();
		delete_transient( 'mailster_last_legacy_key_error' );
	}
	return $error;
}

// change length of licenses keys to accept the one from Envato 36 but allow some whitespace
mailster_freemius()->add_filter( 'license_key_maxlength', 'mailster_license_key_maxlength' );
function mailster_license_key_maxlength( $length ) {
	return 40;
}


mailster_freemius()->add_filter( 'checkout_url', 'mailster_freemius_checkout_url' );
function mailster_freemius_checkout_url( $url ) {

	if ( empty( $url ) ) {
		return $url;
	}

	if ( mailster_freemius()->is_whitelabeled() ) {
		return mailster_url( 'https://mailster.co/go/buy' );
	}

	return add_query_arg(
		array(
			'page'          => 'mailster-pricing',
			'checkout'      => 'true',
			'plan_id'       => 22867,
			'billing_cycle' => 'annual',
			'post_type'     => 'newsletter',
		),
		admin_url( 'edit.php' )
	);
}

mailster_freemius()->add_filter( 'pricing_url', 'mailster_freemius_pricing_url' );
function mailster_freemius_pricing_url( $url ) {

	if ( empty( $url ) ) {
		return $url;
	}

	if ( mailster_freemius()->is_whitelabeled() ) {
		return mailster_url( 'https://mailster.co/go/buy' );
	}

	$url = add_query_arg( array( 'id' => 'mailster-plugin' ), $url );

	$utms = preg_grep( '/^utm_/', array_keys( $_GET ) );
	$utms = array_intersect_key( $_GET, array_flip( $utms ) );
	if ( $utms ) {
		$url = add_query_arg( $utms, $url );
	}

	return $url;
}

mailster_freemius()->add_action( 'after_license_change', 'mailster_freemius_after_license_change_handler', 10, 2 );
function mailster_freemius_after_license_change_handler( $plan_change_desc, FS_Plugin_Plan $plan ) {

	if ( 'changed' !== $plan_change_desc ) {
		return;
	}

	mailster_remove_notice( 'mailster-workflow-limit-reached' );
	mailster_remove_notice( 'mailster-notice-legacy_promo' );

	return;
}




function mailster_freemius_upgrade_license( $args = array(), $label = null, $class = 'button button-primary' ) {

	$license = mailster_freemius()->_get_license();

	$args = wp_parse_args(
		$args,
		array( 'license_key' => $license->secret_key )
	);
	return mailster_freemius_checkout_button( $args, $label, $class );
}




function mailster_freemius_checkout_button( $args = array(), $label = null, $class = 'button button-primary' ) {

	$public_key = mailster_freemius()->get_public_key();
	$license    = mailster_freemius()->_get_license();

	$is_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;

	// TODO: check if coupon code works
	$popup = false;

	if ( ! $label ) {
		$label = __( 'Buy License', 'mailster' );
	}

	$default = array(
		'plugin_id'  => $license->plugin_id,
		'public_key' => $public_key,
	);

	$args = wp_parse_args( $args, $default );

	$args = apply_filters( 'mailster_freemius_args', $args );

	$button_id = 'FS-buy-button-' . uniqid();

	$url_args = array_filter(
		$args,
		function ( $key ) {
			return ! in_array( $key, array( 'public_key', 'plugin_id', 'sandbox', 'dl_endpoint', 'license_key' ) );
		},
		ARRAY_FILTER_USE_KEY
	);

	$return = ' <a href="' . add_query_arg( $url_args, mailster_freemius()->checkout_url() ) . '" class="' . esc_attr( $class ) . '" id="' . esc_attr( $button_id ) . '">' . esc_html( $label ) . '</a>';

	if ( $popup ) {

		wp_enqueue_script( 'freemius-button-checkout', 'https://checkout.freemius.com/js/v1/', array(), 'v1', true );
		wp_add_inline_script(
			'freemius-button-checkout',
			'document.getElementById("' . esc_attr( $button_id ) . '").addEventListener("click", (e) => {
		e.preventDefault(); new FS.Checkout(' . json_encode( $args ) . ').open({
			track: function(event, data){console.warn(event, data);},
			purchaseCompleted: function(data){console.warn("purchaseCompleted", data);},
			cancel: function(data){console.warn("cancel", data);},
			success: function(data){console.warn("success", data);window.location = location.href + (location.href.includes("?") ? "&refresh_license=1" : "?refresh_license=1")},
		});});'
		);
		// append the script on ajax requests
		if ( $is_ajax ) {
			ob_start();
			wp_print_scripts( 'freemius-button-checkout' );
			$return .= ob_get_clean();

		}
	}

	return $return;
}
