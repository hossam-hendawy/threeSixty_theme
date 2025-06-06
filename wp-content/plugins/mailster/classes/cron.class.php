<?php

class MailsterCron {

	private $pid;

	public function __construct() {

		add_filter( 'cron_schedules', array( &$this, 'filter_cron_schedules' ) );
		add_action( 'mailster_cron', array( &$this, 'hourly_cronjob' ) );
		add_action( 'mailster_cron_worker', array( &$this, 'handler' ), -1 );

		add_action( 'mailster_campaign_pause', array( &$this, 'update' ) );
		add_action( 'mailster_campaign_start', array( &$this, 'update' ) );
		add_action( 'mailster_campaign_duplicate', array( &$this, 'update' ) );

		if ( ! wp_next_scheduled( 'mailster_cron' ) ) {
			$this->update( true );
		}

		add_action( 'wp_ajax_mailster_cron', array( &$this, 'cron_worker' ) );
		add_action( 'wp_ajax_nopriv_mailster_cron', array( &$this, 'cron_worker' ) );
		add_action( 'template_redirect', array( &$this, 'template_redirect' ), 1 );
	}






	/**
	 * Checks for new newsletter in the queue to start new cronjob
	 */
	public function hourly_cronjob() {

		// check for bounced emails
		do_action( 'mailster_check_bounces' );

		// send confirmations again
		do_action( 'mailster_resend_confirmations' );

		$this->update();
	}

	/**
	 *
	 *
	 * @return unknown
	 */
	public function handler() {

		if ( defined( 'MAILSTER_DOING_CRON' ) || defined( 'DOING_AJAX' ) || defined( 'DOING_AUTOSAVE' ) || defined( 'WP_INSTALLING' ) || defined( 'MAILSTER_DO_UPDATE' ) ) {
			return false;
		}

		define( 'MAILSTER_DOING_CRON', microtime( true ) );

		register_shutdown_function( array( &$this, 'shutdown_function' ) );
	}


	public function shutdown_function() {

		if ( ! defined( 'MAILSTER_DOING_CRON' ) ) {
			return;
		}

		$error = error_get_last();

		if ( ! is_null( $error ) && $error['type'] == 1 && 0 === strpos( $error['file'], MAILSTER_DIR ) ) {

			$msg = sprintf( esc_html__( 'It looks like your last cronjob hasn\'t been finished! Increase the %1$s, add %2$s to your wp-config.php or reduce the %3$s in the settings.', 'mailster' ), "'max_execution_time'", '<code>define("WP_MEMORY_LIMIT", "256M");</code>', '<a href="' . add_query_arg( array( 'mailster_remove_notice' => 'cron_unfinished' ), admin_url( 'edit.php?post_type=newsletter&page=mailster_settings#delivery' ) ) . '">' . esc_html__( 'Number of mails sent', 'mailster' ) . '</a>' );

			$msg .= '<pre><code>' . esc_html( $error['message'] ) . '</code></pre>';

			mailster_notice( $msg, 'error', false, 'cron_unfinished' );

		} else {

			mailster_remove_notice( 'cron_unfinished' );

		}
	}


	/**
	 *
	 * Run cron in a given interval
	 *
	 * @param unknown $hourly_only (optional)
	 * @return unknown
	 */
	public function run( $action, $interval = 0 ) {

		if ( is_string( $interval ) ) {
			$schedules = wp_get_schedules();
			if ( isset( $schedules[ $interval ] ) ) {
				$interval = $schedules[ $interval ]['interval'];
			}
		}

		if ( ! $interval || false === get_transient( 'mailster_cron_' . $action ) ) {

			do_action( $action );

			if ( $interval ) {
				set_transient( 'mailster_cron_' . $action, true, $interval );
			}
		}
	}


