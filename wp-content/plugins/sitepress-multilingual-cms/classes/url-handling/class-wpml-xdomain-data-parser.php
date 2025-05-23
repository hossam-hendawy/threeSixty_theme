<?php

use WPML\API\Sanitize;

class WPML_XDomain_Data_Parser {

	const SCRIPT_HANDLER = 'wpml-xdomain-data';

	/**
	 * @var array $settings
	 */
	private $settings;

	private $encryptor;


	/**
	 * WPML_XDomain_Data_Parser constructor.
	 *
	 * @param array<string,mixed> $settings
	 * @param \WPML_Data_Encryptor $encryptor
	 */
	public function __construct( &$settings, $encryptor ) {
		$this->settings  = &$settings;
		$this->encryptor = $encryptor;
	}

	public function init_hooks() {
		if ( ! isset( $this->settings['xdomain_data'] ) || $this->settings['xdomain_data'] != WPML_XDOMAIN_DATA_OFF ) {
			add_action( 'init', array( $this, 'init' ) );
			add_filter( 'wpml_get_cross_domain_language_data', array( $this, 'get_xdomain_data' ) );
			add_filter( 'wpml_get_cross_domain_language_data', array( $this, 'get_xdomain_data' ) );
		}
	}

	public function init() {
		add_action( 'wp_ajax_switching_language', array( $this, 'send_xdomain_language_data' ) );
		add_action( 'wp_ajax_nopriv_switching_language', array( $this, 'send_xdomain_language_data' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts_action' ), 100 );
	}

	public function register_scripts_action() {
		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {

			$ls_parameters = WPML_Language_Switcher::parameters();

			$js_xdomain_data = array(
				'css_selector' => $ls_parameters['css_prefix'] . 'item',
				'ajax_url'     => admin_url( 'admin-ajax.php' ),
				'current_lang' => apply_filters( 'wpml_current_language', '' ),
				'_nonce'       => wp_create_nonce( 'wp_ajax_switching_language' ),
			);

			wp_enqueue_script( self::SCRIPT_HANDLER, ICL_PLUGIN_URL . '/res/js/xdomain-data.js', array(), ICL_SITEPRESS_SCRIPT_VERSION );
			wp_script_add_data( self::SCRIPT_HANDLER, 'strategy', 'defer' );
			wp_localize_script( self::SCRIPT_HANDLER, 'wpml_xdomain_data', $js_xdomain_data );
		}
	}

	public function set_up_xdomain_language_data() {
		$ret = array();

		$data = apply_filters( 'WPML_cross_domain_language_data', array() );
		$data = apply_filters( 'wpml_cross_domain_language_data', $data );

		if ( ! empty( $data ) ) {
			$encoded_data        = json_encode( $data );
			$encoded_data        = $this->encryptor->encrypt( $encoded_data );
			$base64_encoded_data = base64_encode( $encoded_data );
			$ret['xdomain_data'] = urlencode( $base64_encoded_data );

			$ret['method'] = WPML_XDOMAIN_DATA_POST == $this->settings['xdomain_data'] ? 'post' : 'get';

		}

		return $ret;

	}

	public function send_xdomain_language_data() {
		$nonce = isset( $_POST['_nonce'] ) ? sanitize_text_field( $_POST['_nonce'] ) : '';

		if ( ! wp_verify_nonce( $nonce, 'wp_ajax_switching_language' ) ) {
			wp_send_json_error( esc_html__( 'Invalid request!', 'sitepress' ), 400 );
			return;
		}

		$data = $this->set_up_xdomain_language_data();

		wp_send_json_success( $data );

	}

	public function get_xdomain_data() {
		$xdomain_data = array();

		if ( isset( $_GET['xdomain_data'] ) || isset( $_POST['xdomain_data'] ) ) {

			$xdomain_data_request = false;

			if ( WPML_XDOMAIN_DATA_GET == $this->settings['xdomain_data'] ) {
				$xdomain_data_request = Sanitize::stringProp( 'xdomain_data', $_GET );
			} elseif ( WPML_XDOMAIN_DATA_POST == $this->settings['xdomain_data'] ) {
				$xdomain_data_request = urldecode( (string) Sanitize::stringProp( 'xdomain_data', $_POST ) );
			}

			if ( $xdomain_data_request ) {
				$data         = base64_decode( $xdomain_data_request );
				$data         = $this->encryptor->decrypt( $data );
				$xdomain_data = (array) json_decode( $data, true );
			}
		}
		return $xdomain_data;
	}
}
