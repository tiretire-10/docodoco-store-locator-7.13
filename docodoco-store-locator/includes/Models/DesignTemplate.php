<?php

namespace DocodocoStoreLocator\Models;

if ( ! defined( 'ABSPATH' ) ) exit;

class DesignTemplate {

    // 定数
    const STORE_LIST_TEMPLATES = [
        1 => 'store-list-template01.php',
        2 => 'store-list-template02.php',
        3 => 'store-list-template03.php',
    ];

    const STORE_DETAIL_TEMPLATES = [
        1 => 'store-detail-template01.php',
        2 => 'store-detail-template02.php',
        3 => 'store-detail-template03.php',
    ];

    const COLUMNS = [
        'template_name' => [
            'label' => 'テンプレート名',
            'required' => true,
            'type' => 'string',
        ],
        'preview_img_filename' => [
            'label' => 'プレビュー画像ファイル名',
            'required' => true,
            'type' => 'string',
        ],
    ];

    public static function create_table() {
        global $wpdb;

        $sql = "CREATE TABLE `{$wpdb->prefix}docosl_design_templates` (
            `id` int unsigned NOT NULL AUTO_INCREMENT,
            `template_name` varchar(255) NOT NULL,
            `preview_img_filename` varchar(100) NOT NULL,
            PRIMARY KEY (`id`)
        ) DEFAULT CHARSET=UTF8MB4;";

        dbDelta($sql);
    }

    public static function set_default_value() {
        global $wpdb;

        // レコード数が0件の場合のみ初期データをセットする
        $record_count = $wpdb->get_results("SELECT COUNT(*) as count FROM {$wpdb->prefix}docosl_design_templates");

        if ($record_count && $record_count[0]->count === '0') {
            $wpdb->insert($wpdb->prefix . "docosl_design_templates", array(
                'template_name' => 'テンプレート1',
                'preview_img_filename' => 'template1.png'
            ));
        
            $wpdb->insert($wpdb->prefix . "docosl_design_templates", array(
                'template_name' => 'テンプレート2',
                'preview_img_filename' => 'template2.png'
            ));
        
            $wpdb->insert($wpdb->prefix . "docosl_design_templates", array(
                'template_name' => 'テンプレート3',
                'preview_img_filename' => 'template3.png'
            ));
        }
    }

    public static function drop_table() {
        global $wpdb;

        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}docosl_design_templates");
    }

    /**
     * 利用可能なテンプレート一覧を取得する
     * 
     * @return array
     */
    public static function get_available_templates() {
        global $wpdb;

        $templates = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}docosl_design_templates", OBJECT_K);

        return $templates;
    }
}
