<?php

/**
 * @link       https://www.docodoco.jp/
 * @since      1.0.1
 *
 * @package    DocoDocoStoreLocator
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

define( 'DOCOSL_PLUGIN', 'docodoco-store-locator' );
define( 'DOCOSL_PLUGIN_SHORT', 'docosl');
define( 'DOCOSL_URL_PATH', plugin_dir_url( __FILE__ ) );
define( 'DOCOSL_PLUGIN_PATH', plugin_dir_path(__FILE__) );
define( 'DOCOSL_PLUGIN_CLASS_PATH', plugin_dir_path(__FILE__) . 'includes/' );
define( 'DOCOSL_VERSION', "1.0.1" );
define( 'DOCOSL_UPLOAD_DIR', $upload_dir['basedir'].'/'.DOCOSL_PLUGIN.'/' );
define( 'DOCOSL_UPLOAD_URL', $upload_dir['baseurl'].'/'.DOCOSL_PLUGIN.'/' );
define( 'DOCOSL_DEBUG', true );

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


\DocodocoStoreLocator\Models\Store::drop_table();
\DocodocoStoreLocator\Models\DesignTemplate::drop_table();
\DocodocoStoreLocator\Models\TemplateColorSetting::drop_table();
\DocodocoStoreLocator\Models\TemplateType::drop_table();
\DocodocoStoreLocator\Models\GlobalSetting::drop_table();
\DocodocoStoreLocator\Models\TemplateDisplaySetting::drop_table();
\DocodocoStoreLocator\Models\TemplateDisplayItem::drop_table();

delete_option('docosl_version');

// アップロードしたファイルの保存場所の削除
require_once ABSPATH . 'wp-admin/includes/file.php';
WP_Filesystem();
global $wp_filesystem;

if ($wp_filesystem->exists(DOCOSL_UPLOAD_DIR)) {
	$r = $wp_filesystem->delete(DOCOSL_UPLOAD_DIR, true);
}
