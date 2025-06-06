<?php

use OTGS\Installer\Collection;
use OTGS\Installer\Rest\Push;
use OTGS\Installer\Recommendations\RecommendationsManager;
use OTGS\Installer\CommercialTab\SectionsManager;
use OTGS\Installer\Recommendations\Storage;
use OTGS\Installer\Settings;
use OTGS\Installer\FP\Obj;
use OTGS\Installer\Subscription\SubscriptionManagerFactory;
use OTGS\Installer\Subscription_Warning_Message;
use OTGS\Installer\Upgrade\IncludeAutoUpgrade;

class WP_Installer {

	const TOOLSET_TYPES = 'Toolset Types';
	const LEGACY_FREE_TYPES_SUBSCRIPTION_ID = 5495;
	const GRACE_TIME = MONTH_IN_SECONDS;

	protected static $_instance = null;

	public $settings = array();

	private $repositories = array();

	protected $api_debug = '';

	private $config = array();

	protected $_plugins_renew_warnings = array();

	private $admin_messages = array();

	private $ajax_messages = array();

	private $_using_icl = false;
	private $_wpml_version = false;

	private $package_source = array();

	private $plugin_finder;

	/**
	 * @phpstan-ignore-next-line
	 * @var Installer_Embedded_Plugins|null
	 */
	public $installer_embedded_plugins;

	/**
	 * @var null|bool
	 */
	private $_old_products_format_backwards_compatibility;

	const SITE_KEY_VALIDATION_SOURCE_OTHER = 0;
	const SITE_KEY_VALIDATION_SOURCE_DOWNLOAD_SPECIFIC = 1;
	const SITE_KEY_VALIDATION_SOURCE_DOWNLOAD_REPORT = 2;
	const SITE_KEY_VALIDATION_SOURCE_REGISTRATION = 3;
	const SITE_KEY_VALIDATION_SOURCE_REVALIDATION = 4;
	const SITE_KEY_VALIDATION_SOURCE_UPDATES_CHECK = 5;
	const SITE_KEY_VALIDATION_SOURCE_REVALIDATION_DAILY = 6;

	public $dependencies;

	private $components_setting;

	/** @var bool $repositories_already_refreshed */
	private $repositories_already_refreshed = false;

	/**
	 * @var OTGS_Products_Config_Xml
	 */
	private $products_config_xml;

	/**
	 * @var OTGS_Products_Manager
	 */
	private $products_manager;

	/**
	 * @var RecommendationsManager
	 */
	private $recommendations_manager;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function __construct() {
		$this->settings = $this->get_settings();

		add_action( 'admin_notices', array( $this, 'show_admin_messages' ) );

		add_action( 'admin_init', array( $this, 'load_embedded_plugins' ), 0 );

		add_action( 'admin_menu', array( $this, 'menu_setup' ) );
		add_action( 'network_admin_menu', array( $this, 'menu_setup' ) );
		add_filter( 'plugins_api', array( $this, 'custom_plugins_api_call' ), 10, 3 );

		// register repositories
		$this->load_repositories_list();
		$this->products_config_xml = $this->prepare_products_config_xml();
		$this->products_manager    = $this->prepare_products_manager();

		// default config
		$this->config['plugins_install_tab'] = false;

		add_action( 'init', array( $this, 'init' ) );
		add_action( 'init', array( $this, 'load_locale' ) );
	}

	/**
	 * @param string $name
	 * @param array  $plugin
	 *
	 * @return bool
	 */
	private static function isFreeToolsetTypes( $name, $plugin ) {
		return 'Toolset Types' === $name && version_compare( $plugin['Version'], '3.0', '<' );
	}

	public function get_repositories() {
		return $this->repositories;
	}

	/**
	 * @return OTGS_Products_Manager|null
	 */
	public function get_products_manager() {
		return $this->products_manager;
	}

	public function set_config( $key, $value ) {
		$this->config[ $key ] = $value;
	}

	public function init() {
		global $pagenow;

		$this->dependencies = new Installer_Dependencies;
		if ( $this->dependencies->php_libraries_missing() ) {
			return false;
		}

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		if ( empty( $this->settings['last_repositories_update'] ) || time() - $this->settings['last_repositories_update'] > 86400
		     || ( isset( $_GET['force-check'] ) && $_GET['force-check'] == 1 )
		) {
			$this->refresh_repositories_data();
		}

		if ( time() - $this->get_last_subscriptions_refresh() > DAY_IN_SECONDS
		     || ( isset( $_GET['force-check'] ) && $_GET['force-check'] == 1 )
		) {
			$this->refresh_subscriptions_data();
		}

		if ( empty( $this->settings['_pre_1_0_clean_up'] ) ) {
			$this->_pre_1_0_clean_up();
		}

		$this->settings = $this->_old_products_format_backwards_compatibility( $this->settings );


		$this->_using_icl    = function_exists( 'wpml_site_uses_icl' ) && wpml_site_uses_icl();
		$this->_wpml_version = defined( 'ICL_SITEPRESS_VERSION' ) ? ICL_SITEPRESS_VERSION : '';

		if ( is_multisite() || ! $this->is_installer_running_on_otgs_plugin() || ( $this->is_commercial_page() && $this->is_installer_running_on_otgs_plugin() ) ) {
			wp_enqueue_script( 'installer-admin', $this->res_url() . '/res/js/admin.js', array( 'jquery' ), $this->version() );
			wp_enqueue_script( 'otgs-installer-notification', $this->res_url() . '/dist/js/notification/app.js', [], $this->version() );
			wp_enqueue_style( 'installer-admin', $this->res_url() . '/res/css/admin.css', array(), $this->version() );
			wp_enqueue_style( 'otgs-icons' );
			wp_enqueue_style( 'installer-admin-notices', $this->res_url() . '/res/css/admin-notices.css', array(), $this->version() );
		}

		if ( $this->is_commercial_page() ) {
			wp_enqueue_script( 'expired-notice', $this->res_url() . '/dist/js/expired-notice/app.js', [], $this->version() );
		}

		wp_enqueue_script( 'installer-dismiss-nag', $this->res_url() . '/res/js/dismiss-nag.js', array( 'jquery' ), $this->version(), true );
		wp_enqueue_script( 'install-recommended_plugin', $this->res_url() . '/res/js/install_recommended_plugin.js', array( 'jquery' ), $this->version(), true );

		$translation_array = array(
			'installing' => __( 'Installing %s', 'installer' ),
			'updating'   => __( 'Updating %s', 'installer' ),
			'activating' => __( 'Activating %s', 'installer' )
		);

		wp_localize_script( 'installer-admin', 'installer_strings', $translation_array );

		if ( $pagenow == 'plugins.php' ) {
			add_action( 'admin_notices', array( $this, 'setup_plugins_page_notices' ) );
			add_action( 'admin_notices', array( $this, 'setup_plugins_renew_warnings' ), 10 );
			add_action( 'admin_notices', array( $this, 'queue_plugins_renew_warnings' ), 20 );

			add_action( 'admin_init', array( $this, 'setup_plugins_action_links' ) );

			wp_enqueue_script( 'installer-plugins', $this->res_url() . '/res/js/plugins.js', array( 'jquery' ), $this->version() );
			wp_enqueue_style( 'installer-admin-notices', $this->res_url() . '/res/css/admin-notices.css', array(), $this->version() );
		}

		if ( $this->is_repositories_page() ) {
			add_action( 'admin_init', array( $this, 'validate_repository_subscription' ) );
		}

		if ( defined( 'DOING_AJAX' ) ) {
			add_action( 'wp_ajax_installer_download_plugin', array( $this, 'download_plugin_ajax_handler' ) );
			add_action( 'wp_ajax_installer_activate_plugin', array( $this, 'activate_plugin' ) );
		}

		if ( $pagenow === 'update.php' ) {
			if ( isset( $_GET['action'] ) && $_GET['action'] === 'update-selected' ) {
				add_action( 'admin_head', array( $this, 'plugin_upgrade_custom_errors' ) );         //iframe/bulk
			} else {
				add_action( 'all_admin_notices', array( $this, 'plugin_upgrade_custom_errors' ) );  //regular/singular
			}
		}

		// WP 4.2
		if ( defined( 'DOING_AJAX' ) ) {
			add_action( 'wp_ajax_update-plugin', array(
				$this,
				'plugin_upgrade_custom_errors'
			), 0 ); // high priority, before WP
		}

		$repositories_factory          = new \OTGS_Installer_Repositories_Factory();
		$this->recommendations_manager = new RecommendationsManager(
			$repositories_factory->create( $this ),
			$this->get_settings()['repositories'],
			new Storage()
		);
		$this->recommendations_manager->addHooks();

		//Include theme support
		include_once $this->plugin_path() . '/includes/class-installer-theme.php';

		// Extra information about the source of Installer
		$package_source_file = $this->plugin_path() . '/installer-source.json';
		if ( file_exists( $package_source_file ) ) {
			WP_Filesystem();
			global $wp_filesystem;
			$this->package_source = json_decode( $wp_filesystem->get_contents( $package_source_file ) );
		}

		add_action( 'rest_api_init', Push::class . '::register_routes' );

		do_action( 'otgs_installer_initialized' );
	}

	public function get_last_subscriptions_refresh() {
		if ( isset( $this->settings['last_subscriptions_update'] ) ) {
			return $this->settings['last_subscriptions_update'];
		}

		return 0;
	}

	/**
	 * @return OTGS_Products_Manager
	 */
	private function prepare_products_manager() {
		return OTGS_Products_Manager_Factory::create(
			$this->products_config_xml,
			otgs_installer_get_logger_storage()
		);
	}

	/**
	 * @return OTGS_Products_Config_Xml
	 */
	private function prepare_products_config_xml() {
		return new OTGS_Products_Config_Xml( $this->get_xml_config_file() );
	}

	private function is_installer_running_on_otgs_plugin() {
		return ( defined( 'ICL_PLUGIN_PATH' ) && false !== strpos( $this->plugin_path(), (string) realpath( ICL_PLUGIN_PATH ) ) )
		       || ( defined( 'TYPES_ABSPATH' ) && false !== strpos( $this->plugin_path(), (string) realpath( TYPES_ABSPATH ) ) )
		       || ( defined( 'WCML_PLUGIN_PATH' ) && false !== strpos( $this->plugin_path(), (string) realpath( WCML_PLUGIN_PATH ) ) );
	}

	private function is_commercial_page() {
		global $pagenow;

		return $pagenow === 'plugin-install.php' && isset( $_GET['tab'] ) && $_GET['tab'] === 'commercial';
	}

