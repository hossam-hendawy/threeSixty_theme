<?php
/*
Plugin Name: WPS Hide Login
Description: Protect your website by changing the login URL and preventing access to wp-login.php page and wp-admin directory while not logged-in
Donate link: https://www.paypal.me/donateWPServeur
Author: WPServeur, NicolasKulka, wpformation
Author URI: https://wpserveur.net
Version: 1.9.6
Requires at least: 4.1
Tested up to: 6.0
Requires PHP: 7.0
Domain Path: languages
Text Domain: wps-hide-login
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if ( $_SERVER['HTTP_HOST'] !== 'localhost' ):
	require WPMU_PLUGIN_DIR . '/wps-hide-login/wps-hide-login.php';
endif;
