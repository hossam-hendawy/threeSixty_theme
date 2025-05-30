<?php

class MailsterNotification {

	private $message;
	private $template;
	private $file;
	private $to;
	private $subject;
	private $headline;
	private $preheader;
	private $attachments;
	private $replace;
	private $requeue = true;
	private $debug   = false;

	public $mail = null;

	private static $_instance = null;

	/**
	 *
	 *
	 * @param unknown $template
	 * @param unknown $file
	 * @return unknown
	 */
	public static function get_instance( $template, $file ) {
		if ( ! isset( self::$_instance ) ) {
			self::$_instance = new self();
		}
		self::$_instance->reset();
		self::$_instance->template( $template );
		self::$_instance->file( $file );

		return self::$_instance;
	}


	private function __construct() {

		add_filter( 'mailster_notification_to', array( &$this, 'filter' ), 1, 4 );
		add_filter( 'mailster_notification_subject', array( &$this, 'filter' ), 1, 4 );
		add_filter( 'mailster_notification_file', array( &$this, 'filter' ), 1, 4 );
		add_filter( 'mailster_notification_headline', array( &$this, 'filter' ), 1, 4 );
		add_filter( 'mailster_notification_preheader', array( &$this, 'filter' ), 1, 4 );
		add_filter( 'mailster_notification_replace', array( &$this, 'filter' ), 1, 4 );
		add_filter( 'mailster_notification_attachments', array( &$this, 'filter' ), 1, 4 );
	}


	public function init() {}


	public function reset() {
		$this->message     = null;
		$this->template    = null;
		$this->file        = null;
		$this->to          = null;
		$this->subject     = null;
		$this->headline    = null;
		$this->preheader   = null;
		$this->attachments = array();
		$this->replace     = array(
			'notification' => '',
			'can-spam'     => '',
		);
		$this->requeue     = true;
		$this->debug       = false;
	}


	/**
	 *
	 *
	 * @param unknown $timestamp (optional)
	 * @param unknown $args      (optional)
	 * @return unknown
	 */
	public function add( $timestamp = null, $args = array() ) {

		$now = time();

		$defaults = array(
			'subscriber_id' => null,
			'template'      => $this->template,
		);

		if ( is_null( $timestamp ) ) {
			$timestamp = $now;
		} elseif ( is_array( $timestamp ) ) {
			$args      = $timestamp;
			$timestamp = $now;
		}

		$args = $this->set_options( $args );

		$options = wp_parse_args( $args, $defaults );

		$subscriber_id = (int) $options['subscriber_id'];

		// send now
		if ( $timestamp <= $now ) {
			// sendnow
			$result = $this->send( (int) $subscriber_id, $options );

			// queue it if there was a problem
			if ( is_wp_error( $result ) ) {
				if ( $this->requeue ) {
					$this->add( $now + 360, $options );
				}

				return false;
			}

			return true;

		} else {

			unset( $options['subscriber_id'] );
			if ( ! $subscriber_id ) {
				$options['to'] = $this->to;
			}

			return mailster( 'queue' )->add(
				array(
					'campaign_id'   => 0,
					'subscriber_id' => $subscriber_id,
					'timestamp'     => $timestamp,
					'priority'      => 5,
					'ignore_status' => 1,
					'options'       => $options,
				)
			);
		}
	}