	public function log( $message ) {
		if ( file_exists( ABSPATH . 'wp-admin/includes/file.php' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			WP_Filesystem();
			global $wp_filesystem;
			if ( defined( 'WPML_INSTALLER_LOGGING' ) && WPML_INSTALLER_LOGGING ) {
				$wp_filesystem->put_contents( $this->plugin_path() . '/installer.log', current_time( 'mysql' ) . "\t" . $message . "\n" );
			}
		}
	}

	public function register_admin_message( $text, $type = 'updated' ) {
		$this->admin_messages[] = array( 'text' => $text, 'type' => $type );
		if ( defined( 'DOING_AJAX' ) ) {
			$this->ajax_messages[] = '<p>' . $text . '</p>';
		}
	}

	public function show_admin_messages() {
		if ( ! empty( $this->admin_messages ) ) {
			$types = array( 'error', 'updated', 'notice' );
			foreach ( $this->admin_messages as $message ) {
				$class = in_array( $message['type'], $types, true ) ? $message['type'] : 'updated';
				?>
                <div class="<?php
				echo $class ?>">
                    <p>
						<?php
						echo $message['text'] ?>
                    </p>
                </div>
				<?php
			}
		}
	}

	public function load_locale() {
		if ( function_exists( 'get_user_locale' ) ) {
			$locale = get_user_locale();
		} else {
			$locale = get_locale();
		}
		$locale  = apply_filters( 'plugin_locale', $locale, 'installer' );
		$mo_file = $this->plugin_path() . '/locale/installer-' . $locale . '.mo';
		if ( file_exists( $mo_file ) ) {
			load_textdomain( 'installer', $mo_file );
		}
	}

	public function load_embedded_plugins() {
		if ( file_exists( $this->plugin_path() . '/embedded-plugins' ) ) {
			include_once $this->plugin_path() . '/embedded-plugins/embedded-plugins.class.php';
			if ( class_exists( 'Installer_Embedded_Plugins' ) ) {
				$this->installer_embedded_plugins = new Installer_Embedded_Plugins();
			}
		}
	}

	public function menu_setup() {
		global $pagenow;

		if ( is_multisite() && ! is_network_admin() ) {
			$this->menu_multisite_redirect();
			add_options_page(
				__( 'Installer', 'installer' ),
				__( 'Installer', 'installer' ),
				'install_plugins',
				'installer',
				array(
					$this,
					'show_products',
				)
			);
		} else {
			if ( $this->config['plugins_install_tab'] && is_admin() && $pagenow === 'plugin-install.php' ) {
				// Default GUI, under Plugins -> Install
				add_filter( 'install_plugins_tabs', array( $this, 'add_install_plugins_tab' ) );
				add_action( 'install_plugins_commercial', array( $this, 'show_products' ) );
			}
		}
	}

	public static function menu_url() {
		if ( is_multisite() ) {
			if ( is_network_admin() ) {
				$url = network_admin_url( 'plugin-install.php?tab=commercial' );
			} else {
				$url = admin_url( 'options-general.php?page=installer' );
			}
		} else {
			$url = admin_url( 'plugin-install.php?tab=commercial' );
		}

		return $url;
	}

	private function menu_multisite_redirect() {
		global $pagenow;

		if ( $pagenow === 'plugin-install.php' && isset( $_GET['tab'] ) && $_GET['tab'] === 'commercial' ) {
			wp_redirect( $this->menu_url() );
			exit;
		}
	}

	private function _pre_1_0_clean_up() {
		global $wpdb;

		if ( ! defined( 'WPRC_VERSION' ) ) {
			$old_tables = array(
				$wpdb->prefix . 'wprc_cached_requests',
				$wpdb->prefix . 'wprc_extension_types',
				$wpdb->prefix . 'wprc_extensions',
				$wpdb->prefix . 'wprc_repositories',
				$wpdb->prefix . 'wprc_repositories_relationships',
			);

			foreach ( $old_tables as $table ) {
				$wpdb->query( sprintf( "DROP TABLE IF EXISTS %s", $table ) );
			}
		}

		$this->settings['_pre_1_0_clean_up'] = true;
		$this->save_settings();
	}

	public function setup_plugins_action_links() {
		$plugins = get_plugins();

		$repositories_plugins = array();

		if ( ! empty( $this->settings['repositories'] ) ) {
			foreach ( $this->settings['repositories'] as $repository_id => $repository ) {
				foreach ( $repository['data']['packages'] as $package ) {
					if ( array_key_exists( 'products', $package ) ) {
						foreach ( $package['products'] as $product ) {
							if ( array_key_exists( 'plugins', $product ) ) {
								foreach ( $product['plugins'] as $plugin_slug ) {
									if ( ! $this->isPluginAvailableInRepositoryDownloads( $repository_id, $plugin_slug ) ) {
										continue;
									}

									$download = $this->settings['repositories'][ $repository_id ]['data']['downloads']['plugins'][ $plugin_slug ];

									if ( ! isset( $repositories_plugins[ $repository_id ][ $download['slug'] ] ) ) {
										$repositories_plugins[ $repository_id ][ $download['slug'] ] = array(
											'name'       => $download['name'],
											'registered' => $this->plugin_is_registered( $repository_id, $download['slug'] ) ? 1 : 0
										);
									}
								}
							} else {
								$this->refresh_repositories_data();
							}
						}
					} else {
						$this->refresh_repositories_data();
					}
				}

				foreach ( $plugins as $plugin_id => $plugin ) {
					$wp_plugin_slug = dirname( $plugin_id );
					if ( empty( $wp_plugin_slug ) ) {
						$wp_plugin_slug = basename( $plugin_id, '.php' );
					}

					foreach ( $repositories_plugins as $repository_id => $r_plugins ) {
						foreach ( $r_plugins as $slug => $r_plugin ) {
							if ( $wp_plugin_slug === $slug || $r_plugin['name'] === $plugin['Name'] || $r_plugin['name'] === $plugin['Title'] ) { //match order: slug, name, title

								$plugin_finder = $this->get_plugin_finder();
								$plugin_obj    = $plugin_finder->get_plugin( $slug, (string) $repository_id );

								if ( $plugin_obj && $plugin_obj->get_external_repo() && $plugin_obj->is_lite() ) {
									continue;
								}

								if ( $r_plugin['registered'] ) {
									remove_filter( 'plugin_action_links_' . $plugin_id, array(
										$this,
										'plugins_action_links_not_registered'
									) );

									add_filter( 'plugin_action_links_' . $plugin_id, array(
										$this,
										'plugins_action_links_registered'
									) );
								} else {
									if ( $this->plugin_is_registered( $plugin_obj->get_external_repo(), $slug ) || $this->plugin_is_registered( 'wpml', $slug ) ) {
										continue;
									}

									remove_filter( 'plugin_action_links_' . $plugin_id, array(
										$this,
										'plugins_action_links_registered'
									) );

									add_filter( 'plugin_action_links_' . $plugin_id, array(
										$this,
										'plugins_action_links_not_registered'
									) );

									if ( $this->should_display_types_upgrade_link( $r_plugin['name'], $plugin['Version'] ) ) {
										add_filter( 'plugin_action_links_' . $plugin_id, array( $this, 'types_upgrade_link' ) );
									}
								}
							}
						}
					}
				}
			}
		}
	}

	private function should_display_types_upgrade_link( $name, $version ) {
		return $name === self::TOOLSET_TYPES && version_compare( $version, '3.0', '<' );
	}

	public function types_upgrade_link( $links ) {
		$links[] = '<a style="color: #55AA55" target="_blank" href="' . esc_url( 'https://toolset.com/buy/?utm_source=typesplugin&utm_campaign=moving-types-to-toolset&utm_medium=plugins-page&utm_term=upgrade-link' ) . '">' . __( 'Upgrade', 'installer' ) . '</a>';

		return $links;
	}

	public function plugins_action_links_registered( $links ) {
		$links[] = '<a href="' . $this->menu_url() . '">' . __( 'Registered', 'installer' ) . '</a>';

		return $links;
	}

	public function plugins_action_links_not_registered( $links ) {
		$links[] = '<a href="' . $this->menu_url() . '">' . __( 'Register', 'installer' ) . '</a>';

		return $links;
	}

	/**
	 * Repository has valid subscription AND plugin is available for this subscription.
	 *
	 * @param $repository_id
	 * @param $slug
	 *
	 * @return bool
	 */
	public function plugin_is_registered( $repository_id, $slug ) {
		$registered = false;

		if ( $this->repository_has_valid_subscription( $repository_id ) ) {
			$subscription_type = $this->get_subscription_type_for_repository( $repository_id );
			$r_plugins         = array();

			if ( $subscription_type === self::LEGACY_FREE_TYPES_SUBSCRIPTION_ID && $slug === 'types' ) {
				return true;
			}

			foreach ( $this->settings['repositories'][ $repository_id ]['data']['packages'] as $package ) {
				foreach ( $package['products'] as $product ) {
					//consider equivalent subscriptions
					if ( ! array_key_exists( 'subscription_type_equivalent', $product ) ) {
						$product['subscription_type_equivalent'] = '';
					}

					if (
						$product['subscription_type'] === (int) $subscription_type || (int) $product['subscription_type_equivalent'] === (int) $subscription_type || $this->have_superior_subscription( $subscription_type, $product )
					) {
						foreach ( $product['plugins'] as $plugin_slug ) {
							$download                       = $this->settings['repositories'][ $repository_id ]['data']['downloads']['plugins'][ $plugin_slug ];
							$r_plugins[ $download['slug'] ] = $download['slug'];
						}
					}
				}
			}

			$registered = isset( $r_plugins[ $slug ] );
		}


		return $registered;
	}

	public function version() {
		return WP_INSTALLER_VERSION;
	}

	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( dirname( __FILE__ ) ) );
	}

	public function plugin_url() {
		if ( isset( $this->config['in_theme_folder'] ) && ! empty( $this->config['in_theme_folder'] ) ) {
			$url = untrailingslashit( get_template_directory_uri() . '/' . $this->config['in_theme_folder'] );
		} else {
			$url = untrailingslashit( plugins_url( '/', dirname( __FILE__ ) ) );
		}

		return $url;
	}

	public function vendor_url() {
		$url = null;

		$site_url    = get_site_url();
		$site_path   = ABSPATH;
		$vendor_path = dirname( dirname( $this->plugin_path() ) );

		return str_replace( '\\', '/', $site_url . '/' . substr( $vendor_path, strlen( $site_path ) ) );
	}

	public function get_embedded_at() {
		$embedded_at = str_replace( array( get_template_directory_uri(), plugins_url() ), '', $this->plugin_url() );
		preg_match( '/\/(.*?)\//', $embedded_at, $matches );

		return isset( $matches[1] ) ? $matches[1] : '';
	}

	public function is_repositories_page() {
		global $pagenow;

		return $pagenow == 'plugin-install.php' && isset( $_GET['tab'] ) && $_GET['tab'] == 'commercial';
	}

	public function res_url() {
		if ( isset( $this->config['in_theme_folder'] ) && ! empty( $this->config['in_theme_folder'] ) ) {
			$url = untrailingslashit( get_template_directory_uri() . '/' . $this->config['in_theme_folder'] );
		} else {
			$url = $this->plugin_url();
		}

		return $url;
	}

	public function save_settings() {
		Settings::save( $this->settings );

		if ( is_multisite() && is_main_site() && isset( $this->settings['repositories'] ) ) {
			$network_settings = array();

			foreach ( $this->settings['repositories'] as $rep_id => $repository ) {
				if ( isset( $repository['subscription'] ) ) {
					$network_settings[ $rep_id ] = $repository['subscription'];
				}
			}

			update_site_option( 'wp_installer_network', $network_settings );
		}
	}

	public function get_settings( $refresh = false, $shouldLoadHardcodedSiteKeys = true ) {
		if ( $refresh || empty( $this->settings ) ) {
			$this->settings = Settings::load();

			// Initialize
			if ( empty( $this->settings ) ) {
				$this->settings = array(
					'repositories' => array()
				);
			}

			if ( is_multisite() ) {
				$network_settings = maybe_unserialize( get_site_option( 'wp_installer_network' ) );
				if ( $network_settings ) {
					foreach ( $this->settings['repositories'] as $rep_id => $repository ) {
						if ( isset( $network_settings[ $rep_id ] ) ) {
							$this->settings['repositories'][ $rep_id ]['subscription'] = $network_settings[ $rep_id ];
						}
					}
				}
			}

			$this->_pre_1_8_backwards_compatibility( $this->settings );

			$this->settings = $this->_old_products_format_backwards_compatibility( $this->settings );

			if ( $shouldLoadHardcodedSiteKeys ) {
				$this->load_hardcoded_site_keys();
			}
		}

		return $this->settings;
	}

	private function load_hardcoded_site_keys() {
		if ( ! empty( $this->settings['repositories'] ) ) {
			foreach ( $this->settings['repositories'] as $repository_id => $repository ) {
				if ( $site_key = self::get_repository_hardcoded_site_key( $repository_id ) ) {
					$site_key_missing = empty( $this->settings['repositories'][ $repository_id ]['subscription']['data'] );
					$site_key_changed = ! $site_key_missing && $this->settings['repositories'][ $repository_id ]['subscription']['key'] != $site_key;

					if ( $site_key_missing || $site_key_changed ) {
						if ( ! function_exists( 'get_plugins' ) ) {
							require_once ABSPATH . 'wp-admin/includes/plugin.php';
						}
						$this->load_repositories_list();
						$response = $this->save_site_key(
							array(
								'repository_id' => $repository_id,
								'site_key'      => $site_key,
								'return'        => true,
								'nonce'         => wp_create_nonce( 'save_site_key_' . $repository_id )
							)
						);

						if ( ! empty( $response['error'] ) ) {
							$this->remove_site_key( $repository_id, false );

							$this->admin_messages[] = array(
								'type' => 'error',
								'text' => sprintf( __( 'You are using an invalid site key defined as the constant %s (most likely in wp-config.php).
                                                Please remove it or use the correct value in order to be able to register correctly.', 'installer' ), 'OTGS_INSTALLER_SITE_KEY_' . strtoupper( $repository_id ) )
							);
						}
					}
				}
			}
		}
	}

	public static function get_repository_hardcoded_site_key( $repository_id ) {
		$site_key = false;

		$site_key_constant = 'OTGS_INSTALLER_SITE_KEY_' . strtoupper( $repository_id );
		if ( defined( $site_key_constant ) ) {
			$site_key = constant( $site_key_constant );
		}

		return $site_key;
	}

	//backward compatibility, add channel
	private function _pre_1_8_backwards_compatibility( &$settings ) {
		if ( empty( $settings['_pre_1_8_clean_up'] ) ) {
			foreach ( $settings['repositories'] as $repository_id => $repository ) {
				foreach ( $repository['data']['downloads']['plugins'] as $slug => $download ) {
					if ( ! isset( $download['channel'] ) ) {
						$settings['repositories'][ $repository_id ]['data']['downloads']['plugins'][ $slug ]['channel'] = '';
					}
				}
			}
			$this->save_settings();
		}
	}

	//backward compatibility - support old products list format (downloads under products instead of global downloads list)
	private function _old_products_format_backwards_compatibility( $settings ) {
		if ( version_compare( $this->version(), '1.8', '<' ) && ! empty( $settings['repositories'] ) && empty( $this->_old_products_format_backwards_compatibility ) ) {
			foreach ( $settings['repositories'] as $repository_id => $repository ) {
				$populate_downloads = false;
				if ( isset( $repository['data'] ) ) {
					foreach ( $repository['data']['packages'] as $package_id => $package ) {
						foreach ( $package['products'] as $product_id => $product ) {
							if ( ! isset( $product['plugins'] ) ) {
								$populate_downloads = true;
								foreach ( $product['downloads'] as $download_id => $download ) {
									$settings['repositories'][ $repository_id ]['data']['packages'][ $package_id ]['products'][ $product_id ]['plugins'][] = $download['slug'];
								}
							}
						}
					}

					if ( $populate_downloads ) {
						// Add downloads branch
						foreach ( $repository['data']['packages'] as $package_id => $package ) {
							foreach ( $package['products'] as $product_id => $product ) {
								foreach ( $product['downloads'] as $download_id => $download ) {
									if ( ! isset( $settings['repositories'][ $repository_id ]['data']['downloads']['plugins'][ $download['slug'] ] ) ) {
										$settings['repositories'][ $repository_id ]['data']['downloads']['plugins'][ $download['slug'] ] = $download;
									}
									$settings['repositories'][ $repository_id ]['data']['packages'][ $package_id ]['products'][ $product_id ]['plugins'][] = $download['slug'];
								}
								unset( $settings['repositories'][ $repository_id ]['data']['packages'][ $package_id ]['products'][ $product_id ]['downloads'] );
							}
						}
					}
				}
			}

			$this->_old_products_format_backwards_compatibility = true;
		}

		return $settings;
	}

	public function get_installer_site_url( $repository_id = false ) {
		global $current_site;

		$site_url = defined( 'ATE_CLONED_SITE_URL' ) ? ATE_CLONED_SITE_URL : get_site_url();

		if ( $repository_id && is_multisite() && isset( $this->settings['repositories'] ) ) {
			$network_settings = maybe_unserialize( get_site_option( 'wp_installer_network' ) );

			if ( isset( $network_settings[ $repository_id ] ) ) {
				$site_url = get_site_url( $current_site->blog_id );
			}
		}

		$filtered_site_url = filter_var( apply_filters( 'otgs_installer_site_url', $site_url ), FILTER_SANITIZE_URL );

		return $filtered_site_url ? $filtered_site_url : $site_url;
	}

	/**
	 * @param string $repository_id
	 *
	 * @return string|null
	 */
	public function get_registered_site_url( $repository_id ) {
		if ( isset( $this->settings['repositories'][ $repository_id ]['subscription']['site_url'] ) ) {
			return $this->settings['repositories'][ $repository_id ]['subscription']['site_url'];
		}

		return null;
	}

	public function get_site_key_nags_config() {
		return isset( $this->config['site_key_nags'] ) ? $this->config['site_key_nags'] : [];
	}

	/**
	 * @return array
	 */
	public function getRepositories() {
		$repositories = [];
		foreach ( $this->repositories as $repositoryId => $repository ) {
			$repositories[] = [ 'repository_id' => $repositoryId ];
		}

		return $repositories;
	}

	public function add_install_plugins_tab( $tabs ) {
		$tabs['commercial'] = __( 'Commercial', 'installer' );

		return $tabs;
	}

	public function load_repositories_list() {
		$config_file = $this->get_xml_config_file();

		if ( $config_file ) {
			$repos = simplexml_load_file( $config_file );

			if ( $repos ) {
				foreach ( $repos as $repo ) {
					$id = strval( $repo->id );

					// excludes rule;
					if ( isset( $this->config['repositories_exclude'] ) && in_array( $id, $this->config['repositories_exclude'] ) ) {
						continue;
					}

					$data['api-url'] = strval( $repo->apiurl );

					$this->repositories[ $id ] = $data;
					$this->set_predefined_config( $id, 'api-url', 'API_URL' );
				}
			}
		}
	}

	/**
	 * @return string|null
	 */
	public function get_xml_config_file() {
		$file_name = 'repositories.xml';

		if ( file_exists( $this->get_config_file_path( $file_name ) ) ) {
			return $this->get_config_file_path( $file_name );
		}

		return null;
	}

	/**
	 * @return string
	 */
	private function get_config_file_path( $file_name ) {
		return __DIR__ . '/../' . $file_name;
	}

	/**
	 * @param string $id
	 */
	private function set_predefined_config( $id, $setting_field, $constant_suffix ) {
		$repo_upper = strtoupper( $id );
		if ( defined( "OTGS_INSTALLER_{$repo_upper}_{$constant_suffix}" ) ) {
			$this->repositories[ $id ][ $setting_field ] = constant( "OTGS_INSTALLER_{$repo_upper}_{$constant_suffix}" );
		}
	}

	public function filter_repositories_list() {
		if ( ! empty( $this->settings['repositories'] ) ) {
			foreach ( $this->settings['repositories'] as $id => $repo_data ) {
				// excludes rule;
				if ( isset( $this->config['repositories_exclude'] ) && in_array( $id, $this->config['repositories_exclude'] ) ) {
					unset( $this->settings['repositories'][ $id ] );
				}
			}
		}
	}

	public function refresh_subscriptions_data() {
		$this->settings['last_subscriptions_update'] = time();

		foreach ( $this->repositories as $repository_id => $data ) {
			$site_key = $this->get_site_key( $repository_id );

			if ( ! $site_key ) {
				continue;
			}

			try {
				$subscriptionManagerFactory = new SubscriptionManagerFactory( $this->get_settings() );
				$subscriptionManager        = $subscriptionManagerFactory->create( $repository_id, $this->repositories[ $repository_id ]['api-url'] );
				list ( $subscription_data, $site_key_data ) = $subscriptionManager->fetch( $site_key, self::SITE_KEY_VALIDATION_SOURCE_REVALIDATION_DAILY );

				if ( ! $subscription_data ) {
					$message = sprintf(
						"Installer could not fetch subscription data for %s. Error message: Invalid site key for the current site.",
						$repository_id
					);

					$this->log_subscription_update( $message );

					continue;
				}

				$this->settings['repositories'][ $repository_id ]['subscription']['data']     = $subscription_data;
				$this->settings['repositories'][ $repository_id ]['subscription']['key_type'] = isset( $site_key_data['type'] )
					? (int) $site_key_data['type'] : OTGS_Installer_Subscription::SITE_KEY_TYPE_PRODUCTION;

				$actualSiteUrl                                                    = $this->get_installer_site_url( $repository_id );
				$this->settings['repositories'][ $repository_id ]['site_key_url'] = $actualSiteUrl;

				$this->setLastSuccessSubscriptionFetch( $repository_id );
				$this->log_subscription_update( "Subscriptions updated successfully." );
			} catch ( Exception $e ) {
				$this->log_subscription_update( $repository_id . ': ' . $e->getMessage() );
			}
		}

		$this->save_settings();
	}

	public function shouldDisplayConnectionIssueMessage( $repositoryId ) {
		return time() - $this->getLastSuccessSubscriptionFetch( $repositoryId ) > WEEK_IN_SECONDS
		       || $this->isUsingProductsFallback( $repositoryId );
	}

	/**
	 * @param string $repositoryId
	 *
	 * @return int
	 */
	private function getLastSuccessSubscriptionFetch( $repositoryId ) {
		if ( defined( 'OTGS_INSTALLER_OVERRIDE_LAST_SUCCESS_SUBSCRIPTION_FETCH' ) ) {
			return constant( 'OTGS_INSTALLER_OVERRIDE_LAST_SUCCESS_SUBSCRIPTION_FETCH' );
		}

		if ( isset( $this->settings['repositories'][ $repositoryId ]['last_successful_subscription_fetch'] ) ) {
			return (int) $this->settings['repositories'][ $repositoryId ]['last_successful_subscription_fetch'];
		} else {
			return time();
		}
	}

	/**
	 * @param string $repositoryId
	 *
	 * @return bool
	 */
	private function isUsingProductsFallback( $repositoryId ) {
		if ( isset( $this->settings['repositories'][ $repositoryId ]['using_products_fallback'] ) ) {
			return (bool) $this->settings['repositories'][ $repositoryId ]['using_products_fallback'];
		}

		return false;
	}

	/**
	 * @param $repositoryId
	 *
	 * @return void
	 */
	private function setLastSuccessSubscriptionFetch( $repositoryId ) {
		$this->settings['repositories'][ $repositoryId ]['last_successful_subscription_fetch'] = time();
	}

	/**
	 * @param string $repositoryId
	 * @param bool   $value
	 *
	 * @return void
	 */
	private function setUsingProductsFallback( $repositoryId, $value ) {
		$this->settings['repositories'][ $repositoryId ]['using_products_fallback'] = $value;
	}

	private function log_subscription_update( $message ) {
		$log = new OTGS_Installer_Log();
		$log->set_component( OTGS_Installer_Logger_Storage::COMPONENT_SUBSCRIPTION );
		$log->set_response( $message );
		otgs_installer_get_logger_storage()->add( $log );
	}

	public function get_recommendations( $repository_id ) {
		$subscription = $this->get_subscription( $repository_id );
		if ( $subscription->is_valid() ) {
			return $this->recommendations_manager->getRepositoryPluginsRecommendations();
		}

		return null;
	}

	public function refresh_repositories_data( $bypass_bucket = false ) {
		if ( defined( 'OTGS_DISABLE_AUTO_UPDATES' ) && OTGS_DISABLE_AUTO_UPDATES && empty( $_GET['force-check'] ) || $this->repositories_already_refreshed ) {
			if ( empty( $this->settings['repositories'] ) && $this->is_repositories_page() ) {
				foreach ( $this->repositories as $repository_id => $data ) {
					$repository_names[] = $repository_id;
				}

				$error = sprintf( __( "Installer cannot display the products information because the automatic updating for %s was explicitly disabled with the configuration below (usually in wp-config.php):", 'installer' ), strtoupper( join( ', ', $repository_names ) ) );
				$error .= '<br /><br /><code>define("OTGS_DISABLE_AUTO_UPDATES", true);</code><br /><br />';
				$error .= sprintf( __( "In order to see the products information, please run the %smanual updates check%s to initialize the products list or (temporarily) remove the above code.", 'installer' ), '<a href="' . admin_url( 'update-core.php' ) . '">', '</a>' );

				$this->register_admin_message( $error, 'error' );
			}

			return;
		}

		$this->repositories_already_refreshed = true;

		foreach ( $this->repositories as $repository_id => $data ) {
			$products_url = $this->products_manager->get_products_url(
				$repository_id,
				$this->get_repository_site_key( $repository_id ),
				$this->get_installer_site_url( $repository_id ),
				$bypass_bucket
			);

			$response = wp_remote_get( $products_url );

			$products_parser = new OTGS_Installer_Products_Parser(
				WP_Installer_Channels(),
				$this->products_config_xml,
				otgs_installer_get_logger_storage()
			);

			if ( is_wp_error( $response ) ) {
				$error
					   = sprintf( __( "Installer cannot contact our updates server to get information about the available products and check for new versions. If you are seeing this message for the first time, you can ignore it, as it may be a temporary communication problem. If the problem persists and your WordPress admin is slowing down, you can disable automated version checks. Add the following line to your wp-config.php file:",
					'installer' ), strtoupper( $repository_id ) );
				$error .= '<br /><br /><code>define("OTGS_DISABLE_AUTO_UPDATES", true);</code>';
				$this->register_admin_message( $error, 'error' );
				$this->store_log( $products_url, null, OTGS_Installer_Logger_Storage::COMPONENT_REPOSITORIES, $error );

				$this->setUsingProductsFallback( $repository_id, true );
				$this->settings['repositories'][ $repository_id ]['data'] = $products_parser->get_default_products( $repository_id );
				$this->_pre_1_8_backwards_compatibility( $this->settings );

				continue;
			}

			if ( $response && isset( $response['response']['code'] ) && $response['response']['code'] == 200 ) {
				try {
					$products = $products_parser->get_products_from_response( $products_url, $repository_id, $response );
					$this->handle_product_parsing_notices( $products_parser->get_product_notices() );
					$this->settings['repositories'][ $repository_id ]['data'] = $products;
					$this->setUsingProductsFallback( $repository_id, false );
					$this->_pre_1_8_backwards_compatibility( $this->settings );
				} catch ( OTGS_Installer_Products_Parsing_Exception $exception ) {
					$this->store_log(
						$products_url,
						null,
						OTGS_Installer_Logger_Storage::COMPONENT_REPOSITORIES,
						$exception->getMessage()
					);
					$error = __( "Information about new versions is invalid. It may be a temporary communication problem, please check for updates again.", 'installer' );
					$this->register_admin_message( $error, 'error' );
				}
			} else {
				$error = __( "Information about new versions is invalid. It may be a temporary communication problem, please check for updates again.", 'installer' );
				$this->register_admin_message( $error, 'error' );
				$this->store_log(
					$products_url,
					null,
					OTGS_Installer_Logger_Storage::COMPONENT_REPOSITORIES,
					$error
				);
			}
			$this->log( sprintf( "Checked for %s updates: %s", $repository_id, $products_url ) );
		}

		// cleanup
		if ( empty( $this->settings['repositories'] ) ) {
			$this->settings['repositories'] = array();
		}
		foreach ( $this->settings['repositories'] as $repository_id => $data ) {
			if ( ! in_array( $repository_id, array_keys( $this->repositories ) ) ) {
				unset( $this->settings['repositories'][ $repository_id ] );
			}
		}

		delete_site_transient( 'update_plugins' );

		$this->settings['last_repositories_update'] = time();
		$this->save_settings();

		return $this->ajax_messages;
	}

	private function handle_product_parsing_notices( $products_notices ) {
		if ( $products_notices ) {
			if ( $products_notices ) {
				$this->register_admin_message( implode( '<br/>', $products_notices ), 'error' );
			}
		}
	}

	public function show_products( $args = array() ) {
		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_die( __( 'Sorry, you are not allowed to manage Installer for this site.', 'installer' ) );

			return;
		}

		$screen = get_current_screen();

		if ( $screen->base === 'settings_page_installer' ) { // settings page
			echo '<div class="wrap">';
			echo '<h2>' . __( 'Installer', 'installer' ) . '</h2>';
		}

		if ( ! is_array( $args ) ) {
			$args = array();
		}
		if ( empty( $args['template'] ) ) {
			$args['template'] = 'default';
		}

		$this->filter_repositories_list();

		if ( ! empty( $this->settings['repositories'] ) ) {
			$this->localize_strings();
			$this->set_filtered_prices( $args );
			$this->set_hierarchy_and_order();

			foreach ( $this->settings['repositories'] as $repository_id => $repository ) {
				if ( $args['template'] === 'compact' ) {
					if ( isset( $args['repository'] ) && $args['repository'] === $repository_id ) {
						include $this->plugin_path() . '/templates/products-compact.php';
					}
				} else {
					include $this->plugin_path() . '/templates/repository-listing.php';
				}

				/** @phpstan-ignore-next-line */
				unset( $site_key, $subscription_type, $expired, $upgrade_options, $products_avaliable );
			}
		} else {
			echo '<p>' . __( 'No repositories defined.', 'installer' ) . '</p>';
		}

		if ( $screen->base === 'settings_page_installer' ) { // settings page
			echo '</div>';
		}
	}

	/**
	 * @param string $repositoryId
	 * @param array  $downloads List of plugins data that are available for subscription type and used on commercial tab.
	 *
	 * @return array
	 */
	public function get_plugins_sections( $repositoryId, $downloads ) {
		$sections_manager = new SectionsManager(
			$this->get_settings()['repositories']
		);

		$sections = $sections_manager->getPluginsSections( $repositoryId, $downloads );

		if ( count( $sections ) === 1 ) {
			reset( $sections );
		} //workaround to uniform legacy sections format

		return $sections;
	}

	public function get_product_price( $repository_id, $package_id, $product_id, $incl_discount = false ) {
		$price = false;

		foreach ( $this->settings['repositories'][ $repository_id ]['data']['packages'] as $package ) {
			if ( $package['id'] == $package_id ) {
				if ( isset( $package['products'][ $product_id ] ) ) {
					if ( $incl_discount && isset( $package['products'][ $product_id ]['price_disc'] ) ) {
						$price = $package['products'][ $product_id ]['price_disc'];
					} elseif ( isset( $package['products'][ $product_id ]['price'] ) ) {
						$price = $package['products'][ $product_id ]['price'];
					}
				}
				break;
			}
		}

		return $price;
	}

	public function get_product_data( $repository_id, $data_type ) {
		return isset( $this->settings['repositories'][ $repository_id ]['data'][ $data_type ] ) ?
			$this->settings['repositories'][ $repository_id ]['data'][ $data_type ]
			: null;
	}

	/**
	 * @param array $products
	 *
	 * @return string
	 */
	private function getProductPriceCurrencySymbol( $products ) {
		return ( array_key_exists( 'shop_currency_symbol', $products ) ) ? $products['shop_currency_symbol'] : '&#36;';
	}

	/**
	 * @param array $products
	 *
	 * @return string
	 */
	private function getProductPriceCurrency( $products ) {
		return ( array_key_exists( 'shop_currency', $products ) ) ? $products['shop_currency'] : 'USD';
	}

	/**
	 * @param array $products
	 * @param array $product
	 *
	 * @return string
	 */
	private function getProductPriceString( $products, $product ) {
		$currencySymbol = $this->getProductPriceCurrencySymbol( $products );
		$currency       = $this->getProductPriceCurrency( $products );

		return sprintf( '%s%d (%s)', $currencySymbol, $product['price'], $currency );
	}

	/**
	 * @param array $products
	 * @param array $product
	 *
	 * @return string
	 */
	public function getProductPriceWithDiscountString( $products, $product ) {
		$currencySymbol = $this->getProductPriceCurrencySymbol( $products );
		$currency       = $this->getProductPriceCurrency( $products );

		return sprintf( '%s%s %s%s%d%s (%s)', $currencySymbol, $product['price_disc'], '&nbsp;&nbsp;<del>', $currencySymbol, $product['price'], '</del>', $currency );
	}

	/**
	 * @param array           $repository
	 * @param array           $packages
	 * @param int|string|null $subscription_type
	 * @param bool            $expired
	 * @param array|null      $upgrade_options
	 * @param string          $repository_id
	 *
	 * @return array
	 */
	public function getRenderProductPackagesData( $repository, $packages, $subscription_type, $expired, $upgrade_options, $repository_id ) {
		return $this->_render_product_packages( $repository, $packages, $subscription_type, $expired, $upgrade_options, $repository_id );
	}

	/**
	 * @param array           $repository
	 * @param array           $packages
	 * @param int|string|null $subscription_type
	 * @param bool            $expired
	 * @param array|null      $upgrade_options
	 * @param string          $repository_id
	 *
	 * @return array
	 */
	private function _render_product_packages( $repository, $packages, $subscription_type, $expired, $upgrade_options, $repository_id ) {
		$data                = array();
		$products            = ( array_key_exists( 'data', $repository ) ) ? $repository['data'] : $repository;
		$recommended_plugins = $this->get_recommendation_plugins_to_be_installed( $repository_id );

		if ( $this->get_subscription( $repository_id )->is_wpml_blog_subscription() ) {
			$packages = $this->syncWpmlDescriptionPackageWithBlog( $packages );
		}

		foreach ( $packages as $package ) {
			$row = array( 'products' => array(), 'downloads' => array() );
			foreach ( $package['products'] as $product ) {
				// filter out free subscriptions from being displayed as buying options
				if ( empty( $product['price'] ) && ( empty( $subscription_type ) || $expired ) ) {
					continue;
				}

				//consider equivalent subscriptions
				if ( empty( $product['subscription_type_equivalent'] ) ) {
					$product['subscription_type_equivalent'] = '';
				}

				// buy base
				if ( empty( $subscription_type ) || $expired ) {
					$p['url']           = $this->append_parameters_to_buy_url( $product['url'], $repository_id );
					$p['shouldDisplay'] = ! ( isset( $product['deprecated'] ) ? (bool) $product['deprecated'] : false );

					if ( ! empty( $product['price_disc'] ) ) {
						$p['label'] = $product['call2action'] . ' - ' . $this->getProductPriceWithDiscountString( $products, $product );
					} else {
						$p['label'] = $product['call2action'] . ' - ' . $this->getProductPriceString( $products, $product );
					}
					$row['products'][] = $p;
					// renew
				} elseif ( $product['subscription_type'] == $subscription_type || $product['subscription_type_equivalent'] == $subscription_type ) {
					if ( $product['renewals'] ) {
						foreach ( $product['renewals'] as $renewal ) {
							$p['url']   = $this->append_parameters_to_buy_url( $renewal['url'], $repository_id );
							$p['label'] = $renewal['call2action'] . ' - ' . $this->getProductPriceString( $products, $renewal );
						}

						$row['products'][] = $p;
					}
				}

				// upgrades
				if ( ! empty( $upgrade_options[ $product['subscription_type'] ] ) ) {
					foreach ( $upgrade_options[ $product['subscription_type'] ] as $stype => $upgrade ) {
						if ( $stype != $subscription_type ) {
							continue;
						}

						$p['url'] = $this->append_parameters_to_buy_url( $upgrade['url'], $repository_id );
						if ( ! empty( $upgrade['price_disc'] ) ) {
							$p['label'] = $upgrade['call2action'] . ' - ' . $this->getProductPriceWithDiscountString( $products, $upgrade );
						} else {
							$p['label'] = $upgrade['call2action'] . ' - ' . $this->getProductPriceString( $products, $upgrade );
						}
						$row['products'][] = $p;
					}
				}

				// downloads
				if ( isset( $subscription_type ) && ! $expired && ( $product['subscription_type'] == $subscription_type || $product['subscription_type_equivalent'] == $subscription_type ) ) {
					foreach ( $product['plugins'] as $plugin_slug ) {
						$plugin_finder = $this->get_plugin_finder();
						$plugin        = $plugin_finder->get_plugin( $plugin_slug, $repository_id );

						if ( ! $plugin ) {
							continue;
						}

						$external_repo = $plugin->get_external_repo();

						if ( $external_repo && $this->repository_has_valid_subscription( $external_repo ) ) {
							continue;
						}

						$row['downloads'][ $plugin_slug ] = $this->settings['repositories'][ $repository_id ]['data']['downloads']['plugins'][ $plugin_slug ];
					}
				}
				// add recommended plugins to be installed
				foreach ( $row['downloads'] as &$download ) {
					$download['is_preselected_plugin'] = self::is_plugin_recommended( $download, $recommended_plugins )
					                                     && $this->can_plugin_be_installed( $download, $repository_id );
				}
				unset( $download );

				//subpackages
				if ( ! empty( $package['sub-packages'] ) ) {
					$row['sub-packages'] = $package['sub-packages'];
				}
			}

			$row['id']          = $package['id'];
			$row['image_url']   = $package['image_url'];
			$row['name']        = $package['name'];
			$row['description'] = $package['description'];

			$row['notification'] = '';
			if ( ! $this->get_subscription( $repository_id )->is_wpml_blog_subscription() && $this->has_any_preselected_plugins( $row['downloads'] ) ) {
				$row['notification'] = __( 'We have preselected the plugins your site needs to be fully multilingual.', 'installer' );
			}

			if ( ! empty( $row['products'] ) || ! empty( $row['downloads'] ) || ! empty( $row['sub-packages'] ) ) {
				$data[] = $row;
			}
		}

		return $data;
	}

	/**
	 * A plugin can be installed if it is not already installed or if it is an embedded version and the dependencies can be downloaded.
	 *
	 * @param $plugin
	 * @param $repository_id
	 *
	 * @return bool
	 */
	public function can_plugin_be_installed( $plugin, $repository_id ) {
		if ( $this->dependencies->cant_download( $repository_id ) ) {
			return false;
		}

		$isPluginIsNotInstalled = $this->plugin_is_not_installed( $plugin['name'], $plugin['slug'], $plugin['version'] );
		$isEmbeddedVersion      = $this->plugin_is_embedded_version( $plugin['name'], $plugin['slug'] );

		return ( $isPluginIsNotInstalled || $isEmbeddedVersion );
	}

	/**
	 * Retrieve the recommendation plugins to be installed for a given repository.
	 *
	 * @param string $repository_id The ID of the repository.
	 *
	 * @return array The list of recommended plugin slugs to be installed.
	 */
	public function get_recommendation_plugins_to_be_installed( $repository_id ): array {
		// Retrieve recommended plugins from the installer
		$recommendations = $this->get_recommendations( $repository_id );
		if ( $recommendations === null ) {
			return [];
		}
		$recommended_plugins = isset( $recommendations['plugins'] ) ? $recommendations['plugins'] : [];
		if ( empty( $recommended_plugins ) ) {
			return [];
		}

		$slugs = array_map( function ( $plugin ) {
			return $plugin['slug'];
		}, $recommended_plugins );

		return array_values( $slugs ); // Ensure keys are reset for the returned array
	}

	private static function is_plugin_recommended( $plugin, $recommended_plugins ): bool {
		return in_array( $plugin['slug'], $recommended_plugins );
	}

	public function get_end_user_renewal_url( $repository_id ) {
		return Collection::of( $this->settings['repositories'][ $repository_id ]['data']['packages'] )
		                 ->filter( function ( $package ) {
			                 return $package['id'] === 'wpml';
		                 } )
		                 ->head()
		                 ->get( 'products' )
		                 ->get( 'end-user-subscription' )
		                 ->getOrNull( 'url' );
	}

	public function get_extra_url_parameters() {
		$parameters = array();

		if ( ! empty( $this->package_source ) ) {
			foreach ( $this->package_source as $key => $val ) {
				$parameters[ $key ] = $val;
			}
		}

		$parameters['installer_version'] = WP_INSTALLER_VERSION;
		$parameters['theme']             = wp_get_theme()->get( 'Name' );
		$parameters['site_name']         = get_bloginfo( 'name' );

		return $parameters;
	}

	public function append_parameters_to_buy_url( $url, $repository_id, $args = array() ) {
		$url = add_query_arg( array( 'icl_site_url' => $this->get_installer_site_url( $repository_id ) ), $url );

		$affiliate_id  = false;
		$affiliate_key = false;

		// Add extra parameters for custom Installer packages
		if ( ! empty( $this->package_source ) ) {
			$extra = $this->get_extra_url_parameters();

			if ( ! empty( $extra['repository'] ) && $extra['repository'] == $repository_id ) {
				if ( ! empty( $extra['affiliate_key'] ) && ! empty( $extra['user_id'] ) ) {
					$this->config[ 'affiliate_id:' . $repository_id ]  = $extra['user_id'];
					$this->config[ 'affiliate_key:' . $repository_id ] = $extra['affiliate_key'];
					unset( $extra['affiliate_key'], $extra['user_id'], $extra['repository'] ); // no need to include these ones
				}

				$url = add_query_arg( $extra, $url );
			}
		}

		if ( isset( $this->config[ 'affiliate_id:' . $repository_id ] ) && isset( $this->config[ 'affiliate_key:' . $repository_id ] ) ) {
			$affiliate_id  = $this->config[ 'affiliate_id:' . $repository_id ];
			$affiliate_key = $this->config[ 'affiliate_key:' . $repository_id ];
		} elseif ( isset( $args[ 'affiliate_id:' . $repository_id ] ) && isset( $args[ 'affiliate_key:' . $repository_id ] ) ) {
			$affiliate_id  = $args[ 'affiliate_id:' . $repository_id ];
			$affiliate_key = $args[ 'affiliate_key:' . $repository_id ];
		} elseif ( defined( 'ICL_AFFILIATE_ID' ) && defined( 'ICL_AFFILIATE_KEY' ) ) { //support for 1 repo

			$affiliate_id  = ICL_AFFILIATE_ID;
			$affiliate_key = ICL_AFFILIATE_KEY;
		} elseif ( isset( $this->config['affiliate_id'] ) && isset( $this->config['affiliate_key'] ) ) {
			// BACKWARDS COMPATIBILITY
			$affiliate_id  = $this->config['affiliate_id'];
			$affiliate_key = $this->config['affiliate_key'];
		}

		if ( $affiliate_id && $affiliate_key ) {
			$url = add_query_arg( array( 'aid' => $affiliate_id, 'affiliate_key' => $affiliate_key ), $url );
		}

		if ( $repository_id == 'wpml' ) {
			$url = add_query_arg( array(
				'using_icl'    => $this->_using_icl,
				'wpml_version' => $this->_wpml_version
			), $url );
		}

		$url = apply_filters( 'wp_installer_buy_url', $url );

		$url = esc_url( $url );

		return $url;
	}

	/**
	 * Syncs the WPML package description with the Blog package description if applicable.
	 *
	 * @param array $packages The package data.
	 *
	 * @return array The modified packages array.
	 */
	private function syncWpmlDescriptionPackageWithBlog( array $packages ): array {
		$blogPackageIndex = $this->get_blog_package_index( $packages );
		$wpmlPackageIndex = $this->get_wpml_package_index( $packages );

		// Return early if either package is missing
		if ( $blogPackageIndex === - 1 || $wpmlPackageIndex === - 1 ) {
			return $packages;
		}

		// Update the WPML package description with the Blog package description
		$packages[ $wpmlPackageIndex ]['description'] = $packages[ $blogPackageIndex ]['description'];

		return $packages;
	}

	/**
	 * @param string $repository_id
	 *
	 * @return bool
	 */
	private function is_wpml_repository( string $repository_id ) {
		return $repository_id === 'wpml';
	}

	/**
	 * @param string $id
	 *
	 * @return bool
	 */
	private function is_blog_package( string $id ) {
		return $id === 'multilingual-blog';
	}

	/**
	 * @return OTGS_Installer_WP_Share_Local_Components_Setting
	 */
	private function get_component_setting() {
		if ( ! $this->components_setting ) {
			$this->components_setting = new OTGS_Installer_WP_Share_Local_Components_Setting();
		}

		return $this->components_setting;
	}

	public function save_site_key( $args = array() ) {
		$error = '';

		if ( isset( $args['repository_id'] ) ) {
			$repository_id = $args['repository_id'];
		} elseif ( isset( $_POST['repository_id'] ) ) {
			$repository_id = sanitize_text_field( $_POST['repository_id'] );
		} else {
			$repository_id = false;
		}

		if ( isset( $args['nonce'] ) ) {
			$nonce = $args['nonce'];
		} elseif ( isset( $_POST['nonce'] ) ) {
			$nonce = sanitize_text_field( $_POST['nonce'] );
		} else {
			$nonce = '';
		}

		if ( isset( $args['site_key'] ) ) {
			$site_key = $args['site_key'];
		} else {
			$site_key = sanitize_text_field( $_POST[ 'site_key_' . $repository_id ] );
		}
		$site_key = preg_replace( "/[^A-Za-z0-9]/", '', $site_key );

		if ( $repository_id && $nonce && wp_verify_nonce( $nonce, 'save_site_key_' . $repository_id ) ) {
			try {
				$subscriptionManagerFactory = new SubscriptionManagerFactory( $this->get_settings( false, false ) );
				$subscriptionManager        = $subscriptionManagerFactory->create( $repository_id, $this->repositories[ $repository_id ]['api-url'] );
				list ( $subscription_data, $site_key_data ) = $subscriptionManager->fetch( $site_key, self::SITE_KEY_VALIDATION_SOURCE_REGISTRATION );

				if ( $subscription_data ) {
					$this->settings['repositories'][ $repository_id ]['subscription'] = array(
						'key'           => $site_key,
						'key_type'      => isset( $site_key_data['type'] )
							? (int) $site_key_data['type'] : OTGS_Installer_Subscription::SITE_KEY_TYPE_PRODUCTION,
						'data'          => $subscription_data,
						'registered_by' => get_current_user_id(),
						'site_url'      => get_site_url(),
					);
					$this->save_settings();
					$this->clean_plugins_update_cache();
				} else {
					$error = __( 'Invalid site key for the current site.', 'installer' )
					         . '<br /> <div class="installer-footnote">' . __( 'Please note that the site key is case sensitive.', 'installer' ) . '</div>';
				}
			} catch ( Exception $e ) {
				$error = $e->getMessage();
				if ( preg_match( '#Could not resolve host: (.*)#', $error, $matches ) || preg_match( '#Couldn\'t resolve host \'(.*)\'#', $error, $matches ) ) {
					$error = sprintf( __( "%s cannot access %s to register. Try again to see if it's a temporary problem. If the problem continues, make sure that this site has access to the Internet.", 'installer' ),
						'<strong><i>' . $this->get_generic_product_name( $repository_id ) . '</i></strong>',
						'<strong><i>' . $matches[1] . '</i></strong>'
					);
				}

				$this->log_subscription_update( $repository_id . ': ' . $e->getMessage() );
			}
		}

		$return = array( 'error' => $error );

		if ( $this->api_debug ) {
			$return['debug'] = $this->api_debug;
		}

		if ( ! empty( $args['return'] ) ) {
			return $return;
		} else {
			echo json_encode( $return );
			exit;
		}
	}

	/**
	 * Alias for WP_Installer::get_repository_site_key
	 *
	 * @param string $repository_id
	 *
	 * @return string|false (site key) or false
	 * @see WP_Installer::get_repository_site_key()
	 *
	 */
	public function get_site_key( $repository_id ) {
		return WP_Installer::get_repository_site_key( $repository_id );
	}

	public function remove_site_key( $repository_id, $refresh_repositories_data = true ) {
		if ( isset( $this->settings['repositories'][ $repository_id ] ) ) {
			unset( $this->settings['repositories'][ $repository_id ]['subscription'] );
			unset( $this->settings['repositories'][ $repository_id ]['last_successful_subscription_fetch'] );

			$this->save_settings();
			$this->clean_plugins_update_cache();
			if ( $refresh_repositories_data ) {
				$this->refresh_repositories_data();
			}
		}
	}

	public function validate_repository_subscription() {
		$repository_id = isset( $_GET['validate_repository'] ) ? sanitize_text_field( $_GET['validate_repository'] ) : false;
		if ( $repository_id ) {
			$site_key = $this->get_site_key( $repository_id );
			if ( $site_key ) {
				try {
					$subscriptionManagerFactory = new SubscriptionManagerFactory( $this->get_settings() );
					$subscriptionManager        = $subscriptionManagerFactory->create( $repository_id, $this->repositories[ $repository_id ]['api-url'] );
					list ( $subscription_data, $site_key_data ) = $subscriptionManager->fetch( $site_key, self::SITE_KEY_VALIDATION_SOURCE_REVALIDATION );
				} catch ( Exception $e ) {
					$subscription_data = false;
				}

				if ( empty( $subscription_data ) ) {
					unset( $this->settings['repositories'][ $repository_id ]['subscription'] );
					delete_site_transient( 'update_plugins' );
				} else {
					$this->settings['repositories'][ $repository_id ]['subscription']['data']     = $subscription_data;
					$this->settings['repositories'][ $repository_id ]['subscription']['key_type'] = isset( $site_key_data['type'] )
						? (int) $site_key_data['type'] : OTGS_Installer_Subscription::SITE_KEY_TYPE_PRODUCTION;
				}
				$this->save_settings();
			}

			wp_redirect( $this->menu_url() . '#repository-' . $repository_id );
			exit;
		}
	}

	public function api_debug_log( $text ) {
		if ( defined( 'WPML_DEBUG_INSTALLER' ) && WPML_DEBUG_INSTALLER ) {
			if ( ! is_scalar( $text ) ) {
				$text = print_r( $text, true );
			}

			$this->api_debug .= $text . "\n";
		}
	}

	public function get_repository_site_key( $repository_id ) {
		$site_key = false;

		if ( ! empty( $this->settings['repositories'][ $repository_id ]['subscription']['key'] ) ) {
			$site_key = $this->settings['repositories'][ $repository_id ]['subscription']['key'];
		}

		return $site_key;
	}

	/**
	 * @param $repository_id
	 *
	 * @return OTGS_Installer_Subscription
	 */
	public function get_subscription( $repository_id ) {
		$data = null;
		if ( ! empty( $this->settings['repositories'][ $repository_id ]['subscription'] ) ) {
			$data = $this->settings['repositories'][ $repository_id ]['subscription'];
		}

		return new OTGS_Installer_Subscription( $data );
	}

	/**
	 * @param string $repository_id
	 * @param int    $expiredForPeriod
	 *
	 * @return bool
	 */
	public function repository_has_valid_subscription( $repository_id, $expiredForPeriod = 0 ) {
		return $this->get_subscription( $repository_id )->is_valid( $expiredForPeriod );
	}

	/**
	 * @param string $repository_id
	 * @param int    $expiredForPeriod
	 *
	 * @return bool
	 */
	public function repository_is_in_grace_period( $repository_id, $expiredForPeriod = 0 ) {
		return $this->get_subscription( $repository_id )->is_in_grace( $expiredForPeriod );
	}

	/**
	 * @param string $repository_id
	 *
	 * @return bool
	 */
	public function repository_has_refunded_subscription( $repository_id ) {
		return $this->get_subscription( $repository_id )->is_refunded();
	}

	/**
	 * @return bool
	 */
	public function should_display_unregister_link_on_refund_notice() {
		$hide_till_date = $this->get_hide_unregister_link_on_refund_notice_till_date();

		return time() > (int) $hide_till_date;
	}

	private function get_hide_unregister_link_on_refund_notice_till_date() {
		if ( defined( 'OTGS_INSTALLER_OVERRIDE_HIDE_UNREGISTERED_TILL' ) ) {
			return constant( 'OTGS_INSTALLER_OVERRIDE_HIDE_UNREGISTERED_TILL' );
		}

		if ( isset( $this->settings['hide_unregister_link_on_refund_notice_till'] ) ) {
			return $this->settings['hide_unregister_link_on_refund_notice_till'];
		}

		$hide_till_date = time() + WEEK_IN_SECONDS;
		$this->set_hide_unregister_link_on_refund_notice_date( $hide_till_date );

		return $hide_till_date;
	}

	public function set_hide_unregister_link_on_refund_notice_date( $hide_till_date ) {
		$this->settings['hide_unregister_link_on_refund_notice_till'] = $hide_till_date;
		$this->save_settings();
	}

	public function repository_has_subscription( $repository_id ) {
		$key = false;
		if ( ! empty( $this->settings['repositories'][ $repository_id ]['subscription']['key'] ) ) {
			$key = $this->settings['repositories'][ $repository_id ]['subscription']['key'];
		}

		return $key;
	}

	public function repository_has_development_site_key( $repository_id ) {
		return isset( $this->settings['repositories'][ $repository_id ]['subscription']['key_type'] )
		       && $this->settings['repositories'][ $repository_id ]['subscription']['key_type'] === OTGS_Installer_Subscription::SITE_KEY_TYPE_DEVELOPMENT;
	}

	public function repository_has_legacy_free_subscription( $repository_id ) {
		return $this->repository_has_valid_subscription( $repository_id )
		       && $this->get_subscription_type_for_repository( $repository_id ) === self::LEGACY_FREE_TYPES_SUBSCRIPTION_ID;
	}

	public function repository_has_expired_subscription( $repository_id, $expiredForPeriod = 0 ) {
		return $this->repository_has_subscription( $repository_id )
		       &&
		       ! $this->repository_has_valid_subscription( $repository_id, $expiredForPeriod )
		       &&
		       ! $this->repository_has_refunded_subscription( $repository_id );
	}

	public function repository_has_in_grace_subscription( $repository_id, $expiredForPeriod = 0 ) {
		return $this->repository_has_expired_subscription( $repository_id ) && $this->repository_is_in_grace_period( $repository_id, $expiredForPeriod );
	}

	public function get_generic_product_name( $repository_id ) {
		return $this->settings['repositories'][ $repository_id ]['data']['product-name'];
	}

	public function show_subscription_renew_warning( $repository_id, $subscription_id ) {
		$subscriptionWarningMessage = new Subscription_Warning_Message( $this );
		$warningMessage             = $subscriptionWarningMessage->get( $repository_id, $subscription_id );

		if ( ! empty( $warningMessage ) ) {
			echo '<div><p class="installer-warn-box notice notice-alt">' . $warningMessage . '</p></div>';
		}

		return ! empty( $warningMessage );
	}

	public function setup_plugins_renew_warnings() {
		$plugins                     = get_plugins();
		$subscriptions_with_warnings = [];

		foreach ( $this->settings['repositories'] as $repositoryId => $repository ) {
			$subscriptionData = Obj::path( [
				'repositories',
				$repositoryId,
				'subscription',
				'data'
			], $this->settings );

			$subscriptionType = Obj::prop( 'subscription_type', $subscriptionData );

			$subscriptionWarningMessage = new Subscription_Warning_Message( $this );
			$warningMessage             = $subscriptionWarningMessage->get( $repositoryId, $subscriptionType );

			if ( ! empty( $warningMessage ) ) {
				$subscriptions_with_warnings[ $subscriptionType ] = $warningMessage;
			}
		}


		foreach ( $plugins as $plugin_id => $plugin ) {
			$slug = dirname( $plugin_id );
			if ( empty( $slug ) ) {
				continue;
			}

			foreach ( $this->settings['repositories'] as $repository_id => $repository ) {
				if ( $this->repository_has_valid_subscription( $repository_id ) ) {
					foreach ( $repository['data']['packages'] as $package ) {
						foreach ( $package['products'] as $product ) {
							foreach ( $product['plugins'] as $plugin_slug ) {
								if ( ! $this->isPluginAvailableInRepositoryDownloads( $repository_id, $plugin_slug ) ) {
									continue;
								}

								$download = $this->settings['repositories'][ $repository_id ]['data']['downloads']['plugins'][ $plugin_slug ];

								if ( $download['slug'] == $slug || $download['name'] == $plugin['Name'] || $download['name'] == $plugin['Title'] ) { //match order: slug, name, title

									if ( isset( $subscriptions_with_warnings[ $product['subscription_type'] ] ) ) {
										$this->_plugins_renew_warnings[ $plugin_id ] = $subscriptions_with_warnings[ $product['subscription_type'] ];
									}
								}
							}
						}
					}
				}
			}
		}
	}

	public function queue_plugins_renew_warnings() {
		if ( ! empty( $this->_plugins_renew_warnings ) ) {
			foreach ( $this->_plugins_renew_warnings as $plugin_id => $message ) {
				add_action( "after_plugin_row_" . $plugin_id, array( $this, 'plugins_renew_warning' ), 10, 3 );
			}
		}
	}

	public function plugins_renew_warning( $plugin_file, $plugin_data, $status ) {
		if ( empty( $this->_plugins_renew_warnings[ $plugin_file ] ) ) {
			return;
		}

		$wp_list_table = _get_list_table( 'WP_Plugins_List_Table' );
		if ( false === $wp_list_table ) {
			return;
		}
		?>

        <tr id="<?php
		echo $plugin_data['slug']; ?>-update" data-slug="<?php
		echo $plugin_data['slug']; ?>" data-plugin="<?php
		echo $plugin_file ?>">
            <td colspan="<?php
			echo $wp_list_table->get_column_count(); ?>" class="notice notice-warning notice-otgs">
                <p>
					<?php
					echo $this->_plugins_renew_warnings[ $plugin_file ];
					?>
                </p>
            </td>
        </tr>

		<?php
	}

	public function get_subscription_type_for_repository( $repository_id ) {
		$subscription_type = false;

		if ( ! empty( $this->settings['repositories'][ $repository_id ]['subscription'] ) ) {
			$subscription_type = $this->settings['repositories'][ $repository_id ]['subscription']['data']->subscription_type;
		}

		return $subscription_type;
	}

	public function have_superior_subscription( $subscription_type, $product ) {
		$have = false;

		if ( is_array( $product['upgrades'] ) ) {
			foreach ( $product['upgrades'] as $u ) {
				if ( $u['subscription_type'] == $subscription_type ) {
					$have = true;
					break;
				}
			}
		}

		return $have;
	}

	public function is_product_available_for_download( $product_name, $repository_id ) {
		$available = false;

		$subscription_type = $this->get_subscription_type_for_repository( $repository_id );
		$expired           = $this->repository_has_expired_subscription( $repository_id );

		if ( ! $expired && $this->repository_has_subscription( $repository_id ) ) {
			$this->set_hierarchy_and_order();

			foreach ( $this->settings['repositories'][ $repository_id ]['data']['packages'] as $package_id => $package ) {
				$has_top_package = false;

				foreach ( $package['products'] as $product ) {
					if ( $subscription_type === (int) $product['subscription_type'] || $subscription_type === (int) $product['subscription_type_equivalent'] ) {
						$has_top_package = true;
						if ( $product['name'] == $product_name ) {
							return $available = true;
						}
					}
				}

				if ( ! empty( $package['sub-packages'] ) ) {
					foreach ( $package['sub-packages'] as $sub_package ) {
						foreach ( $sub_package['products'] as $product ) {
							if ( $product['name'] == $product_name && ( $subscription_type === (int) $product['subscription_type'] || $subscription_type === (int) $product['subscription_type_equivalent'] || $has_top_package ) ) {
								return $available = true;
							}
						}
					}
				}
			}
		}

		return $available;
	}

	public function get_upgrade_options( $repository_id ) {
		$all_upgrades = array();


		//get all products: packages and subpackages
		$all_products = array();
		foreach ( $this->settings['repositories'][ $repository_id ]['data']['packages'] as $package ) {
			foreach ( $package['products'] as $product ) {
				$all_products[] = $product;
			}
			if ( ! empty( $package['sub-packages'] ) ) {
				foreach ( $package['sub-packages'] as $subpackage ) {
					foreach ( $subpackage['products'] as $product ) {
						$all_products[] = $product;
					}
				}
			}
		}

		foreach ( $all_products as $product ) {
			if ( $product['upgrades'] ) {
				foreach ( $product['upgrades'] as $upgrade ) {
					if ( $this->repository_has_valid_subscription( $repository_id ) || ( $this->repository_has_subscription( $repository_id ) && $upgrade['including_expired'] ) ) {
						$all_upgrades[ $upgrade['subscription_type'] ][ $product['subscription_type'] ] = $upgrade;
					}
				}
			}
		}

		return $all_upgrades;
	}

	public function append_site_key_to_download_url( $url, $key, $repository_id ) {
		$url_params['site_key'] = $key;
		$url_params['site_url'] = $this->get_installer_site_url( $repository_id );


		// Add extra parameters for custom Installer packages
		if ( ! empty( $this->package_source ) ) {
			$extra = $this->get_extra_url_parameters();
			if ( ! empty( $extra['repository'] ) && $extra['repository'] == $repository_id ) {
				unset( $extra['repository'] );
				foreach ( $extra as $key => $val ) {
					$url_params[ $key ] = $val;
				}
			}
		}

		$url = add_query_arg( $url_params, $url );

		if ( $repository_id == 'wpml' ) {
			$url = add_query_arg( array(
				'using_icl'    => $this->_using_icl,
				'wpml_version' => $this->_wpml_version
			), $url );
		}

		return $url;
	}

	public function plugin_is_not_installed( $name, $slug, $version = null ) {
		return ! $this->plugin_is_installed( $name, $slug, $version );
	}

	public function plugin_is_installed( $name, $slug, $version = null ) {
		$is = false;

		$plugins = get_plugins();

		foreach ( $plugins as $plugin_id => $plugin ) {
			$wp_plugin_slug = dirname( $plugin_id );

			// Exception: embedded plugins
			if ( $wp_plugin_slug == $slug || $plugin['Name'] == $name || $plugin['Title'] == $name || ( $wp_plugin_slug == $slug . '-embedded' || $plugin['Name'] == $name . ' Embedded' ) ) {
				if ( $version ) {
					if ( version_compare( $plugin['Version'], $version, '>=' ) ) {
						$is = $plugin['Version'];
					}
				} else {
					$is = $plugin['Version'];
				}

				break;
			}
		}

		//exception: Types name difference
		if ( ! $is && $name == 'Types' ) {
			return $this->plugin_is_installed( 'Types - Complete Solution for Custom Fields and Types', $slug, $version );
		}

		return $is;
	}

	public function plugin_is_embedded_version( $name, $slug ) {
		$is = false;

		$plugins = get_plugins();

		//false if teh full version is also installed
		$is_full_installed = false;
		foreach ( $plugins as $plugin_id => $plugin ) {
			if ( ( $plugin['Name'] == $name && ! preg_match( "#-embedded$#", $slug ) ) ) {
				$is_full_installed = true;
				break;
			}
		}

		if ( $is_full_installed ) {
			return false;
		}

		foreach ( $plugins as $plugin_id => $plugin ) {
			// TBD
			$wp_plugin_slug = dirname( $plugin_id );
			if ( $wp_plugin_slug == $slug . '-embedded' && $plugin['Name'] == $name . ' Embedded' ) {
				$is = true;
				break;
			}
		}

		return $is;
	}

	//Alias for plugin_is_installed
	public function get_plugin_installed_version( $name, $slug ) {
		return $this->plugin_is_installed( $name, $slug );
	}

	public function get_plugin_repository_version( $repository_id, $slug ) {
		$version = false;

		if ( ! empty( $this->settings['repositories'][ $repository_id ]['data']['packages'] ) ) {
			foreach ( $this->settings['repositories'][ $repository_id ]['data']['packages'] as $package ) {
				foreach ( $package['products'] as $product ) {
					foreach ( $product['plugins'] as $plugin_slug ) {
						if ( ! array_key_exists( $plugin_slug, $this->settings['repositories'][ $repository_id ]['data']['downloads']['plugins'] ) ) {
							continue;
						}

						$download = $this->settings['repositories'][ $repository_id ]['data']['downloads']['plugins'][ $plugin_slug ];

						if ( $download['slug'] == $slug ) {
							$version = $download['version'];
							break ( 3 );
						}
					}
				}
			}
		}

		return $version;
	}

	public function is_uploading_allowed() {
		//_deprecated_function ( __FUNCTION__, '1.7.3', 'Installer_Dependencies::' . __FUNCTION__ );
		return $this->dependencies->is_uploading_allowed();
	}

	public function download_plugin_ajax_handler() {
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		require_once $this->plugin_path() . '/includes/class-installer-upgrader-skins.php';

		$data = json_decode( base64_decode( sanitize_text_field( $_POST['data'] ) ), true );

		$data_url_parsed = wp_parse_url( $data['url'] );
		$data_url_args   = $data_url_parsed['query'];
		unset( $data_url_parsed['query'] );
		$data_url = http_build_url( $data_url_parsed );

		$ret              = false;
		$plugin_id        = false;
		$message          = '';
		$connection_error = false;

		//validate subscription
		$site_key = $this->get_repository_site_key( $data['repository_id'] );
		try {
			$subscriptionManagerFactory = new SubscriptionManagerFactory( $this->get_settings() );
			$subscriptionManager        = $subscriptionManagerFactory->create( $data['repository_id'], $this->repositories[ $data['repository_id'] ]['api-url'] );
			list ( $subscription_data, $site_key_data ) = $subscriptionManager->fetch( $site_key, self::SITE_KEY_VALIDATION_SOURCE_DOWNLOAD_REPORT );
		} catch ( Exception $e ) {
			$connection_error  = $e->getMessage();
			$subscription_data = false;

			$this->store_log( $data_url, $data_url_args, OTGS_Installer_Logger_Storage::COMPONENT_SUBSCRIPTION, $connection_error );
		}

		if ( $subscription_data && ! is_wp_error( $subscription_data ) && $this->repository_has_valid_subscription( $data['repository_id'] ) ) {
			if ( wp_verify_nonce( $data['nonce'], 'install_plugin_' . $data['url'] ) ) {
				$upgrader_skins = new Installer_Upgrader_Skins(); //use our custom (mute) Skin
				$upgrader       = new Plugin_Upgrader( $upgrader_skins );

				remove_action( 'upgrader_process_complete', array( 'Language_Pack_Upgrader', 'async_upgrade' ), 20 );

				$plugins = get_plugins();

				//upgrade or install?
				$is_embedded = false;
				foreach ( $plugins as $id => $plugin ) {
					$wp_plugin_slug = dirname( $id );
					$is_embedded    = $this->plugin_is_embedded_version( preg_replace( '/ Embedded$/', '', $plugin['Name'] ), preg_replace( '/-embedded$/', '', $wp_plugin_slug ) );

					if ( $wp_plugin_slug == $data['slug'] || $is_embedded && preg_replace( '/-embedded$/', '', $wp_plugin_slug ) == $data['slug'] ) {
						/** @var string $plugin_id */
						$plugin_id = $id;
						break;
					}
				}

				if ( $plugin_id && ! $is_embedded && $this->is_plugin_out_of_date( $plugin_id ) ) { //upgrade
					$response['upgrade'] = 1;

					$plugin_is_active = is_plugin_active( $plugin_id );

					$ret = $upgrader->upgrade( $plugin_id, [ 'clear_update_cache' => false ] );

					if ( ! $ret && ! empty( $upgrader->skin->installer_error ) ) {
						if ( is_wp_error( $upgrader->skin->installer_error ) ) {
							$message = $upgrader->skin->installer_error->get_error_message() .
							           ' (' . $upgrader->skin->installer_error->get_error_data() . ')';
						}
						$plugin_version = 0;
					} else {
						if ( $plugin_is_active ) {
							//prevent redirects
							add_filter( 'wp_redirect', '__return_false' );
							activate_plugin( $plugin_id );
						}
						$plugin_version = $this->get_plugin_repository_version( $data['repository_id'], $data['slug'] );
					}
				} elseif ( $plugin_id && ! $is_embedded ) { // activate
					activate_plugin( $plugin_id );
					$ret = true;
				} else { //install

					if ( $is_embedded && $plugin_id ) {
						delete_plugins( array( $plugin_id ) );
					}

					$response['install'] = 1;
					$ret                 = $upgrader->install( $data['url'] );
					if ( ! $ret && ! empty( $upgrader->skin->installer_error ) ) {
						if ( is_wp_error( $upgrader->skin->installer_error ) ) {
							$message = $upgrader->skin->installer_error->get_error_message() .
							           ' (' . $upgrader->skin->installer_error->get_error_data() . ')';
						}
					}
				}

				$plugins = get_plugins(); //read again

				if ( $ret ) {
					foreach ( $plugins as $id => $plugin ) {
						$wp_plugin_slug = dirname( $id );
						if ( $wp_plugin_slug == $data['slug'] ) {
							$plugin_version = $plugin['Version'];
							$plugin_id      = $id;

							$include_auto_upgrade = new IncludeAutoUpgrade( $this->settings, $data['repository_id'] );
							$include_auto_upgrade->includeDuringInstall( $plugin_id );
							break;
						}
					}
				}

				if ( WP_Installer_Channels()->get_channel( $data['repository_id'] ) !== WP_Installer_Channels::CHANNEL_PRODUCTION ) {
					$download   = $this->settings['repositories'][ $data['repository_id'] ]['data']['downloads']['plugins'][ $data['slug'] ];
					$non_stable = WP_Installer_Channels()->get_download_source_channel( $plugin_version, $data['repository_id'], $download['slug'], 'plugins' );
				}
			}
		} elseif ( $connection_error ) {
			$ret     = false;
			$message = sprintf( __( 'Connection failed! Please refresh the page and try again. (%s)', 'installer' ), '<i>' . $connection_error . '</i>' );
		} else { //subscription not valid
			$ret     = false;
			$message = __( 'Your subscription appears to no longer be valid. Please try to register again using a valid site key.', 'installer' );
		}

		if ( $message ) {
			$this->store_log( $data_url, $data_url_args, OTGS_Installer_Logger_Storage::COMPONENT_DOWNLOAD, $message );
		}

		$response['version']    = isset( $plugin_version ) ? $plugin_version : 0;
		$response['non_stable'] = isset( $non_stable ) ? $non_stable : '';
		$response['plugin_id']  = $plugin_id;
		$response['nonce']      = wp_create_nonce( 'activate_' . $plugin_id );
		$response['success']    = $ret;
		$response['message']    = $message;

		echo json_encode( $response );
		exit;
	}

	private function is_plugin_out_of_date( $plugin ) {
		$current = get_site_transient( 'update_plugins' );

		return isset( $current->response[ $plugin ] );
	}

	public function download_plugin( $slug, $url ) {
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		require_once $this->plugin_path() . '/includes/class-installer-upgrader-skins.php';

		$upgrader_skins = new Installer_Upgrader_Skins(); //use our custom (mute) Skin
		$upgrader       = new Plugin_Upgrader( $upgrader_skins );

		remove_action( 'upgrader_process_complete', array( 'Language_Pack_Upgrader', 'async_upgrade' ), 20 );

		$plugins = get_plugins();

		$plugin_id = false;

		//upgrade or install?
		foreach ( $plugins as $id => $plugin ) {
			$wp_plugin_slug = dirname( $id );
			if ( $wp_plugin_slug == $slug ) {
				/** @var string $plugin_id */
				$plugin_id = $id;
				break;
			}
		}

		if ( $plugin_id ) { //upgrade

			$plugin_is_active = is_plugin_active( $plugin_id );

			$ret = $upgrader->upgrade( $plugin_id );

			if ( $plugin_is_active ) {
				activate_plugin( $plugin_id );
			}
		} else { //install
			$ret = $upgrader->install( $url );
		}

		return $ret;
	}

	public function activate_plugin() {
		$error = '';

		$plugin_id = sanitize_text_field( $_POST['plugin_id'] );

		if ( isset( $_POST['nonce'] ) && $plugin_id && wp_verify_nonce( $_POST['nonce'], 'activate_' . $plugin_id ) ) {
			// Deactivate any embedded version
			$plugin_slug    = dirname( $plugin_id );
			$active_plugins = get_option( 'active_plugins' );
			foreach ( $active_plugins as $plugin ) {
				$wp_plugin_slug = dirname( $plugin );
				if ( $wp_plugin_slug == $plugin_slug . '-embedded' ) {
					deactivate_plugins( array( $plugin ) );
					break;
				}
			}

			//prevent redirects
			add_filter( 'wp_redirect', '__return_false', 10000 );

			$return = activate_plugin( $plugin_id );

			if ( is_wp_error( $return ) ) {
				$error = $return->get_error_message();
			}
		} else {
			$error = 'error';
		}

		$ret = array( 'error' => $error );

		echo json_encode( $ret );
		exit;
	}

	public function custom_plugins_api_call( $result, $action, $args ) {
		if ( $action == 'plugin_information' ) {
			if ( ! function_exists( 'get_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			$plugins           = get_plugins();
			$installed_plugins = array();
			foreach ( $plugins as $plugin_id => $plugin ) {
				// plugins by WP slug which (plugin folder) which can be different
				// will use this to compare by title
				$installed_plugins[ dirname( $plugin_id ) ] = array(
					'name'    => $plugin['Name'],
					'title'   => $plugin['Title'],
					'is_lite' => false !== stripos( $plugin['Version'], '-lite' ),
				);
			}

			$slug          = $args->slug;
			$custom_plugin = false;

			foreach ( $this->settings['repositories'] as $repository_id => $repository ) {
				if ( ! $this->repository_has_valid_subscription( $repository_id ) ) {
					$site_key = false;
				} else {
					$site_key = $repository['subscription']['key'];
				}

				foreach ( $repository['data']['packages'] as $package ) {
					foreach ( $package['products'] as $product ) {
						foreach ( $product['plugins'] as $plugin_slug ) {
							$download = $this->settings['repositories'][ $repository_id ]['data']['downloads']['plugins'][ $plugin_slug ];

							if ( $download['slug'] == $slug
							     || isset( $installed_plugins[ $slug ] )
							        && (
								        $installed_plugins[ $slug ]['name'] == $download['name'] || $installed_plugins[ $slug ]['title'] == $download['name']
							        )
							) {
								$this_plugin           = new stdClass();
								$this_plugin->external = true;

								$this_plugin->is_free               = $this->should_fallback_under_wp_org_repo( $download,
									$site_key );
								$this_plugin->is_lite               = ! empty( $download['is-lite'] );
								$this_plugin->is_download_available = $this->is_product_available_for_download( $product['name'],
									$repository_id );

								if ( $custom_plugin ) {
									if ( ( ! $custom_plugin->is_free && $this_plugin->is_free )
									     || ( ! $custom_plugin->is_lite && $custom_plugin->is_download_available )
									     || ( $custom_plugin->is_lite && ! $this_plugin->is_download_available && $installed_plugins[ $slug ]['is_lite'] )
									) {
										continue;
									}
								}
								$custom_plugin = $this_plugin;

								$custom_plugin->name           = $download['name'];
								$custom_plugin->slug           = $slug;
								$custom_plugin->version        = $download['version'];
								$custom_plugin->author         = '';
								$custom_plugin->author_profile = '';
								$custom_plugin->last_updated   = $download['date'];
								$custom_plugin->tested         = isset( $download['tested'] ) ? $download['tested'] : '';

								if ( $site_key ) {
									$custom_plugin->download_link = $this->append_site_key_to_download_url( $download['url'],
										$site_key, $repository_id );
								}

								$custom_plugin->homepage = $repository['data']['url'];
								$custom_plugin->sections = array(
									'Description' => $download['description'],
									'Changelog'   => $download['changelog']
								);
							}
						}
					}
				}
			}

			if ( $custom_plugin ) {
				if ( $custom_plugin->is_free ) {
					$result = false;
				} else {
					$result = $custom_plugin;
				}
			}
		}

		return $result;
	}

	private function should_fallback_under_wp_org_repo( $download, $site_key ) {
		return ( ! empty( $download['free-on-wporg'] ) || isset( $download['fallback-free-on-wporg'] ) && $download['fallback-free-on-wporg'] && ! $site_key ) && $download['channel'] == WP_Installer_Channels::CHANNEL_PRODUCTION;
	}

	private function has_non_wporg_upgrade_available( $plugin_id ) {
		$plugins_update_data = get_site_transient( 'update_plugins' );

		return ! empty( $plugins_update_data->response[ $plugin_id ] )
		       &&
		       ! preg_match( '/w\.org/', $plugins_update_data->response[ $plugin_id ]->id );
	}

	public function setup_plugins_page_notices() {
		$plugins = get_plugins();

		$template_service = OTGS_Template_Service_Factory::create(
			$this->plugin_path() . '/templates/php/components-setting/'
		);


		$plugin_page_notice = new OTGS_Installer_Plugins_Page_Notice( $template_service, $this->get_plugin_finder() );

		foreach ( $plugins as $plugin_id => $plugin ) {
			$slug = dirname( $plugin_id );
			if ( empty( $slug ) ) {
				continue;
			}

			$name = $plugin['Name'];

			foreach ( $this->settings['repositories'] as $repository_id => $repository ) {
				if ( ! $this->repository_has_valid_subscription( $repository_id ) ) {
					$site_key = false;
				} else {
					$site_key = $repository['subscription']['key'];
				}

				foreach ( $repository['data']['packages'] as $package ) {
					foreach ( $package['products'] as $product ) {
						foreach ( $product['plugins'] as $plugin_slug ) {
							$plugin_finder = $this->get_plugin_finder();
							$plugin_found  = $plugin_finder->get_plugin( $plugin_slug, $repository_id );

							if ( ! $plugin_found ) {
								continue;
							}

							$external_repo = $plugin_found->get_external_repo();

							if ( $external_repo && $this->plugin_is_registered( $external_repo, $plugin_slug ) ) {
								continue;
							}

							$download                    = $this->settings['repositories'][ $repository_id ]['data']['downloads']['plugins'][ $plugin_slug ];
							$display_subscription_notice = false;
							$display_setting_notice      = false;

							if ( $download['slug'] == $slug || $download['name'] == $name ) {
								if ( in_array( $name, array( 'Toolset Types', 'WPML Multilingual CMS' ), true ) ) {
									$display_setting_notice = true;
								}

								if ( ! $site_key || ! $this->plugin_is_registered( $repository_id, $download['slug'] ) ) {
									$display_setting_notice = false;

									if (
										self::isFreeToolsetTypes( $name, $plugin ) || $this->isComplementaryWithWPMLSubscription( $name, $slug ) || $download['fallback-free-on-wporg']
									) {
										$display_subscription_notice = false;
									} else {
										if ( $this->repository_has_in_grace_subscription( $repository_id, self::GRACE_TIME ) ) {
											$display_subscription_notice = [
												'type'    => 'in_grace',
												'repo'    => $repository_id,
												'product' => $repository['data']['product-name']
											];
										} elseif ( $this->repository_has_expired_subscription( $repository_id ) ) {
											$display_subscription_notice = [
												'type'    => 'expired',
												'repo'    => $repository_id,
												'product' => $repository['data']['product-name']
											];
										} elseif ( $this->repository_has_refunded_subscription( $repository_id ) ) {
											$display_subscription_notice = [
												'type'    => 'refunded',
												'repo'    => $repository_id,
												'product' => $repository['data']['product-name']
											];
										} elseif ( ! $this->repository_has_subscription( $repository_id ) ) {
											$display_subscription_notice = apply_filters(
												'otgs_installer_display_subscription_notice',
												[
													'type'    => 'register',
													'repo'    => $repository_id,
													'product' => $repository['data']['product-name']
												]
											);
										}
									}
								}
								if ( $this->plugin_is_registered( $repository_id, $download['slug'] ) && $this->repository_has_legacy_free_subscription( $repository_id ) ) {
									$display_subscription_notice = [
										'type'    => 'legacy_free',
										'repo'    => $repository_id,
										'product' => $repository['data']['product-name']
									];
								}
							}

							if ( $display_setting_notice || $display_subscription_notice ) {
								$plugin_page_notice->add_plugin(
									$plugin_id,
									array(
										OTGS_Installer_Plugins_Page_Notice::DISPLAY_SUBSCRIPTION_NOTICE_KEY => $display_subscription_notice,
										OTGS_Installer_Plugins_Page_Notice::DISPLAY_SETTING_NOTICE_KEY      => $display_setting_notice,
									)
								);
							}
						}
					}
				}
			}
		}

		$plugin_page_notice->add_hooks();
	}

	public function localize_strings() {
		if ( ! empty( $this->settings['repositories'] ) ) {
			foreach ( $this->settings['repositories'] as $repository_id => $repository ) {
				//set name as call2action when don't have any
				//products
				foreach ( $repository['data']['packages'] as $package_id => $package ) {
					foreach ( $package['products'] as $product_id => $product ) {
						if ( empty( $product['call2action'] ) ) {
							$this->settings['repositories'][ $repository_id ]['data']['packages'][ $package_id ]['products'][ $product_id ]['call2action'] = $product['name'];
						}

						foreach ( $product['upgrades'] as $idx => $upg ) {
							if ( empty( $upg['call2action'] ) ) {
								$this->settings['repositories'][ $repository_id ]['data']['packages'][ $package_id ]['products'][ $product_id ]['upgrades'][ $idx ]['call2action'] = $upg['name'];
							}
						}

						foreach ( $product['renewals'] as $idx => $rnw ) {
							if ( empty( $rnw['call2action'] ) ) {
								$this->settings['repositories'][ $repository_id ]['data']['packages'][ $package_id ]['products'][ $product_id ]['renewals'][ $idx ]['call2action'] = $rnw['name'];
							}
						}
					}
				}
			}
		}

		global $sitepress;
		if ( is_null( $sitepress ) ) {
			return;
		}

		// default strings are always in English
		$user_admin_language = $sitepress->get_admin_language();

		if ( $user_admin_language != 'en' ) {
			foreach ( $this->settings['repositories'] as $repository_id => $repository ) {
				$localization = $repository['data']['localization'];

				//packages
				foreach ( $repository['data']['packages'] as $package_id => $package ) {
					if ( isset( $localization['packages'][ $package_id ]['name'][ $user_admin_language ] ) ) {
						$this->settings['repositories'][ $repository_id ]['data']['packages'][ $package_id ]['name'] = $localization['packages'][ $package_id ]['name'][ $user_admin_language ];
					}
					if ( isset( $localization['packages'][ $package_id ]['description'][ $user_admin_language ] ) ) {
						$this->settings['repositories'][ $repository_id ]['data']['packages'][ $package_id ]['description'] = $localization['packages'][ $package_id ]['description'][ $user_admin_language ];
					}
				}

				//products
				foreach ( $repository['data']['packages'] as $package_id => $package ) {
					foreach ( $package['products'] as $product_id => $product ) {
						if ( isset( $localization['products'][ $product_id ]['name'][ $user_admin_language ] ) ) {
							$this->settings['repositories'][ $repository_id ]['data']['packages'][ $package_id ]['products'][ $product_id ]['name']
								= $localization['products'][ $product_id ]['name'][ $user_admin_language ];
						}
						if ( isset( $localization['products'][ $product_id ]['description'][ $user_admin_language ] ) ) {
							$this->settings['repositories'][ $repository_id ]['data']['packages'][ $package_id ]['products'][ $product_id ]['description']
								= $localization['products'][ $product_id ]['description'][ $user_admin_language ];
						}
						if ( isset( $localization['products'][ $product_id ]['call2action'][ $user_admin_language ] ) ) {
							$this->settings['repositories'][ $repository_id ]['data']['packages'][ $package_id ]['products'][ $product_id ]['name']
								= $localization['products'][ $product_id ]['call2action'][ $user_admin_language ];
						}
					}
				}

				//subscription info
				if ( isset( $repository['data']['subscriptions_meta']['expiration'] ) ) {
					foreach ( $repository['data']['subscriptions_meta']['expiration'] as $subscription_id => $note ) {
						if ( isset( $localization['subscriptions-notes'][ $subscription_id ]['expiration-warning'][ $user_admin_language ] ) ) {
							$this->settings['repositories'][ $repository_id ]['data']['subscriptions_meta']['expiration'][ $subscription_id ]['warning_message']
								= $localization['subscriptions-notes'][ $subscription_id ]['expiration-warning'][ $user_admin_language ];
						}
					}
				}
			}
		}
	}

	public function get_matching_cp( $repository, $args = array() ) {
		$match = false;


		$cp_name = $cp_author = false;

		if ( isset( $this->config['src_name'] ) && isset( $this->config['src_author'] ) ) {
			$cp_name   = $this->config['src_name'];
			$cp_author = $this->config['src_author'];
		} elseif ( isset( $args['src_name'] ) && isset( $args['src_author'] ) ) {
			$cp_name   = $args['src_name'];
			$cp_author = $args['src_author'];
		}

		if ( isset( $repository['data']['marketing_cp'] ) ) {
			foreach ( $repository['data']['marketing_cp'] as $cp ) {
				if ( ! empty( $cp['exp'] ) && time() > $cp['exp'] ) {
					continue;
				}

				//Use theme_name for plugins too
				if ( ! empty( $cp['theme_name'] ) ) {
					if ( $cp['author_name'] == $cp_author && $cp['theme_name'] == $cp_name ) {
						$match = $cp;
						continue;
					}
				} else {
					if ( $cp['author_name'] == $cp_author ) {
						$match = $cp;
						continue;
					}
				}
			}
		}

		return $match;
	}

	public function set_filtered_prices( $args = array() ) {
		foreach ( $this->settings['repositories'] as $repository_id => $repository ) {
			$match = $this->get_matching_cp( $repository, $args );

			if ( empty( $match ) ) {
				continue;
			}

			foreach ( $repository['data']['packages'] as $package_id => $package ) {
				foreach ( $package['products'] as $product_id => $product ) {
					if ( $match['dtp'] == '%' ) {
						$fprice = round( $product['price'] * ( 1 - $match['amt'] / 100 ), 2 );
						$fprice = $fprice != round( $fprice ) ? sprintf( '%.2f', $fprice ) : round( $fprice, 0 );
					} elseif ( $match['dtp'] == '-' ) {
						$fprice = $product['price'] - $match['amt'];
					} else {
						$fprice = $product['price'];
					}

					if ( $fprice ) {
						$this->settings['repositories'][ $repository_id ]['data']['packages'][ $package_id ]['products'][ $product_id ]['price_disc'] = $fprice;

						$url_glue                                                                                                              = false !== strpos( $this->settings['repositories'][ $repository_id ]['data']['packages'][ $package_id ]['products'][ $product_id ]['url'], '?' ) ? '&' : '?';
						$cpndata                                                                                                               = base64_encode( (string) json_encode( array(
							'theme_author' => $match['author_name'],
							'theme_name'   => $match['theme_name'],
							'vlc'          => $match['vlc']
						) ) );
						$this->settings['repositories'][ $repository_id ]['data']['packages'][ $package_id ]['products'][ $product_id ]['url'] .= $url_glue . 'cpn=' . $cpndata;

						foreach ( $product['upgrades'] as $upgrade_id => $upgrade ) {
							$fprice = false;
							if ( $match['dtp'] == '%' ) {
								$fprice = round( $upgrade['price'] * ( 1 - $match['amt'] / 100 ), 2 );
								$fprice = $fprice != round( $fprice ) ? sprintf( '%.2f', $fprice ) : round( $fprice, 0 );
							} elseif ( $match['dtp'] == '-' ) {
								$fprice = $upgrade['price'] - $match['amt'];
							}
							if ( $fprice ) {
								$this->settings['repositories'][ $repository_id ]['data']['packages'][ $package_id ]['products'][ $product_id ]['upgrades'][ $upgrade_id ]['price_disc'] = $fprice;
								$this->settings['repositories'][ $repository_id ]['data']['packages'][ $package_id ]['products'][ $product_id ]['upgrades'][ $upgrade_id ]['url']        .= $url_glue . 'cpn=' . $cpndata;
							}
						}
					}
				}
			}
		}
	}

	public function set_hierarchy_and_order() {
		//2 levels
		if ( ! empty( $this->settings['repositories'] ) ) {
			foreach ( $this->settings['repositories'] as $repository_id => $repository ) {
				if ( empty( $repository['data']['packages'] ) ) {
					continue;
				}

				$all_packages     = $repository['data']['packages'];
				$ordered_packages = array();

				//backward compatibility - 'order'
				foreach ( $all_packages as $k => $v ) {
					if ( ! isset( $v['order'] ) ) {
						$all_packages[ $k ]['order'] = 0;
					}
				}

				//select parents
				foreach ( $all_packages as $package_id => $package ) {
					if ( empty( $package['parent'] ) ) {
						$ordered_packages[ $package_id ] = $package;
					}
				}

				//add sub-packages
				foreach ( $all_packages as $package_id => $package ) {
					if ( ! empty( $package['parent'] ) ) {
						if ( isset( $ordered_packages[ $package['parent'] ] ) ) {
							$ordered_packages[ $package['parent'] ]['sub-packages'][ $package_id ] = $package;
						}
					}
				}

				// order parents
				usort( $ordered_packages, array( $this, 'compare_package_order' ) );
				//order sub-packages
				foreach ( $ordered_packages as $package_id => $package ) {
					if ( ! empty( $package['sub-packages'] ) ) {
						usort( $ordered_packages[ $package_id ]['sub-packages'], array( $this, 'compare_package_order' ) );
					}
				}

				$this->settings['repositories'][ $repository_id ]['data']['packages'] = $ordered_packages;
			}
		}
	}

	public function compare_package_order( $a, $b ) {
		return $a['order'] - $b['order'];
	}

	public function get_support_tag_by_name( $name, $repository ) {
		if ( is_array( $this->settings['repositories'][ $repository ]['data']['support_tags'] ) ) {
			foreach ( $this->settings['repositories'][ $repository ]['data']['support_tags'] as $support_tag ) {
				if ( $support_tag['name'] == $name ) {
					return $support_tag['url'];
				}
			}
		}

		return false;
	}

	/**
	 * @return OTGS_Installer_Plugin_Finder
	 */
	private function get_plugin_finder() {
		if ( ! $this->plugin_finder ) {
			$this->plugin_finder = new OTGS_Installer_Plugin_Finder( new OTGS_Installer_Plugin_Factory(), $this->settings['repositories'] );
		}

		return $this->plugin_finder;
	}

	private function clean_plugins_update_cache() {
		do_action( 'otgs_installer_clean_plugins_update_cache' );
	}

	public function plugin_upgrade_custom_errors() {
		if ( isset( $_REQUEST['action'] ) ) {
			$action = isset( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : '';

			//bulk mode
			if ( 'update-selected' == $action ) {
				global $plugins;

				if ( isset( $plugins ) && is_array( $plugins ) ) {
					foreach ( $plugins as $k => $plugin ) {
						$plugin_repository = false;

						$wp_plugin_slug = dirname( $plugin );

						foreach ( $this->settings['repositories'] as $repository_id => $repository ) {
							foreach ( $repository['data']['packages'] as $package ) {
								foreach ( $package['products'] as $product ) {
									foreach ( $product['plugins'] as $plugin_slug ) {
										if ( $this->settings['repositories'][ $repository_id ]['data']['downloads']['plugins'][ $plugin_slug ]['slug'] == $wp_plugin_slug ) {
											$download          = $this->settings['repositories'][ $repository_id ]['data']['downloads']['plugins'][ $plugin_slug ];
											$plugin_repository = $repository_id;
											$product_name      = $repository['data']['product-name'];
											$plugin_name       = $download['name'];
											$free_on_wporg     = ! empty( $download['free-on-wporg'] ) && $download['channel'] == WP_Installer_Channels::CHANNEL_PRODUCTION;
											break;
										}
									}
								}
							}
						}

						if ( $plugin_repository ) {
							//validate subscription
							static $sub_cache = array();

							if ( empty( $sub_cache[ $plugin_repository ] ) ) {
								$subscription_data = false;
								$site_key          = $this->get_repository_site_key( $plugin_repository );

								if ( ! $site_key ) {
									list( $plugin_repository, $site_key ) = $this->match_product_in_external_repository( $plugin_repository, $wp_plugin_slug );
								}

								if ( $site_key ) {
									try {
										$subscriptionManagerFactory = new SubscriptionManagerFactory( $this->get_settings() );
										$subscriptionManager        = $subscriptionManagerFactory->create( $plugin_repository, $this->repositories[ $plugin_repository ]['api-url'] );
										list ( $subscription_data, $site_key_data ) = $subscriptionManager->fetch( $site_key, self::SITE_KEY_VALIDATION_SOURCE_REVALIDATION );
									} catch ( Exception $e ) {
									}
								}

								$sub_cache[ $plugin_repository ]['site_key']          = $site_key;
								$sub_cache[ $plugin_repository ]['subscription_data'] = $subscription_data;
							} else {
								$site_key          = $sub_cache[ $plugin_repository ]['site_key'];
								$subscription_data = $sub_cache[ $plugin_repository ]['subscription_data'];
							}

							if ( ! $site_key && ( ! empty( $free_on_wporg ) || $this->should_fallback_under_wp_org_repo( $download, $site_key ) ) ) { // allow the download from wp.org
								continue;
							}

							if ( empty( $site_key ) || empty( $subscription_data ) ) {
								$error_message = sprintf( __( "%s cannot update because your site's registration is not valid. Please %sregister %s%s again for this site first.", 'installer' ),
									'<strong>' . $plugin_name . '</strong>', '<a target="_top" href="' . $this->menu_url() . '&validate_repository=' . $plugin_repository .
									                                         '#repository-' . $plugin_repository . '">', $product_name, '</a>' );

								echo '<div class="updated error"><p>' . $error_message . '</p></div>';

								unset( $plugins[ $k ] );
							}
						}
					}
				}
			}


			if ( 'upgrade-plugin' == $action || 'update-plugin' == $action ) {
				$plugin = isset( $_REQUEST['plugin'] ) ? trim( sanitize_text_field( $_REQUEST['plugin'] ) ) : '';

				$wp_plugin_slug = dirname( $plugin );

				$plugin_repository = false;

				foreach ( $this->settings['repositories'] as $repository_id => $repository ) {
					foreach ( $repository['data']['packages'] as $package ) {
						foreach ( $package['products'] as $product ) {
							foreach ( $product['plugins'] as $plugin_slug ) {
								$download = $this->settings['repositories'][ $repository_id ]['data']['downloads']['plugins'][ $plugin_slug ];

								//match by folder, will change to match by name and folder
								if ( $download['slug'] == $wp_plugin_slug ) {
									$plugin_repository = $repository_id;
									$product_name      = $repository['data']['product-name'];
									$plugin_name       = $download['name'];
									$free_on_wporg     = ! empty( $download['free-on-wporg'] ) && $download['channel'] == WP_Installer_Channels::CHANNEL_PRODUCTION;
									break;
								}
							}
						}
					}
				}

				if ( $plugin_repository ) {
					//validate subscription
					$site_key = $this->get_repository_site_key( $plugin_repository );

					if ( ! $site_key ) {
						list( $plugin_repository, $site_key ) = $this->match_product_in_external_repository( $plugin_repository, $wp_plugin_slug );
					}

					if ( $site_key ) {
						try {
							$subscriptionManagerFactory = new SubscriptionManagerFactory( $this->get_settings() );
							$subscriptionManager        = $subscriptionManagerFactory->create( $plugin_repository, $this->repositories[ $plugin_repository ]['api-url'] );
							list ( $subscription_data, $site_key_data ) = $subscriptionManager->fetch( $site_key, self::SITE_KEY_VALIDATION_SOURCE_REVALIDATION );
						} catch ( Exception $e ) {
							$subscription_data = false;
						}
					}

					$no_subscription = empty( $site_key ) || empty( $subscription_data );
					$not_on_wporg    = empty( $free_on_wporg ) && ! $this->should_fallback_under_wp_org_repo( $download, $site_key );

					if ( $no_subscription && $not_on_wporg ) {
						$error_message = sprintf( __( "%s cannot update because your site's registration is not valid. Please %sregister %s%s again for this site first.", 'installer' ),
							'<strong>' . $plugin_name . '</strong>', '<a href="' . $this->menu_url() . '&validate_repository=' . $plugin_repository .
							                                         '#repository-' . $plugin_repository . '">', $product_name, '</a>' );

						if ( defined( 'DOING_AJAX' ) ) { //WP 4.2

							$status = array(
								'update'     => 'plugin',
								'plugin'     => $plugin,
								'slug'       => sanitize_key( $_POST['slug'] ),
								'oldVersion' => '',
								'newVersion' => '',
							);

							$status['errorCode'] = 'wp_installer_invalid_subscription';
							$status['error']     = $error_message;

							wp_send_json_error( $status );
						} else { // WP 4.1.1
							echo '<div class="updated error"><p>' . $error_message . '</p></div>';


							echo '<div class="wrap">';
							echo '<h2>' . __( 'Update Plugin', 'installer' ) . '</h2>';
							echo '<a href="' . admin_url( 'plugins.php' ) . '">' . __( 'Return to the plugins page', 'installer' ) . '</a>';
							echo '</div>';
							require_once( ABSPATH . 'wp-admin/admin-footer.php' );
							exit;
						}
					}
				}
			}
		}
	}

	private function store_log( $url, $url_args, $component, $response ) {
		$log = new OTGS_Installer_Log();
		$log->set_request_url( $url )
		    ->set_component( $component )
		    ->set_response( $response )
		    ->set_request_args( $url_args );

		otgs_installer_get_logger_storage()->add( $log );
	}

	public function get_api_debug() {
		return $this->api_debug;
	}

	/**
	 * @param string $current_repository
	 * @param string $plugin_slug
	 *
	 * @return array
	 */
	private function match_product_in_external_repository( $current_repository, $plugin_slug ) {
		foreach ( $this->get_repositories() as $repo => $repo_data ) {
			if ( $repo !== $current_repository ) {
				$plugin_finder = $this->get_plugin_finder();
				$plugin_obj    = $plugin_finder->get_plugin( $plugin_slug, $repo );

				if ( $plugin_obj ) {
					$site_key = $this->get_repository_site_key( $repo );

					if ( $site_key ) {
						return array( $repo, $site_key );
					}
				}
			}
		}

		return array( '', '' );
	}

	/**
	 * @param $name
	 * @param $slug
	 *
	 * @return bool
	 */
	private function isComplementaryWithWPMLSubscription( $name, $slug ) {
		return Collection::of( [ 'Toolset Types', 'Toolset Module Manager' ] )->contains( $name ) && $this->plugin_is_registered( 'wpml', $slug );
	}

	/**
	 * @param string $repository_id
	 * @param string $plugin_slug
	 *
	 * @return bool
	 */
	private function isPluginAvailableInRepositoryDownloads( $repository_id, $plugin_slug ) {
		return $plugin_slug && isset( $this->settings['repositories'][ $repository_id ]['data']['downloads']['plugins'][ $plugin_slug ] );
	}

	/**
	 * Check if any plugin in the downloads array is preselected.
	 *
	 * @param array $downloads
	 *
	 * @return bool true if there is any preselected plugin in the downloads.
	 */
	private function has_any_preselected_plugins( array $downloads ) {
		return Collection::of( $downloads )->any( function ( $download ) {
			return $download['is_preselected_plugin'] === true;
		} );
	}

	private function get_blog_package_index( $packages ) {
		return Collection::of( $packages )->firstIndex( function ( $package ) {
			return $package['id'] === 'multilingual-blog';
		} );
	}

	private function get_wpml_package_index( $packages ) {
		return Collection::of( $packages )->firstIndex( function ( $package ) {
			return $package['id'] === 'wpml';
		} );
	}
}
