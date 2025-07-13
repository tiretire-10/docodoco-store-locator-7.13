<?php

namespace DocodocoStoreLocator;

if ( ! defined( 'ABSPATH' ) ) exit;

class Activator {

    /**
     * プラグインが有効化されたときに実行する処理
     */
    public static function activate() {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        // テーブル作成
        self::create_table();

        // 初期値の設定
        self::set_default_value();

        // ファイルのアップロード場所作成
        self::create_dirs();

        // プラグインのバージョンを保存
        update_option('docosl_version', DOCOSL_VERSION);
    }

    /**
     * テーブルを作成する
     */
    public static function create_table() {
        Models\Store::create_table();
        Models\DesignTemplate::create_table();
        Models\TemplateColorSetting::create_table();
        Models\TemplateType::create_table();
        Models\GlobalSetting::create_table();
        Models\TemplateDisplaySetting::create_table();
        Models\TemplateDisplayItem::create_table();
    }

    /**
     * テーブルに初期値を設定する
     */
    public static function set_default_value() {
        Models\Store::set_default_value();
        Models\DesignTemplate::set_default_value();
        Models\TemplateColorSetting::set_default_value();
        Models\TemplateType::set_default_value();
        Models\GlobalSetting::set_default_value();
        Models\TemplateDisplaySetting::set_default_value();
        Models\TemplateDisplayItem::set_default_value();
    }

    /**
     * ファイルのアップロード場所、エクスポート用のファイル置き場を作成する
     */
    public static function create_dirs() {
        $dirs = array(
            DOCOSL_UPLOAD_DIR . 'store-images',
            DOCOSL_PLUGIN_PATH . 'exports',
        );
        foreach($dirs as $k => $dir) {
            if (!file_exists($dir)) {
                wp_mkdir_p($dir);
            }
        }
    }

}