	/**
	 *
	 *
	 * @param unknown $content
	 * @param unknown $template
	 * @param unknown $subscriber
	 * @param unknown $options
	 * @return unknown
	 */
	public function filter( $content, $template, $subscriber, $options ) {

		$filter = str_replace( 'mailster_notification_', '', current_filter() );

		switch ( $template . '_' . $filter ) {

			// new subscriber
			case 'new_subscriber_to':
			case 'new_subscriber_delayed_to':
				return explode( ',', mailster_option( 'subscriber_notification_receviers' ) );

			case 'new_subscriber_subject':
				return esc_html__( 'A new user has subscribed to your newsletter!', 'mailster' );
			case 'new_subscriber_delayed_subject':
				$delay    = mailster_option( 'subscriber_notification_delay' );
				$subjects = array(
					'day'   => esc_html__( 'Your daily summary', 'mailster' ),
					'week'  => esc_html__( 'Your weekly summary', 'mailster' ),
					'month' => esc_html__( 'Your monthly summary', 'mailster' ),
				);
				return isset( $subjects[ $delay ] ) ? $subjects[ $delay ] : esc_html__( 'New subscribers to your newsletter!', 'mailster' );

			case 'new_subscriber_file':
			case 'new_subscriber_delayed_file':
				return mailster_option( 'subscriber_notification_template' );

			case 'new_subscriber_replace':
			case 'new_subscriber_delayed_replace':
				$message = sprintf( esc_html__( 'You are receiving this email because you have enabled notifications for new subscribers %s', 'mailster' ), '<a href="' . admin_url( 'edit.php?post_type=newsletter&page=mailster_settings#subscribers' ) . '">' . esc_html__( 'on your settings page', 'mailster' ) . '</a>' );
				return array(
					'preheader'    => $subscriber ? ( ( $subscriber->fullname ? $subscriber->fullname . ' - ' : '' ) . $subscriber->email ) : '',
					'notification' => $message,
					'can-spam'     => $message,
				);

			// unsubscription
			case 'unsubscribe_to':
			case 'unsubscribe_delayed_to':
				return explode( ',', mailster_option( 'unsubscribe_notification_receviers' ) );

			case 'unsubscribe_subject':
				return esc_html__( 'A user has canceled your newsletter!', 'mailster' );
			case 'unsubscribe_delayed_subject':
				$delay    = mailster_option( 'unsubscribe_notification_delay' );
				$subjects = array(
					'day'   => esc_html__( 'Your daily summary', 'mailster' ),
					'week'  => esc_html__( 'Your weekly summary', 'mailster' ),
					'month' => esc_html__( 'Your monthly summary', 'mailster' ),
				);
				return isset( $subjects[ $delay ] ) ? $subjects[ $delay ] : esc_html__( 'You have new cancellations!', 'mailster' );

			case 'unsubscribe_file':
			case 'unsubscribe_delayed_file':
				return mailster_option( 'unsubscribe_notification_template' );

			case 'unsubscribe_replace':
			case 'unsubscribe_delayed_replace':
				$message = sprintf( esc_html__( 'You are receiving this email because you have enabled notifications for unsubscriptions %s', 'mailster' ), '<a href="' . admin_url( 'edit.php?post_type=newsletter&page=mailster_settings#subscribers' ) . '">' . esc_html__( 'on your settings page', 'mailster' ) . '</a>' );

				return array(
					'preheader'    => $subscriber ? ( ( $subscriber->fullname ? $subscriber->fullname . ' - ' : '' ) . $subscriber->email ) : '',
					'notification' => $message,
					'can-spam'     => $message,
				);

			// confirmation
			case 'confirmation_to':
				return $subscriber->email;

			case 'confirmation_subject':
				$form_id = mailster( 'subscribers' )->meta( $subscriber->ID, 'form' );
				$form    = get_post( $form_id );

				if ( ! $form || $form->post_type != 'mailster-form' ) {

					// legacy
					$form = $this->get_form_options( $options['form'], $subscriber );
					if ( ! empty( $form->subject ) ) {

						return $form->subject;
					}

					// fallback if no form is defined
					return sprintf( esc_html__( 'Welcome to %s! Please Confirm Your Email', 'mailster' ), '{company}' );

				}

				return get_post_meta( $form_id, 'subject', true );

			case 'confirmation_file':
				$form_id = mailster( 'subscribers' )->meta( $subscriber->ID, 'form' );
				$form    = get_post( $form_id );

				if ( ! $form || $form->post_type != 'mailster-form' ) {

					// legacy
					$form = $this->get_form_options( $options['form'], $subscriber );
					if ( ! empty( $form->template ) ) {
						return $form->template;
					}

					// fallback if no form is defined
					return null;
				}
				return false;

			case 'confirmation_headline':
				$form_id = mailster( 'subscribers' )->meta( $subscriber->ID, 'form' );
				$form    = get_post( $form_id );

				if ( ! $form || $form->post_type != 'mailster-form' ) {

					// legacy
					$form = $this->get_form_options( $options['form'], $subscriber );
					if ( ! empty( $form->headline ) ) {
						return $form->headline;
					}

					// fallback if no form is defined
					return esc_html__( 'Please confirm your Email', 'mailster' );
				}

				return get_post_meta( $form_id, 'headline', true );

			case 'confirmation_replace':
				$form_id = mailster( 'subscribers' )->meta( $subscriber->ID, 'form' );
				$form    = get_post( $form_id );

				$link_text = esc_html__( 'Confirm your email address', 'mailster' );

				if ( ! $form || $form->post_type != 'mailster-form' ) {

					// legacy
					if ( isset( $options['form'] ) ) {
						$form      = $this->get_form_options( $options['form'], $subscriber, false, true );
						$form_id   = $form->ID;
						$link_text = $form->link;
					} else {
						$form_id = null;
					}
				}

				$subscriber_lists = mailster( 'subscribers' )->get_lists( $subscriber->ID );
				$list_names       = wp_list_pluck( $subscriber_lists, 'name' );

				$list_ids = isset( $options['list_ids'] ) ? array_filter( $options['list_ids'] ) : null;
				$link     = mailster( 'subscribers' )->get_confirm_link( $subscriber->ID, $form_id, $list_ids );

				$message = esc_html__( 'If you received this email by mistake, simply delete it. You won\'t be subscribed if you don\'t click the confirmation link.', 'mailster' );

				return wp_parse_args(
					array(
						'link'         => '<a href="' . htmlentities( $link ) . '">' . esc_html( $link_text ) . '</a>',
						'linkaddress'  => $link,
						'lists'        => implode( ', ', $list_names ),
						'notification' => $message,
						'can-spam'     => $message,
					),
					$content
				);

			case 'confirmation_attachments':
				$form_id = mailster( 'subscribers' )->meta( $subscriber->ID, 'form' );
				$form    = get_post( $form_id );

				if ( ! $form || $form->post_type != 'mailster-form' ) {

					// legacy
					$form = $this->get_form_options( $options['form'], $subscriber );
					if ( ! empty( $form->vcard ) ) {

						$wp_filesystem = mailster_require_filesystem();

						if ( $wp_filesystem && $wp_filesystem->put_contents( MAILSTER_UPLOAD_DIR . '/vCard.vcf', $form->vcard_content, FS_CHMOD_FILE ) ) {
							$content[] = MAILSTER_UPLOAD_DIR . '/vCard.vcf';
						}
					}
				}
				return $content;

			// test mail
			case 'test_subject':
				return sprintf( esc_html_x( '%s Test Email', 'Mailster', 'mailster' ), 'Mailster' );

			case 'test_replace':
				$message = sprintf( esc_html__( 'This is a test mail sent from %s', 'mailster' ), '<a href="' . admin_url( 'edit.php?post_type=newsletter&page=mailster_settings#delivery' ) . '">' . esc_html__( 'from your settings page', 'mailster' ) . '</a>' );
				return array(
					'headline'     => sprintf( esc_html_x( '%s Test Email', 'Mailster', 'mailster' ), 'Mailster' ),
					'notification' => $message,
					'can-spam'     => $message,
				);

			default:
				return apply_filters( "mailster_notification_{$template}_{$filter}", $content, $subscriber, $options );
		}
	}


	/**
	 *
	 *
	 * @param unknown $template
	 */
	public function template( $template ) {
		$this->template = $template;
	}


	/**
	 *
	 *
	 * @param unknown $file
	 */
	public function file( $file ) {
		$this->file = $file;
	}


	/**
	 *
	 *
	 * @param unknown $to
	 */
	public function to( $to ) {
		$this->to = $to;
	}


	/**
	 *
	 *
	 * @param unknown $subject
	 */
	public function subject( $subject ) {
		$this->subject = $subject;
	}

	/**
	 *
	 *
	 * @param unknown $message
	 */
	public function message( $message ) {
		$this->message = $message;
	}


	/**
	 *
	 *
	 * @param unknown $attachments
	 */
	public function attachments( $attachments ) {
		$this->attachments = is_array( $attachments ) ? $attachments : array( $attachments );
	}


