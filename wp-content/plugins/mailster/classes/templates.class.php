<?php

use PHPUnit\Runner\Version;

class MailsterTemplates {

	public $path;
	public $url;

	private $endpoint = 'https://mailster.co/templates.json';

	private $headers = array(
		'name'        => 'Template Name',
		'label'       => 'Name',
		'uri'         => 'Template URI',
		'url'         => 'Template URI',
		'slug'        => 'Template Slug',
		'description' => 'Description',
		'author'      => 'Author',
		'author_uri'  => 'Author URI',
		'author_url'  => 'Author URI',
		'version'     => 'Version',
	);

	private $template_fields = array(
		'ID'               => null,
		'name'             => null,
		'slug'             => null,
		'image'            => null,
		'imagex2'          => null,
		'description'      => null,
		'index'            => null,
		'url'              => null,
		'version'          => null,
		'author'           => null,
		'author_profile'   => null,
		'requires'         => '3.0',
		'is_default'       => null,
		'is_supported'     => null,
		'is_premium'       => null,
		'author_profile'   => null,
		'download'         => null,
		'download_url'     => null,
		'price'            => null,
		'envato_item_id'   => null,
		'update_available' => false,
	);

	public function __construct() {

		$this->path = MAILSTER_UPLOAD_DIR . '/templates';
		$this->url  = MAILSTER_UPLOAD_URI . '/templates';

		add_action( 'admin_menu', array( &$this, 'admin_menu' ), 50 );
		add_action( 'mailster_copy_template', array( &$this, 'copy_template' ) );
		add_action( 'wp_version_check', array( &$this, 'check_for_updates' ) );
		add_action( 'mailster_get_screenshots', array( &$this, 'get_screenshots' ), 10, 4 );
	}


