<?php

namespace DocodocoStoreLocator\ShortCode;

if ( ! defined( 'ABSPATH' ) ) exit;

use DocodocoStoreLocator\Models\Store;

class StoreDetailTemplateHandler {

    /**
     * コンストラクタ
     */
    public function __construct() {
    }

    /**
     * スタイルの読み込み
     */
    public function enqueue_styles() {
        wp_enqueue_style(DOCOSL_PLUGIN_SHORT .'-template-style', DOCOSL_URL_PATH . 'public/css/template.css', array(), DOCOSL_VERSION);
        wp_enqueue_style(DOCOSL_PLUGIN_SHORT .'-google-fonts', 'https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200');
    }

    /**
     * スクリプトの読み込み
     */
    public function enqueue_scripts($global_settings, $display_settings) {        
        // Google Maps API を読み込む
        $google_maps_apikey = $global_settings->google_maps_apikey;
        if (!empty($google_maps_apikey) && $display_settings->map_display_enabled == 1) {
            $google_map_url='https://maps.googleapis.com/maps/api/js?key=' . $google_maps_apikey . '&callback=docoslInitMap';
            wp_enqueue_script(DOCOSL_PLUGIN_SHORT . '-google-maps', $google_map_url, array(), null, true);
        }
    }
    
    /**
     * 店舗詳細ページテンプレートの読み込み
     */
    public function store_detail_template_include() {        
        ob_start();
        global $wpdb;

        // 定数の取得
        $item_labels = \DocodocoStoreLocator\Models\Store::LABELS;
        $templates = \DocodocoStoreLocator\Models\DesignTemplate::STORE_DETAIL_TEMPLATES;
        $template_type_id = \DocodocoStoreLocator\Models\TemplateType::TEMPLATE_TYPE_ID;
        
        // テンプレートの共通設定を取得
        $global_settings = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}docosl_global_settings");
        $template_id = $global_settings->design_template_id;
        
        // 地図の表示設定の取得
        $display_settings = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}docosl_template_display_settings WHERE template_type_id = %d", $template_type_id['STORE_DETAIL']));

        // 表時項目の取得
        $display_items = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}docosl_template_display_items WHERE template_type_id = %d ORDER BY sort_order", $template_type_id['STORE_DETAIL']));

        // 店舗データの取得
        if (empty($_GET['storeId'])) {
            // 詳細ページURLに storeId パラメータなしでアクセスしてきた場合などを考慮して、 id 指定がなかったら公開されている最初の店舗を取得する
            $store = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}docosl_stores WHERE publish_status = %d ORDER BY sort_order, name ASC LIMIT 1", Store::PUBLISH_STATUS['PUBLISHED']));
        } else {
            $store = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}docosl_stores WHERE id = %d AND publish_status = %d", sanitize_text_field(wp_unslash($_GET['storeId'])), Store::PUBLISH_STATUS['PUBLISHED']));
        }

        $is_map_display_enabled = (!empty($global_settings->google_maps_apikey) && $display_settings->map_display_enabled == 1);

        // 一覧ページのURLを取得
        $list_page_url = $wpdb->get_var($wpdb->prepare("SELECT url FROM {$wpdb->prefix}docosl_template_display_settings WHERE template_type_id = %d", $template_type_id['STORE_LIST']));

        // テンプレートの読み込み
        $template_filename = $templates[$template_id];
        include DOCOSL_PLUGIN_PATH . 'public/templates/' . $template_filename;
            
        // スタイルとスクリプトの読み込み
        $this->enqueue_styles();
        $this->enqueue_scripts($global_settings, $display_settings);

        return ob_get_clean();
    }
}