	/**
	 *
	 *
	 * @param unknown $replace
	 */
	public function replace( $replace, $add = false ) {
		$replace = is_array( $replace ) ? $replace : array( $replace );
		if ( $add ) {
			$this->replace = array_merge( $this->replace, $replace );
		} else {
			$this->replace = $replace;
		}
	}


	/**
	 *
	 *
	 * @param unknown $requeue
	 */
	public function requeue( $requeue ) {
		$this->requeue = $requeue;
	}


	/**
	 *
	 *
	 * @param unknown $bool (optional)
	 */
	public function debug( $bool = true ) {
		$this->debug = (bool) $bool;
	}


	/**
	 *
	 *
	 * @param unknown $subscriber_id
	 * @param unknown $options
	 * @return unknown
	 */
	public function send( $subscriber_id, $options ) {

		$template = isset( $options['template'] ) ? $options['template'] : '';

		$this->apply_options( $options );
		if ( $subscriber_id && $subscriber = mailster( 'subscribers' )->get( $subscriber_id, true ) ) {
			$userdata = mailster( 'subscribers' )->get_userdata( $subscriber );
			$this->to = $subscriber->email;
		} else {
			$subscriber = null;
		}

		$return = null;

		if ( ! $this->message ) {
			ob_start();

			if ( method_exists( $this, 'template_' . $template ) ) {
				$return = call_user_func( array( $this, 'template_' . $template ), $subscriber, $options );
			}

			$output = ob_get_contents();

			ob_end_clean();

			if ( false === $return ) {
				return true;
			}

			// hook for custom templates
			ob_start();

			do_action( "mailster_notification_{$template}", $subscriber, $options, $output );

			$output2 = ob_get_contents();

			ob_end_clean();

			$this->message = ! empty( $output2 ) ? $output2 : $output;

		}

		$this->message = apply_filters( 'mailster_notification_content', $this->message, $template, $subscriber, $options );

		if ( empty( $this->message ) ) {
			return new WP_Error( 'notification_error', 'no content' );
		}

		$this->to        = apply_filters( 'mailster_notification_to', $this->to, $template, $subscriber, $options );
		$this->subject   = apply_filters( 'mailster_notification_subject', $this->subject, $template, $subscriber, $options );
		$this->file      = apply_filters( 'mailster_notification_file', $this->file, $template, $subscriber, $options );
		$this->headline  = apply_filters( 'mailster_notification_headline', $this->headline, $template, $subscriber, $options );
		$this->preheader = apply_filters( 'mailster_notification_preheader', $this->preheader, $template, $subscriber, $options );

		$this->replace = apply_filters( 'mailster_notification_replace', $this->replace, $template, $subscriber, $options );

		if ( ! isset( $this->file ) || empty( $this->file ) ) {
			$this->file = 'notification.html';
		}

		$this->mail = mailster( 'mail' );

		$this->to = (array) $this->to;

		$this->mail->to        = $this->to;
		$this->mail->from      = apply_filters( 'mailster_notification_from', $this->mail->from, $template, $subscriber, $options );
		$this->mail->from_name = apply_filters( 'mailster_notification_from_name', $this->mail->from_name, $template, $subscriber, $options );
		$this->mail->reply_to  = apply_filters( 'mailster_notification_reply_to', mailster_option( 'reply_to', false ), $template, $subscriber, $options );

		$this->mail->subject = $this->subject;

		$MID = mailster_option( 'ID' );
		$this->mail->add_header( 'X-Mailster-ID', $MID );

		$this->mail->bouncemail  = mailster_option( 'bounce' );
		$this->mail->attachments = apply_filters( 'mailster_notification_attachments', $this->attachments, $template, $subscriber, $options );

		$t = mailster( 'template', mailster_option( 'default_template' ), $this->file );
		$t->use_notification();
		$raw = $t->get( true, true );

		$placeholder = mailster( 'placeholder', $raw );

		$placeholder->add_defaults();

		// only if the subscriber is in the list of receivers
		if ( $subscriber && in_array( $subscriber->email, $this->to ) ) {
			$this->mail->hash = $subscriber->hash;
			$this->mail->add_header( 'X-Mailster', $subscriber->hash );
			$placeholder->set_subscriber( $subscriber->ID );
			$placeholder->set_hash( $subscriber->hash );
			$this->mail->set_subscriber( $subscriber->ID );
			$placeholder->add_custom();
			$placeholder->add( $userdata );
			$placeholder->add(
				array(
					'emailaddress' => $subscriber->email,
					'hash'         => $subscriber->hash,
				)
			);

			// add list unsubscribe headers
			$listunsubscribe = array();
			if ( mailster_option( 'mail_opt_out' ) ) {
				$listunsubscribe_mail    = $this->mail->bouncemail ? $this->mail->bouncemail : $this->mail->from;
				$listunsubscribe_subject = rawurlencode( 'Please remove me from the list' );
				$listunsubscribe_link    = mailster()->get_unsubscribe_link( null, $subscriber->hash );
				$listunsubscribe_body    = rawurlencode( "Please remove me from your list! {$subscriber->email} X-Mailster: {$subscriber->hash} X-Mailster-ID: {$MID} Link: {$listunsubscribe_link}" );

				$listunsubscribe[] = "<mailto:$listunsubscribe_mail?subject=$listunsubscribe_subject&body=$listunsubscribe_body>";
			}
			$listunsubscribe[] = '<' . mailster( 'frontpage' )->get_link( 'unsubscribe', $subscriber->hash ) . '>';

			$this->mail->add_header( 'List-Unsubscribe', implode( ',', $listunsubscribe ) );

		}

		$placeholder->add(
			array(
				'subject'   => $this->subject,
				'preheader' => $this->preheader,
				'headline'  => $this->headline,
				'content'   => $this->message,
			)
		);

		$placeholder->add( $this->replace );

		$content = $placeholder->get_content();
		$content = mailster( 'helper' )->prepare_content( $content );
		if ( apply_filters( 'mailster_inline_css', true ) ) {
			$content = mailster( 'helper' )->inline_css( $content );
		}

		$this->mail->content = $content;

		$placeholder->set_content( $this->mail->subject );
		$this->mail->subject = $placeholder->get_content();

		$this->mail->add_tracking_image = false;
		$this->mail->embed_images       = mailster_option( 'embed_images' );
		if ( $this->debug ) {
			$this->mail->debug();
		}

		foreach ( (array) $this->to as $receiver ) {

			$this->mail->to = $receiver;
			$result         = $this->mail->send();

			if ( $result && ! is_wp_error( $result ) ) {
				continue;
			}

			if ( is_wp_error( $result ) ) {
				return $result;
			}

			if ( $this->mail->is_user_error() ) {
				return new WP_Error( 'user_error', $this->mail->last_error->getMessage() );
			}

			if ( $this->mail->last_error ) {
				return new WP_Error( 'notification_error', $this->mail->last_error->getMessage() );
			}

			return new WP_Error( 'notification_error', esc_html__( 'unknown', 'mailster' ) );
		}

		return true;
	}


