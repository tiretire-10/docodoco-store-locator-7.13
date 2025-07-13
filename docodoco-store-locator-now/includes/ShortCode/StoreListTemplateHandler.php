<?php

namespace DocodocoStoreLocator\ShortCode;

if ( ! defined( 'ABSPATH' ) ) exit;

class StoreListTemplateHandler {

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
            $google_map_url='https://maps.googleapis.com/maps/api/js?key=' . $google_maps_apikey . '&callback=callback_docoslInitMap';
            wp_enqueue_script(DOCOSL_PLUGIN_SHORT . '-google-maps', $google_map_url, array(), null, true);
        }

        // どこどこJPのスクリプトを読み込む
        $docodocojp_apikey = $global_settings->docodocojp_apikey;
        if (!empty($docodocojp_apikey) && $global_settings->docodocojp_enabled == 1) {
            wp_enqueue_script(DOCOSL_PLUGIN_SHORT . 'docodoco-script', '//api.docodoco.jp/search_latlon.js', array(), null, true);
        }  

    }
    
    /**
     * 店舗一覧ページテンプレートの読み込み
     */
    public function store_list_template_include() {        
        ob_start();
        global $wpdb;

        // 定数の取得
        $item_labels = \DocodocoStoreLocator\Models\Store::LABELS;
        $publish_status = \DocodocoStoreLocator\Models\Store::PUBLISH_STATUS;
        $templates = \DocodocoStoreLocator\Models\DesignTemplate::STORE_LIST_TEMPLATES;
        $template_type_id = \DocodocoStoreLocator\Models\TemplateType::TEMPLATE_TYPE_ID;
        
        // テンプレートの共通設定を取得
        $global_settings = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}docosl_global_settings");
        $template_id = $global_settings->design_template_id;
        
        // 地図の表示設定の取得
        $display_settings = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}docosl_template_display_settings WHERE template_type_id = %d", $template_type_id['STORE_LIST']));

        // 表時項目の取得
        $display_items = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}docosl_template_display_items WHERE template_type_id = %d ORDER BY sort_order", $template_type_id['STORE_LIST']));

        // 登録済みの店舗データの取得
        $stores = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}docosl_stores WHERE publish_status = %d ORDER BY sort_order, name ASC", $publish_status['PUBLISHED']));

        // 詳細ページのURLを取得
        $detail_page_url = $wpdb->get_var($wpdb->prepare("SELECT url FROM {$wpdb->prefix}docosl_template_display_settings WHERE template_type_id = %d", $template_type_id['STORE_DETAIL']));

        $docodocojp_apikey = $global_settings->docodocojp_apikey;
        $is_map_display_enabled = (!empty($global_settings->google_maps_apikey) && $display_settings->map_display_enabled == 1);
        $is_docodocojp_enabled = (!empty($docodocojp_apikey) && $global_settings->docodocojp_enabled == 1);
        
        /** @var bool 詳細ページが有効化どうか */
        $store_detail_page_enabled = $global_settings->store_detail_page_enabled;

        // スタイルとスクリプトの読み込み
        $this->enqueue_styles();
        $this->enqueue_scripts($global_settings, $display_settings);
        
        // テンプレートの読み込み
        $template_filename = $templates[$template_id];
        include DOCOSL_PLUGIN_PATH . 'public/templates/' . $template_filename;
        
        return ob_get_clean();
    }
}
