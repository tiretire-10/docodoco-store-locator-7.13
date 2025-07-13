<?php

namespace DocodocoStoreLocator\Models;

if ( ! defined( 'ABSPATH' ) ) exit;

class TemplateDisplayItem {

    const IS_DISPLAY = array(
        'DISABLED' => 0,
        'ENABLED'  => 1,
    );

    const COLUMNS = array(
        'template_type_id' => array(
            'label' => 'テンプレートタイプID',
            'required' => true,
            'type' => 'int',
        ),
        'item_name' => array(
            'label' => '項目名',
            'required' => true,
            'type' => 'string',
        ),
        'is_display' => array(
            'label' => '表示する',
            'required' => true,
            'type' => 'int',
            'options' => array(
                0 => 'しない',
                1 => 'する',
            ),
        ),
        'sort_order' => array(
            'label' => '表示順',
            'required' => true,
            'type' => 'int',
        ),
    );

    public static function create_table() {
        global $wpdb;

        $sql = "CREATE TABLE `{$wpdb->prefix}docosl_template_display_items` (
            `id` int unsigned NOT NULL AUTO_INCREMENT,
            `template_type_id` int NOT NULL,
            `item_name` varchar(50) NOT NULL,
            `is_display` tinyint NOT NULL,
            `sort_order` int NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `unique_template_item` (`template_type_id`, `item_name`)
        ) DEFAULT CHARSET=UTF8MB4;";