	/**
	 *
	 *
	 * @param unknown $options
	 * @return unknown
	 */
	private function set_options( $options ) {
		$params = array( 'to', 'subject' );
		foreach ( $params as $key ) {
			if ( ! is_null( $this->{$key} ) ) {
				$options[ $key ] = $this->{$key};
			}
		}

		return $options;
	}


	/**
	 *
	 *
	 * @param unknown $options
	 */
	private function apply_options( $options ) {
		if ( is_array( $options ) ) {
			foreach ( $options as $key => $value ) {
				if ( method_exists( $this, $key ) ) {
					$this->{$key}( $value );
				}
			}
		}
	}


	/**
	 *
	 *
	 * @param unknown $options
	 */
	private function get_form_options( $form_id, $subscriber, $fields = false, $lists = false ) {
		$form = mailster( 'forms' )->get( $form_id, $fields, $lists );
		if ( $form_key = mailster( 'subscribers' )->meta( $subscriber->ID, 'formkey' ) ) {
			$form_args = (array) get_transient( '_mailster_form_' . $form_key );
			$form      = (object) wp_parse_args( $form_args, (array) $form );
		}
		return (object) $form;
	}


	// Templates
	/**
	 *
	 *
	 * @param unknown $subscriber
	 * @param unknown $options
	 */
	private function template_basic( $subscriber, $options ) {
	}


	/**
	 *
	 *
	 * @param unknown $subscriber
	 * @param unknown $options
	 */
	private function template_confirmation( $subscriber, $options ) {

		$form_id = mailster( 'subscribers' )->meta( $subscriber->ID, 'form' );
		$form    = get_post( $form_id );

		if ( ! $form || $form->post_type != 'mailster-form' ) {

			// legacy
			$form = $this->get_form_options( $options['form'], $subscriber );
			if ( ! empty( $form->content ) ) {
				$content = $form->content;
				if ( false === strpos( $content, '{link}' ) ) {
					$content .= "\n{link}";
				}
				// fallback if no form is defined
			} else {
				$content = sprintf( esc_html__( 'By clicking on the following link, you are confirming your email address. %s', 'mailster' ), "\n\n{link}" );
			}
		} else {
			$content = get_post_meta( $form_id, 'content', true );
		}

		echo wpautop( $content );

		?>
		<div itemscope itemtype="http://schema.org/EmailMessage">
			<div itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction">
			<meta itemprop="name" content="<?php echo esc_url( $form->link ); ?>"/>
			<div itemprop="handler" itemscope itemtype="http://schema.org/HttpActionHandler">
				<link itemprop="url" href="{linkaddress}"/>
			</div>
			</div>
			<meta itemprop="description" content="<?php esc_attr_e( 'Confirmation Message', 'mailster' ); ?>"/>
		</div>
		<?php
	}


	/**
	 *
	 *
	 * @param unknown $subscriber
	 * @param unknown $options
	 */
	private function template_test( $subscriber, $options ) {
		?>
		<p><?php esc_html_e( 'This email is a test to ensure the integrity and functionality of your current delivery method. If you\'re receiving this message, it indicates that your email system is operating correctly.', 'mailster' ); ?></p>
		<p><?php esc_html_e( 'Consider this message as a confirmation of the effective operation of you email delivery system.', 'mailster' ); ?></p>
		<p><?php printf( esc_html__( 'You can also run %s to check your deliverbilty.', 'mailster' ), '<a href="' . admin_url( 'admin.php?page=mailster_health' ) . '">' . esc_html__( 'an Email Health Check', 'mailster' ) . '</a>' ); ?></p>
		<p><?php esc_html_e( 'There is no action required on your part; feel free to delete this email.', 'mailster' ); ?></p>

		<?php
	}


	private function template_health_check() {
		esc_html_e( 'This is a health check email from Mailster', 'mailster' );
	}


