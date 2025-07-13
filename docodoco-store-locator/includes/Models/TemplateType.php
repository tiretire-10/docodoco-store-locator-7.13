<?php

namespace DocodocoStoreLocator\Models;

if ( ! defined( 'ABSPATH' ) ) exit;

class TemplateType {

    // 定数
    const TEMPLATE_TYPE_ID = [
        'STORE_LIST' => 1,
        'STORE_DETAIL' => 2,
    ];

    public static function create_table() {
        global $wpdb;

        $sql = "CREATE TABLE `{$wpdb->prefix}docosl_template_type` (
            `id` int unsigned NOT NULL,
            `name` varchar(255) NOT NULL,
            PRIMARY KEY (`id`)
        ) DEFAULT CHARSET=UTF8MB4;";

        dbDelta($sql);
    }

    public static function set_default_value() {
        global $wpdb;

        // レコード数が0件の場合のみ初期データをセットする
        $record_count = $wpdb->get_results("SELECT COUNT(*) as count FROM {$wpdb->prefix}docosl_template_type");

        if ($record_count && $record_count[0]->count === '0') {
            $wpdb->insert($wpdb->prefix . 'docosl_template_type', array(
                'id' => 1,
                'name' => '一覧ページ'
            ));
        
            $wpdb->insert($wpdb->prefix . 'docosl_template_type', array(
                'id' => 2,
                'name' => '詳細ページ'
            ));
        }
    }

    public static function drop_table() {
        global $wpdb;

        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}docosl_template_type");
    }
}
