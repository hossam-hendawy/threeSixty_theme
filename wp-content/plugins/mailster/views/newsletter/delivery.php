<?php

$now = time();

$editable = ! in_array( $post->post_status, array( 'active', 'finished' ) );
if ( isset( $_GET['showstats'] ) && $_GET['showstats'] ) {
	$editable = false;
}

$is_autoresponder = 'autoresponder' == $post->post_status || $this->post_data['autoresponder'];

$timestamp = ( ! empty( $this->post_data['timestamp'] ) ) ? $this->post_data['timestamp'] : $now + ( 60 * mailster_option( 'send_offset' ) );

$timestamp = ( ! $this->post_data['active'] ) ? max( $now + ( 60 * mailster_option( 'send_offset' ) ), $timestamp ) : $timestamp;

$dateformat = mailster( 'helper' )->dateformat();
$timeformat = mailster( 'helper' )->timeformat();
$timeoffset = mailster( 'helper' )->gmt_offset( true );

$current_user = wp_get_current_user();

$sent = $this->get_sent( $post->ID );

?>
<?php if ( $editable ) : ?>

	<?php if ( current_user_can( 'mailster_edit_autoresponders' ) ) : ?>
		<ul class="category-tabs">
			<li<?php echo ! $is_autoresponder ? ' class="tabs"' : ''; ?>>
				<a href="#regular-campaign"><?php esc_html_e( 'Regular Campaign', 'mailster' ); ?></a>
			</li>
			<li<?php echo $is_autoresponder ? ' class="tabs"' : ''; ?>>
				<a href="#autoresponder"><?php esc_html_e( 'Auto Responder', 'mailster' ); ?></a>
			</li>
		</ul>
		<div id="regular-campaign" class="tabs-panel" <?php echo ( $is_autoresponder ) ? ' style="display:none"' : ''; ?>>
	<?php endif; ?>
	<p class="howto" title="<?php echo date( $timeformat, $now + $timeoffset ); ?>">
	<?php
		printf(
			esc_html__( 'Server time: %1$s %2$s', 'mailster' ),
			'<span title="' . date( $timeformat, $now + $timeoffset ) . '">' . date( $dateformat, $now + $timeoffset ) . '</span>',
			'<span class="time" data-timestamp="' . ( $now + $timeoffset ) . '"></span>'
		);

	elseif ( 'finished' == $post->post_status ) :

		printf(
			esc_html__( 'This campaign has been sent on %s.', 'mailster' ),
			'<strong>' . date( $timeformat, $this->post_data['finished'] + $timeoffset ) . '</strong>'
		);

	endif;
	?>
	</p>
<?php if ( $editable ) : ?>
<label>
	<input name="mailster_data[active]" id="mailster_data_active" value="1" type="checkbox" <?php echo ( $this->post_data['active'] && ! $is_autoresponder ) ? 'checked' : ''; ?> <?php echo ( ! $editable ) ? ' disabled' : ''; ?>>
	<?php esc_html_e( 'send this campaign', 'mailster' ); ?>
</label>
	<div class="active_wrap<?php echo $this->post_data['active'] && ! $is_autoresponder ? ' disabled' : ''; ?>">
		<div class="active_overlay"></div>
		<?php
			esc_html_e( 'on', 'mailster' );
			echo ' <input name="mailster_data[date]" class="datepicker deliverydate inactive" type="datetime-local" value="' . date( 'Y-m-d H:i', $timestamp + $timeoffset ) . '" _min="' . date( 'Y-m-d\TH:i', time() + $timeoffset ) . '" maxlength="10" readonly' . ( ( ( ! $this->post_data['active'] && ! $is_autoresponder ) || ! $editable ) ? ' disabled' : '' ) . '>';
		?>
		<?php if ( mailster_option( 'track_location' ) ) : ?>
			<p><label title="<?php esc_attr_e( 'Send this campaign based on the subscribers timezone if known', 'mailster' ); ?>">
			<input type="checkbox" class="timezone" name="mailster_data[timezone]" value="1" <?php checked( $this->post_data['timezone'] ); ?>> <?php esc_html_e( 'Use Subscribers timezone', 'mailster' ); ?>
			</label><?php echo mailster()->beacon( array( '63fb2e7c52af714471a1738a' ) ); ?>
			</p>
		<?php endif; ?>
	</div>
	<?php
	if ( $sent && ! $is_autoresponder ) :

		$totals = $this->get_totals( $post->ID );
		$p      = round( $this->get_sent_rate( $post->ID ) * 100 );
		$pg     = sprintf( esc_html__( '%1$s of %2$s sent', 'mailster' ), number_format_i18n( $sent ), number_format_i18n( $totals ) );
		?>
		<p>
			<div class="progress paused">
				<span class="bar" style="width:<?php echo esc_attr( $p ); ?>%"><span>&nbsp;<?php echo esc_attr( $pg ); ?></span></span><span>&nbsp;<?php echo esc_attr( $pg ); ?></span><var><?php echo esc_attr( $p ); ?>%</var>
			</div>
		</p>
	<?php endif; ?>

	<?php if ( current_user_can( 'mailster_edit_autoresponders' ) ) : ?>