	/**
	 *
	 *
	 * @param unknown $subscriber
	 * @param unknown $options
	 */
	private function template_new_subscriber( $subscriber, $options ) {

		$custom_fields = mailster()->get_custom_fields();

		?>

		<?php if ( get_option( 'show_avatars' ) ) : ?>

		<table style="width:100%;table-layout:fixed">
			<tr>
			<td valign="top" align="center">
				<a href="<?php echo admin_url( 'edit.php?post_type=newsletter&page=mailster_subscribers&ID=' . $subscriber->ID ); ?>" >
					<img src="<?php echo mailster( 'subscribers' )->get_gravatar_uri( $subscriber->email, 240 ); ?>" width="120" style="border-radius:50%;display:block;width:120px;overflow:hidden;">
				</a>
			</td>
			</tr>
		</table>

	<?php endif; ?>

		<table style="width:100%;table-layout:fixed"><tr><td valign="top" align="center">&nbsp;</td></tr></table>

		<table style="width:100%;table-layout:fixed">
			<tr>
			<td valign="top" align="center">
				<h2><?php printf( esc_html__( '%s has joined', 'mailster' ), '<a href="' . admin_url( 'edit.php?post_type=newsletter&page=mailster_subscribers&ID=' . $subscriber->ID ) . '">' . ( ( $subscriber->fullname ) ? $subscriber->fullname . ' - ' : '' ) . $subscriber->email . '</a>' ); ?></h2>

				<table style="width:100%;table-layout:fixed">
					<tr><td><?php mailster( 'subscribers' )->output_referer( $subscriber->ID ); ?></td></tr>
				<?php foreach ( $custom_fields as $id => $field ) : ?>
					<?php
					if ( empty( $subscriber->{$id} ) && ! in_array( $field['type'], array( 'checkbox' ) ) ) {
						continue;
					}
					?>
					<tr><td height="20" style="border-top:1px solid #ccc;height:30px"><strong><?php echo strip_tags( $field['name'] ); ?>:</strong>
					<?php
					switch ( $field['type'] ) {
						case 'checkbox':
							echo $subscriber->{$id} ? esc_html__( 'yes', 'mailster' ) : esc_html__( 'no', 'mailster' );
							break;
						case 'textarea':
							echo wpautop( esc_html( $subscriber->{$id} ) );
							break;
						case 'date':
							echo $subscriber->{$id} && is_integer( strtotime( $subscriber->{$id} ) )
							? date_i18n( mailster( 'helper' )->dateformat(), strtotime( $subscriber->{$id} ) )
							: $subscriber->{$id};
							break;
						default:
							echo esc_html( $subscriber->{$id} );
					}
					?>
				</td></tr>
				<?php endforeach; ?>

					<?php if ( $lists = mailster( 'subscribers' )->get_lists( $subscriber->ID ) ) : ?>
				<tr><td height="30" style="border-top:1px solid #ccc;height:30px"><strong><?php esc_html_e( 'Lists', 'mailster' ); ?>:</strong>
						<?php foreach ( $lists as $i => $list ) : ?>
							<a href="<?php echo admin_url( 'edit.php?post_type=newsletter&page=mailster_lists&ID=' . $list->ID ); ?>">
								<?php echo $list->name; ?></a>
							<?php
							if ( $i + 1 < count( $lists ) ) {
								echo ', ';
							}
							?>
					<?php endforeach; ?>
				</td></tr>
					<?php endif; ?>

				</table>
			</td>
			</tr>
		</table>

		<?php $meta = mailster( 'subscribers' )->meta( $subscriber->ID ); ?>

		<?php if ( mailster_option( 'static_map' ) && $meta['coords'] ) : ?>

			<?php

			$coords = explode( ',', $meta['coords'] );

			$args = array(
				'zoom'   => 4,
				'lat'    => $coords[0],
				'lon'    => $coords[1],
				'width'  => 300,
				'height' => 250,
			);

			$mapurl      = mailster( 'helper' )->static_map( $args, WEEK_IN_SECONDS );
			$mapurl_zoom = mailster( 'helper' )->static_map( wp_parse_args( array( 'zoom' => 8 ), $args ), WEEK_IN_SECONDS );

			?>
			<table style="width:100%;table-layout:fixed">
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr>
					<td align="left">
						<img src="<?php echo esc_url( $mapurl ); ?>" width="300" heigth="250">
					</td>
					<td align="right">
						<img src="<?php echo esc_url( $mapurl_zoom ); ?>" width="300" heigth="250">
					</td>
				</tr>
				<tr><td colspan="2">&nbsp;</td></tr>
			</table>

		<?php endif; ?>

		<?php
	}


