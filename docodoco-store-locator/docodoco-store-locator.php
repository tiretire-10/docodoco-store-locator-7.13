<?php
/**
 * DocoDoco Store Locator
 * 
 * @package		docodoco-store-locator
 * @author  	Geolocation Technology, Inc.
 * @copyright	2023 Geolocation Technology, Inc.
 * @license 	GPL-2.0-or-later
 * 
 * @wordpress-plugin
 * Plugin Name: 		DocoDoco Store Locator
 * Plugin URI: 			https://www.docodoco.jp/
 * Description: 		DocoDoco Store Locator is store locations management system for WordPress.
 * Version: 			1.0.1
 * Requires at least: 	6.0
 * Requires PHP: 		7.4
 * Author: 				Geolocation Technology, Inc.
 * Author URI: 			https://www.geolocation.co.jp/
 * Text Domain: 		docodoco-store-locator
 * License: 			GPLv2
 * License URI: 		https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('WPINC')) {
	die;
}

if (!class_exists('DocodocoStoreLocator_Bootstrap')) {

	class DocodocoStoreLocator_Bootstrap {
		function __construct() {
			global $wpdb;

			$upload_dir  = wp_upload_dir();

			define( 'DOCOSL_PLUGIN', 'docodoco-store-locator' );
			define( 'DOCOSL_PLUGIN_SHORT', 'docosl');
			define( 'DOCOSL_URL_PATH', plugin_dir_url( __FILE__ ) );
			define( 'DOCOSL_PLUGIN_PATH', plugin_dir_path(__FILE__) );
			define( 'DOCOSL_PLUGIN_CLASS_PATH', plugin_dir_path(__FILE__) . 'includes/' );
			define( 'DOCOSL_VERSION', "1.0.1" );
			define( 'DOCOSL_UPLOAD_DIR', $upload_dir['basedir'].'/'.DOCOSL_PLUGIN.'/' );
			define( 'DOCOSL_UPLOAD_URL', $upload_dir['baseurl'].'/'.DOCOSL_PLUGIN.'/' );
			define( 'DOCOSL_DEBUG', true );

			// 名前空間を使用するためオートローダを登録
			$this->register_autoloader();

			$core = new \DocodocoStoreLocator\Plugin();
			$core->run();

			register_activation_hook(__FILE__, array($this, 'activate'));
			register_deactivation_hook(__FILE__, array($this, 'deactivate'));
		}

		public function activate() {
			\DocodocoStoreLocator\Activator::activate();
		}

		public function deactivate() {
			\DocodocoStoreLocator\Deactivator::deactivate();
		}

		/**
		 * DocodocoStoreLocator 名前空間のオートロードをサポートする
		 */
		public function register_autoloader() {
			spl_autoload_register(function ($class) {
				$prefix = 'DocodocoStoreLocator\\';
				$len = strlen($prefix);
				if (strncmp($prefix, $class, $len) !== 0) {
					return;
				}
				$relative_class = substr($class, $len);
				$file = DOCOSL_PLUGIN_CLASS_PATH . str_replace('\\', '/', $relative_class) . '.php';
				if (file_exists($file)) {
					require_once $file;
				}
			});
		}
	}

	$bootstrap = new DocodocoStoreLocator_Bootstrap();

}