        dbDelta($sql);
    }

    public static function set_default_value() {
        global $wpdb;

        // レコード数が0件の場合のみ初期データをセットする
        $record_count = $wpdb->get_results("SELECT COUNT(*) as count FROM {$wpdb->prefix}docosl_template_display_items");

        if ($record_count && $record_count[0]->count === '0') {
            // 一覧ページ
            $wpdb->insert($wpdb->prefix . "docosl_template_display_items", array(
                'template_type_id' => 1,
                'item_name' => 'address',
                'is_display' => 1,
                'sort_order' => 1
            ));
            $wpdb->insert($wpdb->prefix . "docosl_template_display_items", array(
                'template_type_id' => 1,
                'item_name' => 'tel',
                'is_display' => 1,
                'sort_order' => 2
            ));
            $wpdb->insert($wpdb->prefix . "docosl_template_display_items", array(
                'template_type_id' => 1,
                'item_name' => 'fax',
                'is_display' => 1,
                'sort_order' => 3
            ));
            $wpdb->insert($wpdb->prefix . "docosl_template_display_items", array(
                'template_type_id' => 1,
                'item_name' => 'url',
                'is_display' => 1,
                'sort_order' => 4
            ));
            $wpdb->insert($wpdb->prefix . "docosl_template_display_items", array(
                'template_type_id' => 1,
                'item_name' => 'email',
                'is_display' => 1,
                'sort_order' => 5
            ));
            $wpdb->insert($wpdb->prefix . "docosl_template_display_items", array(
                'template_type_id' => 1,
                'item_name' => 'open_hours',
                'is_display' => 1,
                'sort_order' => 6
            ));
            $wpdb->insert($wpdb->prefix . "docosl_template_display_items", array(
                'template_type_id' => 1,
                'item_name' => 'regular_holiday',
                'is_display' => 1,
                'sort_order' => 7
            ));
            $wpdb->insert($wpdb->prefix . "docosl_template_display_items", array(
                'template_type_id' => 1,
                'item_name' => 'parking',
                'is_display' => 1,
                'sort_order' => 8
            ));
            $wpdb->insert($wpdb->prefix . "docosl_template_display_items", array(
                'template_type_id' => 1,
                'item_name' => 'remarks',
                'is_display' => 1,
                'sort_order' => 9
            ));
            $wpdb->insert($wpdb->prefix . "docosl_template_display_items", array(
                'template_type_id' => 1,
                'item_name' => 'department',
                'is_display' => 1,
                'sort_order' => 10
            ));
            $wpdb->insert($wpdb->prefix . "docosl_template_display_items", array(
                'template_type_id' => 1,
                'item_name' => 'contact',
                'is_display' => 1,
                'sort_order' => 11
            ));
            $wpdb->insert($wpdb->prefix . "docosl_template_display_items", array(
                'template_type_id' => 1,
                'item_name' => 'age',
                'is_display' => 1,
                'sort_order' => 12
            ));
            $wpdb->insert($wpdb->prefix . "docosl_template_display_items", array(
                'template_type_id' => 1,
                'item_name' => 'period',
                'is_display' => 1,
                'sort_order' => 13
            ));
            $wpdb->insert($wpdb->prefix . "docosl_template_display_items", array(
                'template_type_id' => 1,
                'item_name' => 'document',
                'is_display' => 1,
                'sort_order' => 14
            ));
            $wpdb->insert($wpdb->prefix . "docosl_template_display_items", array(
                'template_type_id' => 1,
                'item_name' => 'application',
                'is_display' => 1,
                'sort_order' => 15
            ));
            $wpdb->insert($wpdb->prefix . "docosl_template_display_items", array(
                'template_type_id' => 1,
                'item_name' => 'belongings',
                'is_display' => 1,
                'sort_order' => 16
            ));
            $wpdb->insert($wpdb->prefix . "docosl_template_display_items", array(
                'template_type_id' => 1,
                'item_name' => 'cost',
                'is_display' => 1,
                'sort_order' => 17
            ));
    
            // 詳細ページ
            $wpdb->insert($wpdb->prefix . "docosl_template_display_items", array(
                'template_type_id' => 2,
                'item_name' => 'address',
                'is_display' => 1,
                'sort_order' => 1
            ));
            $wpdb->insert($wpdb->prefix . "docosl_template_display_items", array(
                'template_type_id' => 2,
                'item_name' => 'tel',
                'is_display' => 1,
                'sort_order' => 2
            ));
            $wpdb->insert($wpdb->prefix . "docosl_template_display_items", array(
                'template_type_id' => 2,
                'item_name' => 'fax',
                'is_display' => 1,
                'sort_order' => 3
            ));
            $wpdb->insert($wpdb->prefix . "docosl_template_display_items", array(
                'template_type_id' => 2,
                'item_name' => 'url',
                'is_display' => 1,
                'sort_order' => 4
            ));
            $wpdb->insert($wpdb->prefix . "docosl_template_display_items", array(
                'template_type_id' => 2,
                'item_name' => 'email',
                'is_display' => 1,
                'sort_order' => 5
            ));
            $wpdb->insert($wpdb->prefix . "docosl_template_display_items", array(
                'template_type_id' => 2,
                'item_name' => 'open_hours',
                'is_display' => 1,
                'sort_order' => 6
            ));
            $wpdb->insert($wpdb->prefix . "docosl_template_display_items", array(
                'template_type_id' => 2,
                'item_name' => 'regular_holiday',
                'is_display' => 1,
                'sort_order' => 7
            ));
            $wpdb->insert($wpdb->prefix . "docosl_template_display_items", array(
                'template_type_id' => 2,
                'item_name' => 'parking',
                'is_display' => 1,
                'sort_order' => 8
            ));
            $wpdb->insert($wpdb->prefix . "docosl_template_display_items", array(
                'template_type_id' => 2,
                'item_name' => 'remarks',
                'is_display' => 1,
                'sort_order' => 9
            ));
            $wpdb->insert($wpdb->prefix . "docosl_template_display_items", array(
                'template_type_id' => 2,
                'item_name' => 'department',
                'is_display' => 1,
                'sort_order' => 10
            ));
            $wpdb->insert($wpdb->prefix . "docosl_template_display_items", array(
                'template_type_id' => 2,
                'item_name' => 'contact',
                'is_display' => 1,
                'sort_order' => 11
            ));
            $wpdb->insert($wpdb->prefix . "docosl_template_display_items", array(
                'template_type_id' => 2,
                'item_name' => 'age',
                'is_display' => 1,
                'sort_order' => 12
            ));
            $wpdb->insert($wpdb->prefix . "docosl_template_display_items", array(
                'template_type_id' => 2,
                'item_name' => 'period',
                'is_display' => 1,
                'sort_order' => 13
            ));
            $wpdb->insert($wpdb->prefix . "docosl_template_display_items", array(
                'template_type_id' => 2,
                'item_name' => 'document',
                'is_display' => 1,
                'sort_order' => 14
            ));
            $wpdb->insert($wpdb->prefix . "docosl_template_display_items", array(
                'template_type_id' => 2,
                'item_name' => 'application',
                'is_display' => 1,
                'sort_order' => 15
            ));
            $wpdb->insert($wpdb->prefix . "docosl_template_display_items", array(
                'template_type_id' => 2,
                'item_name' => 'belongings',
                'is_display' => 1,
                'sort_order' => 16
            ));
            $wpdb->insert($wpdb->prefix . "docosl_template_display_items", array(
                'template_type_id' => 2,
                'item_name' => 'cost',
                'is_display' => 1,
                'sort_order' => 17
            ));
        }
    
    }

    public static function drop_table() {
        global $wpdb;

        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}docosl_template_display_items");
    }
}
