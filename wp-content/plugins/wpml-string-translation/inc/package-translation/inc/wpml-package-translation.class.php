<?php

class WPML_Package_Translation extends WPML_Package_Helper {
	var $load_priority = 100;
	var $package_translation_active;
	var $admin_lang_switcher = null;

	function __construct() {
		parent::__construct();
		add_action( 'wpml_loaded', array( $this, 'loaded' ), $this->load_priority, 1 );
	}

	function loaded( SitePress $sitepress = null ) {
		parent::loaded();

		if ( null === $sitepress ) {
			global $sitepress;
		}

		$is_setup_complete = (bool) $sitepress->get_setting( 'setup_complete', false );

		if ( $is_setup_complete ) {
			$this->run_db_update();
		}

		if ( $this->passed_dependencies() && $is_setup_complete ) {
			$this->add_admin_hooks();
			$this->add_global_hooks();

			if ( is_admin() ) {

				if ( $this->is_refresh_required() ) {
					add_action( 'init', array( $this, 'refresh_packages' ), 999, 0 );
					$this->set_refresh_not_required();
				}
			}
		}
	}

	private function add_admin_hooks() {
		if ( is_admin() || $this->is_doing_xmlrpc() || wpml_is_rest_request() || ( defined( 'DOING_CRON' ) && DOING_CRON ) ) {
			add_action( 'wp_ajax_wpml_delete_packages', array( $this, 'delete_packages_ajax' ) );
			add_action( 'wp_ajax_wpml_change_package_lang', array( $this, 'change_package_lang_ajax' ) );

			/* Core hooks */
			add_filter( 'wpml_pt_all_packages', array( $this, 'get_all_packages' ) );

			/* Translation hooks for other plugins to use */
			add_filter( 'wpml_tm_element_type', array( $this, 'get_element_type' ), 10, 2 );
			add_filter( 'wpml_tm_dashboard_title_locations', array( $this, 'add_title_db_location' ), 10, 1 );

			add_filter( 'wpml_string_title_from_id', array( $this, 'string_title_from_id_filter' ), 10, 2 );
			// TODO: deprecated, use the 'wpml_register_string' action
			add_filter( 'WPML_register_string', array( $this, 'register_string_for_translation' ), 10, 5 );

			add_action( 'wpml_add_string_translation', array( $this, 'add_string_translation_action' ), 10, 7 );

			// TODO: These 3 hooks are deprecated. They are needed for Layouts 1.0. Consider removing them after Layouts 1.2 is released
			add_filter( 'WPML_get_translated_strings', array( $this, 'get_translated_strings' ), 10, 2 );
			add_action( 'WPML_set_translated_strings', array( $this, 'set_translated_strings' ), 10, 2 );
			add_action( 'WPML_show_package_language_ui', array( $this, 'show_language_selector' ), 10, 2 );

			add_filter( 'wpml_get_translated_strings', array( $this, 'get_translated_strings' ), 10, 2 );
			add_action( 'wpml_set_translated_strings', array( $this, 'set_translated_strings' ), 10, 2 );
			add_action( 'wpml_show_package_language_ui', array( $this, 'show_language_selector' ), 10, 2 );
			add_action( 'wpml_show_package_language_admin_bar', array( $this, 'show_admin_bar_language_selector' ), 10, 2 );

			/* WPML hooks */
			add_filter( 'wpml_active_string_package_kinds', array( $this, 'get_active_string_package_kinds' ) );
			add_filter( 'wpml_get_translatable_types', array( $this, 'get_translatable_types' ), 10, 1 );
			add_filter( 'wpml_get_translatable_item', array( $this, 'get_translatable_item' ), 10, 3 );
			add_filter( 'wpml_external_item_url', array( $this, 'get_package_edit_url' ), 10, 2 );
			add_filter( 'wpml_external_item_link', array( $this, 'get_package_edit_link' ), 10, 3 );
			add_filter( 'wpml_get_external_item_title', array( $this, 'get_package_title' ), 10, 3 );
			add_filter( 'wpml_element_id_from_package', array( $this, 'get_element_id_from_package_filter' ), 10, 2 );
			add_filter( 'wpml_get_package_type', array( $this, 'get_package_type' ), 10, 2 );
			add_filter( 'wpml_get_package_type_prefix', array( $this, 'get_package_type_prefix' ), 10, 2 );
			add_filter( 'wpml_language_for_element', array( $this, 'get_language_for_element' ), 10, 2 );
			add_filter( 'wpml_st_get_string_package', array( $this, 'get_string_package' ), 10, 2 );
			add_action( 'wpml_save_external', [ $this, 'save_package_translations' ], 10, 3 );

			/* Translation queue hooks */
			add_filter( 'wpml_tm_external_translation_job_title', array( $this, 'get_post_title' ), 10, 2 );
			add_filter( 'wpml_tm_add_to_basket', array( $this, 'add_to_basket' ), 10, 1 );
			add_filter( 'wpml_tm_translation_jobs_basket', array( $this, 'update_translation_jobs_basket' ), 10, 3 );
			add_filter( 'wpml_tm_basket_items_types', array( $this, 'basket_items_types' ), 10, 1 );

			/*
			 TM Hooks */
			// This is called by \TranslationManagement::send_all_jobs - The hook is dynamically built.
			add_filter( 'wpml_tm_dashboard_sql', array( $this, 'tm_dashboard_sql_filter' ), 10, 1 );

			/* Translation editor hooks */
			add_filter( 'wpml_tm_editor_string_name', array( $this, 'get_editor_string_name' ), 10, 2 );
			add_filter( 'wpml_tm_editor_string_style', array( $this, 'get_editor_string_style' ), 10, 3 );

			/*
			 API Hooks */
			// @deprecated @since 3.2 Use 'wpml_delete_package'
			add_action( 'wpml_delete_package_action', array( $this, 'delete_package_action' ), 10, 2 );

			add_action( 'deleted_post', array( $this, 'remove_post_packages' ) );
		}
	}

