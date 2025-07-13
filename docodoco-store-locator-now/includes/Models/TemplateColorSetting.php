<?php

namespace DocodocoStoreLocator\Models;

if ( ! defined( 'ABSPATH' ) ) exit;

class TemplateColorSetting {
    public static function create_table() {
        global $wpdb;

        $sql = "CREATE TABLE `{$wpdb->prefix}docosl_template_color_settings` (
            `id` int unsigned NOT NULL AUTO_INCREMENT,
            `primary_color` varchar(20) NOT NULL,
            `secondary_color` varchar(20) NOT NULL,
            `table_header_bg_color` varchar(20) NOT NULL,
            `table_body_bg_color` varchar(20) NOT NULL,
            `table_text_color` varchar(20) NOT NULL,
            `table_border_color` varchar(20) NOT NULL,
            `link_text_color` varchar(20) NOT NULL,
            PRIMARY KEY (`id`)
        ) DEFAULT CHARSET=UTF8MB4;";

        dbDelta($sql);
    }

    public static function set_default_value() {
        global $wpdb;

        // レコード数が0件の場合のみ初期データをセットする
        $record_count = $wpdb->get_results("SELECT COUNT(*) as count FROM {$wpdb->prefix}docosl_template_color_settings");

        if ($record_count && $record_count[0]->count === '0') {
            $wpdb->insert($wpdb->prefix . "docosl_template_color_settings", array(
                'primary_color' => '#c7c7c7',
                'secondary_color' => '#f6f6f6',
                'table_header_bg_color' => 'f6f6f6',
                'table_body_bg_color' => '#ffffff',
                'table_text_color' => '#555a60',
                'table_border_color' => '#c7c7c7',
                'link_text_color' => '#5988b9'
            ));
        }
    }

    public static function drop_table() {
        global $wpdb;

        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}docosl_template_color_settings");
    }
}