	/**
	 *
	 *
	 * @param unknown $hourly_only (optional)
	 * @return unknown
	 */
	public function update( $hourly_only = false ) {

		// not installed yet
		if ( ! get_option( 'mailster' ) ) {
			return;
		}

		if ( ! wp_next_scheduled( 'mailster_cron' ) ) {

			// main schedule always 5 minutes before full hour
			wp_schedule_event( strtotime( 'midnight' ) - 300, 'hourly', 'mailster_cron' );
			// stop here cause mailster_cron triggers the worker if required
			return true;
		} elseif ( $hourly_only ) {
			return false;
		}

		// remove the WordPress cron if "normal" cron is used
		if ( mailster_option( 'cron_service' ) != 'wp_cron' ) {
			$this->unschedule();
			return false;
		}

		$this->schedule();

		return false;
	}


	public function schedule( $unschedule = false ) {

		// not installed yet
		if ( ! get_option( 'mailster' ) ) {
			return;
		}

		if ( $unschedule ) {
			$this->unschedule();
		}

		// add worker only once
		if ( ! wp_next_scheduled( 'mailster_cron_autoresponder' ) ) {
			wp_schedule_event( floor( time() / 300 ) * 300 - 30, 'mailster_cron_interval', 'mailster_cron_autoresponder' );
		}
		if ( ! wp_next_scheduled( 'mailster_cron_bounce' ) ) {
			wp_schedule_event( floor( time() / 300 ) * 300 - 30, 'mailster_cron_interval', 'mailster_cron_bounce' );
		}
		if ( ! wp_next_scheduled( 'mailster_cron_workflow' ) ) {
			wp_schedule_event( floor( time() / 300 ) * 300, 'mailster_cron_interval', 'mailster_cron_workflow' );
		}
		if ( ! wp_next_scheduled( 'mailster_cron_worker' ) ) {
			wp_schedule_event( floor( time() / 300 ) * 300, 'mailster_cron_interval', 'mailster_cron_worker' );
		}
		if ( ! wp_next_scheduled( 'mailster_cron_cleanup' ) ) {
			wp_schedule_event( strtotime( 'midnight' ) - 180, 'daily', 'mailster_cron_cleanup' );
		}
	}


	public function unschedule() {
		wp_clear_scheduled_hook( 'mailster_cron_autoresponder' );
		wp_clear_scheduled_hook( 'mailster_cron_bounce' );
		wp_clear_scheduled_hook( 'mailster_cron_workflow' );
		wp_clear_scheduled_hook( 'mailster_cron_worker' );
		wp_clear_scheduled_hook( 'mailster_cron_cleanup' );
		wp_clear_scheduled_hook( 'mailster_cron' );
	}


	/**
	 * add custom time to cron
	 *
	 * @param unknown $cron_schedules
	 * @return unknown
	 */
	public function filter_cron_schedules( $cron_schedules ) {

		$cron_schedules['mailster_cron_interval'] = array(
			'interval' => mailster_option( 'interval', 5 ) * 60, // seconds
			'display'  => 'Mailster Cronjob Interval',
		);

		return $cron_schedules;
	}


	/**
	 *
	 *
	 * @param unknown $general (optional)
	 */
	public function remove_crons( $general = false ) {
		wp_clear_scheduled_hook( 'mailster_cron_worker' );
		if ( $general ) {
			wp_clear_scheduled_hook( 'mailster_cron' );
		}
	}