	/**
	 *
	 *
	 * @param unknown $subscriber
	 * @param unknown $options
	 * @return unknown
	 */
	private function template_new_subscriber_delayed( $subscriber, $options ) {

		global $wpdb;

		// should be odd
		$limit = apply_filters( 'mailster_subscriber_notification_subscriber_limit', 7 );

		$delay = mailster_option( 'subscriber_notification_delay' );
		if ( ! $delay ) {
			return false;
		}

		// get timestamp in UTC
		$timestamp = mailster( 'helper' )->get_timestamp_by_string( $delay, true );

		$sql = $wpdb->prepare( "SELECT a.ID, b.meta_value as coords, c.meta_value as geo FROM {$wpdb->prefix}mailster_subscribers AS a LEFT JOIN {$wpdb->prefix}mailster_subscriber_meta AS b ON a.ID = b.subscriber_ID AND b.meta_key = 'coords' LEFT JOIN {$wpdb->prefix}mailster_subscriber_meta AS c ON a.ID = c.subscriber_ID AND c.meta_key = 'geo' WHERE (a.signup >= %d OR a.confirm >= %d) AND a.status = 1 GROUP BY a.ID ORDER BY a.signup DESC, a.confirm DESC", $timestamp, $timestamp );

		$subscribers = $wpdb->get_results( $sql );

		$date_format = mailster( 'helper' )->dateformat();

		$count = count( $subscribers );
		if ( ! $count ) {
			return false;
		}

		if ( $count == 1 ) {
			$subscriber = mailster( 'subscribers' )->get( $subscribers[0]->ID, true );
			return $this->template_new_subscriber( $subscriber, $options );
		}

		$gmt_offset = mailster( 'helper' )->gmt_offset( true );

		$total = mailster( 'subscribers' )->get_count_by_status( 1 );
		?>

		<table style="width:100%;table-layout:fixed">
			<tr>
			<td valign="top" align="center">
				<h2><?php printf( esc_html__( 'You have %1$s new subscribers since %2$s.', 'mailster' ), '<strong>' . number_format_i18n( $count ) . '</strong>', date_i18n( $date_format, $timestamp + $gmt_offset ) ); ?></h2>
				<?php printf( esc_html__( 'You have now %s subscribers in total.', 'mailster' ), '<strong>' . number_format_i18n( $total ) . '</strong>' ); ?>
			</td>
			</tr>
		</table>

		<table style="width:100%;table-layout:fixed"><tr><td valign="top" align="center">&nbsp;</td></tr></table>

		<table cellpadding="0" cellspacing="0" class="o-fix">
		<tr>

			<td width="552" valign="top" align="center" style="border-top:1px solid #ccc;">

		<?php foreach ( $subscribers as $i => $subscriber ) : ?>

			<?php
			if ( $i >= $limit ) {
				break;
			}

			$subscriber = mailster( 'subscribers' )->get( $subscriber->ID, true );
			$link       = admin_url( 'edit.php?post_type=newsletter&page=mailster_subscribers&ID=' . $subscriber->ID );

			?>
			<table cellpadding="0" cellspacing="0" align="<?php echo ! ( $i % 2 ) ? 'left' : 'right'; ?>">
				<tr>
					<td width="264" valign="top" align="left" class="m-b">
					<table cellpadding="0" cellspacing="0">
						<tr><td width="80">&nbsp;</td><td>&nbsp;</td></tr>
						<tr>
						<?php if ( get_option( 'show_avatars' ) ) : ?>
						<td valign="top" align="center" width="80">
							<div style="border-radius:50%;width:60px;height:60px;background-color:#fafafa">
							<a href="<?php echo $link; ?>">
							<img src="<?php echo mailster( 'subscribers' )->get_gravatar_uri( $subscriber->email, 120 ); ?>" width="60" style="border-radius:50%;display:block;width:60px;overflow:hidden">
							</div>
							</a>
						</td>
						<?php endif; ?>
						<td valign="top" align="left">
							<h4 style="margin:0"><?php echo $subscriber->fullname ? '<a href="' . $link . '">' . esc_html( $subscriber->fullname ) . '</a>' : '&nbsp;'; ?></h4>
							<small><?php echo esc_html( $subscriber->email ); ?></small>
						</td>
						</tr>
						<tr><td width="80">&nbsp;</td><td>&nbsp;</td></tr>
					</table>
					</td>
				</tr>
			</table>
			<?php
			if ( (bool) ( $i % 2 ) ) {
				echo '</td></tr></table><table cellpadding="0" cellspacing="0" class="o-fix"><tr><td width="552" valign="top" align="center" style="border-top:1px solid #ccc;">'; }
			?>

	<?php endforeach; ?>

		<?php if ( $count > $limit ) : ?>

			<?php $link = admin_url( 'edit.php?post_type=newsletter&page=mailster_subscribers&since=' . date( 'Y-m-d', $timestamp ) ); ?>

				<table cellpadding="0" cellspacing="0" align="<?php echo ! ( $i % 2 ) ? 'left' : 'right'; ?>">
				<tr>
					<td width="264" valign="top" align="left" class="m-b">
					<table style="width:100%;table-layout:fixed">
					<tr><td width="80">&nbsp;</td><td>&nbsp;</td></tr>
					<tr>
					<td valign="center" align="center" width="80">
						<a href="<?php echo esc_url( $link ); ?>">
							<div style="border-radius:50%;width:60px;height:60px;background-color:#fafafa"></div>
						</a>
					</td>
					<td valign="center" align="left">
						<h4 style="margin:0;"><a href="<?php echo esc_url( $link ); ?>"><?php printf( esc_html__( _n( '%s other', '%s others', $count - $limit, 'mailster' ) ), number_format_i18n( $count - $limit ) ); ?></a></h5>
					</td>
					</tr>
					<tr><td width="80">&nbsp;</td><td>&nbsp;</td></tr>
					</table>
					</td>
				</tr>
				</table>

		<?php endif; ?>

			</td>

		</tr>
		</table>

		<?php
		$coords = wp_list_pluck( $subscribers, 'coords' );
		$geo    = wp_list_pluck( $subscribers, 'geo' );

		$coords        = array_values( array_slice( array_filter( $coords ), 0, 30 ) );
		$locationcount = count( $coords );

		// sanitization
		$geo = preg_replace( '/^([A-Z]+)|.*/', '$1', $geo );
		$geo = array_filter( $geo, 'strlen' );
		$geo = array_count_values( $geo );
		arsort( $geo );
		$other = array_sum( array_slice( $geo, 9, 9999 ) );
		$geo   = array_slice( $geo, 0, 9 );

		?>
		<?php if ( array_sum( $coords ) ) : ?>

		<table style="width:100%;table-layout:fixed"><tr><td valign="top" align="center">&nbsp;</td></tr></table>

		<table style="width:100%;table-layout:fixed">
			<tr>
				<td valign="top" align="center">
				<h2><?php printf( esc_html__( 'Subscribers are located in %s different countries', 'mailster' ), '<strong>' . $locationcount . '</strong>' ); ?></h2>
				</td>
			</tr>
		</table>

			<?php if ( mailster_option( 'static_map' ) ) : ?>

				<?php
				$mapurl = mailster( 'helper' )->static_map(
					array(
						'zoom'      => 3,
						'autoscale' => true,
						'coords'    => $coords,
						'width'     => 600,
						'height'    => 300,
					),
					WEEK_IN_SECONDS
				);
				?>

		<table style="width:100%;table-layout:fixed">
			<tr>
				<td valign="top" align="center">
				<img width="600" height="300" src="<?php echo esc_url( $mapurl ); ?>" alt="<?php printf( esc_html__( _n( 'location of %d subscriber', 'location of %d subscribers', $locationcount, 'mailster' ) ), $locationcount ); ?>">
				</td>
			</tr>
		</table>

	<?php endif; ?>

		<table style="width:100%;table-layout:fixed"><tr><td valign="top" align="center">&nbsp;</td></tr></table>

		<table cellpadding="0" cellspacing="0" class="o-fix">
			<tr>
			<td width="100%" valign="top" align="center" style="border-top:1px solid #ccc;">

			<?php $i = 0; foreach ( $geo as $code => $number ) : ?>
				<table cellpadding="0" cellspacing="0" align="<?php echo ! ( $i % 2 ) ? 'left' : 'right'; ?>">
				<tr>
					<td width="264" valign="top" align="left" class="m-b">
					<table style="width:100%;table-layout:fixed">
					<tr>
					<td>&nbsp;</td>
					<td align="left" width="75%">
						<?php echo mailster( 'geo' )->code2Country( $code ); ?>
					</td>
					<td align="right" width="15%">
						<strong><?php echo number_format_i18n( $number ); ?></strong>
					</td>
					<td>&nbsp;</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>

				<?php
				if ( (bool) ( $i % 2 ) ) :
					echo '</td></tr></table><table cellpadding="0" cellspacing="0" class="o-fix"><tr><td width="100%" valign="top" align="center" style="border-top:1px solid #ccc;">';
				endif;
				++$i;
				?>
				<?php endforeach; ?>

			<?php if ( ! empty( $other ) ) : ?>

			<table cellpadding="0" cellspacing="0" align="<?php echo ! ( $i % 2 ) ? 'left' : 'right'; ?>">
			<tr>
				<td width="264" valign="top" align="left" class="m-b">
				<table style="width:100%;table-layout:fixed">
				<tr>
				<td>&nbsp;</td>
				<td align="left" width="75%">
					<?php esc_html_e( 'from other countries', 'mailster' ); ?>
				</td>
				<td align="right" width="15%">
					<strong><?php echo number_format_i18n( (int) $other ); ?></strong>
				</td>
				<td>&nbsp;</td>
				</tr>
				</table>
				</td>
			</tr>
			</table>

	<?php endif; ?>

		</td>

	</tr>
	</table>

	<?php endif; ?>

		<?php
	}