	public function admin_menu() {

		if ( $updates = $this->get_updates() ) {
			$updates = ' <span class="update-plugins count-' . $updates . '" title="' . sprintf( esc_html__( _n( '%d Update available', '%d Updates available', $updates, 'mailster' ) ), $updates ) . '"><span class="update-count">' . $updates . '</span></span>';
		} else {
			$updates = '';
		}

		$page = add_submenu_page( 'edit.php?post_type=newsletter', esc_html__( 'Templates', 'mailster' ), esc_html__( 'Templates', 'mailster' ) . $updates, 'mailster_manage_templates', 'mailster_templates', array( &$this, 'templates' ) );
		add_action( 'load-' . $page, array( &$this, 'scripts_styles' ) );
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_path() {
		return $this->path;
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_url() {
		return $this->url;
	}



	public function download_by_slug( $slug = null ) {

		$templates = $this->get_templates( true );

		if ( in_array( $slug, $templates ) ) {
			return true;
		}

		$templates = $this->query(
			array(
				's'      => $slug,
				'browse' => 'latest',
				'type'   => 'slug',
			)
		);

		if ( is_wp_error( $templates ) ) {
			return $templates;
		}

		if ( empty( $templates['total'] ) ) {
			return new WP_Error( 'not_found', esc_html__( 'Template not found', 'mailster' ) );
		}

		if ( isset( $templates['items'][ $slug ] ) ) {

			return $this->download_template( $templates['items'][ $slug ]['download_url'], $slug );

		}

		return new WP_Error( 'not_found', esc_html__( 'Template not found', 'mailster' ) );
	}



	public function update_locally( $slug ) {

		$local_folder = MAILSTER_DIR . 'templates/' . $slug;
		if ( ! file_exists( $local_folder . '/index.html' ) ) {
			return false;
		}

		$templates = $this->get_templates();
		if ( ! isset( $templates[ $slug ] ) ) {
			return false;
		}

		$local_template_data = $this->get_template_data( $local_folder . '/index.html' );

		if ( ! $local_template_data ) {
			return false;
		}

		if ( version_compare( $templates[ $slug ]['version'], $local_template_data['version'], '>=' ) ) {
			return false;
		}

		$wp_filesystem = mailster_require_filesystem();

		if ( ! $wp_filesystem ) {
			return new WP_Error( 'wp_filesystem', esc_html__( 'The content folder is not writeable', 'mailster' ) );
		}

		$temp_folder = mailster( 'helper' )->mkdir( 'uploads/' . uniqid(), false );
		if ( ! wp_is_writable( $temp_folder ) ) {
			$wp_filesystem->delete( $temp_folder, true );
			return new WP_Error( 'not_writeable', esc_html__( 'The content folder is not writeable', 'mailster' ) );
		}

		// copy local folder to temp folder
		$result = copy_dir( $local_folder, $temp_folder . '/' . $slug );

		if ( is_wp_error( $result ) ) {
			$wp_filesystem->delete( $temp_folder, true );
			return $result;
		}

		$renamefolder = null;
		$overwrite    = true;
		$backup_old   = true;
		return $this->install_template( $temp_folder, $renamefolder, $overwrite, $backup_old );
	}



	public function download_template( $url, $slug = null ) {

		$download_url = rawurldecode( $url );
		$slug         = isset( $slug ) ? rawurldecode( $slug ) : null;

		if ( ! function_exists( 'download_url' ) ) {
			include ABSPATH . 'wp-admin/includes/file.php';
		}

		add_filter( 'http_request_args', array( &$this, 'additional_download_headers' ), 10, 2 );
		$tempfile = download_url( $download_url );
		remove_filter( 'http_request_args', array( &$this, 'additional_download_headers' ), 10, 2 );
		if ( is_wp_error( $tempfile ) ) {
			return $tempfile;
		}

		$result = $this->unzip_template( $tempfile, $slug, true, true );

		if ( is_wp_error( $result ) ) {
			return $result;
		} else {
			$redirect = admin_url( 'edit.php?post_type=newsletter&page=mailster_templates' );
			$redirect = add_query_arg( array( 'new' => $slug ), $redirect );

			$this->schedule_screenshot( $slug, 'index.html' );

			return $redirect;
		}

		return false;
	}


	public function additional_download_headers( $parsed_args, $url ) {

		$headers = array(
			'x-mailster-version' => MAILSTER_VERSION,
			'x-mailster-site'    => get_bloginfo( 'url' ),
			'x-mailster-license' => mailster()->get_license(),
		);

		$parsed_args['headers'] = wp_parse_args( $parsed_args['headers'], $headers );

		return $parsed_args;
	}


	public function remove_template( $slug, $file = null ) {

		$location = $this->path . '/' . $slug;

		if ( ! is_null( $file ) ) {
			$location .= '/' . $file;
		}

		$wp_filesystem = mailster_require_filesystem();

		if ( $wp_filesystem && $wp_filesystem->delete( $location, true ) ) {

			if ( ! $file ) {
				$this->remove_screenshot( $slug );
			}

			$this->reset_query_cache();
			return true;

		}

		return false;
	}


	/**
	 *
	 * Unzips a template file and saves it to a temp folder
	 *
	 * @param string $zipfile
	 * @param string $renamefolder (optional)
	 * @param bool   $overwrite    (optional)
	 * @param bool   $backup_old   (optional)
	 * @return string|WP_Error
	 */
	public function unzip_template( $zipfile, $renamefolder = null, $overwrite = false, $backup_old = false ) {

		$wp_filesystem = mailster_require_filesystem();

		$temp_folder = mailster( 'helper' )->mkdir( 'uploads/' . uniqid(), false );

		if ( ! $wp_filesystem ) {
			return new WP_Error( 'wp_filesystem', esc_html__( 'The content folder is not writeable', 'mailster' ) );
		}

		if ( ! wp_is_writable( $temp_folder ) ) {
			return new WP_Error( 'not_writeable', esc_html__( 'The content folder is not writeable', 'mailster' ) );
		}

		$result = unzip_file( $zipfile, $temp_folder );

		if ( is_wp_error( $result ) ) {
			$wp_filesystem->delete( $temp_folder, true );
			return $result;
		}

		$result = $this->install_template( $temp_folder, $renamefolder, $overwrite, $backup_old );

		if ( is_wp_error( $result ) ) {
			$wp_filesystem->delete( $temp_folder, true );
		}

		return $result;
	}


	/**
	 * Moves template files to the template folder and checks the files, santized them.
	 *
	 * @param unknown $uploadfolder
	 * @param unknown $renamefolder (optional)
	 * @param unknown $overwrite    (optional)
	 * @param unknown $backup_old   (optional)
	 * @return unknown
	 */
	public function install_template( $uploadfolder, $renamefolder = null, $overwrite = false, $backup_old = false ) {

		$wp_filesystem = mailster_require_filesystem();

		$uploadfolder = trailingslashit( $uploadfolder );

		// get installed templates
		$templates = $this->get_templates();

		$template_slugs = array_keys( $templates );

		if ( $folders = scandir( $uploadfolder ) ) {

			foreach ( $folders as $folder ) {

				if ( in_array( $folder, array( '.', '..' ) ) ) {
					continue;
				}

				if ( ! is_null( $renamefolder ) ) {

					$renamefolder = sanitize_file_name( $renamefolder );

					if ( $renamefolder == $folder ) {
						$moved = true;
					} elseif ( ! ( $moved = $wp_filesystem->move( $uploadfolder . $folder, $uploadfolder . $renamefolder, true ) ) ) {
						$moved = rename( $uploadfolder . $folder, $uploadfolder . $renamefolder );
					}

					if ( $moved ) {
						$folder = $renamefolder;
					} else {

						return new WP_Error( 'not_writeable', esc_html__( 'Unable to save template', 'mailster' ) );
					}
				}

				$data = $this->get_template_data( $uploadfolder . $folder . '/index.html' );

				// need index.html file
				if ( ! $data ) {

					$all_files = list_files( $uploadfolder );
					$all_files = str_replace( trailingslashit( $uploadfolder ), '', $all_files );

					// strict search (only in filename)
					$zips = preg_grep( '#(mailster|mymail)([^\/]+)?\.zip$#i', $all_files );
					if ( empty( $zips ) ) {
						// lazy search (also in dirname)
						$zips = preg_grep( '#(mailster|mymail)(.*)?\.zip$#i', $all_files );
					}

					foreach ( $zips as $zip ) {

						$result = $this->unzip_template( trailingslashit( $uploadfolder ) . $zip, $renamefolder, $overwrite, $backup_old );
						if ( ! is_wp_error( $result ) ) {

							return $result;
						}
					}

					return new WP_Error( 'wrong_file', esc_html__( 'This is not a valid Mailster template ZIP', 'mailster' ) );

				}

				$templateslug = sanitize_title( $data['slug'], $data['name'] );

				if ( ! $overwrite && in_array( $templateslug, $template_slugs ) ) {

					return new WP_Error( 'template_exists', sprintf( esc_html__( 'Template %s already exists!', 'mailster' ), '"' . $data['name'] . '"' ) );

				}

				$files = list_files( $uploadfolder . $folder );

				$removed_files = array();
				$allowed_mimes = array( 'text/html', 'text/xml', 'text/plain', 'image/svg+xml', 'image/svg', 'image/png', 'image/gif', 'image/jpeg', 'image/tiff', 'image/x-icon' );
				$safelist      = array( 'json', 'woff', 'woff2', 'ttf', 'eot' );
				$blocklist     = array( 'php', 'bin', 'exe' );

				foreach ( $files as $file ) {

					$basename = wp_basename( $file );

					if ( ! is_file( $file ) ) {
						$wp_filesystem->delete( $file, true );
						continue;
					}

					if ( function_exists( 'mime_content_type' ) ) {
						$mimetype = mime_content_type( $file );
					} else {
						$validate = wp_check_filetype( $file );
						$mimetype = $validate['type'];
					}

					if ( ( ! in_array( $mimetype, $allowed_mimes ) && ! preg_match( '#\.(' . implode( '|', $safelist ) . ')$#i', $file ) || preg_match( '#\.(' . implode( '|', $blocklist ) . ')$#i', $file ) ) ) {
						$removed_files[] = $basename;
						$wp_filesystem->delete( $file, true );
						continue;
					}
					// sanitize HTML upload
					if ( 'text/html' == $mimetype ) {
						$raw = file_get_contents( $file );
						$wp_filesystem->put_contents( $file, mailster()->sanitize_content( $raw, null, true ), FS_CHMOD_FILE );
					}
				}

				// with name value
				if ( ! empty( $data['name'] ) ) {

					$path = $this->path . '/' . $templateslug;
					wp_mkdir_p( $path );

					if ( $backup_old && file_exists( $path . '/index.html' ) ) {
						$old_data  = $this->get_template_data( $path . '/index.html' );
						$new_files = list_files( $uploadfolder . $folder, 1 );
						// add notification file so it get removed
						if ( ! preg_grep( '#notification\.html$#', $new_files ) ) {
							$new_files[] = $uploadfolder . $folder . '/notification.html';
						}

						foreach ( $new_files as $new_file ) {
							if ( ! preg_match( '#\.html$#', $new_file ) ) {
								continue;
							}

							$old_file = $path . '/' . basename( $new_file );

							// don't overwrite if it's the same file
							if ( file_exists( $new_file ) && file_exists( $old_file ) && md5_file( $new_file ) === md5_file( $old_file ) ) {
								continue;
							}

							// append version to old file
							$old_file_name = preg_replace( '#\.html$#', '-' . $old_data['version'] . '.html', $old_file );
							// move old file
							if ( ! $wp_filesystem->move( $old_file, $old_file_name, $old_file ) ) {
								rename( $old_file, $old_file_name );
							}

							// replace the file in the post meta table
							global $wpdb;
							$sql = "UPDATE $wpdb->postmeta AS template_file JOIN $wpdb->postmeta AS template ON template.post_id = template_file.post_id AND template.meta_key = %s AND template.meta_value = %s SET template_file.meta_value = %s WHERE template_file.meta_key = %s AND template_file.meta_value = %s";
							$sql = $wpdb->prepare( $sql, '_mailster_template', $templateslug, basename( $old_file_name ), '_mailster_file', basename( $old_file ) );
							$wpdb->query( $sql );

							// update these if it's the current used template
							if ( $templateslug == mailster_option( 'default_template' ) ) {
								if ( mailster_option( 'system_mail_template' ) == basename( $old_file ) ) {
									mailster_update_option( 'system_mail_template', basename( $old_file_name ) );
								}
								if ( mailster_option( 'subscriber_notification_template' ) == basename( $old_file ) ) {
									mailster_update_option( 'subscriber_notification_template', basename( $old_file_name ) );
								}
								if ( mailster_option( 'unsubscribe_notification_template' ) == basename( $old_file ) ) {
									mailster_update_option( 'unsubscribe_notification_template', basename( $old_file_name ) );
								}
							}
						}
					}
					// copy the files
					copy_dir( $uploadfolder . $folder, $path );
				} else {

					return new WP_Error( 'wrong_header', esc_html__( 'The header of this template files is missing or corrupt', 'mailster' ) );
				}

				if ( ! empty( $removed_files ) ) {
					mailster_notice( '<strong>' . esc_html__( 'Following files have been removed during upload:', 'mailster' ) . '</strong><ul><li>' . implode( '</li><li>', $removed_files ) . '</li></ul>', 'info', true );
				}

				// looks like an update
				if ( isset( $templates[ $data['slug'] ] ) && version_compare( $data['version'], $templates[ $data['slug'] ]['version'], '>' ) ) {
					$this->reset_query_cache();
					if ( $update_count = $this->get_updates() ) {
						update_option( 'mailster_templates_updates', --$update_count );
					}
				}

				// newly added
				if ( ! isset( $templates[ $data['slug'] ] ) ) {
					$this->reset_query_cache();
				}
				$this->process_colors( $data['slug'] );
			}

			if ( isset( $templateslug ) && $templateslug ) {

				return $data;
			}
		}

		return new WP_Error( 'file_error', esc_html__( 'There was a problem progressing the file', 'mailster' ) );
	}




	/**
	 *
	 *
	 * @param unknown $slug (optional)
	 * @return unknown
	 */
	public function renew_default_template( $slug = 'mailster' ) {

		$this->copy_template();
		$this->process_colors( $slug );
	}


	private function get_colors_from_json( $slug ) {

		$wp_filesystem = mailster_require_filesystem();

		$path = mailster( 'helper' )->mkdir( 'templates' );

		$folder = trailingslashit( $path ) . $slug;

		if ( file_exists( $folder . '/template.json' ) ) {

			$template_data = $wp_filesystem->get_contents( $folder . '/template.json' );

			if ( $template_data ) {
				$template_data = json_decode( $template_data, true );

				if ( isset( $template_data['schemas'] ) ) {
					return $template_data['schemas'];
				}
			}
		}

		return array();
	}

	public function process_colors( $slug = null ) {

		$wp_filesystem = mailster_require_filesystem();

		$path = mailster( 'helper' )->mkdir( 'templates' );

		if ( is_null( $slug ) ) {

			$files = list_files( $path, 1 );

			foreach ( $files as $file ) {
				if ( is_dir( $file ) ) {
					$this->process_colors( basename( $file ) );
				}
			}
			return;

		}

		$folder = trailingslashit( $path ) . $slug;

		// legacy
		if ( file_exists( $folder . '/colors.json' ) ) {

			$colors = $wp_filesystem->get_contents( $folder . '/colors.json' );

			if ( $colors ) {
				$colorschemas = json_decode( $colors );

				$customcolors = (array) get_option( 'mailster_colors', array() );

				if ( true || ! isset( $customcolors[ $slug ] ) ) {

					$customcolors[ $slug ] = array();
					foreach ( $colorschemas as $colorschema ) {
						$hash                           = md5( implode( '', $colorschema ) );
						$customcolors[ $slug ][ $hash ] = $colorschema;
					}

					update_option( 'mailster_colors', $customcolors );

				}
			}
		}
	}


	public function templates() {

		if ( current_user_can( 'mailster_upload_templates' ) ) {
			remove_action( 'post-plupload-upload-ui', 'media_upload_flash_bypass' );
			wp_enqueue_script( 'plupload-all' );
		}

		include MAILSTER_DIR . 'views/templates.php';
	}

	public function install_templates() {

		if ( current_user_can( 'mailster_upload_templates' ) ) {
			remove_action( 'post-plupload-upload-ui', 'media_upload_flash_bypass' );
			wp_enqueue_script( 'plupload-all' );
		}

		include MAILSTER_DIR . 'views/templates-install.php';
	}


	/**
	 *
	 *
	 * @param unknown $return (optional)
	 * @param unknown $nonce  (optional)
	 */
	private function ajax_nonce( $return = null, $nonce = 'mailster_nonce' ) {
		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], $nonce ) ) {
			die( $return );
		}
	}


	private function ajax_filesystem() {
		if ( 'ftpext' == get_filesystem_method() && ( ! defined( 'FTP_HOST' ) || ! defined( 'FTP_USER' ) || ! defined( 'FTP_PASS' ) ) ) {
			$return['msg']     = esc_html__( 'WordPress is not able to access to your filesystem!', 'mailster' );
			$return['msg']    .= "\n" . sprintf( esc_html__( 'Please add following lines to the wp-config.php %s', 'mailster' ), "\n\ndefine('FTP_HOST', 'your-ftp-host');\ndefine('FTP_USER', 'your-ftp-user');\ndefine('FTP_PASS', 'your-ftp-password');\n" );
			$return['success'] = false;
			echo json_encode( $return );
			exit;
		}
	}


	/**
	 *
	 *
	 * @param unknown $force (optional)
	 * @return unknown
	 */
	public function get_available_templates( $force = false ) {

		if ( $force || ! ( $available_templates = get_transient( 'mailster_templates_count' ) ) ) {

			$cachetime = HOUR_IN_SECONDS * 6;

			$result = $this->query( array( 'browse' => 'latest' ) );

			if ( is_wp_error( $result ) ) {
				$available_templates = 400;
				$cachetime           = MINUTE_IN_SECONDS;
			} else {
				$available_templates = $result['total'];
			}

			set_transient( 'mailster_templates_count', $available_templates, $cachetime );

		}

		return $available_templates;
	}

	/**
	 *
	 *
	 * @param unknown $slugsonly     (optional)
	 * @param unknown $load_if_empty (optional)
	 * @return unknown
	 */
	public function get_templates( $slugsonly = false, $load_if_empty = false ) {

		$templates = array();

		if ( ! function_exists( 'list_files' ) ) {
			include ABSPATH . 'wp-admin/includes/file.php';
		}

		$files = list_files( $this->path, 2 );

		if ( $load_if_empty && empty( $files ) ) {
			$this->renew_default_template();
			$files = list_files( $this->path, 2 );
		}

		$current = mailster_option( 'default_template' );
		sort( $files );

		foreach ( $files as $file ) {
			if ( basename( $file ) == 'index.html' && dirname( $file ) != $this->path ) {

				$filename = str_replace( $this->path . '/', '', $file );
				$slug     = dirname( $filename );
				if ( $slugsonly ) {
					$templates[] = $slug;
				} else {
					$templates[ $slug ] = $this->get_template_data( $file );
				}
			}
		}

		if ( $slugsonly ) {
			sort( $templates );
		} else {
			ksort( $templates );
			// set new default if it doesn't exist
			if ( $current && ! isset( $templates[ $current ] ) ) {
				$current = key( array_slice( $templates, 0, 1 ) );
				mailster_update_option( 'default_template', $current );
			}
			// bring the current one to the first position
			if ( $current && isset( $templates[ $current ] ) ) {
				$templates = array( $current => $templates[ $current ] ) + $templates;
			}
		}

		return $templates;
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_all_files() {

		$templates = $this->get_templates();

		$files = array();

		foreach ( $templates as $slug => $data ) {
			$files[ $slug ] = $this->get_files( $slug );
		}

		return $files;
	}


	/**
	 *
	 *
	 * @param unknown $slug           (optional)
	 * @param unknown $group_versions (optional)
	 * @return unknown
	 */
	public function get_files( $slug = '', $group_versions = false ) {

		if ( empty( $slug ) ) {
			return array();
		}

		$templates = array();
		$files     = list_files( $this->path . '/' . $slug, 1 );

		sort( $files );

		$list = array(
			'index.html' => $this->get_template_data( $this->path . '/' . $slug . '/index.html' ),
		);

		if ( file_exists( $this->path . '/' . $slug . '/notification.html' ) ) {
			$list['notification.html'] = $this->get_template_data( $this->path . '/' . $slug . '/notification.html' );
		}

		foreach ( $files as $file ) {

			if ( strpos( $file, '.html' ) && is_file( $file ) ) {
				$list[ basename( $file ) ] = $this->get_template_data( $file );
			}
		}

		if ( ! $group_versions ) {
			return $list;
		}

		$group_list = array();
		foreach ( $list as $file => $data ) {
			$v = 'edge';
			if ( preg_match( '#-(([0-9.]+)\.([0-9]+))\.html$#', $file, $hits ) ) {
				$v = $hits[1];
			}
			if ( ! isset( $group_list[ $v ] ) ) {
				$group_list[ $v ] = array();
			}

			$group_list[ $v ][ $file ] = $data;
		}

		return $group_list;
	}


	/**
	 *
	 *
	 * @param unknown $slug (optional)
	 * @return unknown
	 */
	public function get_versions( $slug = null ) {

		$templates = $this->get_templates();
		$versions  = array();
		foreach ( $templates as $s => $data ) {

			$versions[ $s ] = $data['version'];
		}

		return ! is_null( $slug ) ? ( isset( $versions[ $slug ] ) ? $versions[ $slug ] : null ) : $versions;
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_updates() {

		if ( ! current_user_can( 'mailster_update_templates' ) ) {
			return 0;
		}

		return (int) get_option( 'mailster_templates_updates' );
	}


	public function check_for_updates( $force = false ) {

		$result = $this->query( array(), $force );

		if ( ! is_wp_error( $result ) ) {
			$updates = array_sum( wp_list_pluck( $result['items'], 'update_available' ) );
			update_option( 'mailster_templates_updates', $updates );
		}
	}

	/**
	 *
	 *
	 * @param unknown $file (optional)
	 * @return unknown
	 */
	public function get_raw_template( $file = 'index.html' ) {
		if ( ! file_exists( $this->path . '/' . $this->slug . '/' . $file ) ) {
			return false;
		}

		return file_get_contents( $this->path . '/' . $this->slug . '/' . $file );
	}


	public function scripts_styles() {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_register_style( 'mailster-templates', MAILSTER_URI . 'assets/css/templates-style' . $suffix . '.css', array( 'themes' ), MAILSTER_VERSION );
		wp_enqueue_style( 'mailster-templates' );
		wp_enqueue_style( 'mailster-codemirror', MAILSTER_URI . 'assets/css/libs/codemirror' . $suffix . '.css', array(), MAILSTER_VERSION );
		wp_enqueue_script( 'mailster-codemirror', MAILSTER_URI . 'assets/js/libs/codemirror' . $suffix . '.js', array(), MAILSTER_VERSION, true );
		wp_enqueue_script( 'mailster-templates', MAILSTER_URI . 'assets/js/templates-script' . $suffix . '.js', array( 'mailster-script' ), MAILSTER_VERSION, true );

		if ( function_exists( 'wp_enqueue_code_editor' ) ) {
			wp_enqueue_code_editor( array( 'type' => 'htmlmixed' ) );
		} else {
			wp_enqueue_script( 'mailster-codemirror', MAILSTER_URI . 'assets/js/libs/codemirror' . $suffix . '.js', array(), MAILSTER_VERSION );
			wp_enqueue_style( 'mailster-codemirror', MAILSTER_URI . 'assets/css/libs/codemirror' . $suffix . '.css', array(), MAILSTER_VERSION );
		}

		mailster_localize_script(
			'templates',
			array(
				'delete_template_file' => esc_html__( 'Do you really like to remove file %1$s from template %2$s?', 'mailster' ),
				'enter_template_name'  => esc_html__( 'Please enter the name of the new template.', 'mailster' ),
				'uploading'            => esc_html__( 'uploading zip file %s', 'mailster' ),
				'downloading'          => esc_html__( 'Downloading...', 'mailster' ),
				'downloaded'           => esc_html__( 'Template loaded!', 'mailster' ),
				'updating'             => esc_html__( 'Updating...', 'mailster' ),
				'updated'              => esc_html__( 'Template has been updated!', 'mailster' ),
				'confirm_delete'       => esc_html__( 'You are about to delete this template %s.', 'mailster' ),
				'confirm_delete_file'  => esc_html__( 'You are about to delete file %1$s from template %2$s.', 'mailster' ),
				'confirm_default'      => esc_html__( 'You are about to make %s your default template.', 'mailster' ),
				'editing'              => esc_html__( 'Editing template file %1$s for %2$s', 'mailster' ),
				'update_note'          => esc_html__( 'You are about to update your exiting template files with a new version!', 'mailster' ) . "\n\n" . esc_html__( 'Old template files will be preserved in the templates folder.', 'mailster' ),
			)
		);
	}


	public function download_envato_template() {

		if ( ! isset( $_GET['mailster_nonce'] ) ) {
			return;
		}

		if ( wp_verify_nonce( $_GET['mailster_nonce'], 'envato-activate' ) ) {

			$redirect = admin_url( 'edit.php?post_type=newsletter&page=mailster_templates&more' );

			if ( isset( $_GET['mailster_error'] ) ) {

				$error = urldecode( $_GET['mailster_error'] );
				// thanks Envato :(
				if ( 'The purchase you have requested is not downloadable at this time.' == $error ) {
					$error .= '<p>' . esc_html__( 'Please make sure you have signed in to the account you have purchased the template!', 'mailster' ) . '</p>';
					$error .= '<p>';
					if ( isset( $_GET['mailster_slug'] ) ) {
						$template = $this->get_mailster_templates( sanitize_key( $_GET['mailster_slug'] ) );
						$error   .= '<a href="' . esc_url( $template['uri'] ) . '" class="external button button-primary">' . sprintf( esc_html__( 'Buy %1$s from %2$s now!', 'mailster' ), $template['name'], 'Envato' ) . '</a> ';
						$error   .= esc_html__( 'or', 'mailster' ) . ' <a href="https://account.envato.com/" class="external">' . esc_html__( 'Visit Envato Account', 'mailster' ) . '</a>';
					}
					$error .= '</p>';
				}

				$error = sprintf( 'There was an error loading the template: %s', $error );
				mailster_notice( $error, 'error', true );
			}

			if ( isset( $_GET['mailster_download_url'] ) ) {
				$download_url = urldecode( $_GET['mailster_download_url'] );
				$slug         = isset( $_GET['mailster_slug'] ) ? urldecode( $_GET['mailster_slug'] ) : null;

				if ( ! function_exists( 'download_url' ) ) {
					include ABSPATH . 'wp-admin/includes/file.php';
				}

				$tempfile = download_url( $download_url );

				$result = $this->unzip_template( $tempfile, $slug, true, true );
				if ( is_wp_error( $result ) ) {
					mailster_notice( sprintf( 'There was an error loading the template: %s', $result->get_error_message() ), 'error', true );
				} else {
					mailster_notice( esc_html__( 'Template successful loaded!', 'mailster' ), 'success', true );
					$redirect = admin_url( 'edit.php?post_type=newsletter&page=mailster_templates' );
					$redirect = add_query_arg( array( 'new' => $slug ), $redirect );
					// force a reload
					update_option( 'mailster_templates', false );
				}
			}
		}

		mailster_redirect( $redirect );
		exit;
	}

	/**
	 *
	 *
	 * @param unknown $slug (optional)
	 * @return unknown
	 */
	public function remove_screenshot( $slug = null ) {

		global $wp_filesystem;

		$folder = MAILSTER_UPLOAD_DIR . '/screenshots';

		if ( ! is_null( $slug ) ) {
			$folder .= '/' . $slug;
		}

		if ( ! is_dir( $folder ) ) {
			return;
		}

		$wp_filesystem = mailster_require_filesystem();

		return $wp_filesystem && $wp_filesystem->delete( $folder, true );
	}


	/**
	 *
	 *
	 * @param unknown $slug
	 * @param unknown $file    (optional)
	 * @param unknown $async   (optional)
	 * @param unknown $custom_modules (optional)
	 */
	public function get_screenshots( $slug, $file = 'index.html', $async = true, $custom_modules = false ) {

		$slug = ( $slug );
		$file = ( $file );

		$filedir = MAILSTER_UPLOAD_DIR . '/templates/' . $slug . '/' . $file;
		$fileuri = MAILSTER_UPLOAD_URI . '/templates/' . $slug . '/' . $file;

		if ( ! file_exists( $filedir ) ) {
			return;
		}

		$hash = hash( 'crc32', md5_file( $filedir ) );
		if ( is_rtl() ) {
			$hash .= '-rtl';
		}
		$screenshot_folder_base = mailster( 'helper' )->mkdir( 'screenshots' );

		$screenshot_folder         = $screenshot_folder_base . $slug . '/';
		$screenshot_modules_folder = $screenshot_folder_base . $slug . '/modules/' . $hash . '/';
		$screenshoturi             = MAILSTER_UPLOAD_URI . '/screenshots/' . $slug . '/' . $hash . '.jpg';

		$raw      = file_get_contents( $filedir );
		$template = mailster( 'template', $slug, $file, $custom_modules );
		$raw      = $template->get();

		if ( $custom_modules ) {

			$module_dir = dirname( $filedir ) . '/modules/';
			// no modules
			if ( ! is_dir( $module_dir ) ) {
				return;
			}

			$modules = glob( $module_dir . '*.html' );

			$html = '';

			foreach ( $modules as $module ) {

				$module = basename( $module );

				$html .= file_get_contents( $module_dir . $module ) . "\n";
			}

			$raw = preg_replace( '#<modules>(.*)<\/modules>#is', '<modules>' . trim( $html ) . '</modules>', $raw );

		}

		// no modules found
		if ( ! preg_match( '#<modules([^>]*)>(.*?)<\/modules>#is', $raw, $modules ) ) {
			return;
		}

		if ( ! preg_match_all( '#<module([^>]*)>(.*?)<\/module>#is', $modules[2], $matches ) ) {
			return;
		}

		$modules = $template->get_modules_list();

		$wp_filesystem = mailster_require_filesystem();

		if ( ! is_dir( $screenshot_folder ) ) {
			mailster( 'helper' )->mkdir( $screenshot_folder, true );
		}

		$request_url = 'https://api.mailster.co/module/v3/';

		$file_size = strlen( $raw );
		$hash      = md5( $raw );
		$blocked   = get_transient( '_mailster_screenshot_error' );

		if ( $blocked && isset( $blocked[ $hash ] ) ) {
			return;
		}

		$headers = array(
			'accept'               => 'application/json',
			'x-mailster-length'    => $file_size,
			'x-mailster-hash'      => $hash,
			'x-mailster-version'   => MAILSTER_VERSION,
			'x-mailster-site'      => get_bloginfo( 'url' ),
			'x-mailster-license'   => mailster()->get_license(),
			'x-mailster-url'       => $fileuri,
			'x-mailster-custom'    => $custom_modules,
			'x-mailster-direction' => is_rtl() ? 'rtl' : 'ltr',
		);

		$response = wp_remote_get(
			$request_url,
			array(
				'headers' => $headers,
				'timeout' => 2,
			)
		);

		$response_code         = wp_remote_retrieve_response_code( $response );
		$http_response_headers = wp_remote_retrieve_headers( $response );

		// file hasn't been generated yet
		if ( 404 == $response_code ) {

			$headers['content-type']   = 'application/binary';
			$headers['content-length'] = $file_size;

			$response = wp_remote_post(
				$request_url,
				array(
					'headers'  => $headers,
					'body'     => $raw,
					'timeout'  => $async ? 1 : 20,
					'blocking' => $async ? false : true,
				)
			);

			unset( $raw );

			if ( $async ) {
				$this->schedule_screenshot( $slug, $file, 10, $async, $custom_modules );
				return;
			}

			$response_code = wp_remote_retrieve_response_code( $response );

		}

		if ( 200 != $response_code ) {

			switch ( $response_code ) {
				case 201:
					$this->schedule_screenshot( $slug, $file, 10, $async, $custom_modules );
					break;
				case 500:
				case 503:
					$this->schedule_screenshot( $slug, $file, 1800, $async, $custom_modules );
					break;
				case 406:
					if ( ! is_array( $blocked ) ) {
						$blocked = array();
					}
					$blocked[ $hash ] = time();
					set_transient( '_mailster_screenshot_error', $blocked );
					mailster_notice( sprintf( esc_html__( 'Not able to create module screen shots of %1$s. Read more about this %2$s.', 'mailster' ), $slug . '/' . $file, '<a href="' . mailster_url( 'https://kb.mailster.co/611bb2b36ffe270af2a9990d' ) . '" data-article="611bb2b36ffe270af2a9990d">' . esc_html__( 'here', 'mailster' ) . '</a>' ), 'error', false, 'screenshot_error' );
					break;
			}

			return;

		}

		$body   = wp_remote_retrieve_body( $response );
		$result = json_decode( $body );

		if ( ! function_exists( 'download_url' ) ) {
			include ABSPATH . 'wp-admin/includes/file.php';

		}

		$processed = 0;

		if ( isset( $result->modules ) && is_array( $result->modules ) ) {
			foreach ( $result->modules as $i => $fileurl ) {

				$hash = $modules[ $i ]['id'];

				$screenshot_name = $screenshot_modules_folder . $hash . '.jpg';

				if ( file_exists( $screenshot_name ) ) {
					continue;
				}

				$tempfile = download_url( $fileurl );

				if ( ! is_wp_error( $tempfile ) ) {

					if ( function_exists( 'exif_imagetype' ) && 2 != exif_imagetype( $tempfile ) ) {
						continue;
					}

					if ( ! is_dir( dirname( $screenshot_name ) ) ) {
						wp_mkdir_p( dirname( $screenshot_name ) );
					}

					if ( ! $wp_filesystem->copy( $tempfile, $screenshot_name ) ) {
						copy( $tempfile, $screenshot_name );
					}

					++$processed;

					if ( $processed >= 30 ) {
						$this->schedule_screenshot( $slug, $file, 1, $custom_modules );
						break;
					}
				}
			}
		}
	}


	/**
	 *
	 *
	 * @param unknown $slug
	 * @param unknown $file
	 * @param unknown $delay   (optional)
	 * @param unknown $async   (optional)
	 * @param unknown $custom_modules (optional)
	 */
	public function schedule_screenshot( $slug, $file, $delay = 0, $async = true, $custom_modules = null ) {

		if ( ! mailster_option( 'module_thumbnails' ) ) {
			return false;
		}
		// schedule custom modules first
		if ( ! wp_next_scheduled( 'mailster_get_screenshots', array( $slug, $file, $async, true ) ) ) {
			wp_schedule_single_event( time() + $delay, 'mailster_get_screenshots', array( $slug, $file, $async, true ) );
		}

		if ( ! wp_next_scheduled( 'mailster_get_screenshots', array( $slug, $file, $async, false ) ) ) {
			wp_schedule_single_event( time() + $delay, 'mailster_get_screenshots', array( $slug, $file, $async, false ) );
		}
	}


	/**
	 *
	 *
	 * @param unknown $new
	 */
	public function on_install( $new ) {

		if ( $new ) {
			update_option( 'mailster_templates_updates', '0' );
			update_option( 'mailster_colors', '', false );

			try {
				$this->copy_template();
				$this->copy_social_icons();
			} catch ( Exception $e ) {
				if ( ! wp_next_scheduled( 'mailster_copy_template' ) ) {
					wp_schedule_single_event( time(), 'mailster_copy_template' );
				}
			}
		}
	}


	public function copy_template() {

		$success = false;

		if ( $path = mailster( 'helper' )->mkdir( 'templates' ) ) {
			$success = copy_dir( MAILSTER_DIR . 'templates', $path );
			$this->process_colors();
		}

		return $success;
	}

	public function copy_social_icons() {

		if ( $path = mailster( 'helper' )->mkdir( 'social' ) ) {
			copy_dir( MAILSTER_DIR . 'assets/img/social', $path );
		}
	}


	public function colors( $campaign, $template, $file ) {

		$campaign = mailster( 'campaigns' )->get( $campaign );

		$campaign_template = get_post_meta( $campaign->ID, '_mailster_template', true );
		$campaign_file     = get_post_meta( $campaign->ID, '_mailster_file', true );

		$templateobj     = mailster( 'template', $template, $file );
		$template_colors = $this->parse_colors( $templateobj->raw, 'original' );
		$current         = null;

		// if these are different a template change has happend or the campaign is new
		if ( $campaign_template != $template || $campaign_file != $file ) {
			$merged_colors = $template_colors;
		} else {
			// otherwise merge the ones defined in the current campaign
			$campaign_colors = $this->parse_colors( $campaign->post_content );
			$merged_colors   = array_replace_recursive( $template_colors, $campaign_colors );
			$current         = wp_list_pluck( $campaign_colors, 'value', 'id' );

		}

		$schemas        = $this->get_colors_from_json( $template );
		$stored         = get_option( 'mailster_colors', array() );
		$custom_schemas = array();
		if ( isset( $stored[ $template ] ) ) {
			$stored = $stored[ $template ];
			$i      = 1;
			foreach ( $stored as $hash => $colors ) {
				$custom_schemas[] = array(
					'name'   => sprintf( __( 'Custom %s', 'mailster' ), '#' . $i++ ),
					'hash'   => $hash,
					'colors' => $colors,
				);
			}
		}
		// prepend to schemas
		$schemas = array_merge( $custom_schemas, $schemas );

		return array(
			'colors'  => $merged_colors,
			'current' => $current,
			'schemas' => $schemas,
		);
	}


	private function parse_colors( $html, $value_name = 'value' ) {
		$colors = array();

		// get all style blocks, search for variables
		preg_match_all( '#<style(.*?)>(.*?)</style>#is', $html, $style_blocks );
		foreach ( $style_blocks[2] as $style_block ) {
			// get all variables
			preg_match_all( '/--mailster-([a-zA-z0-9-]+):([^;}]+)/', $style_block, $variables );
			foreach ( $variables[1] as $i => $id ) {
				$value                        = trim( $variables[2][ $i ] );
				$colors[ $id ]                = array(
					'id'        => $id,
					'var'       => '--mailster-' . $id,
					'value'     => $value,
					$value_name => $value,
					'label'     => $this->color_label( $id ),
				);
				$colors[ $id ][ $value_name ] = $value;
			}
		}

		// no colors => fallback to the legacy method < 4.0
		if ( empty( $colors ) ) {
			preg_match_all( '/#[a-fA-F0-9]{6}/', $html, $hits );
			$original_colors = array_keys( array_count_values( $hits[0] ) );

			foreach ( $original_colors as $i => $color ) {
				preg_match( '/' . $color . '\/\*([^*]+)\*\//i', $html, $match );
				$value        = strtoupper( $color );
				$id           = strtolower( substr( $value, 1 ) );
				$colors[ $i ] = array(
					'id'        => $i,
					'var'       => null,
					'value'     => $value,
					'original'  => $value,
					$value_name => $value,
					'label'     => isset( $match[1] ) ? $match[1] : $value,
				);
			}
		}

		return $colors;
	}

	private function color_label( $raw_label ) {

		$raw_label = str_replace( 'color', '', $raw_label );
		$raw_label = str_replace( 'bg', 'Background', $raw_label );

		$label = ucwords( str_replace( '-', ' ', $raw_label ) );
		return $label;
	}


	public function reset_query_cache() {
		global $wpdb;

		$wpdb->query( "UPDATE {$wpdb->options} SET option_value = 0 WHERE option_name LIKE '_transient_timeout_mailster_templates_%'" );
	}



	public function query( $query_args = array(), $force = false ) {

		$query_args = wp_parse_args(
			rawurlencode_deep( $query_args ),
			array(
				's'      => '',
				'type'   => 'keyword',
				'browse' => 'installed',
				'page'   => 1,
			)
		);

		if ( $query_args['browse'] == 'installed' ) {
			$templates               = $this->get_templates( false, true );
			$query_args['templates'] = implode( ',', array_keys( $templates ) );
		}

		$cache_key = 'mailster_templates_' . $query_args['browse'] . '_' . md5( serialize( $query_args ) . MAILSTER_VERSION );

		if ( $force || ! ( $result = get_transient( $cache_key ) ) ) {

			$cachetime = HOUR_IN_SECONDS * 6;

			$result = array(
				'total' => 0,
				'items' => array(),
				'error' => null,
			);

			if ( $query_args['browse'] == 'installed' ) {
				$result['items'] = $templates;
			}

			$headers = array(
				'hash'               => sha1( mailster_option( 'ID' ) ),
				'x-mailster-version' => MAILSTER_VERSION,
				'x-mailster-site'    => get_bloginfo( 'url' ),
				'x-mailster-license' => mailster()->get_license(),
			);
			$args    = array(
				'timeout' => 5,
				'headers' => $headers,
			);

			$url = add_query_arg( $query_args, $this->endpoint );

			$response      = wp_remote_get( $url, $args );
			$response_code = wp_remote_retrieve_response_code( $response );

			if ( $response_code != 200 || is_wp_error( $response ) ) {
				$result['error'] = esc_html__( 'We are currently not able to handle your request. Please try again later.', 'mailster' );
				$cachetime       = 12;
			} else {

				$response_body = wp_remote_retrieve_body( $response );

				$response_result = json_decode( $response_body, true );

				if ( json_last_error() === JSON_ERROR_NONE ) {
					$result['items'] = array_replace_recursive( ( $result['items'] ), ( $response_result['items'] ) );
					$result['total'] = max( count( $result['items'] ), $response_result['total'] );
				} else {
					$result['items'] = array();
					$result['total'] = 0;
				}
			}

			$result = $this->prepare_results( $result );

			if ( $query_args['browse'] == 'installed' ) {
				$default = mailster_option( 'default_template' );

				$updates = array_sum( wp_list_pluck( $result['items'], 'update_available' ) );
				update_option( 'mailster_templates_updates', $updates );

				// reset error on installed page
				$result['error'] = null;

				if ( $default && isset( $result['items'][ $default ] ) ) {
					$temp = $result['items'][ $default ];
					unset( $result['items'][ $default ] );
					$result['items'] = array( $default => $temp ) + $result['items'];
				}
			}

			set_transient( $cache_key, $result, $cachetime );

		}

		return $result;
	}

	public function prepare_results( $result ) {

		$templates = $this->get_templates();

		foreach ( $result['items'] as $slug => $item ) {

			// fill response with default values
			$result['items'][ $slug ]                 = array_merge( $this->template_fields, $result['items'][ $slug ] );
			$result['items'][ $slug ]['description']  = wpautop( $result['items'][ $slug ]['description'] );
			$result['items'][ $slug ]['is_supported'] = empty( $result['items'][ $slug ]['requires'] ) || version_compare( $result['items'][ $slug ]['requires'], MAILSTER_VERSION, '<=' );

			if ( $result['items'][ $slug ]['installed'] = isset( $templates[ $slug ] ) ) {
				$result['items'][ $slug ] = array_merge( $templates[ $slug ], array_filter( $result['items'][ $slug ] ) );

				// check if the template included in the plugin package has a new version
				if ( file_exists( MAILSTER_DIR . 'templates/' . $slug . '/index.html' ) ) {
					$local_template_data = $this->get_template_data( MAILSTER_DIR . 'templates/' . $slug . '/index.html' );
					if ( version_compare( $result['items'][ $slug ]['version'], $local_template_data['version'], '<' ) ) {
						$result['items'][ $slug ]['new_version'] = $local_template_data['version'];
					}
				}

				$result['items'][ $slug ]['update_available'] = isset( $result['items'][ $slug ]['new_version'] ) && version_compare( $result['items'][ $slug ]['new_version'], $result['items'][ $slug ]['version'], '>' );
				$result['items'][ $slug ]['files']            = $this->get_files( $slug );

			}
		}

		return $result;
	}

	public function result_to_html( $result, $browse = null ) {

		if ( empty( $result ) || empty( $result['items'] ) ) {
			return '';
		}

		ob_start();

		foreach ( $result['items'] as $slug => $item ) {
			if ( $browse === 'samples' ) {
				add_filter( 'mailster_excerpt_length', array( $this, 'excerpt_length_for_sample' ), 999 );
				include MAILSTER_DIR . 'views/templates/sample.php';
			} else {
				include MAILSTER_DIR . 'views/templates/template.php';
			}
		}

		$html = ob_get_contents();

		ob_end_clean();

		return $html;
	}

	public function excerpt_length_for_sample() {
		return 20;
	}


	public function get_template_data( $file ) {

		$cache_key = 'get_template_data_' . md5( $file );
		$cached    = mailster_cache_get( $cache_key );
		if ( $cached ) {
			return $cached;
		}

		$basename = false;
		$path     = dirname( $file );
		$slug     = basename( $path );
		if ( ! file_exists( $file ) && is_string( $file ) ) {
			$file_data = $file;
		} elseif ( ! file_exists( $file ) ) {
			return false;
		} else {
			$basename  = basename( $file );
			$file_data = file_get_contents( $file );
		}

		// no header
		if ( 0 !== strpos( trim( $file_data ), '<!--' ) ) {
			return false;
		}

		$template_data = $this->template_fields;

		foreach ( $this->headers as $field => $regex ) {
			if ( preg_match( '/^[ \t\/*#@]*' . preg_quote( $regex, '/' ) . ':(.*)$/mi', $file_data, $match ) ) {
				$template_data[ $field ] = trim( $match[1] );
			}
		}

		$template_data['slug'] = $slug;

		$template_data['index'] = str_replace( MAILSTER_UPLOAD_DIR, MAILSTER_UPLOAD_URI, $path ) . '/index.html';
		$template_data['src']   = str_replace( MAILSTER_UPLOAD_DIR, MAILSTER_UPLOAD_URI, $file );

		if ( empty( $template_data['name'] ) ) {
			$template_data['name'] = ucwords( $slug );
		}

		if ( empty( $template_data['author'] ) ) {
			$template_data['author'] = '';
		}

		if ( preg_match( '#index(-([0-9.]+))?\.html?#', $basename, $hits ) ) {
			$template_data['label'] = esc_html__( 'Base', 'mailster' ) . ( ! empty( $hits[2] )
			? ' ' . $hits[2] : '' );
		}

		if ( preg_match( '#notification(-([0-9.]+))?\.html?#', $basename, $hits ) ) {
			$template_data['label'] = esc_html__( 'Notification', 'mailster' ) . ( ! empty( $hits[2] )
			? ' ' . $hits[2] : '' );
		}

		$template_data['notification_module'] = preg_match( '#<module[^>]*?type="notification"(.*?)".*?</module>#ms', $file_data );

		if ( empty( $template_data['label'] ) ) {
			$template_data['label'] = substr( $basename, 0, strrpos( $basename, '.' ) );
		}
		if ( mailster_option( 'default_template' ) == $slug ) {
			$template_data['is_default'] = true;
		}

		$template_data['added'] = date( 'Y-m-d H:i:s', filectime( $file ) );

		if ( empty( $file_data['slug'] ) ) {
			$template_data['slug'] = basename( dirname( $file ) );
		}

		if ( isset( $template_data['uri'] ) ) {
			unset( $template_data['uri'] );
		}

		mailster_cache_set( $cache_key, $template_data );
		return $template_data;
	}


	/**
	 * Rename old way of storing module screnshots (indexes) with the new way (module id)
	 *
	 * Since 4.0.0
	 */
	public function update_module_thumbnails() {

		if ( ! mailster_option( 'module_thumbnails' ) ) {
			return;
		}

		$templates = $this->get_templates();

		$screenshot_folder_base = mailster( 'helper' )->mkdir( 'screenshots' );

		$wp_filesystem = mailster_require_filesystem();

		foreach ( $templates as $template ) {

			$t = mailster( 'template', $template['slug'], basename( $template['src'] ) );

			$modules = $t->get_modules_list();

			$file = $t->path . '/' . $t->slug . '/' . basename( $template['src'] );

			$hash = hash( 'crc32', md5_file( $file ) );
			if ( is_rtl() ) {
				$hash .= '-rtl';
			}
			$screenshot_modules_folder = $screenshot_folder_base . $template['slug'] . '/modules/' . $hash . '/';
			foreach ( $modules as $i => $modules ) {
				if ( ! file_exists( $screenshot_modules_folder . $i . '.jpg' ) ) {
					continue;
				}
				// rename it
				$wp_filesystem->move( $screenshot_modules_folder . $i . '.jpg', $screenshot_modules_folder . $modules['id'] . '.jpg' );
			}
		}
	}


	/**
	 *
	 *
	 * @param unknown $errors (optional)
	 */
	public function media_upload_form( $errors = null ) {

		global $type, $tab, $pagenow, $is_IE, $is_opera;

		if ( function_exists( '_device_can_upload' ) && ! _device_can_upload() ) {
			echo '<p>' . esc_html__( 'The web browser on your device cannot be used to upload files. You may be able to use the <a href="http://wordpress.org/extend/mobile/">native app for your device</a> instead.', 'mailster' ) . '</p>';
			return;
		}

		$upload_size_unit = $max_upload_size = wp_max_upload_size();
		$sizes            = array( 'KB', 'MB', 'GB' );

		for ( $u = -1; $upload_size_unit > 1024 && $u < count( $sizes ) - 1; $u++ ) {
			$upload_size_unit /= 1024;
		}

		if ( $u < 0 ) {
			$upload_size_unit = 0;
			$u                = 0;
		} else {
			$upload_size_unit = (int) $upload_size_unit;
		}
		?>

	<div id="media-upload-notice">
		<?php

		if ( isset( $errors['upload_notice'] ) ) {
			echo $errors['upload_notice'];
		}

		?>
		</div>
	<div id="media-upload-error">
		<?php

		if ( isset( $errors['upload_error'] ) && is_wp_error( $errors['upload_error'] ) ) {
			echo $errors['upload_error']->get_error_message();
		}

		?>
		</div>
		<?php
		if ( is_multisite() && ! is_upload_space_available() ) {
			return;
		}

		$post_params       = array(
			'action'   => 'mailster_template_upload_handler',
			'_wpnonce' => wp_create_nonce( 'mailster_nonce' ),
		);
		$upload_action_url = admin_url( 'admin-ajax.php' );

		$plupload_init = array(
			'runtimes'            => 'html5,silverlight,flash,html4',
			'browse_button'       => 'plupload-browse-button',
			'container'           => 'plupload-upload-ui',
			'drop_element'        => 'drag-drop-area',
			'file_data_name'      => 'async-upload',
			'multiple_queues'     => true,
			'max_file_size'       => $max_upload_size . 'b',
			'url'                 => $upload_action_url,
			'flash_swf_url'       => includes_url( 'js/plupload/plupload.flash.swf' ),
			'silverlight_xap_url' => includes_url( 'js/plupload/plupload.silverlight.xap' ),
			'filters'             => array(
				array(
					'title'      => sprintf( esc_html_x( '%s Template ZIP file', 'Mailster', 'mailster' ), 'Mailster' ),
					'extensions' => 'zip',
				),
			),
			'multipart'           => true,
			'urlstream_upload'    => true,
			'multipart_params'    => $post_params,
			'multi_selection'     => false,
		);

		?>

	<script type="text/javascript">
	var wpUploaderInit = <?php echo json_encode( $plupload_init ); ?>;
	</script>

	<div id="plupload-upload-ui" class="hide-if-no-js">
	<div id="drag-drop-area">
		<div class="drag-drop-inside">
		<p class="drag-drop-info"><?php esc_html_e( 'Drop your ZIP file here to upload new template', 'mailster' ); ?></p>
		<p><?php echo esc_html_x( 'or', 'Uploader: Drop files here - or - Select Files', 'mailster' ); ?></p>
		<p class="drag-drop-buttons"><input id="plupload-browse-button" type="button" value="<?php esc_attr_e( 'Select File', 'mailster' ); ?>" class="button" /></p>
		<p class="max-upload-size"><?php printf( esc_html__( 'Maximum upload file size: %s.', 'mailster' ), esc_html( $upload_size_unit . $sizes[ $u ] ) ); ?></p>
		<p class="uploadinfo"></p>
		</div>
	</div>
	</div>

	<div id="html-upload-ui" class="hide-if-js">
		<p id="async-upload-wrap">
			<label class="screen-reader-text" for="async-upload"><?php esc_html_e( 'Upload', 'mailster' ); ?></label>
			<input type="file" name="async-upload" id="async-upload" />
		<?php submit_button( esc_html__( 'Upload', 'mailster' ), 'button', 'html-upload', false ); ?>
			<a href="#" onclick="try{top.tb_remove();}catch(e){}; return false;"><?php esc_html_e( 'Cancel', 'mailster' ); ?></a>
		</p>
		<div class="clear"></div>
	</div>

		<?php
		if ( ( $is_IE || $is_opera ) && $max_upload_size > 100 * 1024 * 1024 ) {
			?>
		<span class="big-file-warning"><?php esc_html_e( 'Your browser has some limitations uploading large files with the multi-file uploader. Please use the browser uploader for files over 100MB.', 'mailster' ); ?></span>
			<?php
		}
	}
}
