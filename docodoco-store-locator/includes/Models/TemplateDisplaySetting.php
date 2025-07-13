<?php

namespace DocodocoStoreLocator\Models;

if ( ! defined( 'ABSPATH' ) ) exit;

class TemplateDisplaySetting {
    const MAP_DISPLAY_ENABLED = array(
        'DISABLED' => 0,
        'ENABLED'  => 1,
    );

    const COLUMNS = array(
        'template_type_id' => array(
            'label' => 'テンプレートタイプID',
            'required' => true,
            'type' => 'int',
        ),
        'map_display_enabled' => array(
            'label' => '地図表示',
            'required' => true,
            'type' => 'int',
            'options' => array(
                self::MAP_DISPLAY_ENABLED['DISABLED'] => '使わない',
                self::MAP_DISPLAY_ENABLED['ENABLED']  => '使う',
            ),
        ),
        'url' => array(
            'label' => 'ページURL',
            'required' => false,
            'type' => 'string',
        )
    );

    public static function create_table() {
        global $wpdb;

        $sql = "CREATE TABLE `{$wpdb->prefix}docosl_template_display_settings` (
            `id` int unsigned NOT NULL AUTO_INCREMENT,
            `template_type_id` int NOT NULL,
            `map_display_enabled` tinyint NOT NULL DEFAULT '0',
            `url` varchar(255) DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) DEFAULT CHARSET=UTF8MB4;";

        dbDelta($sql);
    }

    public static function set_default_value() {
        global $wpdb;

        // レコード数が0件の場合のみ初期データをセットする
        $record_count = $wpdb->get_results("SELECT COUNT(*) as count FROM {$wpdb->prefix}docosl_template_display_settings");

        if ($record_count && $record_count[0]->count === '0') {
            $wpdb->insert($wpdb->prefix . 'docosl_template_display_settings', array(
                'template_type_id' => 1,
                'map_display_enabled' => 0,
                'url' => null,
            ));
        
            $wpdb->insert($wpdb->prefix . 'docosl_template_display_settings', array(
                'template_type_id' => 2,
                'map_display_enabled' => 0,
                'url' => null,
            ));
        }
    }

    public static function drop_table() {
        global $wpdb;

        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}docosl_template_display_settings");
    }
}
