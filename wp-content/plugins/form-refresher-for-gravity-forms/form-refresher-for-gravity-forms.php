<?php
/**
 * Plugin Name: Form Refresher for Gravity Forms
 * Plugin URI: https://wordpress.org/plugins/form-refresher-for-gravity-forms
 * Description: Reload the Gravity form on the AJAX submission. Useful in situations where you would like to allow multiple form submissions by refreshing the page.
 * Version:1.1
 * Author: Galaxy Weblinks
 * Author URI: http://galaxyweblinks.com/
 * Text Domain: form-refresher-for-gravity-forms
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {	die(); }

// Absolute path to the WordPress directory. 
define( 'FRFGF_URL', plugin_dir_url(__FILE__));
define( 'FRFGF_PATH', plugin_dir_path(__FILE__));

function gwrf_has_parent_plugin(){

	if (is_admin() && current_user_can('activate_plugins') && !is_plugin_active('gravityforms/gravityforms.php')) {
        deactivate_plugins(plugin_basename(__FILE__));
    

        /* If we try to activate this plugin while the parent plugin isn't active. */
        if (isset($_GET['activate']) && !wp_verify_nonce($_GET['activate'])) {

            add_action('admin_notices', 'gwrf_admin_error_notice');
            unset($_GET['activate']);
            /* If we deactivate the parent plugin while this plugin is still active. */
        } elseif (!isset($_GET['activate'])) {
        
            add_action('admin_notices', 'gwrf_parent_plugin_notice');
            unset($_GET['activate']);
        
        }
    }
}
add_action('admin_init', 'gwrf_has_parent_plugin');
// Gravity Forms is not active, deactivate this plugin and show error

function gwrf_admin_error_notice(){ 
    ?>
    <div class="error">
		<p>The <strong>Form Refresher for Gravity Forms</strong> plugin requires the <strong>Gravity Forms</strong> plugin to be active to run correctly. Please Activate it now or <a href="https://www.gravityforms.com/" target="_blank">purchase it today!</a></p>
	</div>
    <?php 
 }

function gwrf_parent_plugin_notice(){ 
	?>
	<div class="error">
		<p>Form Refresher for Gravity Forms has been deactivated because Gravity Forms has been deactivated. Gravity Forms must be active in order for you to use Form Refresher for Gravity Forms.</p>
	</div>
	<?php
}

/**
 * Including main functions
 * The FormRefresherGravityForm class.
 */
require_once( FRFGF_PATH . 'includes/form-refresher-gforms.php' );

if ( class_exists( 'FormRefresherGravityForm' ) ) {
    $gf_reload_form = new FormRefresherGravityForm();
}