</div>
<div id="autoresponder" class="tabs-panel"<?php echo ! $is_autoresponder ? ' style="display:none"' : ''; ?>>
		<?php
		$autoresponderdata = wp_parse_args(
			$this->post_data['autoresponder'],
			array(
				'operator'          => '',
				'action'            => 'mailster_subscriber_insert',
				'unit'              => '',
				'before_after'      => 1,
				'userunit'          => 'day',
				'uservalue'         => '',
				'userexactdate'     => false,
				'timestamp'         => $now,
				'endtimestamp'      => $now,
				'weekdays'          => array(),
				'post_type'         => 'post',
				'time_post_type'    => 'post',
				'time_post_count'   => 1,
				'post_count'        => 0,
				'post_count_status' => 0,
				'issue'             => 1,
				'since'             => false,
				'interval'          => 1,
				'time_frame'        => 'day',
				'timezone'          => false,
				'hook'              => '',
				'hook_type'         => false,
				'priority'          => 10,
				'once'              => false,
				'multiple'          => false,
				'followup_action'   => 1,
			)
		);

		include_once MAILSTER_DIR . 'includes/autoresponder.php';
		?>
	<label>
		<input name="mailster_data[active_autoresponder]" id="mailster_data_autoresponder_active" value="1" type="checkbox" <?php checked( ( $this->post_data['active'] && $is_autoresponder ), true ); ?> <?php echo ( ! $editable ) ? ' disabled' : ''; ?>> <?php esc_html_e( 'send this auto responder', 'mailster' ); ?>
	</label>

	<div id="autoresponder_wrap" class="autoresponder-<?php echo $autoresponderdata['action']; ?>">
		<div class="autoresponder_active_wrap<?php echo $this->post_data['active'] && $is_autoresponder ? ' disabled' : ''; ?>">
			<div class="autoresponder_active_overlay"></div>
		<p class="autoresponder_time">
		<input type="text" class="small-text" name="mailster_data[autoresponder][amount]" value="<?php echo isset( $autoresponderdata['amount'] ) ? $autoresponderdata['amount'] : 1; ?>">
			<select name="mailster_data[autoresponder][unit]">
			<?php
			foreach ( $mailster_autoresponder_info['units'] as $value => $name ) {
				echo '<option value="' . $value . '"' . selected( $autoresponderdata['unit'], $value, false ) . '>' . $name . '</option>';
			}
			?>
			</select>
			<span class="autoresponder_after"><?php esc_html_e( 'after', 'mailster' ); ?></span>
			<span class="autoresponder_before"><?php esc_html_e( 'before', 'mailster' ); ?></span>
			<select class="autoresponder_before_after" name="mailster_data[autoresponder][before_after]">
				<option value="1" <?php selected( $autoresponderdata['before_after'], 1 ); ?>><?php esc_html_e( 'after', 'mailster' ); ?></option>
				<option value="-1" <?php selected( $autoresponderdata['before_after'], -1 ); ?>><?php esc_html_e( 'before', 'mailster' ); ?></option>
			</select>
		</p>
		<p>
			<select class="widefat" name="mailster_data[autoresponder][action]" id="mailster_autoresponder_action">
			<?php
			foreach ( $mailster_autoresponder_info['actions'] as $id => $action ) {
				echo '<option value="' . $id . '"' . selected( $autoresponderdata['action'], $id, false ) . '>' . $action['label'] . '</option>';
			}
			?>
			</select>
		</p>

		<div class="mailster_autoresponder_more autoresponderfield-mailster_subscriber_insert autoresponderfield-mailster_subscriber_unsubscribed">
			<?php echo mailster()->beacon( array( '611bb908f886c9486f8d9925' ) ); ?>
			<p>
			<span class="mailster_autoresponder_more autoresponderfield-mailster_subscriber_insert">
			<?php esc_html_e( 'only for subscribers who signed up', 'mailster' ); ?>
			</span>
			<span class="mailster_autoresponder_more autoresponderfield-mailster_subscriber_unsubscribed">
			<?php esc_html_e( 'only for subscribers who canceled their subscription', 'mailster' ); ?>
			</span>
			<?php
			esc_html_e( 'after', 'mailster' );
			$timestamp = $this->post_data['timestamp'] ? $this->post_data['timestamp'] : $now;

			esc_html_e( 'on', 'mailster' );
			echo ' <input name="mailster_data[autoresponder_signup_date]" class="datepicker deliverydate inactive nolimit" type="datetime-local" value="' . date( 'Y-m-d H:i', $timestamp + $timeoffset ) . '" maxlength="10" readonly>';

			?>
			</p>
		</div>
		<div class="mailster_autoresponder_more autoresponderfield-mailster_subscriber_unsubscribed">
			<p class="howto">
				<?php esc_html_e( 'Keep in mind it is bad practice to send campaigns after subscribers opt-out so use this option for "Thank you" messages or surveys.', 'mailster' ); ?>
			</p>
		</div>

			<?php $pts = mailster( 'helper' )->get_post_types( true, 'object' ); ?>

		<div class="mailster_autoresponder_more autoresponderfield-mailster_post_published">
			<?php echo mailster()->beacon( array( '611bb8eab37d837a3d0e47b8' ) ); ?>
			<p>
				<?php
				$count = '<input type="number" name="mailster_data[autoresponder][post_count]" class="small-text" value="' . $autoresponderdata['post_count'] . '">';
				$type  = '<select id="autoresponder-post_type" name="mailster_data[autoresponder][post_type]">';
				foreach ( $pts as $pt => $data ) {
					$type .= '<option value="' . $pt . '"' . selected( $autoresponderdata['post_type'], $pt, false ) . '>' . $data->labels->singular_name . '</option>';
				}
				$type .= '<option value="rss"' . selected( $autoresponderdata['post_type'], 'rss', false ) . '>' . esc_html__( 'RSS Feed', 'mailster' ) . '</option>';
				$type .= '</select>';
				printf( esc_html__( 'create a new campaign every time a new %s has been published', 'mailster' ), $type );
				?>
			</p>
			<?php if ( mailster_option( 'track_location' ) ) : ?>
			<p>
				<label title="<?php esc_attr_e( 'Send this campaign based on the subscribers timezone if known', 'mailster' ); ?>">
				<input type="checkbox" class="autoresponder-timezone" name="mailster_data[autoresponder][post_published_timezone]" value="1" <?php checked( $this->post_data['timezone'] ); ?>> <?php esc_html_e( 'Use Subscribers timezone', 'mailster' ); ?>
				</label><?php echo mailster()->beacon( array( '63fb2e7c52af714471a1738a' ) ); ?>
			</p>
			<?php endif; ?>
			<div id="autoresponderfield-mailster_post_published_advanced">
				<div id="autoresponder-taxonomies"></div>
				<p>
					<?php
					printf( esc_html__( _n( 'always skip %s release', 'always skip %s releases', $autoresponderdata['post_count'], 'mailster' ) ), $count );
					?>
				</p>
			</div>
		</div>

		<div class="mailster_autoresponder_more autoresponderfield-mailster_autoresponder_timebased">
			<?php echo mailster()->beacon( array( '611bb8ccf886c9486f8d9921' ) ); ?>
			<p>
				<?php
				$timestamp = $this->post_data['timestamp'] ? $this->post_data['timestamp'] : $now;

				$interval   = '<br><input type="number" name="mailster_data[autoresponder][interval]" class="small-text" value="' . $autoresponderdata['interval'] . '">';
				$time_frame = '<select name="mailster_data[autoresponder][time_frame]">';
				$values     = array(
					'hour'  => esc_html__( 'hour(s)', 'mailster' ),
					'day'   => esc_html__( 'day(s)', 'mailster' ),
					'week'  => esc_html__( 'week(s)', 'mailster' ),
					'month' => esc_html__( 'month(s)', 'mailster' ),
				);
				foreach ( $values as $i => $value ) {
					$time_frame .= '<option value="' . $i . '"' . selected( $autoresponderdata['time_frame'], $i, false ) . '>' . $value . '</option>';
				}
				$time_frame .= '</select>';
				printf( esc_html_x( 'create a new campaign every %1$s%2$s', 'every [x] [timeframe] starting [startdate]', 'mailster' ), $interval, $time_frame );
				?>
			</p>
				<?php
				echo '<h4>' . esc_html__( 'next schedule', 'mailster' ) . '</h4>';
				?>
			<p>
			<?php
				esc_html_e( 'on', 'mailster' );
				echo ' <input name="mailster_data[autoresponder_date]" class="datepicker deliverydate inactive" type="datetime-local" value="' . date( 'Y-m-d H:i', $timestamp + $timeoffset ) . '" maxlength="10" readonly>';

				$autoresponderdata['endschedule'] = isset( $autoresponderdata['endschedule'] );
			?>
			</p>
			<?php if ( mailster_option( 'track_location' ) ) : ?>
			<p>
				<label title="<?php esc_attr_e( 'Send this campaign based on the subscribers timezone if known', 'mailster' ); ?>">
				<input type="checkbox" class="autoresponder-timezone" name="mailster_data[autoresponder][timebased_timezone]" value="1" <?php checked( $this->post_data['timezone'] ); ?>> <?php esc_html_e( 'Use Subscribers timezone', 'mailster' ); ?>
				</label><?php echo mailster()->beacon( array( '63fb2e7c52af714471a1738a' ) ); ?>
			</p>
			<?php endif; ?>
			<div>
			<label><input type="checkbox" name="mailster_data[autoresponder][endschedule]" class="mailster_autoresponder_timebased-end-schedule" <?php checked( $autoresponderdata['endschedule'] ); ?> value="1"> <?php esc_html_e( 'end schedule', 'mailster' ); ?></label>
				<div class="mailster_autoresponder_timebased-end-schedule-field"<?php echo ! $autoresponderdata['endschedule'] ? ' style="display:none"' : ''; ?>>
					<?php
					$timestamp = max( $timestamp, $autoresponderdata['endtimestamp'] );
					esc_html_e( 'on', 'mailster' );
					echo ' <input name="mailster_data[autoresponder_enddate]" class="datepicker deliverydate inactive" type="datetime-local" value="' . date( 'Y-m-d H:i', $timestamp + $timeoffset ) . '" maxlength="10" readonly>';
					?>
					<p class="howto"><?php esc_html_e( 'set an end date for your campaign', 'mailster' ); ?></p>
				</div>
			</div>
			<p class="howto">
				<?php esc_html_e( 'send campaigns only on these weekdays', 'mailster' ); ?>
			</p>
			<p>
				<?php
				$start_at = get_option( 'start_of_week' );

				for ( $i = $start_at; $i < 7 + $start_at; $i++ ) {
					$j = $i;
					if ( $j >= 7 ) {
						$j = $j - 7;
					}
					echo '<label title="' . date_i18n( 'l', strtotime( 'sunday +' . $j . ' days' ) ) . '" class="weekday"><input name="mailster_data[autoresponder][weekdays][]" type="checkbox" value="' . $j . '" ' . checked( ( in_array( $j, $autoresponderdata['weekdays'] ) || ! $autoresponderdata['weekdays'] ), true, false ) . '>' . date_i18n( 'D', strtotime( 'sunday +' . $j . ' days' ) ) . '&nbsp;</label> ';
				}
				?>
			</p>
			<p><label><input type="checkbox" name="mailster_data[autoresponder][time_conditions]" id="time_extra" value="1" <?php checked( isset( $autoresponderdata['time_conditions'] ) ); ?>> <?php esc_html_e( 'only if', 'mailster' ); ?></label></p>
			<div id="autoresponderfield-mailster_timebased_advanced"<?php echo ! isset( $autoresponderdata['time_conditions'] ) ? ' style="display:none"' : ''; ?>>
				<p>
				<?php
				$count = '<input type="number" name="mailster_data[autoresponder][time_post_count]" class="small-text" value="' . $autoresponderdata['time_post_count'] . '">';
				$type  = '<select id="autoresponder-post_type_time" name="mailster_data[autoresponder][time_post_type]">';
				foreach ( $pts as $pt => $data ) {
					if ( in_array( $pt, array( 'attachment', 'newsletter' ) ) ) {
						continue;
					}
					$type .= '<option value="' . $pt . '"' . selected( $autoresponderdata['time_post_type'], $pt, false ) . '>' . $data->labels->name . '</option>';
				}
				$type .= '<option value="rss"' . selected( $autoresponderdata['time_post_type'], 'rss', false ) . '>' . esc_html__( 'RSS Feeds', 'mailster' ) . '</option>';
				$type .= '</select><br>';
				printf( esc_html__( '%1$s %2$s have been published', 'mailster' ), $count, $type );
				?>
				</p>
			</div>
			<p><label><input type="checkbox" name="mailster_data[autoresponder][since]" value="<?php echo esc_attr( $autoresponderdata['since'] ); ?>" <?php checked( ! ! $autoresponderdata['since'] ); ?>> <?php esc_html_e( 'only if new content is available.', 'mailster' ); ?></label></p>
		</div>

		<div class="mailster_autoresponder_more autoresponderfield-mailster_post_published autoresponderfield-mailster_autoresponder_timebased">
				<p>
				<?php
				$issue = '<input type="number" id="mailster_autoresponder_issue" name="mailster_data[autoresponder][issue]" class="small-text" value="' . $autoresponderdata['issue'] . '">';
				printf( esc_html__( 'Next issue: %s', 'mailster' ), $issue );
				?>
				</p>
				<p class="howto">
				<?php printf( esc_html__( 'Use the %s tag to display the current issue in the campaign', 'mailster' ), '<code>{issue}</code>' ); ?>
				</p>
		</div>

		<div class="mailster_autoresponder_more autoresponderfield-mailster_post_published<?php echo isset( $autoresponderdata['time_conditions'] ) ? ' autoresponderfield-mailster_autoresponder_timebased' : ''; ?>">
			<p class="description">
				<?php
				$post_type = ( 'mailster_autoresponder_timebased' == $autoresponderdata['action'] )
					? $autoresponderdata['time_post_type']
					: $autoresponderdata['post_type'];

				if ( 'rss' == $post_type ) {
					$post_type_label = ( 1 == $autoresponderdata['post_count_status'] ? esc_html__( 'RSS Feed', 'mailster' ) : esc_html__( 'RSS Feeds', 'mailster' ) );
				} else {
					$post_type_label = '<a href="' . admin_url( 'edit.php?post_type=' . $post_type ) . '">' . ( 1 == $autoresponderdata['post_count_status'] ? $pts[ $post_type ]->labels->singular_name : $pts[ $post_type ]->labels->name ) . '</a>';
				}

				printf(
					_n( '%1$s matching %2$s has been published', '%1$s matching %2$s have been published', $autoresponderdata['post_count_status'], 'mailster' ),
					'<strong>' . $autoresponderdata['post_count_status'] . '</strong>',
					'<strong>' . $post_type_label . '</strong>'
				);
				if ( $autoresponderdata['since'] ) {
					printf(
						'<br><span title="' . esc_attr( 'The time which is used in this campaign. All posts must have been published after this date.', 'mailster' ) . '">' . esc_html__( 'Only %1$s after %2$s count.', 'mailster' ) . '</span>',
						strip_tags( $post_type_label ),
						date( $timeformat, $autoresponderdata['since'] + $timeoffset )
					);
				}
				?>
			</p>
			<p>
				<label><input type="checkbox" name="post_count_status_reset" value="1"> <?php esc_html_e( 'reset counter', 'mailster' ); ?></label>
			</p>
			<input type="hidden" name="mailster_data[autoresponder][post_count_status]" value="<?php echo $autoresponderdata['post_count_status']; ?>">

		</div>

		<div class="mailster_autoresponder_more autoresponderfield-mailster_autoresponder_usertime">
			<?php echo mailster()->beacon( array( '611bb7bd21ef206e5592c2fd' ) ); ?>
			<p>
				<?php
				if ( $customfields = mailster()->get_custom_date_fields() ) :

					$amount = '<input type="number" class="small-text" name="mailster_data[autoresponder][useramount]" value="' . ( isset( $autoresponderdata['useramount'] ) ? $autoresponderdata['useramount'] : 1 ) . '">';

					$unit   = '<select name="mailster_data[autoresponder][userunit]">';
					$values = array(
						'day'   => esc_html__( 'day(s)', 'mailster' ),
						'week'  => esc_html__( 'week(s)', 'mailster' ),
						'month' => esc_html__( 'month(s)', 'mailster' ),
						'year'  => esc_html__( 'year(s)', 'mailster' ),
					);
					foreach ( $values as $key => $value ) {
						$unit .= '<option value="' . $key . '"' . selected( $autoresponderdata['userunit'], $key, false ) . '>' . $value . '</option>';
					}
					$unit .= '</select>';

					$uservalue  = '<select name="mailster_data[autoresponder][uservalue]">';
					$uservalue .= '<option value="-1">--</option>';

					foreach ( $customfields as $key => $data ) {
						$uservalue .= '<option value="' . $key . '"' . selected( $autoresponderdata['uservalue'], $key, false ) . '>' . esc_html( $data['name'] ) . '</option>';
					}
					$uservalue .= '</select>';
					?>
			</p>
			<p id="userexactdate">
				<label>
					<input type="radio" class="userexactdate" name="mailster_data[autoresponder][userexactdate]" value="0" <?php checked( ! $autoresponderdata['userexactdate'] ); ?>>
					<span <?php echo ( $autoresponderdata['userexactdate'] ) ? ' class="disabled"' : ''; ?>><?php printf( esc_html__( 'every %1$s %2$s', 'mailster' ), $amount, $unit ); ?></span>
				</label><br>
				<label>
					<input type="radio" class="userexactdate" name="mailster_data[autoresponder][userexactdate]" value="1" <?php checked( $autoresponderdata['userexactdate'] ); ?>>
					<span <?php echo ( ! $autoresponderdata['userexactdate'] ) ? ' class="disabled"' : ''; ?>><?php esc_html_e( 'on the exact date', 'mailster' ); ?></span>
				</label>
			</p>
			<p>
					<?php
					printf( esc_html__( 'of the users %1$s value', 'mailster' ), $uservalue );
				else :
					esc_html_e( 'No custom date fields found!', 'mailster' );
					if ( current_user_can( 'manage_options' ) ) {
						echo '<br><a href="edit.php?post_type=newsletter&page=mailster_settings&settings-updated=true#subscribers">' . esc_html__( 'add new fields', 'mailster' ) . '</a>';
					}
				endif;
				?>
			</p>			
			<?php if ( mailster_option( 'track_location' ) ) : ?>
			<p>
				<label title="<?php esc_attr_e( 'Send this campaign based on the subscribers timezone if known', 'mailster' ); ?>">
					<input type="checkbox" class="autoresponder-timezone" name="mailster_data[autoresponder][usertime_timezone]" value="1" <?php checked( $this->post_data['timezone'] ); ?>> <?php esc_html_e( 'Use Subscribers timezone', 'mailster' ); ?>
				</label><?php echo mailster()->beacon( array( '63fb2e7c52af714471a1738a' ) ); ?>
			</p>
			<?php endif; ?>

			<p>
				<label>
					<input type="checkbox" name="mailster_data[autoresponder][usertime_once]" value="1" <?php checked( $autoresponderdata['once'] ); ?>> <?php esc_html_e( 'send campaign only once', 'mailster' ); ?>
				</label>
			</p>
		</div>
		<div class="mailster_autoresponder_more autoresponderfield-mailster_autoresponder_followup">
			<?php echo mailster()->beacon( array( '611bb745b37d837a3d0e479a' ) ); ?>
				<?php
				if ( $all_campaigns = $this->get_campaigns( 'post__not_in[]=' . $post->ID . '&orderby=post_title' ) ) :

					// bypass post_status sort limitation.
					$all_campaigns_stati = wp_list_pluck( $all_campaigns, 'post_status' );
					asort( $all_campaigns_stati );

					?>
				<p>
					<select name="mailster_data[autoresponder][followup_action]">
						<option value="1" <?php selected( $autoresponderdata['followup_action'], 1 ); ?>><?php esc_html_e( 'has been sent', 'mailster' ); ?></option>
						<option value="2" <?php selected( $autoresponderdata['followup_action'], 2 ); ?>><?php esc_html_e( 'has been opened', 'mailster' ); ?></option>
						<option value="3" <?php selected( $autoresponderdata['followup_action'], 3 ); ?>><?php esc_html_e( 'has been clicked', 'mailster' ); ?></option>
					</select>
				</p>
				<fieldset>
					<label><?php esc_html_e( 'Campaign', 'mailster' ); ?>
					<select name="parent_id" id="parent_id" class="widefat">
					<option value="0">--</option>
					<?php
					global $wp_post_statuses;
					$status = '';
					foreach ( $all_campaigns_stati as $i => $c ) :
						$c = $all_campaigns[ $i ];
						if ( $status != $c->post_status ) {
							if ( $status ) {
								echo '</optgroup>';
							}
							echo '<optgroup label="' . $wp_post_statuses[ $c->post_status ]->label . '">';
							$status = $c->post_status;
						}
						?>
					<option value="<?php echo esc_attr( $c->ID ); ?>" <?php selected( $post->post_parent, $c->ID ); ?>><?php echo $c->post_title ? esc_html( $c->post_title ) : '[' . esc_html__( 'no title', 'mailster' ) . ']'; ?></option>
						<?php
					endforeach;
					?>
					</optgroup></select></label>
				</fieldset>
			<?php else : ?>
				<p><?php esc_html_e( 'No campaigns available', 'mailster' ); ?></p>
			<?php endif; ?>
		</div>

		<div class="mailster_autoresponder_more autoresponderfield-mailster_autoresponder_hook">
			<?php echo mailster()->beacon( array( '611bb5e1b55c2b04bf6df0fc' ) ); ?>
			<div>
				<p><label>
					<select name="mailster_data[autoresponder][hook_type]">
						<option value="" <?php selected( ! $autoresponderdata['hook_type'] ); ?>><?php esc_html_e( 'Send this auto responder', 'mailster' ); ?></option>
						<option value="1" <?php selected( $autoresponderdata['hook_type'] ); ?>><?php esc_html_e( 'Create a new campaign based on this auto responder', 'mailster' ); ?></option>
					</select>
				</label></p>
			</div>
			<div>
				<label>
					<?php esc_html_e( 'whenever the action hook', 'mailster' ); ?>
				</label>
			</div>
			<?php if ( $hooks = apply_filters( 'mailster_action_hooks', array() ) ) : ?>
			<p>
				<label>
					<select class="widefat mailster-action-hooks">
						<option value=""><?php esc_html_e( 'Choose', 'mailster' ); ?></option>
						<?php foreach ( $hooks as $hook => $name ) : ?>
							<option value="<?php echo esc_attr( $hook ); ?>" <?php selected( $hook, $autoresponderdata['hook'] ); ?>><?php echo esc_html( $name ); ?></option>
						<?php endforeach; ?>
					</select>
				</label>
			</p>
				<?php endif; ?>
			<p>
				<input type="text" class="widefat code mailster-action-hook" name="mailster_data[autoresponder][hook]" value="<?php echo esc_attr( $autoresponderdata['hook'] ); ?>" placeholder="hook_name">
			</p>
			<div>
				<label>
					<?php esc_html_e( 'is triggered.', 'mailster' ); ?> (<abbr title="<?php esc_attr_e( 'use `do_action("hook_name")`, or `do_action("hook_name", $subscriber_id)` to trigger this campaign', 'mailster' ); ?>">?</abbr>)
				</label>
			</div>
			<div class="hide-if-hook-type"<?php echo ( $autoresponderdata['hook_type'] ) ? ' style="display:none"' : ''; ?>>
				<p><label>
				<?php esc_html_e( 'Priority', 'mailster' ); ?>:
					<select name="mailster_data[autoresponder][priority]">
						<option value="5" <?php selected( $autoresponderdata['priority'], 5 ); ?>><?php esc_html_e( 'High', 'mailster' ); ?></option>
						<option value="10" <?php selected( $autoresponderdata['priority'], 10 ); ?>><?php esc_html_e( 'Normal', 'mailster' ); ?></option>
						<option value="15" <?php selected( $autoresponderdata['priority'], 15 ); ?>><?php esc_html_e( 'Low', 'mailster' ); ?></option>
					</select>
				</label></p>
			</div>
			<div class="hide-if-hook-type"<?php echo ( $autoresponderdata['hook_type'] ) ? ' style="display:none"' : ''; ?>>
				<p><label>
					<input type="checkbox" name="mailster_data[autoresponder][hook_once]" value="1" <?php checked( $autoresponderdata['once'] ); ?>> <?php esc_html_e( 'send campaign only once', 'mailster' ); ?>
				</label></p>
			</div>
			<div class="hide-if-hook-type"<?php echo ( $autoresponderdata['hook_type'] ) ? ' style="display:none"' : ''; ?>>
				<label>
					<input type="checkbox" name="mailster_data[autoresponder][multiple]" value="1" <?php checked( $autoresponderdata['multiple'] ); ?>> <?php esc_html_e( 'allow multiple triggers', 'mailster' ); ?>
				</label>
				<p class="howto"><?php esc_html_e( 'Hooks can get triggered multiple times and cause multiple emails.', 'mailster' ); ?></p>
			</div>
		</div>

			<?php do_action( 'mailster_autoresponder_more' ); ?>

	</div>
	</div>
	</div>
	<?php endif; ?>
	<div>
	<?php	include_once MAILSTER_DIR . 'views/newsletter/test.php'; ?>

		
		<div class="clear"></div>

	</div>

