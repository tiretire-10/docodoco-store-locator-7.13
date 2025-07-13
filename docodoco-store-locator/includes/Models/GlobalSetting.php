<?php

namespace DocodocoStoreLocator\Models;

if ( ! defined( 'ABSPATH' ) ) exit;

class GlobalSetting {

    const DOCODOCOJP_ENABLED = array(
        'DISABLED' => 0,
        'ENABLED'  => 1,
    );

    const STORE_DETAIL_PAGE_ENABLED = array(
        'DISABLED' => 0,
        'ENABLED'  => 1,
    );

    const COLUMNS = array(
        'google_maps_apikey' => array(
            'label' => 'Google Maps APIキー',
            'required' => false,
            'type' => 'string',
        ),
        'docodocojp_apikey' => array(
            'label' => 'どこどこJP APIキー',
            'required' => false,
            'type' => 'string',
        ),
        'docodocojp_enabled' => array(
            'label' => 'どこどこJP連携',
            'required' => false,
            'type' => 'int',
            'options' => array(
                self::DOCODOCOJP_ENABLED['DISABLED'] => '使わない',
                self::DOCODOCOJP_ENABLED['ENABLED']  => '使う',
            ),
        ),
        'design_template_id' => array(
            'label' => 'デザインテンプレート',
            'required' => true,
            'type' => 'int',
        ),
        'template_color_id' => array(
            'label' => 'テンプレートカラー',
            'required' => true,
            'type' => 'int',
        ),
        'store_detail_page_enabled' => array(
            'label' => '店舗詳細ページの有効状態',
            'required' => true,
            'type' => 'int',
            'options' => array(
                self::STORE_DETAIL_PAGE_ENABLED['DISABLED'] => '無効',
                self::STORE_DETAIL_PAGE_ENABLED['ENABLED'] => '有効',
            ),
        ),
    );

    public static function create_table() {
        global $wpdb;

        $sql = "CREATE TABLE `{$wpdb->prefix}docosl_global_settings` (
            `id` int unsigned NOT NULL AUTO_INCREMENT,
            `google_maps_apikey` varchar(100) NULL,
            `docodocojp_apikey` varchar(100) NULL,
            `docodocojp_enabled` tinyint NOT NULL DEFAULT '0',
            `design_template_id` int NOT NULL DEFAULT '1',
            `template_color_id` int NOT NULL DEFAULT '1',
            `store_detail_page_enabled` tinyint NOT NULL DEFAULT '0',
            PRIMARY KEY (`id`)
        ) DEFAULT CHARSET=UTF8MB4;";

        dbDelta($sql);
    }

    public static function set_default_value() {
        global $wpdb;

        // レコード数が0件の場合のみ初期データをセットする
        $record_count = $wpdb->get_results("SELECT COUNT(*) as count FROM {$wpdb->prefix}docosl_global_settings");

        if ($record_count && $record_count[0]->count === '0') {
            $wpdb->insert($wpdb->prefix . "docosl_global_settings", array(
                'docodocojp_enabled' => '0',
                'design_template_id' => '1',
                'template_color_id' => '1',
                'store_detail_page_enabled' => '0',
            ));
        }
    }

    public static function drop_table() {
        global $wpdb;

        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}docosl_global_settings");
    }
}
