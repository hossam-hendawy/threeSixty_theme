<?php

class WPML_Sync_Term_Meta_Action {

	/** @var SitePress $sitepress */
	private $sitepress;

	/** @var int $term_taxonomy_id */
	private $term_taxonomy_id;

	/** @var bool $is_new_term */
	private $is_new_term;

	/**
	 * WPML_Sync_Term_Meta_Action constructor.
	 *
	 * @param SitePress $sitepress
	 * @param int       $term_taxonomy_id just saved term's term_taxonomy_id
	 * @param bool      $is_new_term
	 */
	public function __construct( $sitepress, $term_taxonomy_id, $is_new_term = false ) {
		$this->sitepress        = $sitepress;
		$this->term_taxonomy_id = $term_taxonomy_id;
		$this->is_new_term      = $is_new_term;
	}

	/**
	 * Copies to be synchronized term meta data to the translations of the term.
	 */
	public function run() {
		$term_taxonomy_id_from = $this->sitepress->term_translations()->get_original_element( $this->term_taxonomy_id );

		if ( ! $term_taxonomy_id_from ) {
			$term_taxonomy_id_from = $this->term_taxonomy_id;
		}

		$translations = $this->sitepress
			->term_translations()
			->get_element_translations( $term_taxonomy_id_from, false, true );

		if ( ! empty( $translations ) ) {
			foreach ( $translations as $term_taxonomy_id_to ) {
				$this->copy_custom_fields( (int) $term_taxonomy_id_to, $term_taxonomy_id_from );
			}
		}
	}

	/**
	 * @param int $term_taxonomy_id_to
	 * @param int $term_taxonomy_id_from
	 */
	private function copy_custom_fields( $term_taxonomy_id_to, $term_taxonomy_id_from ) {
		$cf_copy = array();

		$setting_factory = $this->sitepress->core_tm()->settings_factory();
		$meta_keys       = $setting_factory->get_term_meta_keys();

		foreach ( $meta_keys as $meta_key ) {
			$meta_key_status = $setting_factory->term_meta_setting( $meta_key )->status();

			if ( WPML_COPY_CUSTOM_FIELD === $meta_key_status
				 || $this->should_copy_once( $meta_key_status, $term_taxonomy_id_to )
			) {
				$cf_copy[] = $meta_key;
			}
		}

		$term_id_to   = $this->sitepress->term_translations()->adjust_term_id_for_ttid( $term_taxonomy_id_to );
		$term_id_from = $this->sitepress->term_translations()->adjust_term_id_for_ttid( $term_taxonomy_id_from );

		foreach ( $cf_copy as $meta_key ) {
			$meta_from = $this->sitepress->get_wp_api()->get_term_meta( $term_id_from, $meta_key );
			$meta_to   = $this->sitepress->get_wp_api()->get_term_meta( $term_id_to, $meta_key );
			if ( $meta_from || $meta_to ) {
				$this->sync_custom_field( $term_id_from, $term_id_to, $meta_key );
			}
		}
	}

	private function sync_custom_field(
		$term_id_from,
		$term_id_to,
		$meta_key
	) {
		$wpdb        = $this->sitepress->wpdb();
		$sql         = "SELECT meta_value FROM {$wpdb->termmeta} WHERE term_id=%d AND meta_key=%s";
		$values_from = $wpdb->get_col(
			$wpdb->prepare(
				$sql,
				array( $term_id_from, $meta_key )
			)
		);
		$values_to   = $wpdb->get_col(
			$wpdb->prepare(
				$sql,
				array( $term_id_to, $meta_key )
			)
		);

		$removed = array_diff( $values_to, $values_from );
		foreach ( $removed as $v ) {
			$prepare_arguments    = array( $term_id_to, $meta_key, $v );
			$meta_value_condition = 'meta_value=%s';
			if ( null === $v ) {
				$prepare_arguments    = array( $term_id_to, $meta_key );
				$meta_value_condition = '(meta_value="" OR meta_value IS NULL)';
			}
			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			// phpcs:disable WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber
			$delete_prepared = $wpdb->prepare(
				"DELETE FROM {$wpdb->termmeta}
												WHERE term_id=%d
												AND meta_key=%s
												AND {$meta_value_condition}",
				$prepare_arguments
			);
			// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			// phpcs:enable WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber
			$wpdb->query( $delete_prepared );
		}

		$added = array_diff( $values_from, $values_to );
		foreach ( $added as $v ) {
			$insert_prepared = $wpdb->prepare(
				"INSERT INTO {$wpdb->termmeta}(term_id, meta_key, meta_value)
												VALUES(%d, %s, %s)",
				array( $term_id_to, $meta_key, $v )
			);
			$wpdb->query( $insert_prepared );
		}

		/**
		 * @param int    $term_id_from The term_id of the source term.
		 * @param int    $term_id_to   The term_id of the destination term.
		 * @param string $meta_key     The key of the term meta being copied.
		 *
		 * @since 4.7.0
		 */
		do_action( 'wpml_after_copy_term_field', $term_id_from, $term_id_to, $meta_key );

		wp_cache_init();
	}

	/**
	 * @param int $meta_key_status
	 * @param int $term_taxonomy_id_to
	 *
	 * @return bool
	 */
	private function should_copy_once( $meta_key_status, $term_taxonomy_id_to ) {
		return $this->is_new_term
			   && WPML_COPY_ONCE_CUSTOM_FIELD === $meta_key_status
			   && $term_taxonomy_id_to === $this->term_taxonomy_id;
	}
}