<?php elseif ( 'active' == $post->post_status ) : ?>
	<p>
	<?php
		printf(
			esc_html__( 'This campaign has been started on %1$s, %2$s ago', 'mailster' ),
			'<br><strong>' . date( $timeformat, $this->post_data['timestamp'] + $timeoffset ),
			human_time_diff( $now, $this->post_data['timestamp'] ) . '</strong>'
		);
	?>
	</p>
	<?php
	if ( $sent && ! $is_autoresponder ) :

		$totals = $this->get_totals( $post->ID );
		$p      = round( $this->get_sent_rate( $post->ID ) * 100 );
		$pg     = sprintf( esc_html__( '%1$s of %2$s sent', 'mailster' ), number_format_i18n( $sent ), number_format_i18n( $totals ) );
		?>
		<div class="progress">
			<span class="bar" style="width:<?php echo esc_attr( $p ); ?>%"><span>&nbsp;<?php echo esc_attr( $pg ); ?></span></span><span>&nbsp;<?php echo esc_attr( $pg ); ?></span><var><?php echo esc_attr( $p ); ?>%</var>
		</div>

		<?php if ( $p ) : ?>
		<p>
			<?php
			$timepast = $now - $this->post_data['timestamp'];
			$timeleft = human_time_diff( $now + ( 100 - $p ) * ( $timepast / $p ) );
			printf( esc_html__( 'finished in approx. %s', 'mailster' ), '<strong>' . $timeleft . '</strong>' );
			?>
		</p>
		<?php endif; ?>
	<?php endif; ?>

