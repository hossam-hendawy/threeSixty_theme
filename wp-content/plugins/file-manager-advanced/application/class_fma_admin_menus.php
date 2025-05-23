<?php
/*
@package: File Manager Advanced
@Class: fma_admin_menus
*/
if(class_exists('class_fma_admin_menus')) {
	return;
}
class class_fma_admin_menus {
	var $langs;
	/**
	 * AFM - Languages
	 */
	 public function __construct() {
             include('class_fma_lang.php');
			$this->langs = new class_fma_adv_lang();
	  }

	/**
	 * Loading Menus
	 */
	public function load_menus() {
		
        $fmaPer = $this->fmaPer();

        /** Authorizing only super admin to manage settings */
        $subPer = 'manage_options';
        if ( is_multisite() && !is_network_admin() ) {
            $subPer = 'manage_network';
            $fmaPer = $this->networkPer();
        }

        add_menu_page(
            __( 'File Manager', 'file-manager-advanced' ),
            __( 'File Manager', 'file-manager-advanced' ),
            $fmaPer,
            'file_manager_advanced_ui',
            array($this, 'file_manager_advanced_ui'),
            plugins_url( 'assets/icon/fma.png', __FILE__ ),
            4
        );
        add_submenu_page( 'file_manager_advanced_ui', 'Settings', 'Settings', $subPer, 'file_manager_advanced_controls', array(&$this, 'file_manager_advanced_controls'));
        if(!class_exists('file_manager_advanced_shortcode')) {
		    add_submenu_page( 'file_manager_advanced_ui', 'Shortcodes', 'Shortcodes', $subPer, 'file_manager_advanced_shortcodes', array(&$this, 'file_manager_advanced_shortcodes'));
	    }

		if ( ! class_exists( 'AFMP\\Modules\\Adminer' ) ) {
			add_submenu_page( 'file_manager_advanced_ui', 'DB Access', 'DB Access', 'manage_options', 'afmp-adminer', array( $this, 'adminer_menu' ) );
		}

        if ( ! class_exists( 'AFMP\\Modules\\Dropbox' ) ) {
            add_submenu_page( 'file_manager_advanced_ui', 'Dropbox Settings', 'Dropbox', 'manage_options', 'afmp-dropbox', array( $this, 'dropbox_menu'  ) );
        }
	}

