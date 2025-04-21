<?php

class MailsterUpdate {


	public function __construct() {

		add_filter( 'upgrader_pre_download', array( &$this, 'upgrader_pre_download' ), 10, 4 );
		add_action( 'after_plugin_row_' . MAILSTER_SLUG, array( &$this, 'add_license_info' ), 10, 3 );
		add_filter( 'upgrader_package_options', array( &$this, 'upgrader_package_options' ) );

		add_action( 'install_plugins_pre_plugin-information', array( &$this, 'add_css_for_information_screen' ), 1 );

		add_action( 'plugins_api_result', array( &$this, 'plugins_api_result' ), 10, 3 );

		add_action( 'plugins_loaded', array( &$this, 'init' ) );
	}


	public function init() {

		if ( get_option( 'mailster_freemius' ) ) {
			return;
		}

		if ( ! class_exists( 'UpdateCenterPlugin' ) ) {
			require_once MAILSTER_DIR . 'classes/UpdateCenterPlugin.php';
		}

		UpdateCenterPlugin::add(
			array(
				'licensecode' => mailster()->get_license(),
				'remote_url'  => apply_filters( 'mailster_updatecenter_endpoint', 'https://update.mailster.co/' ),
				'plugin'      => MAILSTER_SLUG,
				'slug'        => 'mailster',
			)
		);

		if ( isset( $_GET['mailster_allow_usage_tracking'] ) ) {
			if ( wp_verify_nonce( $_GET['_wpnonce'], 'mailster_allow_usage_tracking' ) ) {
				$track = (bool) $_GET['mailster_allow_usage_tracking'];
				mailster_update_option( 'usage_tracking', $track );
				if ( ! $track ) {
					mailster_update_option( 'ask_usage_tracking', false );
					mailster_notice( esc_html__( 'Thanks, we\'ll respect your opinion. You can always opt in anytime on the advanced tab in the settings!', 'mailster' ), 'info', true );
				}
			}
		}
	}


	public function ask_for_auto_update() {

		if ( ! $this->is_auto_update() && mailster()->is_verified() ) {

			$link = sprintf( '<a href="%s" class="button button-primary">%s</a>', $this->get_auto_update_url(), esc_html__( 'Enable Auto Updates for Mailster', 'mailster' ) );

			mailster_notice( '<p><strong>' . esc_html__( 'Automatic Updates are not enabled for Mailster!', 'mailster' ) . '</strong></p>' . $link, 'info', 20, 'ask_for_auto_update' );

		}
	}

	public function get_auto_update_url() {
		$query_args = array(
			'action' => 'enable-auto-update',
			'plugin' => MAILSTER_SLUG,
			's'      => MAILSTER_SLUG,
		);
		$url        = add_query_arg( $query_args, 'plugins.php' );

		return wp_nonce_url( $url, 'updates' );
	}

	public function is_auto_update() {

		if ( ! function_exists( 'wp_is_auto_update_enabled_for_type' ) ) {
			return false;
		}

		$auto_updates = (array) get_site_option( 'auto_update_plugins', array() );

		return in_array( MAILSTER_SLUG, $auto_updates );
	}


	/**
	 *
	 *
	 * @param unknown $reply
	 * @param unknown $package
	 * @param unknown $upgrader
	 * @param unknown $hook_extra
	 * @return unknown
	 */
	public function upgrader_pre_download( $reply, $package, $upgrader, $hook_extra ) {

		if ( ! isset( $hook_extra['plugin'] ) ) {
			return $reply;
		}

		if ( $hook_extra['plugin'] !== MAILSTER_SLUG ) {
			return $reply;
		}

		// using UpdateCenter
		if ( ! get_option( 'mailster_freemius' ) ) {
			return $this->legacy_upgrader_pre_download( $reply, $package, $upgrader, $hook_extra );
		}

		return $reply;
	}