<?php elseif ( $is_autoresponder ) : ?>
	<p>
	<?php printf( esc_html__( 'You have to %s to change the delivery settings', 'mailster' ), '<a href="post.php?post=' . $post_id . '&action=edit">' . esc_html__( 'switch to the edit mode', 'mailster' ) . '</a>' ); ?>
	</p>
<?php elseif ( 'finished' != $post->post_status ) : ?>
	<?php
		$totals = $this->get_totals( $post->ID );
		$p      = round( $this->get_sent_rate( $post->ID ) * 100 );
		$pg     = sprintf( esc_html__( '%1$s of %2$s sent', 'mailster' ), number_format_i18n( $sent ), number_format_i18n( $totals ) );
	?>
	<div class="progress paused">
		<span class="bar" style="width:<?php echo esc_attr( $p ); ?>%"><span>&nbsp;<?php echo esc_attr( $pg ); ?></span></span><span>&nbsp;<?php echo esc_attr( $pg ); ?></span><var><?php echo esc_attr( $p ); ?>%</var>
	</div>
<?php endif; ?>

<?php if ( $this->post_data['parent_id'] && current_user_can( 'edit_newsletter', $post->ID ) && current_user_can( 'edit_others_newsletters', $this->post_data['parent_id'] ) ) : ?>
	<p>
	<?php
		printf(
			esc_html__( 'This campaign is based on an %s', 'mailster' ),
			'<a href="post.php?post=' . $this->post_data['parent_id'] . '&action=edit&showstats=1">' . esc_html__( 'auto responder campaign', 'mailster' ) . '</a>'
		);
	?>
	</p>
<?php endif; ?>
<input type="hidden" id="mailster_is_autoresponder" name="mailster_data[is_autoresponder]" value="<?php echo $is_autoresponder; ?>">