	/**
	 * Dropbox menu
	 * @since 6.7.2
	 */
    public function dropbox_menu() {

        echo '<style type="text/css">
            .dropbox__heading {
                color: #000;
                font-size: 18px;
                font-style: normal;
                font-weight: 600;
                line-height: normal;
            }
            
            .dropbox__heading-pro-tag {
                display: inline-block;
                padding: 2px 8px;
                background: linear-gradient(270deg, #011D33 0%, #3F6972 100%);
                border-radius: 4px;
                color: #fff;
                font-size: 12px;
                margin-left: 25px;
            }
            
            .dropbox__wrap {
                opacity: 0.5;
                position:relative;
            }
            
            .dropbox__wrap::before {
                content: "";
                display: block;
                width: 100%;
                height: 100%;
                position: absolute;
                top: 0;
                left: 0;
                z-index: 1;
                background: transparent;
            }
        </style>
        <h2 class="dropbox__heading">Dropbox Settings <span class="dropbox__heading-pro-tag">PRO</span></h2>

        <div class="dropbox__wrap">
            <table class="form-table">
                <tr>
                    <th>
                        <lable for="fma__enable">Enable</lable>
                    </th>
                    <td>
                        <input type="checkbox" id="fma__enable">
                    </td>
                </tr>
                
                <tr>
                    <th>
                        <label for="afm__alias">Alias</label>
                    </th>
                    <td>
                        <input type="text" id="afm__alias" class="regular-text">
                        <p class="desc">
                            <strong>Enter a title which will be displayed on File Manager</strong>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th>
                        <label for="afm__app_key">App Key</label>
                    </th>
                    <td>
                        <input type="text" id="afm__app_key" class="regular-text">
                        <p class="desc">
                            <strong>Enter your Dropbox App key, you will get your app key from <a href="#">Dropbox App Console</a></strong>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th>
                        <label for="afm__app_secret">App Secret</label>
                    </th>
                    <td>
                        <input type="text" id="afm__app_secret" class="regular-text">
                        <p class="desc">
                            <strong>Enter your Dropbox App secret, you will get your app secret from <a href="#">Dropbox App Console</a></strong>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th>
                        <label for="afm__redirect_url">Redirect URL</label>
                    </th>
                    <td>
                        <input type="text" id="afm__redirect_uri" class="regular-text">
                        
                        <p class="desc">
                            <strong>
                                Copy this URL and paste it in your Dropbox App Console under Redirect URIs
                            </strong>
                        </p>
                    </td>
                </tr>
            </table>';

        submit_button();

        echo '</div>';
    }

	/**
	 * Adminer menu
	 * @since 6.7.2
	 */
	public function adminer_menu() {
		require_once FMAFILEPATH . 'templates/adminer.php';
	}

	/** 
	 * Fma permissions
	 */
	public function fmaPer() {
		$settings = $this->get();
		$user = wp_get_current_user();
		$allowed_fma_user_roles = isset($settings['fma_user_roles']) ? $settings['fma_user_roles'] : array('administrator');

		if(!in_array('administrator', $allowed_fma_user_roles)) {
		$fma_user_roles = array_merge(array('administrator'), $allowed_fma_user_roles);
		} else {
			$fma_user_roles = $allowed_fma_user_roles;
		}

		$checkUserRoleExistance = array_intersect($fma_user_roles, $user->roles);

		if(count($checkUserRoleExistance) > 0 && !in_array('administrator', $checkUserRoleExistance)) {
            $fmaPer = 'read';
		} else {
			$fmaPer = 'manage_options';
		}
		return $fmaPer;
	}
	/**
	 * Fma - Network Permissions
	 */
	public function networkPer() {
		$settings = $this->get();
		$user = wp_get_current_user();
		$allowed_fma_user_roles = isset($settings['fma_user_roles']) ? $settings['fma_user_roles'] : array();

		$fma_user_roles = $allowed_fma_user_roles;

		$checkUserRoleExistance = array_intersect($fma_user_roles, $user->roles);

		if(count($checkUserRoleExistance) > 0 ) {
			if(!in_array('administrator', $checkUserRoleExistance)) {
				$fmaPer = 'read';
			} else {
				$fmaPer = 'manage_options';
			}
		} else {
			$fmaPer = 'manage_network';
		}
		return $fmaPer;	
	}
	/**
	* Diaplying AFM
    */
     public function file_manager_advanced_ui() {
		 $fmaPer = $this->fmaPer();
		 if(current_user_can($fmaPer)) {
		    include('pages/main.php');
		 }
	 }
	/**
	* Settings
    */
    public function file_manager_advanced_controls(){
		if(current_user_can('manage_options')) {
		    include('pages/controls.php');
		 }
	}
	/**
	* Shortcode
    */
    public function file_manager_advanced_shortcodes(){
		if(current_user_can('manage_options')) {
		    include('pages/buy_shortcode.php');
		 }
	}
   /**
	* Saving Options
    */
    public function save() {
	   if(isset($_POST['submit']) && wp_verify_nonce( $_POST['_fmaform'], 'fmaform' )) {
		    _e('Saving options, Please wait...','file-manager-advanced');
		   $save = array();
		   $defaultRole = array('administrator');
		   if(is_multisite()) {
			$defaultRole = array();
		   }
		   $public_dir = isset($_POST['public_path']) ? sanitize_text_field($_POST['public_path']) : '';
		   $save['fma_user_roles'] = isset($_POST['fma_user_role']) ? array_map('sanitize_text_field',$_POST['fma_user_role']) : $defaultRole;
		   $save['fma_theme'] = isset($_POST['fma_theme']) ? sanitize_text_field($_POST['fma_theme']) : 'light';
		   $save['fma_locale'] = isset($_POST['fma_locale']) ? sanitize_text_field($_POST['fma_locale']) : 'en';
		   /* Directory Traversal fix @220723 */
		   $save['public_path'] = $this->afm_sanitize_directory($public_dir);
           $save['public_url'] = isset($_POST['public_url']) ? sanitize_text_field($_POST['public_url']) : '';
		   //25122022
		   $save['upload_max_size'] = isset($_POST['upload_max_size']) ? sanitize_text_field($_POST['upload_max_size']) : '0';
		   $save['display_ui_options'] = isset($_POST['display_ui_options']) ? array_map('sanitize_text_field',$_POST['display_ui_options']) : array();
           $save['hide_path'] = isset($_POST['hide_path']) ? sanitize_text_field($_POST['hide_path']) : 0;
		   $save['enable_trash'] = isset($_POST['enable_trash']) ? sanitize_text_field($_POST['enable_trash']) : 0;
		   $save['enable_htaccess'] = isset($_POST['enable_htaccess']) ? sanitize_text_field($_POST['enable_htaccess']) : 0;
		   $save['fma_upload_allow'] = isset($_POST['fma_upload_allow']) ? sanitize_text_field($_POST['fma_upload_allow']) : 'all';
		   $save['fma_cm_theme'] = isset($_POST['fma_cm_theme']) ? sanitize_text_field($_POST['fma_cm_theme']) : 'default';	   
		  $u = update_option('fmaoptions',$save);
		  if($u) {
			  $this->f('?page=file_manager_advanced_controls&status=1');
		  } else {
			  $this->f('?page=file_manager_advanced_controls&status=2');
		  }
	   }
   }
   /**
	* Sanitize directory path
    */
	public function afm_sanitize_directory($path = '') {
        if(!empty($path)) {
			$path = str_replace('..', '', htmlentities(trim($path)));
		}
		return $path;	
	}
   /**
	* Getting Options
    */
   public function get() {
	   return get_option('fmaoptions');
   }
   /**
	* Diplay Notices
    */
   public function notice($type, $message) {
	    if(isset($type) && !empty($type)) {
	     $class = ($type == '1') ? 'updated' : 'error';
         return '<div class="'.$class.' notice">
		  <p>'.$message.'</p>
		  </div>';
		}
   }
   /**
	* Redirection
    */
    public function f($u) {
		$url = esc_url_raw($u);
		wp_register_script( 'fma-redirect-script', '');
		wp_enqueue_script( 'fma-redirect-script' );
		wp_add_inline_script(
		'fma-redirect-script',
		' window.location.href="'.$url.'" ;'
	  );
	}
	public static function shortcodeUpdateNotice() {
		if(class_exists('file_manager_advanced_shortcode')):
			if(defined('fmas_ver')){ 
				if(fmas_ver < '2.4.1') { 
					return '<div class="error notice" style="background: #f7dfdf">
					<p><strong>Advanced File manager shortcode addon update:</strong> You are using version <strong>'.fmas_ver.'</strong> we recommend you to update to latest version. If you did not receive update please download from <a href="https://advancedfilemanager.com/my-account/" target="_blank">my account</a> page.</p>
					</div>';
				}
			} else {
				return '<div class="error notice" style="background: #f7dfdf">
					<p><strong>Advanced File manager shortcode addon update:</strong> You are using old version, we recommend you to update to latest version. If you did not receive update please download from <a href="https://advancedfilemanager.com/my-account/" target="_blank">my account</a> page.</p>
					</div>';
			}
		endif;
	}
	/**
	 * Get User Roles
	 */
	public function wpUserRoles() {
		global $wp_roles;
        return $wp_roles->roles; 
	}
}