	private function legacy_upgrader_pre_download( $reply, $package, $upgrader, $hook_extra ) {
		$upgrader->strings['mailster_download'] = sprintf( esc_html_x( 'Downloading the latest version of %s', 'Mailster', 'mailster' ), 'Mailster' ) . '...';
		$upgrader->skin->feedback( 'mailster_download' );

		$res = $upgrader->fs_connect( array( WP_CONTENT_DIR ) );
		if ( ! $res ) {
			return new WP_Error( 'fs_unavailable', $upgrader->strings['fs_unavailable'] );
		}

		add_filter( 'http_response', array( &$this, 'alter_update_message' ), 10, 3 );
		$download_file = download_url( $package );
		remove_filter( 'http_response', array( &$this, 'alter_update_message' ), 10, 3 );

		if ( is_wp_error( $download_file ) ) {

			$short_msg = isset( $_SERVER['HTTP_REFERER'] ) ? preg_match( '#page=envato-market#', $_SERVER['HTTP_REFERER'] ) : false;

			$upgrader->strings['mailster_download_error'] = esc_html__( 'Not able to download Mailster!', 'mailster' );
			$upgrader->skin->feedback( 'mailster_download_error' );

			$code = $download_file->get_error_message();

			$error_msg = mailster()->get_update_error( $code, $short_msg, esc_html__( 'An error occurred while updating Mailster!', 'mailster' ) );

			switch ( $code ) {

				case 680:
					$error_msg = $error_msg . ' <a href="' . mailster_url( 'https://mailster.co/go/buy' ) . '" target="_blank" rel="noopener"><strong>' . sprintf( esc_html__( 'Buy an additional license for %s.', 'mailster' ), ( mailster_is_local() ? esc_html__( 'your new site', 'mailster' ) : $_SERVER['HTTP_HOST'] ) . '</strong></a>' );

				case 679: // No Licensecode provided
				case 678:
					add_filter( 'update_plugin_complete_actions', array( &$this, 'add_update_action_link' ) );
					add_filter( 'install_plugin_complete_actions', array( &$this, 'add_update_action_link' ) );

					break;

				case 500: // Internal Server Error
				case 503: // Service Unavailable
					$error = esc_html__( 'Authentication servers are currently down. Please try again later!', 'mailster' );
					break;

				default:
					$error = esc_html__( 'An error occurred while updating Mailster!', 'mailster' );
					if ( $error_msg ) {
						$error .= '<br>' . $error_msg;
					}
					break;
			}

			if ( is_a( $upgrader->skin, 'Bulk_Plugin_Upgrader_Skin' ) ) {

				return new WP_Error( 'mailster_download_error', $error_msg );

			} else {

				$upgrader->strings['mailster_error'] = '<div class="error inline"><p><strong>' . $error_msg . '</strong></p></div>';
				$upgrader->skin->feedback( 'mailster_error' );
				$upgrader->skin->result = new WP_Error( 'mailster_download_error', $error_msg );
				return new WP_Error( 'mailster_download_error', '' );

			}
		}

		return $download_file;
	}


	/**
	 *
	 *
	 * @param unknown $response
	 * @param unknown $r
	 * @param unknown $url
	 * @return unknown
	 */
	public function alter_update_message( $response, $r, $url ) {

		$code = wp_remote_retrieve_response_code( $response );

		$response['response']['message'] = $code;

		return $response;
	}


	public function add_update_action_link( $actions ) {

		$actions['mailster_get_license'] = '<a href="' . mailster_url( 'https://mailster.co/go/buy' ) . '">' . esc_html__( 'Buy a new Mailster License', 'mailster' ) . '</a>';

		return $actions;
	}


	public function plugins_api_result( $res, $action, $args ) {

		if ( is_wp_error( $res ) || ! $res ) {
			return $res;
		}
		if ( ! isset( $res->slug ) || $res->slug !== 'mailster' ) {
			return $res;
		}

		foreach ( $res->sections as $i => $section ) {
			$res->sections[ $i ] = mailster_links_add_args( $section );
		}

		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$plugin_data = get_plugin_data( MAILSTER_DIR . basename( MAILSTER_SLUG ) );

		$res->homepage = mailster_url( isset( $res->homepage ) ? $res->homepage : 'https://mailster.co' );
		$res->author   = strip_tags( $res->author );

		// remove the rating from the repo version
		$res->rating      = 86;
		$res->ratings     = array();
		$res->num_ratings = 1825;

		$res->name    = $plugin_data['Name'];
		$res->version = $plugin_data['Version'];

		$res->banners = array(
			'low'  => 'https://static.mailster.co/images/plugin-header-772x250.png',
			'high' => 'https://static.mailster.co/images/plugin-header-1544x500.png',
		);

		return $res;
	}


	public function add_license_info( $plugin_file, $plugin_data, $status ) {

		if ( mailster()->is_outdated() ) {
			echo '<tr class="plugin-update-tr active" id="mailster-update" data-slug="mailster" data-plugin="' . esc_attr( MAILSTER_SLUG ) . '"><td colspan="4" class="plugin-update colspanchange"><div class="error notice inline notice-error notice-alt"><p><strong>' . sprintf( esc_html__( 'Hey! Looks like you have an outdated version of Mailster! It\'s recommended to keep the plugin up to date for security reasons and new features. Check the %s for the most recent version.', 'mailster' ), '<a href="' . mailster_url( 'https://mailster.co/changelog' ) . '" class="external">' . esc_html__( 'changelog page', 'mailster' ) . '</a>' ) . '</strong></p></td></tr>';

		}
	}

	public function add_css_for_information_screen() {

		// remove ugly h2 headline in plugin info screen for all mailster plugins
		if ( isset( $_GET['plugin'] ) && false !== strpos( $_GET['plugin'], 'mailster' ) ) {
			wp_add_inline_style( 'common', '#plugin-information #plugin-information-title h2{display: none;}' );
		}
	}

	public function upgrader_package_options( $options ) {
		if ( isset( $options['package'] ) && preg_match( '/^mailster-([0-9.]+)-dev\./', basename( $options['package'] ) ) ) {
			$options['clear_destination'] = true;
		}

		return $options;
	}
}
