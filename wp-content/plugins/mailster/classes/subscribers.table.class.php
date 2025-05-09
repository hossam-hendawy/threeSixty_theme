<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Mailster_Subscribers_Table extends WP_List_Table {

	public $total_items;
	public $total_pages;
	public $per_page;

	private $post_type;
	private $page;
	private $paged;
	private $status;
	private $lists;
	private $search;
	private $search_fields;
	private $conditions;
	private $orderby;
	private $order;
	private $since;
	private $strict;

	public function __construct() {

		parent::__construct(
			array(
				'singular' => esc_html__( 'Subscriber', 'mailster' ), // singular name of the listed records
				'plural'   => esc_html__( 'Subscribers', 'mailster' ), // plural name of the listed records
				'ajax'     => false, // does this table support ajax?
			)
		);

		add_action( 'admin_footer', array( &$this, 'script' ) );
		add_filter( 'manage_newsletter_page_mailster_subscribers_columns', array( &$this, 'get_columns' ) );

		$this->post_type     = isset( $_GET['post_type'] ) ? $_GET['post_type'] : null;
		$this->page          = isset( $_GET['page'] ) ? $_GET['page'] : null;
		$this->paged         = isset( $_GET['paged'] ) ? (int) $_GET['paged'] - 1 : null;
		$this->status        = isset( $_GET['status'] ) ? (int) $_GET['status'] : false;
		$this->lists         = isset( $_GET['lists'] ) ? array_filter( (array) $_GET['lists'], 'is_numeric' ) : false;
		$this->search        = isset( $_GET['s'] ) ? stripslashes( $_GET['s'] ) : null;
		$this->search_fields = isset( $_GET['fields'] ) ? stripslashes( $_GET['fields'] ) : false;
		$this->strict        = isset( $_GET['strict'] ) ? $_GET['strict'] : null;
		$this->conditions    = isset( $_GET['conditions'] ) ? (array) $_GET['conditions'] : null;
		$this->orderby       = isset( $_GET['orderby'] ) ? $_GET['orderby'] : 'id';
		$this->order         = isset( $_GET['order'] ) ? $_GET['order'] : 'DESC';
		$this->since         = isset( $_GET['since'] ) ? strtotime( $_GET['since'] ) : null;
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_views() {

		// return array();
		$query = array(
			'conditions'    => $this->conditions,
			'lists'         => $this->lists,
			'search_fields' => $this->search_fields,
			's'             => $this->search,
		);

		$counts = mailster( 'subscribers' )->get_counts_by_status( $query );

		$total = array_sum( $counts );
		// remove deleted from all
		if ( $this->status != 5 && isset( $counts[5] ) ) {
			$total -= $counts[5];
		}
		$statuses = mailster( 'subscribers' )->get_status( null, true );
		$views    = array(
			'view-status-all' => '<a href="' . remove_query_arg( 'status' ) . '" class="' . ( $this->status === null ? 'current' : '' ) . '">' . esc_html__( 'All', 'mailster' ) . ' <span class="count">(' . number_format_i18n( $total ) . ')</span></a>',
		);

		foreach ( $statuses as $id => $status ) {
			if ( $count = isset( $counts[ $id ] ) ? $counts[ $id ] : 0 ) {
				$views[ 'view-status-' . $id ] = '<a href="' . add_query_arg( array( 'status' => $id ) ) . '" class="' . ( $this->status === $id ? 'current' : '' ) . '">' . esc_html( $status ) . ' <span class="count">(' . number_format_i18n( $count ) . ')</span></a>';
			}
		}

		return $views;
	}


	public function script() {
	}


	public function no_items() {

		esc_html_e( 'No subscribers found', 'mailster' );

		if ( current_user_can( 'mailster_add_subscribers' ) ) {
			echo ' <a href="edit.php?post_type=newsletter&page=mailster_subscribers&new">' . esc_html__( 'Add New', 'mailster' ) . '</a>';
		}
	}


	/**
	 *
	 *
	 * @param unknown $text
	 * @param unknown $input_id
	 */
	public function search_box( $text, $input_id ) {

		?>

		<?php if ( 5 == $this->status ) : ?>
		<div class="notice notice-error error">
			<p><strong><?php printf( esc_html__( 'These subscribers are marked as "deleted" and will be removed automatically after %d days at the earliest.', 'mailster' ), 14 ); ?></strong></p>
		</div>
		<?php endif; ?>

	<form id="searchform" action method="get">

		<?php if ( $this->conditions ) : ?>
		<div id="mailster-filter-wrap" class="align-right clear">
			<div id="mailster-conditions-render">
				<?php mailster( 'conditions' )->render( $_GET['conditions'] ); ?>
			<a href="<?php echo remove_query_arg( 'conditions' ); ?>" class="button button-small"><?php esc_html_e( 'Clear Filters', 'mailster' ); ?></a>
			<a href="<?php echo add_query_arg( array( 'conditions' => $this->conditions ), admin_url( 'post-new.php?post_type=newsletter' ) ); ?>" class="button button-small"><?php esc_html_e( 'Create Campaign', 'mailster' ); ?></a>
			</div>
		</div>

	<?php endif; ?>
		<?php if ( $this->post_type ) : ?>
			<input type="hidden" name="post_type" value="<?php echo esc_attr( $this->post_type ); ?>">
		<?php endif; ?>
		<?php if ( $this->page ) : ?>
			<input type="hidden" name="page" value="<?php echo esc_attr( $this->page ); ?>">
		<?php endif; ?>
		<?php if ( $this->paged ) : ?>
			<input type="hidden" name="_paged" value="<?php echo esc_attr( $this->paged ); ?>">
		<?php endif; ?>
		<?php if ( $this->status ) : ?>
			<input type="hidden" name="status" value="<?php echo esc_attr( $this->status ); ?>">
		<?php endif; ?>
		<?php if ( $this->search_fields ) : ?>
			<input type="hidden" name="search_fields" value="<?php echo esc_attr( $this->search_fields ); ?>">
		<?php endif; ?>
		<?php if ( $this->lists ) : ?>
			<?php foreach ( $this->lists as $list_id ) : ?>
				<input type="hidden" name="lists[]" value="<?php echo esc_attr( $list_id ); ?>">
			<?php endforeach ?>
		<?php endif; ?>

		<p class="search-box">
			<label class="screen-reader-text" for="sa-search-input"><?php echo esc_html( $text ); ?></label>
			<input type="search" id="<?php echo $input_id; ?>" name="s" value="<?php echo esc_attr( $this->search ); ?>">
			<input type="submit" name="" id="search-submit" class="button" value="<?php echo esc_attr( $text ); ?>">
			<a id="filter" class="button"><?php esc_html_e( 'Filter', 'mailster' ); ?></a>
		</p>

	</form>

	<div id="mailster-subscriber-conditions" style="display:none;">
		<div class="mailster-subscriber-conditions-thickbox">
			<div class="inner">
				<?php mailster( 'conditions' )->view( $this->conditions ); ?>
			</div>
			<div class="foot">
				<button class="button button-primary" id="apply-filter"><?php esc_html_e( 'Filter', 'mailster' ); ?></button>
				<button class="button" id="close-filter"><?php esc_html_e( 'Close', 'mailster' ); ?></button>
			</div>
		</div>
	</div>
		<?php
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_columns() {
		return mailster( 'subscribers' )->get_columns();
	}


	/**
	 *
	 *
	 * @param unknown $item
	 * @param unknown $column_name
	 * @return unknown
	 */
	private function searchmark( $string, $search = null ) {

		if ( ! is_string( $string ) ) {
			return $string;
		}

		if ( is_null( $search ) && $this->search ) {
			$search = stripslashes( $this->search );
		}

		if ( empty( $search ) ) {
			return esc_html( $string );
		}

		foreach ( explode( ' ', $search ) as $term ) {
			$term   = str_replace( array( '+', '-', '"', '*', '?' ), '', $term );
			$string = preg_replace( '/(' . preg_quote( $term ) . ')/i', '<span class="highlight wp-ui-text-highlight">$1</span>', $string );
		}

		return $string;
	}

	/**
	 *
	 *
	 * @param unknown $item
	 * @param unknown $column_name
	 * @return unknown
	 */
	public function column_default( $item, $column_name ) {

		$data = mailster( 'subscribers' )->get_custom_fields( $item->ID );

		switch ( $column_name ) {

			case 'name':
				if ( get_option( 'show_avatars' ) ) {
					$initials = trim( preg_replace( '/[^A-Z]+/', '', ucwords( $data['fullname'] ) ) );
					$avatar   = '<div class="mailster-avatar"><a href="' . admin_url( 'edit.php?post_type=newsletter&page=mailster_subscribers&ID=' . $item->ID ) . '"><span class="mailster-avatar-40' . ( $item->wp_id ? ' wp-user' : '' ) . '" style="background-image:url(' . mailster( 'subscribers' )->get_gravatar_uri( $item->email, 80 ) . ')"></span></a></div>';
				} else {
					$avatar = '';
				}

				if ( $data['fullname'] ) {
					$html = '<a class="name" href="' . admin_url( 'edit.php?post_type=newsletter&page=mailster_subscribers&ID=' . $item->ID ) . '">' . $this->searchmark( $data['fullname'] ) . '</a><br><a class="email" href="' . admin_url( 'edit.php?post_type=newsletter&page=mailster_subscribers&ID=' . $item->ID ) . '" title="' . esc_attr( $item->{'email'} ) . '">' . $this->searchmark( $item->{'email'} ) . '</a>';
				} else {
					$html = '<a class="name" href="' . admin_url( 'edit.php?post_type=newsletter&page=mailster_subscribers&ID=' . $item->ID ) . '" title="' . $item->{'email'} . '">' . $this->searchmark( $item->{'email'} ) . '</a><br><span class="email">&nbsp;</span>';
				}

				$stars = ( round( $item->rating / 10, 2 ) * 50 );
				$full  = max( 0, min( 5, floor( $stars ) ) );
				$half  = max( 0, min( 5, round( $stars - $full ) ) );
				$empty = max( 0, min( 5, 5 - $full - $half ) );

				$userrating = '<div class="userrating" title="' . ( $item->rating * 100 ) . '%">'
				. str_repeat( '<span class="mailster-icon mailster-icon-star"></span>', $full )
				. str_repeat( '<span class="mailster-icon mailster-icon-star-half"></span>', $half )
				. str_repeat( '<span class="mailster-icon mailster-icon-star-empty"></span>', $empty ) . '</div>';

				return '<div class="table-data">' . $avatar . '<div class="mailster-name">' . $html . $userrating . '</div></div>';

			case 'lists':
				$lists = mailster( 'subscribers' )->get_lists( $item->ID );
				$tags  = mailster( 'subscribers' )->get_tags( $item->ID );

				$lists_get = isset( $_GET['lists'] ) ? (array) $_GET['lists'] : array();

				$list_elements = array();
				$tag_elements  = array();

				foreach ( $lists as $i => $list ) {
					$list_elements[] = '<a href="edit.php?post_type=newsletter&page=mailster_lists&ID=' . $list->ID . '" title="' . ( $list->confirmed ? sprintf( esc_attr__( 'confirmed %s ago', 'mailster' ), human_time_diff( $list->confirmed ) ) : esc_attr__( 'not confirmed', 'mailster' ) ) . '" class="' . ( $list->confirmed ? 'confirmed' : 'not-confirmed' ) . '">' . ( in_array( $list->ID, $lists_get ) ? '<span class="highlight wp-ui-text-highlight">' . esc_html( $list->name ) . '</span>' : esc_html( $list->name ) ) . '</a>';
				}
				foreach ( $tags as $i => $tag ) {
					$tag_elements[] = '<span class="mailster-tag">' . esc_html( $tag->name ) . '</span>';
				}
				return '<div class="table-data">' . implode( ', ', $list_elements ) . '</div><div class="table-data">' . implode( ' ', $tag_elements ) . '</div>';

			case 'emails':
				return '<div class="table-data">' . number_format_i18n( mailster( 'subscribers' )->get_sent( $item->ID, true ) ) . '</div>';

			case 'status':
				return '<div class="table-data"><span class="nowrap tiny">' . mailster( 'subscribers' )->get_status( $item->{$column_name}, true ) . '</span></div>';

			case 'signup':
				$timestring = ( ! $item->{$column_name} ) ? esc_html__( 'unknown', 'mailster' ) : date_i18n( mailster( 'helper' )->timeformat(), $item->{$column_name} + mailster( 'helper' )->gmt_offset( true ) );
				return '<div class="table-data">' . $this->searchmark( $timestring ) . '</div>';

			default:
				$custom_fields = mailster()->get_custom_fields();
				if ( in_array( $column_name, array_keys( $custom_fields ) ) ) {

					$value = mailster( 'subscribers' )->get_custom_field( $item->ID, $column_name );

					switch ( $custom_fields[ $column_name ]['type'] ) {
						case 'checkbox':
							return '<div class="table-data">' . ( $value ? '&#10004;' : '&#10005;' ) . '</div>';
						break;
						case 'date':
							return '<div class="table-data">' . $this->searchmark( $value ? date_i18n( mailster( 'helper' )->dateformat(), strtotime( $value ) ) : '' ) . '</div>';
						break;
						default:
							return '<div class="table-data">' . $this->searchmark( $value ) . '</div>';
					}
				}
				return print_r( $item, true ); // Show the whole array for troubleshooting purposes
		}
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'name'   => array( 'name', false ),
			'status' => array( 'status', false ),
			'signup' => array( 'signup', false ),

		);
		$custom_fields = mailster()->get_custom_fields();
		foreach ( $custom_fields as $key => $field ) {
			$sortable_columns[ $key ] = array( $key, false );
		}
		return $sortable_columns;
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_bulk_actions() {
		$actions = array(
			'empty_trash'    => esc_html__( 'Permanently Delete', 'mailster' ),
			'restore'        => esc_html__( 'Restore', 'mailster' ),
			'delete'         => esc_html__( 'Delete', 'mailster' ),
			'delete_actions' => esc_html__( 'Delete (with Activities)', 'mailster' ),
			'send_campaign'  => esc_html__( 'Send new Campaign', 'mailster' ),
			'confirmation'   => esc_html__( 'Resend Confirmation', 'mailster' ),
			'verify'         => esc_html__( 'Verify', 'mailster' ),
		);

		if ( ! current_user_can( 'mailster_delete_subscribers' ) || 5 == $this->status ) {
			unset( $actions['delete'] );
			unset( $actions['delete_actions'] );
		}
		if ( 5 != $this->status ) {
			unset( $actions['empty_trash'] );
			unset( $actions['restore'] );
		}

		return $actions;
	}


	/**
	 *
	 *
	 * @param unknown $which (optional)
	 */
	public function bulk_actions( $which = '' ) {

		ob_start();
		parent::bulk_actions( $which );
		$actions = ob_get_contents();
		ob_end_clean();

		$status  = '<option value="pending">&#x2514; ' . esc_html__( 'pending', 'mailster' ) . '</option>';
		$status .= '<option value="subscribed">&#x2514; ' . esc_html__( 'subscribed', 'mailster' ) . '</option>';
		$status .= '<option value="unsubscribed">&#x2514; ' . esc_html__( 'unsubscribed', 'mailster' ) . '</option>';

		$actions = str_replace( '</select>', '<optgroup label="' . esc_html__( 'change status', 'mailster' ) . '">' . $status . '</optgroup></select>', $actions );

		$lists = mailster( 'lists' )->get();

		if ( empty( $lists ) ) {
			echo $actions;
			return;
		}

		$add       = '<option value="add_list_all">&nbsp;' . esc_html__( 'add to all', 'mailster' ) . '</option>';
		$remove    = '<option value="remove_list_all">&nbsp;' . esc_html__( 'remove from all', 'mailster' ) . '</option>';
		$confirm   = '<option value="confirm_list_all">&nbsp;' . esc_html__( 'confirm all', 'mailster' ) . '</option>';
		$unconfirm = '<option value="unconfirm_list_all">&nbsp;' . esc_html__( 'unconfirm all', 'mailster' ) . '</option>';
		foreach ( $lists as $list ) {
			$add       .= '<option value="add_list_' . $list->ID . '">' . ( $list->parent_id ? '&nbsp;' : '' ) . '&#x2514; ' . $list->name . '</option>';
			$remove    .= '<option value="remove_list_' . $list->ID . '">' . ( $list->parent_id ? '&nbsp;' : '' ) . '&#x2514; ' . $list->name . '</option>';
			$confirm   .= '<option value="confirm_list_' . $list->ID . '">' . ( $list->parent_id ? '&nbsp;' : '' ) . '&#x2514; ' . $list->name . '</option>';
			$unconfirm .= '<option value="unconfirm_list_' . $list->ID . '">' . ( $list->parent_id ? '&nbsp;' : '' ) . '&#x2514; ' . $list->name . '</option>';
		}

		echo str_replace( '</select>', '<optgroup label="' . esc_html__( 'add to list', 'mailster' ) . '">' . $add . '</optgroup><optgroup label="' . esc_html__( 'remove from list', 'mailster' ) . '">' . $remove . '</optgroup><optgroup label="' . esc_html__( 'confirm list', 'mailster' ) . '">' . $confirm . '</optgroup><optgroup label="' . esc_html__( 'unconfirm list', 'mailster' ) . '">' . $unconfirm . '</optgroup></select>', $actions );

		if ( 5 == $this->status ) {
			echo submit_button( __( 'Permanently remove all deleted Subscribers', 'mailster' ), 'action', 'empty_trash', false );
		}
	}


	/**
	 *
	 *
	 * @param unknown $which (optional)
	 */
	public function extra_tablenav( $which = '' ) {}


	/**
	 *
	 *
	 * @param unknown $item
	 * @return unknown
	 */
	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="subscribers[]" value="%s" class="subscriber_cb" />',
			$item->ID
		);
	}


	/**
	 *
	 *
	 * @param unknown $current_mode
	 * @return unknown
	 */
	public function view_switcher( $current_mode ) {
		return '';
	}


	/**
	 *
	 *
	 * @param unknown $domain  (optional)
	 * @param unknown $post_id (optional)
	 */
	public function prepare_items( $domain = null, $post_id = null ) {

		global $wpdb;
		$screen        = get_current_screen();
		$columns       = $this->get_columns();
		$hidden        = get_hidden_columns( $screen );
		$sortable      = $this->get_sortable_columns();
		$custom_fields = mailster()->get_custom_fields();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$args = array(
			'status'     => $this->status,
			's'          => $this->search,
			'strict'     => $this->strict,
			'lists'      => $this->lists,
			'conditions' => $this->conditions,
		);

		// How many to display per page?
		if ( ! ( $this->per_page = (int) get_user_option( 'mailster_subscribers_per_page' ) ) ) {
			$this->per_page = 50;
		}

		$fields = array( 'ID', 'email', 'rating', 'wp_id', 'status', 'signup' );

		// $offset  = isset( $_GET['paged'] ) ? ( (int) $_GET['paged'] - 1 ) * $this->per_page : 0;
		$offset = $this->paged * $this->per_page;

		$orderby       = $this->orderby;
		$order         = $this->order;
		$since         = $this->since;
		$search_fields = $this->search_fields;

		if ( isset( $custom_fields[ $orderby ] ) ) {
			$fields[] = $orderby;
		}

		if ( $since ) {
			$args['conditions'][] = array(
				'field'    => 'signup',
				'operator' => '>',
				'value'    => $since,
			);
		}

		switch ( $orderby ) {
			case 'name':
			case 'lastname':
				$orderby  = array( 'lastname', 'firstname' );
				$fields[] = 'fullname';
				break;
			case 'firstname':
				$orderby  = array( 'firstname', 'lastname' );
				$fields[] = 'fullname';
				break;
		}

		$args = wp_parse_args(
			$args,
			array(
				'calc_found_rows' => true,
				'orderby'         => $orderby,
				'order'           => $order,
				'fields'          => $fields,
				'limit'           => $this->per_page,
				'search_fields'   => $search_fields,
				'offset'          => $offset,
			)
		);

		$items = mailster( 'subscribers' )->query( $args );

		$this->items       = $items;
		$this->total_items = $wpdb->get_var( 'SELECT FOUND_ROWS();' );
		$this->total_pages = ceil( $this->total_items / $this->per_page );

		$this->set_pagination_args(
			array(
				'total_items' => $this->total_items,
				'total_pages' => $this->total_pages,
				'per_page'    => $this->per_page,
			)
		);
	}
}