	public function check( $strict = false ) {

		global $wpdb;

		$now                     = time();
		$cron_service            = mailster_option( 'cron_service' );
		$db_structure_up_to_date = MAILSTER_DBVERSION == get_option( 'mailster_dbversion' );

		if ( ( ! mailster( 'queue' )->size() && ! $strict ) || ! $db_structure_up_to_date ) :

			mailster_remove_notice( 'check_cron' );

			return true;

		else :

			$interval = mailster_option( 'interval' ) * 60;
			$last_hit = get_option( 'mailster_cron_lasthit' );

			if ( ! $last_hit ) {
				if ( is_array( $last_hit ) ) {
					return new WP_Error( 'cron_error', sprintf( esc_html__( 'Your Cron page hasn\'t get triggered recently. This is required to send campaigns. Please check the %s', 'mailster' ), '<a href="' . admin_url( 'edit.php?post_type=newsletter&page=mailster_settings#cron' ) . '"><strong>' . esc_html__( 'settings page', 'mailster' ) . '</strong></a>.' ) );
				}

				return new WP_Error( 'cron_error', sprintf( esc_html__( 'The Cron Process is not setup correctly. This is required to send campaigns. Please check the %s', 'mailster' ), '<a href="' . admin_url( 'edit.php?post_type=newsletter&page=mailster_settings#cron' ) . '"><strong>' . esc_html__( 'settings page', 'mailster' ) . '</strong></a>.' ) );
			}

			// get real delay...
			$real_delay    = max( $interval, $last_hit['timestamp'] - $last_hit['oldtimestamp'] );
			$current_delay = $now - $last_hit['timestamp'];

			// ..and compare it with the interval (3 times) - also something in the queue
			if ( ( $current_delay > $real_delay * 3 || ! $real_delay && ! $current_delay ) ) :

				$this->update();

				return new WP_Error( 'cron_warning', sprintf( esc_html__( 'Are your campaigns not sending? You may have to check your %1$s', 'mailster' ), '<a href="' . admin_url( 'edit.php?post_type=newsletter&page=mailster_settings#cron' ) . '"><strong>' . esc_html__( 'cron settings', 'mailster' ) . '</strong></a>' . mailster()->beacon( '611bbaabf886c9486f8d9937' ) ) );

			else :

				mailster_remove_notice( 'check_cron' );
				return true;

			endif;

		endif;
	}


	/**
	 *
	 *
	 * @param unknown $key (optional)
	 * @return unknown
	 */
	public function lock( $key = 0 ) {

		if ( mailster_option( 'cron_lock' ) == 'db' ) {

			$this->pid = get_option( 'mailster_cron_lock_' . $key, false );

			if ( $this->pid ) {
				if ( $this->is_locked( $key ) ) {
					return $this->pid;
				} else {
				}
			}

			if ( function_exists( 'getmypid' ) ) {
				$this->pid = getmypid();
			} else {
				$this->pid = uniqid();
			}
			update_option( 'mailster_cron_lock_' . $key, $this->pid, false );
			return true;

		} else {

			$lockfile = MAILSTER_UPLOAD_DIR . '/CRON_' . $key . '.lockfile';

			if ( file_exists( $lockfile ) ) {
				// Is running?
				$this->pid = file_get_contents( $lockfile );
				if ( $this->is_locked( $key ) ) {
					return $this->pid;
				} else {
				}
			}

			if ( function_exists( 'getmypid' ) ) {
				$this->pid = getmypid();
			} else {
				$this->pid = uniqid();
			}
			register_shutdown_function( array( $this, 'unlock' ), $key );
			file_put_contents( $lockfile, $this->pid );
			return true;

		}
	}


	/**
	 *
	 *
	 * @param unknown $key (optional)
	 * @return unknown
	 */
	public function unlock( $key = 0 ) {

		if ( mailster_option( 'cron_lock' ) == 'db' ) {

			update_option( 'mailster_cron_lock_' . $key, false, false );

		} else {
			$lockfile = MAILSTER_UPLOAD_DIR . '/CRON_' . $key . '.lockfile';

			if ( file_exists( $lockfile ) ) {

				unlink( $lockfile );
			}
		}

		return true;
	}


