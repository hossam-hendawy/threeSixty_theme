<?php

class MailsterBounceHandler {

	public $mailbox;
	public $bounce_delete;
	public $MID;
	public $service;
	protected $max_email_at_once = 100;

	/**
	 *
	 *
	 * @param unknown $service (optional)
	 */
	public function __construct( $service = 'pop3' ) {

		$this->bounce_delete = mailster_option( 'bounce_delete' );
		$this->MID           = mailster_option( 'ID' );
		$this->service       = $service;
	}



	/**
	 *
	 *
	 * @param unknown $server
	 * @param unknown $user
	 * @param unknown $pwd
	 * @param unknown $port    (optional)
	 * @param unknown $secure  (optional)
	 * @param unknown $timeout (optional)
	 * @return unknown
	 */
	public function connect( $server, $user, $pwd, $port = 110, $secure = false, $timeout = 60 ) {

		$securepath = '';
		if ( $secure ) {
			$securepath = '/' . $secure;
		}

		$path = '{' . $server . ':' . $port . '/' . $this->service . $securepath . '/novalidate-cert}INBOX';

		require_once MAILSTER_DIR . 'classes/libs/PhpImap/__autoload.php';

		try {

			imap_timeout( IMAP_OPENTIMEOUT, (int) $timeout );
			imap_timeout( IMAP_READTIMEOUT, (int) $timeout );

			$this->mailbox = new PhpImap\Mailbox( $path, $user, $pwd );
			$this->mailbox->checkMailbox();

		} catch ( Exception $e ) {

			return new WP_Error( 'connect_error', $e->getMessage() );

		}

		return true;
	}


	public function process_bounces() {

		$messages = $this->get_messages();

		require_once MAILSTER_DIR . 'classes/libs/bounce/bounce_driver.class.php';

		foreach ( $messages as $id => $message ) {

			$bouncehandler = new Bouncehandler();
			$bounceresult  = $bouncehandler->parse_email( $message );

			// not a bounce message we can handle
			if ( empty( $bounceresult ) ) {
				continue;
			}

			$bounceresult = (object) $bounceresult[0];
			$action       = $bounceresult->action;
			$status       = $bounceresult->status;

			preg_match( '#X-(Mailster|MyMail): ([a-f0-9]{32})#i', $message, $hash );
			preg_match( '#X-(Mailster|MyMail)-Campaign: ([0-9-]+)#i', $message, $camp );

			// no hash or campaign found
			if ( ! $hash || ! $camp ) {
				continue;
			}

			$subscriber     = mailster( 'subscribers' )->get_by_hash( $hash[2], false );
			$campaign_index = 0;

			// get the campaign index
			if ( false !== strpos( $camp[2], '-' ) ) {
				$campaign_index = absint( strrchr( $camp[2], '-' ) );
			}

			$campaign = ! empty( $camp ) ? mailster( 'campaigns' )->get( (int) $camp[2] ) : null;

			if ( $subscriber ) {

				$campaign_id = $campaign ? $campaign->ID : 0;
				switch ( $action ) {
					case 'success':
						break;

					case 'unsubscribe':
						// unsubscribe
						mailster( 'subscribers' )->unsubscribe( $subscriber->ID, $campaign_id, 'list_unsubscribe', $campaign_index );
						break;
					case 'failed':
						// hardbounce
						mailster( 'subscribers' )->bounce( $subscriber->ID, $campaign_id, true, $status, $campaign_index );
						break;

					case 'transient':
					default:
						// softbounce
						mailster( 'subscribers' )->bounce( $subscriber->ID, $campaign_id, false, $status, $campaign_index );

				}
			}

			// remove bounce message
			$this->delete_message( $id );

		}
	}


	/**
	 *
	 *
	 * @param unknown $id
	 */
	protected function delete_message( $id ) {
		$this->mailbox->deleteMail( $id );
		$this->mailbox->expungeDeletedMails();
	}


	/**
	 *
	 *
	 * @param unknown $all (optional)
	 * @return unknown
	 */
	protected function get_messages( $all = false ) {

		$mailsIds = $this->mailbox->searchMailbox();

		$messages = array();

		foreach ( $mailsIds as $i => $id ) {

			$mail = $this->mailbox->getMail( $id );

			$message = $mail->textPlain;

			if ( $all || preg_match( '#X-(Mailster|MyMail)-ID: ' . preg_quote( $this->MID ) . '#i', $message ) ) {

				$messages[ $id ] = $this->mailbox->getRawMail( $id, false );

			} elseif ( $this->bounce_delete ) {

				$this->delete_message( $id );

			}
		}

		return $messages;
	}


	/**
	 *
	 *
	 * @param unknown $identifier
	 * @return unknown
	 */
	public function check_bounce_message( $identifier ) {

		$messages = $this->get_messages( true );

		foreach ( $messages as $id => $message ) {

			if ( false !== strpos( $message, $identifier ) ) {
				$this->delete_message( $id );
				return true;
				break;
			}
		}

		return false;
	}
}


class MailsterBounceLegacyHandler extends MailsterBounceHandler {

	public $msgcount = 0;

	/**
	 *
	 *
	 * @param unknown $server
	 * @param unknown $user
	 * @param unknown $pwd
	 * @param unknown $port    (optional)
	 * @param unknown $secure  (optional)
	 * @param unknown $timeout (optional)
	 * @return unknown
	 */
	public function connect( $server, $user, $pwd, $port = 110, $secure = false, $timeout = 60 ) {

		require_once ABSPATH . WPINC . '/class-pop3.php';
		$this->mailbox          = new POP3();
		$this->mailbox->TIMEOUT = (int) $timeout;

		if ( $secure ) {
			$server = $secure . '://' . $server;
		}

		$this->mailbox->connect( $server, $port );

		if ( ! empty( $this->mailbox->ERROR ) ) {
			return new WP_Error( 'connect_error', $this->mailbox->ERROR );
		}

		$this->mailbox->user( $user );

		if ( ! empty( $this->mailbox->ERROR ) ) {
			return new WP_Error( 'connect_error_user', $this->mailbox->ERROR );
		}

		$this->msgcount = $this->mailbox->pass( $pwd );

		if ( ! empty( $this->mailbox->ERROR ) ) {
			return new WP_Error( 'connect_error_user', $this->mailbox->ERROR );
		}

		if ( false === $this->msgcount ) {

			$this->msgcount = 0;
		}
	}


	public function __destruct() {
		if ( $this->mailbox ) {
			$this->mailbox->quit();
		}
	}


	/**
	 *
	 *
	 * @param unknown $id
	 */
	protected function delete_message( $id ) {
		$this->mailbox->delete( $id );
	}


	/**
	 *
	 *
	 * @param unknown $all (optional)
	 * @return unknown
	 */
	protected function get_messages( $all = false ) {

		$messages = array();

		// start with the last (most recent one) and only process the last $max_email_at_once
		for ( $i = $this->msgcount; $i > $this->msgcount - $this->max_email_at_once; $i-- ) {

			$message = $this->mailbox->get( $i );

			if ( ! $message ) {
				if ( $this->bounce_delete ) {
					$this->delete_message( $i );
				}

				continue;
			}

			$message = implode( $message );

			if ( $all || preg_match( '#X-(Mailster|MyMail)-ID: ' . preg_quote( $this->MID ) . '#i', $message ) ) {

				$messages[ $i ] = $message;

			} elseif ( $this->bounce_delete ) {

				$this->delete_message( $i );

			}
		}

		return $messages;
	}
}