	/**
	 *
	 *
	 * @param unknown $subscriber
	 * @param unknown $options
	 */
	private function template_delete_job( $subscriber, $options ) {

		$subscribers = $options['subscribers'];
		$job         = $options['job'];
		$count       = count( $subscribers );
		$limit       = 100;

		$this->subject( sprintf( esc_html__( _n( '%s Subscriber was marked for deletion', '%s Subscribers were marked for deletion', $count, 'mailster' ) ), number_format_i18n( $count ) ) );
		$this->replace( array( 'notification' => sprintf( esc_html__( 'You can update these jobs on the %s.', 'mailster' ), '<a href="' . admin_url( 'edit.php?post_type=newsletter&page=mailster_manage_subscribers&tab=delete' ) . '">' . esc_html__( 'Delete Subscribers page', 'mailster' ) . '</a>' ) ) );

		?>

		<table style="width:100%;table-layout:fixed">
			<tr>
				<td valign="top" align="left">
				<h3><?php printf( _n( '%1$s removed %2$s Subscriber.', '%1$s removed %2$s Subscribers.', $count, 'mailster' ), '"' . $job['name'] . '"', number_format_i18n( $count ) ); ?></h3>
				</td>
			</tr>
			<tr>
				<td valign="top" align="left">
				<?php include MAILSTER_DIR . 'views/manage/job.php'; ?>
				</td>
			</tr>
		</table>
		<table style="width:100%;table-layout:fixed">
			<tr>
				<td valign="top" align="left">
				<p><?php printf( esc_html__( 'You can find these subscribers on the %s.', 'mailster' ), '<a href="' . admin_url( 'edit.php?post_type=newsletter&page=mailster_subscribers&status=5' ) . '">' . esc_html__( 'Deleted Subscribers page', 'mailster' ) . '</a>' ); ?></p>
				</td>
			</tr>
		</table>
		<table cellpadding="0" cellspacing="0" class="o-fix">
		<tr>
			<td width="552" valign="top" align="center" style="border-top:1px solid #ccc;">
		<?php foreach ( $subscribers as $i => $subscriber ) : ?>
			<?php
			if ( $i >= $limit ) {
				break;
			}
			$subscriber = mailster( 'subscribers' )->get( $subscriber, true );
			if ( ! $subscriber ) {
				continue;
			}
			$link = admin_url( 'edit.php?post_type=newsletter&page=mailster_subscribers&ID=' . $subscriber->ID );
			?>

		<table cellpadding="0" cellspacing="0" align="<?php echo ! ( $i % 2 ) ? 'left' : 'right'; ?>">
			<tr>
				<td width="264" valign="top" align="left" class="m-b">
				<table cellpadding="0" cellspacing="0">
				<tr><td width="80">&nbsp;</td><td>&nbsp;</td></tr>
				<tr>
				<?php if ( get_option( 'show_avatars' ) ) : ?>
				<td valign="top" align="center" width="80">
					<div style="border-radius:50%;width:60px;height:60px;background-color:#fafafa">
					<a href="<?php echo $link; ?>">
					<img src="<?php echo mailster( 'subscribers' )->get_gravatar_uri( $subscriber->email, 120 ); ?>" width="60" style="border-radius:50%;display:block;width:60px;overflow:hidden">
					</div>
					</a>
				</td>
				<?php endif; ?>
				<td valign="top" align="left">
					<h4 style="margin:0"><?php echo esc_html( $subscriber->fullname ) ? '<a href="' . $link . '">' . esc_html( $subscriber->fullname ) . '</a>' : '&nbsp;'; ?></h4>
					<small><?php echo esc_html( $subscriber->email ); ?></small>
				</td>
				</tr>
				<tr><td width="80">&nbsp;</td><td>&nbsp;</td></tr>
				</table>
				</td>
			</tr>
		</table>


	<?php endforeach; ?>
		<?php if ( $count - $limit > 0 ) : ?>
		<table style="width:100%;table-layout:fixed">
			<tr>
				<td valign="top" align="left">
				<p><?php printf( esc_html__( _n( '%s other hidden', '%s others hidden', $count - $limit, 'mailster' ) ), number_format_i18n( $count - $limit ) ); ?></p>
				</td>
			</tr>
		</table>
	<?php endif; ?>

		<?php
	}


	/**
	 *
	 *
	 * @param unknown $subscriber
	 * @param unknown $options
	 */
	private function template_unsubscribe( $subscriber, $options ) {

		$custom_fields = mailster()->get_custom_fields();

		?>
		<?php if ( get_option( 'show_avatars' ) ) : ?>

		<table style="width:100%;table-layout:fixed">
			<tr>
			<td valign="top" align="center">
				<a href="<?php echo admin_url( 'edit.php?post_type=newsletter&page=mailster_subscribers&ID=' . $subscriber->ID ); ?>" >
					<img src="<?php echo mailster( 'subscribers' )->get_gravatar_uri( $subscriber->email, 240 ); ?>" width="120" style="border-radius:50%;display:block;width:120px;overflow:hidden;">
				</a>
			</td>
			</tr>
		</table>

		<?php endif; ?>

		<table style="width:100%;table-layout:fixed"><tr><td valign="top" align="center">&nbsp;</td></tr></table>

		<table style="width:100%;table-layout:fixed">
			<tr>
			<td valign="top" align="center">
				<h2><?php printf( esc_html__( '%s has canceled', 'mailster' ), '<a href="' . admin_url( 'edit.php?post_type=newsletter&page=mailster_subscribers&ID=' . $subscriber->ID ) . '">' . ( ( $subscriber->fullname ) ? $subscriber->fullname . ' - ' : '' ) . $subscriber->email . '</a>' ); ?></h2>
			</td>
			</tr>
		</table>

		<?php
	}