	/**
	 *
	 *
	 * @param unknown $key (optional)
	 * @return unknown
	 */
	public function is_locked( $key = null ) {

		global $wpdb;

		$exec = is_callable( 'shell_exec' ) && false === stripos( ini_get( 'disable_functions' ), 'shell_exec' );

		if ( is_integer( $key ) && $exec ) {
			$pids = explode( PHP_EOL, `ps -e | awk '{print $1}'` );
			if ( in_array( $this->pid, $pids ) || empty( $pids[0] ) ) {
				return true;
			}

			return false;

		} elseif ( ! is_integer( $key ) ) {
			$key = '';
		}

		if ( mailster_option( 'cron_lock' ) == 'db' ) {

			$sql = "SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name LIKE %s AND option_value != ''";
			$res = $wpdb->get_var( $wpdb->prepare( $sql, 'mailster_cron_lock_' . $key . '%' ) );

			return (bool) $res;

		} else {

			$lockfiles = glob( MAILSTER_UPLOAD_DIR . '/CRON_' . $key . '*.lockfile' );

			return ! empty( $lockfiles );

		}
	}



	/**
	 *
	 *
	 * @return unknown
	 */
	public function url( $alternative = false ) {

		if ( ! $alternative ) {

			if ( mailster_option( 'got_url_rewrite' ) ) {
				return apply_filters( 'mailster_cron_url', get_home_url( null, 'mailster/' . mailster_option( 'cron_secret' ) ), $alternative );
			} else {
				return apply_filters(
					'mailster_cron_url',
					add_query_arg(
						array(
							'secret' => mailster_option( 'cron_secret' ),
						),
						MAILSTER_URI . 'cron.php'
					),
					$alternative
				);

			}
		} else {
			return apply_filters(
				'mailster_cron_url',
				add_query_arg(
					array(
						'action' => 'mailster_cron',
						'secret' => mailster_option( 'cron_secret' ),
					),
					admin_url( 'admin-ajax.php' )
				),
				$alternative
			);

		}
	}


	public function path( $arguments = false ) {

		$path = MAILSTER_DIR . 'cron.php';

		if ( $arguments ) {
			$path .= ' ' . mailster_option( 'cron_secret' );
		}

		return $path;
	}


	public function template_redirect() {

		if ( $secret = get_query_var( '_mailster_cron' ) ) {
			$this->cron_page( $secret );
		}

		global $wp;

		// legacy support
		if ( 'wp-content/plugins/mailster/cron.php' === $wp->request ) {

			_doing_it_wrong( 'mailster', 'Using this file is deprecated and no longer supported. Please use the new PHP Cron URL instead. Read more: https://mailster.co/go/phpcron', '4.0.7' );

			$query  = isset( $_SERVER['REQUEST_URI'] ) ? parse_url( $_SERVER['REQUEST_URI'], PHP_URL_QUERY ) : null;
			$secret = null;

			// get the secret from the request
			if ( isset( $_GET['secret'] ) && preg_match( '/^([a-f0-9]{32})$/', $_GET['secret'] ) ) {
				$secret = $_GET['secret'];
				// as HTTP header
			} elseif ( isset( $_SERVER['HTTP_SECRET'] ) && preg_match( '/^([a-f0-9]{32})$/', $_SERVER['HTTP_SECRET'] ) ) {
				$secret = $_SERVER['HTTP_SECRET'];
				// as query string
			} elseif ( $query ) {
				parse_str( $query, $parsed );
				$keys   = array_keys( $parsed );
				$parsed = array_values( preg_grep( '/^([a-f0-9]{32})$/', $keys ) );
				$secret = isset( $parsed[0] ) ? $parsed[0] : null;
			}
			$this->cron_page( $secret );

		}
	}


	public function cron_worker() {

		$secret = isset( $_GET['secret'] ) ? $_GET['secret'] : false;
		$this->cron_page( $secret );
	}

	public function cron_page( $secret ) {

		if ( ! defined( 'MAILSTER_CRON_SECRET' ) ) {
			define( 'MAILSTER_CRON_SECRET', $secret );
		}

		include MAILSTER_DIR . '/includes/cron.php';
		exit();
	}


	/**
	 *
	 *
	 * @param unknown $new
	 */
	public function on_install( $new ) {

		$this->update();

		if ( $new ) {
			add_option( 'mailster_cron_lasthit', false, '', 'no' );
		}
	}


	public function on_deactivate() {

		$this->remove_crons( true );
	}
}