	private function is_doing_xmlrpc() {
		return ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST );
	}

	private function add_global_hooks() {

		// TODO: deprecated, use the 'wpml_translate_string' filter
		add_filter( 'WPML_translate_string', array( $this, 'translate_string' ), 10, 3 );
		add_filter( 'wpml_translate_string', array( $this, 'translate_string' ), 10, 3 );

		add_action( 'wpml_register_string', array( $this, 'register_string_action' ), 10, 5 );
		add_action( 'wpml_start_string_package_registration', array( $this, 'start_string_package_registration_action' ), 10, 1 );
		add_action( 'wpml_delete_unused_package_strings', array( $this, 'delete_unused_package_strings_action' ), 10, 1 );
		add_action( 'wpml_delete_package', array( $this, 'delete_package_action' ), 10, 2 );
		add_filter( 'wpml_st_get_post_string_packages', array( $this, 'get_post_string_packages' ), 10, 2 );

		add_filter( 'wpml_string_id_from_package', array( $this, 'string_id_from_package_filter' ), 10, 4 );

		/* API Hooks */
		add_filter( 'wpml_is_external', array( $this, 'is_external' ), 10, 2 );
	}

	private function run_db_update() {
		if ( is_admin() ) {
			WPML_Package_Translation_Schema::run_update();
		}
	}

	/**
	 * @return bool
	 */
	private function passed_dependencies() {
		return defined( 'ICL_SITEPRESS_VERSION' )
			   && defined( 'WPML_ST_VERSION' )
			   && defined( 'WPML_TM_VERSION' );
	}

	public function add_title_db_location( $locations ) {
		global $wpdb;

		$locations[ $wpdb->prefix . 'icl_string_packages' ] = array(
			'id_column'           => 'ID',
			'title_column'        => 'title',
			'element_type_prefix' => 'package',
			'date_column'         => false,
		);

		return $locations;
	}

	function get_package_edit_url( $url, $post_id ) {
		$package = new WPML_Package( $post_id );
		if ( ! $package ) {
			return false;
		} //not ours
		if ( isset( $package->edit_link ) ) {
			$url = $package->edit_link;
		}

		return $url;
	}

	public function get_package_title( $title, $kind, $id ) {
		$package = new WPML_Package( $id );
		if ( ! $package ) {
			return $title;
		} //not ours
		if ( isset( $package->title ) ) {
			$title = $package->title;
		}

		return $title;
	}

	function get_package_view_link( $link, $post_id, $hide_if_missing_link = false ) {
		$package = new WPML_Package( $post_id );
		if ( ! $package ) {
			return $link;
		} //not ours
		return $this->build_package_link( $package->view_link, $package->title, $hide_if_missing_link );
	}

	function get_package_edit_link( $link, $post_id, $hide_if_missing_link = false ) {
		$package = new WPML_Package( $post_id );
		if ( ! $package ) {
			return $link;
		} //not ours
		return $this->build_package_link( $package->edit_link, esc_html( $package->title ), $hide_if_missing_link );
	}

	private function build_package_link( $url, $title, $hide_if_missing_link = false ) {
		$link = '<a href="' . $url . '">' . $title . '</a>';
		if ( false === $hide_if_missing_link ) {
			if ( ! $url ) {
				$link = $title;
			}
		} elseif ( ! $url ) {
			$link = '';
		}

		return $link;
	}

	/**
	 * @param stdClass|WPML_Package|array|int $package
	 * @param array<string,mixed>             $args
	 */
	function show_language_selector( $package, $args = array() ) {
		global $wpdb, $sitepress;

		$wpml_pt_meta = new WPML_Package_Translation_Metabox( $package, $wpdb, $sitepress, $args );
		echo $wpml_pt_meta->get_metabox();
	}

	/**
	 * @param stdClass|WPML_Package|array|int $package
	 * @param array<string,mixed>             $args
	 */
	function show_admin_bar_language_selector( $package, $args = array() ) {
		require WPML_PACKAGE_TRANSLATION_PATH . '/inc/wpml-package-admin-lang-switcher.class.php';
		$this->admin_lang_switcher = new WPML_Package_Admin_Lang_Switcher( $package, $args );
	}

	function cleanup_translation_jobs_basket_packages( $translation_jobs_basket ) {
		if ( empty( $translation_jobs_basket['packages'] ) ) {
			return;
		}

		foreach ( $translation_jobs_basket['packages'] as $id => $data ) {
			if ( ! new WPML_Package( $id ) ) {
				TranslationProxy_Basket::delete_item_from_basket( $id );
			}
		}
	}

	public function update_translation_jobs_basket( $translation_jobs_cart, $translation_jobs_basket, $item_type ) {
		if ( $item_type == 'package' ) {
			if ( ! isset( $translation_jobs_basket[ $item_type ] ) ) {
				return false;
			}

			$packages = $translation_jobs_basket[ $item_type ];
			if ( empty( $packages ) ) {
				return false;
			}

			$this->cleanup_translation_jobs_basket_packages( $translation_jobs_basket );

			global $sitepress;

			/** @var int[] $packages_ids */
			$packages_ids = array_keys( $packages );

			foreach ( $packages_ids as $package_id ) {
				$package = new WPML_Package( $package_id );
				if ( $package ) {
					if ( ! isset( $package->ID ) || ! $package->ID ) {
						TranslationProxy_Basket::delete_item_from_basket( $package_id, 'package' );
						continue;
					}

					$package_source_language  = $packages[ $package_id ]['from_lang'];
					$package_target_languages = $packages[ $package_id ]['to_langs'];
					$language_names           = $this->languages_to_csv( $package_target_languages );

					$final_post = array();

					$final_post['post_title']       = $package->title;
					$final_post['post_notes']       = get_post_meta( $package->ID, '_icl_translator_note', true );
					$final_post['post_type']        = $package->kind;
					if ( isset( $package->post_status ) ) {
						$final_post['post_status']      = $package->post_status;
					}
					if ( isset( $package->post_date ) ) {
						$final_post['post_date']        = $package->post_date;
					}
					$final_post['from_lang']        = $package_source_language;
					$final_post['from_lang_string'] = ucfirst( $sitepress->get_display_language_name( $package_source_language, $sitepress->get_admin_language() ) );
					$final_post['to_langs']         = $package_target_languages;
					$final_post['to_langs_string']  = implode( ', ', $language_names );

					$translation_jobs_cart[ $package_id ] = $final_post;
				}
			}
		}

		return $translation_jobs_cart;
	}

	public function basket_items_types( $item_types ) {
		$item_types['package'] = 'custom';

		return $item_types;
	}

	/**
	 * @param array<string,string> $package_target_languages
	 *
	 * @return array<string>
	 */
	private function languages_to_csv( $package_target_languages ) {
		global $sitepress;

		$language_names = array();
		foreach ( $package_target_languages as $language_code => $value ) {
			$language_names[] = ucfirst( $sitepress->get_display_language_name( $language_code, $sitepress->get_admin_language() ) );
		}

		return $language_names;
	}

	function is_external( $result, $type ) {
		return $result || is_a( $type, 'WPML_Package' ) || $type == 'package';
	}

	public function get_element_type( $type, $element ) {
		if ( $this->is_a_package( $element ) ) {
			$type = 'package';
		}

		return $type;
	}

	/**
	 * @param array<string,string> $attributes
	 *
	 * @return string
	 */
	public function attributes_to_string( $attributes ) {
		$result = '';
		foreach ( $attributes as $key => $value ) {
			if ( $result ) {
				$result .= ' ';
			}
			$result .= esc_html( $key ) . '="' . esc_attr( $value ) . '"';
		}

		return $result;
	}

	/**
	 * @param string $kind_slug
	 *
	 * @return string
	 */
	public static function get_package_element_type( $kind_slug ) {
		if ( is_object( $kind_slug ) ) {
			$kind_slug = $kind_slug->kind_slug;
		}
		if ( is_array( $kind_slug ) ) {
			$kind_slug = $kind_slug['kind_slug'];
		}

		return 'package_' . $kind_slug;
	}

	/**
	 * @param array<string,string> $package
	 *
	 * @return bool
	 */
	public function package_has_kind( $package ) {
		return isset( $package['kind'] ) && $package['kind'];
	}

	/**
	 * @param array<string,string> $package
	 *
	 * @return bool
	 */
	public function package_has_name( $package ) {
		return isset( $package['name'] ) && $package['name'];
	}

	/**
	 * @param array<string,string> $package
	 *
	 * @return bool
	 */
	public function package_has_title( $package ) {
		return isset( $package['title'] ) && $package['title'];
	}

	/**
	 * @param array<string,string> $package
	 *
	 * @return bool
	 */
	public function package_has_kind_and_name( $package ) {
		return $this->package_has_kind( $package ) && $this->package_has_name( $package );
	}

	/**
	 * @param string $string_name
	 *
	 * @return mixed
	 */
	public function sanitize_string_with_underscores( $string_name ) {
		return preg_replace( '/[ \[\]]+/', '_', $string_name );
	}

	function new_external_item( $type, $package_item, $get_string_data = false ) {
		// create a new external item for the Translation Dashboard or for translation jobs

		$package_id = $package_item['ID'];

		$item                 = new stdClass();
		$item->external_type  = true;
		$item->type           = $type;
		$item->ID             = $package_id;
		$item->post_type      = $type;
		$item->post_id        = 'external_' . $item->post_type . '_' . $package_item['ID'];
		$item->post_date      = '';
		$item->post_status    = __( 'Active', 'wpml-string-translation' );
		$item->post_title     = $package_item['title'];
		$item->is_translation = false;

		if ( $get_string_data ) {
			$item->string_data = $this->_get_package_strings( $package_item );
		}

		return $item;
	}

	function get_package_from_external_id( $post_id ) {

		global $wpdb;

		$packages = $wpdb->get_col( "SELECT ID FROM {$wpdb->prefix}icl_string_packages WHERE ID>0" );

		foreach ( $packages as $package_id ) {

			$package = new WPML_Package( $package_id );

			$test = $this->get_external_id_from_package( $package );
			if ( is_string( $post_id ) && $post_id == $test ) {
				return $package;
			}
		}

		return false; // not a package type
	}

	function _get_package_strings( $package_item ) {
		global $wpdb;
		$strings = array();

		$package_item_id = $package_item['ID'];
		$results         = $wpdb->get_results( $wpdb->prepare( "SELECT name, value FROM {$wpdb->prefix}icl_strings WHERE string_package_id=%d", $package_item_id ) );

		foreach ( $results as $result ) {
			$string_name             = (bool) $package_item_id === true && strpos(
				$result->name,
				(string) $package_item_id
			) === 0
				? substr( $result->name, strlen( $package_item_id ) + 1 ) : $result->name;
			$strings[ $string_name ] = $result->value;
		}

		// Add/update any registered strings
		if ( isset( $this->registered_strings[ $package_item_id ]['strings'] ) ) {
			foreach ( $this->registered_strings[ $package_item_id ]['strings'] as $id => $string_data ) {
				$strings[ $id ] = $string_data['value'];
			}
		}

		return $strings;
	}

	function get_link( $item, $package_item, $anchor, $hide_empty ) {
		if ( $item == '' ) {
			$package_item = $this->get_package_from_external_id( $package_item );
			if ( ! $package_item ) {
				return '';
			}

			$has_link = isset( $package_item->edit_link ) && $package_item->edit_link;
			if ( false === $anchor ) {
				if ( $has_link ) {
					$anchor = '<a href="' . $package_item->edit_link . '">' . $package_item['title'] . '</a>';
				} elseif ( ! $hide_empty ) {
					$anchor = $package_item['title'];
				}
			} else {
				if ( $has_link ) {
					$anchor = '<a href="' . $package_item['edit_link'] . '">' . $anchor . '</a>';
				}
			}

			$item = $anchor;
		}

		return $item;
	}

	/**
	 * Update translations
	 *
	 * @param int  $package_id
	 * @param bool $is_new       - set to true for newly created form (first save without fields)
	 * @param bool $needs_update - when deleting single field we do not need to change the translation status of the form
	 *
	 * @internal param array $item - package information
	 */
	function update_package_translations( $package_id, $is_new, $needs_update = true ) {

		global $sitepress, $wpdb, $iclTranslationManagement;

		$item = $this->get_package_details( $package_id );

		$post_id = $this->get_external_id_from_package( new WPML_Package( $package_id ) );
		/** @var WPML_Package $post */
		$post    = $this->get_translatable_item( null, $post_id );
		if ( ! $post ) {
			return;
		}
		$default_lang = $sitepress->get_default_language();
		$icl_el_type  = $this->get_package_element_type( $item );
		$trid         = $sitepress->get_element_trid( $item['ID'], $icl_el_type );

		if ( $is_new ) {
			$sitepress->set_element_language_details( $post->ID, $icl_el_type, false, $default_lang, null, false );

			// for new package nothing more to do
			return;
		}

		$sql                  = "
					            SELECT t.translation_id, s.md5
					            FROM {$wpdb->prefix}icl_translations t
					                NATURAL JOIN {$wpdb->prefix}icl_translation_status s
					            WHERE t.trid=%d
					                AND t.source_language_code IS NOT NULL
					            ";
		$element_translations = $wpdb->get_results( $wpdb->prepare( $sql, $trid ) );

		if ( ! empty( $element_translations ) ) {

			$md5 = $iclTranslationManagement->post_md5( $post );

			if ( $md5 != $element_translations[0]->md5 ) { // all translations need update

				$translation_package = $iclTranslationManagement->create_translation_package( $post );

				foreach ( $element_translations as $trans ) {
					$_prevstate = $wpdb->get_row(
						$wpdb->prepare(
							"
                        SELECT status, translator_id, needs_update, md5, translation_service, translation_package, timestamp, links_fixed
                        FROM {$wpdb->prefix}icl_translation_status
                        WHERE translation_id = %d
                    ",
							$trans->translation_id
						),
						ARRAY_A
					);
					if ( ! empty( $_prevstate ) ) {
						$data['_prevstate'] = serialize( $_prevstate );
					}
					$data = array(
						'translation_id'      => $trans->translation_id,
						'translation_package' => serialize( $translation_package ),
						'md5'                 => $md5,
					);

					// update only when something changed (we do not need to change status when deleting a field)
					if ( $needs_update ) {
						$data['needs_update'] = 1;
					}

					$update_result = $iclTranslationManagement->update_translation_status( $data );
					$rid           = $update_result[0];
					$this->update_icl_translate( $rid, $post );

					// change job status only when needs update
					if ( $needs_update ) {
						$job_id = $wpdb->get_var( $wpdb->prepare( "SELECT MAX(job_id) FROM {$wpdb->prefix}icl_translate_job WHERE rid=%d GROUP BY rid", $rid ) );
						if ( $job_id ) {
							$wpdb->update( "{$wpdb->prefix}icl_translate_job", array( 'translated' => 0 ), array( 'job_id' => $job_id ), array( '%d' ), array( '%d' ) );
						}
					}
				}
			}
		}
	}

	/**
	 * Functions to update translations when packages are modified in admin
	 *
	 * @param int       $rid
	 * @param \stdClass|WPML_Package $post
	 */

	function update_icl_translate( $rid, $post ) {

		global $wpdb;

		$job_id   = $wpdb->get_var( $wpdb->prepare( "SELECT MAX(job_id) FROM {$wpdb->prefix}icl_translate_job WHERE rid=%d GROUP BY rid", $rid ) );
		$elements = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT field_type, field_data, tid, field_translate FROM {$wpdb->prefix}icl_translate
        												WHERE job_id=%d",
				$job_id
			),
			OBJECT_K
		);

		foreach ( $post->string_data as $field_type => $field_value ) {
			$field_data = base64_encode( $field_value );
			if ( ! isset( $elements[ $field_type ] ) ) {
				// insert new field

				$data = array(
					'job_id'                => $job_id,
					'content_id'            => 0,
					'field_type'            => $field_type,
					'field_format'          => 'base64',
					'field_translate'       => 1,
					'field_data'            => $field_data,
					'field_data_translated' => 0,
					'field_finished'        => 0,
				);

				$wpdb->insert( $wpdb->prefix . 'icl_translate', $data );
			} elseif ( $elements[ $field_type ]->field_data != $field_data ) {
				// update field value
				$wpdb->update(
					$wpdb->prefix . 'icl_translate',
					array(
						'field_data'     => $field_data,
						'field_finished' => 0,
					),
					array( 'tid' => $elements[ $field_type ]->tid )
				);
			}
		}

		foreach ( $elements as $field_type => $el ) {
			// delete fields that are no longer present
			if ( $el->field_translate && ! isset( $post->string_data[ $field_type ] ) ) {
				$wpdb->delete( $wpdb->prefix . 'icl_translate', array( 'tid' => $el->tid ), array( '%d' ) );
			}
		}
	}

	private function get_package_details( $package_id ) {
		global $wpdb;
		static $cache = array();

		if ( ! isset( $cache[ $package_id ] ) ) {
			$item                 = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}icl_string_packages WHERE ID=%d", $package_id ), ARRAY_A );
			$cache[ $package_id ] = $item;
		}

		return $cache[ $package_id ];
	}

	function get_string_context_title( $context, $string_details ) {
		global $wpdb;

		static $cache = array();

		if ( ! isset( $cache[ $context ] ) ) {
			$package_id = $wpdb->get_var( $wpdb->prepare( "SELECT string_package_id FROM {$wpdb->prefix}icl_strings WHERE id=%d", $string_details['string_id'] ) );
			if ( $package_id ) {
				$package_details = $this->get_package_details( $package_id );
				if ( $package_details ) {
					$cache[ $context ] = $package_details->kind . ' - ' . $package_details->title;
				} else {
					$cache[ $context ] = $context;
				}
			} else {
				$cache[ $context ] = $context;
			}
		}

		return $cache[ $context ];
	}

	function get_string_title( $title, $string_details ) {
		global $wpdb;

		$string_title = $wpdb->get_var( $wpdb->prepare( "SELECT title FROM {$wpdb->prefix}icl_strings WHERE id=%d", $string_details['string_id'] ) );
		if ( $string_title ) {
			return $string_title;
		} else {
			return $title;
		}
	}

	function _get_post_translations( $package ) {
		global $sitepress;

		if ( is_object( $package ) ) {
			$package = get_object_vars( $package );
		}

		$element_type = $this->get_package_element_type( $package['kind_slug'] );
		$trid         = $sitepress->get_element_trid( $package['ID'], $element_type );

		return $sitepress->get_element_translations( $trid, $element_type );
	}

	function _is_translation_in_progress( $package ) {
		global $wpdb;

		$post_translations = self::_get_post_translations( $package );

		foreach ( $post_translations as $lang => $translation ) {
			$res = $wpdb->get_row( $wpdb->prepare( "SELECT status, needs_update, md5 FROM {$wpdb->prefix}icl_translation_status WHERE translation_id=%d", $translation->translation_id ) );
			if ( $res && $res->status == ICL_TM_IN_PROGRESS ) {
				return true;
			}
		}

		return false;
	}

	function _delete_translation_job( $package_id ) {
		global $wpdb;

		$package = $this->get_package_details( $package_id );

		$post_translations = $this->_get_post_translations( $package );
		foreach ( $post_translations as $lang => $translation ) {
			$rid = $wpdb->get_var( $wpdb->prepare( "SELECT rid FROM {$wpdb->prefix}icl_translation_status WHERE translation_id=%d", $translation->translation_id ) );
			if ( $rid ) {
				$job_id = $wpdb->get_var( $wpdb->prepare( "SELECT job_id FROM {$wpdb->prefix}icl_translate_job WHERE rid=%d", $rid ) );

				if ( $job_id ) {
					$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}icl_translate_job WHERE job_id=%d", $job_id ) );
					$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}icl_translate WHERE job_id=%d", $job_id ) );
				}
			}
		}
	}

	public function add_to_basket( $data ) {
		if ( isset( $data['tr_action'] ) ) {
			$posts_ids = TranslationProxy_Basket::get_elements_ids( $data, 'package' );

			foreach ( $posts_ids as $id ) {
				$post_id          = $id;
				$source_language  = $data['translate_from'];
				$target_languages = $data['tr_action'];
				foreach ( $target_languages as $translate_to => $translation_action ) {
					$package = new WPML_Package( $post_id );
					$tm      = new WPML_Package_TM( $package );
					$tm->add_package_to_basket( $translation_action, $source_language, $translate_to );
				}
			}
		}
	}


	function _no_wpml_warning() {
		?>
		<div class="message error">
			<p>
				<?php printf( __( 'WPML Package Translation is active but requires both the <a href="%s">WPML core</a> and WPML String Translation plugins to function properly.', 'wpml-string-translation' ), 'http://wpml.org/' ); ?>
			</p>
		</div>
		<?php
	}

	public function tm_dashboard_sql_filter( $sql ) {
		global $wpdb;

		$sql .= " AND i.element_id NOT IN ( SELECT ID FROM {$wpdb->prefix}icl_string_packages WHERE post_id IS NOT NULL AND element_type = 'package_layout' )";
		return $sql;
	}

	/**
	 * @return bool
	 */
	private function is_refresh_required() {
		$refresh_required = get_option( 'wpml-package-translation-refresh-required', 'yes' );
		return 'yes' === $refresh_required || '1' === $refresh_required;
	}

	private function set_refresh_not_required() {
		update_option( 'wpml-package-translation-refresh-required', 'no', false );
	}

	public function save_package_translations( $element_type_prefix, $job, $decoder ) {
		if ( $element_type_prefix === 'package' ) {
			$element_type_prefix             = $this->get_package_type_prefix( $element_type_prefix, $job->original_doc_id );
			$needs_process_translation_files = false;

			foreach ( $job->elements as $field ) {
				if ( $field->field_translate ) {
					/**
					 * @param string    $element_type_prefix
					 * @param \stdClass $field
					 * @param \stdClass $job
					 *
					 * @return string
					 */
					$field_context = apply_filters( 'wpml_save_external_package_field_context', $element_type_prefix, $field, $job );
					$string_id = icl_st_is_registered_string( $field_context, $field->field_type );
					if ( ! $string_id ) {
						icl_register_string( $field_context, $field->field_type, $decoder( $field->field_data, $field->field_format ) );
						$string_id = icl_st_is_registered_string( $field_context, $field->field_type );
					}
					if ( $string_id ) {
						$needs_process_translation_files = true;
						icl_add_string_translation( $string_id, $job->language_code, $decoder( $field->field_data_translated, $field->field_format ), ICL_TM_COMPLETE );
					}
				}
			}

			if ( $needs_process_translation_files ) {
				do_action( 'wpml_st_translation_files_process_queue' );
			}
		}
	}

}