	/**
	 *
	 *
	 * @param unknown $subscriber
	 * @param unknown $options
	 * @return unknown
	 */
	private function template_unsubscribe_delayed( $subscriber, $options ) {

		global $wpdb;

		// should be odd
		$limit = apply_filters( 'mailster_subscriber_unsubscribe_notification_subscriber_limit', 7 );

		$delay = mailster_option( 'unsubscribe_notification_delay' );
		if ( ! $delay ) {
			return false;
		}

		// get timestamp in UTC
		$timestamp = mailster( 'helper' )->get_timestamp_by_string( $delay, true );

		$sql = $wpdb->prepare( "SELECT a.ID FROM {$wpdb->prefix}mailster_subscribers AS a LEFT JOIN {$wpdb->prefix}mailster_action_unsubs AS b ON a.ID = b.subscriber_ID WHERE b.timestamp >= %d AND a.status = 2 GROUP BY a.ID ORDER BY b.timestamp DESC, a.signup DESC", $timestamp );

		$subscribers = $wpdb->get_results( $sql );

		$date_format = mailster( 'helper' )->dateformat();

		$count = count( $subscribers );
		if ( ! $count ) {
			return false;
		}

		if ( $count == 1 ) {
			$subscriber = mailster( 'subscribers' )->get( $subscribers[0]->ID, true );
			return $this->template_unsubscribe( $subscriber, $options );
		}

		$gmt_offset = mailster( 'helper' )->gmt_offset( true );

		$total = mailster( 'subscribers' )->get_count_by_status( 1 );

		?>
		<table style="width:100%;table-layout:fixed">
			<tr>
			<td valign="top" align="center">
				<h2><?php printf( esc_html__( 'You have %1$s cancellations since %2$s.', 'mailster' ), '<strong>' . number_format_i18n( $count ) . '</strong>', date_i18n( $date_format, $timestamp + $gmt_offset ) ); ?></h2>
				<?php printf( esc_html__( 'You have now %s subscribers in total.', 'mailster' ), '<strong>' . number_format_i18n( $total ) . '</strong>' ); ?>
			</td>
			</tr>
		</table>

		<table style="width:100%;table-layout:fixed"><tr><td valign="top" align="center">&nbsp;</td></tr></table>

		<table cellpadding="0" cellspacing="0" class="o-fix">
		<tr>

			<td width="552" valign="top" align="center" style="border-top:1px solid #ccc;">
		<?php foreach ( $subscribers as $i => $subscriber ) : ?>
			<?php

			if ( $i >= $limit ) {
				break;
			}

			$subscriber = mailster( 'subscribers' )->get( $subscriber->ID, true );
			$link       = admin_url( 'edit.php?post_type=newsletter&page=mailster_subscribers&ID=' . $subscriber->ID );

			?>
		<table cellpadding="0" cellspacing="0" align="<?php echo ! ( $i % 2 ) ? 'left' : 'right'; ?>">
			<tr>
				<td width="264" valign="top" align="left" class="m-b">
				<table cellpadding="0" cellspacing="0">
				<tr><td width="80">&nbsp;</td><td>&nbsp;</td></tr>
				<tr>
				<?php if ( get_option( 'show_avatars' ) ) : ?>
				<td valign="top" align="center" width="80">
					<div style="border-radius:50%;width:60px;height:60px;background-color:#fafafa">
					<a href="<?php echo $link; ?>">
					<img src="<?php echo mailster( 'subscribers' )->get_gravatar_uri( $subscriber->email, 120 ); ?>" width="60" style="border-radius:50%;display:block;width:60px;overflow:hidden">
					</div>
					</a>
				</td>
				<?php endif; ?>
				<td valign="top" align="left">
					<h4 style="margin:0"><?php echo esc_html( $subscriber->fullname ) ? '<a href="' . $link . '">' . esc_html( $subscriber->fullname ) . '</a>' : '&nbsp;'; ?></h4>
					<small><?php echo esc_html( $subscriber->email ); ?></small>
				</td>
				</tr>
				<tr><td width="80">&nbsp;</td><td>&nbsp;</td></tr>
				</table>
				</td>
			</tr>
		</table>

			<?php
			if ( (bool) ( $i % 2 ) ) {
				echo '</td></tr></table><table cellpadding="0" cellspacing="0" class="o-fix"><tr><td width="552" valign="top" align="center" style="border-top:1px solid #ccc;">'; }
			?>

		<?php endforeach; ?>

		<?php if ( $count > $limit ) : ?>

			<?php $link = admin_url( 'edit.php?post_type=newsletter&page=mailster_subscribers&since=' . date( 'Y-m-d', $timestamp ) ); ?>

			<table cellpadding="0" cellspacing="0" align="<?php echo ! ( $i % 2 ) ? 'left' : 'right'; ?>">
				<tr>
					<td width="264" valign="top" align="left" class="m-b">
					<table style="width:100%;table-layout:fixed">
					<tr><td width="80">&nbsp;</td><td>&nbsp;</td></tr>
					<tr>
					<td valign="center" align="center" width="80">
						<a href="<?php echo $link; ?>">
							<div style="border-radius:50%;width:60px;height:60px;background-color:#fafafa"></div>
						</a>
					</td>
					<td valign="center" align="left">
						<h4 style="margin:0;"><a href="<?php echo $link; ?>"><?php printf( esc_html__( _n( '%s other', '%s others', $count - $limit, 'mailster' ) ), number_format_i18n( $count - $limit ) ); ?></a></h5>
					</td>
					</tr>
					<tr><td width="80">&nbsp;</td><td>&nbsp;</td></tr>
					</table>
					</td>
				</tr>
			</table>

		<?php endif; ?>

		</td>

	</tr>
	</table>
		<?php
	}
}
