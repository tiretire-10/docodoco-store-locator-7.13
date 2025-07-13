<?php

namespace DocodocoStoreLocator\Admin;

if ( ! defined( 'ABSPATH' ) ) exit;

use DocodocoStoreLocator\Models\DesignTemplate;
use DocodocoStoreLocator\Models\GlobalSetting;
use DocodocoStoreLocator\Models\TemplateDisplayItem;
use DocodocoStoreLocator\Models\TemplateDisplaySetting;

class DisplaySetting {
    /** @var 全体的な通知メッセージ（フォームの入力値に紐づかないものなど） */
    public $view_alerts = array();

    /** @var array フォームの各入力値のエラー */
    public $view_form_errors = array();

    /** @var 現在の設定値 */
    public $all_settings = array();

    public function __construct() {
    }

    public function admin_menu_callback() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->post_display_setting();
        } else {
            $this->get_display_setting();
        }
    }

    public function get_display_setting() {
        $this->all_settings = self::get_all_settings();
        $this->render();
    }

    public function post_display_setting() {
        global $wpdb;

        // nonce チェック
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'])), 'docosl-display-setting')) {
            wp_die('不正なリクエストです');
        }

        /**
         * WordPress が magic_quotes_gpc をエミュレートしてしまう (wp_magic_quotes()) ため wp_unslash をかける
         * 最初に入力文字をサニタイズする
         */
        $input_data = array(
            'google_maps_apikey' => sanitize_text_field(wp_unslash($_POST['google_maps_apikey'])),
            'docodocojp_apikey' => sanitize_text_field(wp_unslash($_POST['docodocojp_apikey'])),
            'docodocojp_enabled' => sanitize_text_field(wp_unslash($_POST['docodocojp_enabled'])),
            'design_template_id' => sanitize_text_field(wp_unslash($_POST['design_template_id'])),
            'store_detail_page_enabled' => sanitize_text_field(wp_unslash($_POST['store_detail_page_enabled'])),
            'type1_map_display_enabled' => sanitize_text_field(wp_unslash($_POST['type1_map_display_enabled'])),
            'type2_map_display_enabled' => sanitize_text_field(wp_unslash($_POST['type2_map_display_enabled'])),
        );
        // store_list_url, store_detail_url は詳細ページを無効にしたときPOSTで送られてこないので、存在チェックを行う
        $input_data['store_list_url'] = isset($_POST['store_list_url']) ? sanitize_url(wp_unslash($_POST['store_list_url'])) : '';
        $input_data['store_detail_url'] = isset($_POST['store_detail_url']) ? sanitize_url(wp_unslash($_POST['store_detail_url'])) : '';

        $data = array(
            'google_maps_apikey' => $input_data['google_maps_apikey'] === '' ? null : $input_data['google_maps_apikey'],
            'docodocojp_apikey' => $input_data['docodocojp_apikey'] === '' ? null : $input_data['docodocojp_apikey'],
            'docodocojp_enabled' => $input_data['docodocojp_enabled'] === '' ? null : (int)$input_data['docodocojp_enabled'],
            'design_template_id' => $input_data['design_template_id'] === '' ? null : (int)$input_data['design_template_id'],
            'store_detail_page_enabled' => $input_data['store_detail_page_enabled'] === '' ? null : (int)$input_data['store_detail_page_enabled'],
            'store_list_url' => $input_data['store_list_url'] === '' ? null : $input_data['store_list_url'],
            'store_detail_url' => $input_data['store_detail_url'] === '' ? null : $input_data['store_detail_url'],

            'template_color_id' => 1,

            'store_list_map_display_enabled' => $input_data['type1_map_display_enabled'] === '' ? null : (int)$input_data['type1_map_display_enabled'],
            'store_detail_map_display_enabled' => $input_data['type2_map_display_enabled'] === '' ? null : (int)$input_data['type2_map_display_enabled'],
        );

        // google_maps_apikey のチェック。地図表示が有効な場合は必須項目とする
        if ($data['store_list_map_display_enabled'] === 1 || $data['store_detail_map_display_enabled'] === 1) {
            if ($data['google_maps_apikey'] === null) {
                $this->view_form_errors['google_maps_apikey'] = 'Google Maps APIキーが入力されていません。';
            }
            if (preg_match('/\AAIza[0-9a-zA-Z-_]{35}\z/', $data['google_maps_apikey']) !== 1) {
                // https://learn.microsoft.com/ja-jp/purview/sit-defn-google-api-key
                $this->view_form_errors['google_maps_apikey'] = 'Google Maps APIキーが不正です。APIキーは AIza で始まる 39 文字の文字列です。';
            }
        }

        // docodocojp_apikey のチェック。どこどこJP連携が有効な場合は必須項目とする
        if ($data['docodocojp_enabled'] === 1) {
            if ($data['docodocojp_apikey'] === null) {
                $this->view_form_errors['docodocojp_apikey'] = 'どこどこJP APIキーが入力されていません。';
            }
            if (preg_match('/\A[0-9a-zA-Z]{64}\z/', $data['docodocojp_apikey']) !== 1) {
                $this->view_form_errors['docodocojp_apikey'] = 'どこどこJP APIキーが不正です。APIキーは 64 文字の文字列です。';
            }
        }

        // docodocojp_enabled のチェック。必須項目
        if ($data['docodocojp_enabled'] === null) {
            $this->view_form_errors['docodocojp_enabled'] = 'どこどこJP連携の有効状態が入力されていません。';
        } else {
            if (!in_array($data['docodocojp_enabled'], GlobalSetting::DOCODOCOJP_ENABLED)) {
                $this->view_form_errors['docodocojp_enabled'] = 'どこどこJP連携の有効状態が不正です。';
            }
        }

        // design_template_id のチェック。必須項目
        if ($data['design_template_id'] === null) {
            $this->view_form_errors['design_template_id'] = 'デザインテンプレートが入力されていません。';
        } else {
            if (!in_array($data['design_template_id'], array_keys(DesignTemplate::get_available_templates()))) {
                $this->view_form_errors['design_template_id'] = 'デザインテンプレートが不正です。';
            }
        }

        // store_detail_page_enabled のチェック。必須項目
        if ($data['store_detail_page_enabled'] === null) {
            $this->view_form_errors['store_detail_page_enabled'] = '店舗詳細ページの有効状態が入力されていません。';
        } else {
            if (!in_array($data['store_detail_page_enabled'], GlobalSetting::STORE_DETAIL_PAGE_ENABLED)) {
                $this->view_form_errors['store_detail_page_enabled'] = '店舗詳細ページの有効状態が不正です。';
            }
        }

        // store_list_map_display_enabled のチェック。必須項目
        if ($data['store_list_map_display_enabled'] === null) {
            $this->view_form_errors['type1_map_display_enabled'] = '店舗一覧ページの地図表示の有効状態が入力されていません。';
        } else {
            if (!in_array($data['store_list_map_display_enabled'], TemplateDisplaySetting::MAP_DISPLAY_ENABLED)) {
                $this->view_form_errors['type1_map_display_enabled'] = '店舗一覧ページの地図表示の有効状態が不正です。';
            }
        }

        // store_detail_map_display_enabled のチェック。必須項目
        if ($data['store_detail_map_display_enabled'] === null) {
            $this->view_form_errors['type2_map_display_enabled'] = '店舗一覧ページの地図表示の有効状態が入力されていません。';
        } else {
            if (!in_array($data['store_detail_map_display_enabled'], TemplateDisplaySetting::MAP_DISPLAY_ENABLED)) {
                $this->view_form_errors['type2_map_display_enabled'] = '店舗一覧ページの地図表示の有効状態が不正です。';
            }
        }

        // store_list_url のチェック。詳細ページが有効な場合は必須項目。
        if ($data['store_detail_page_enabled'] === 1 && empty($data['store_list_url'])) {
                $this->view_form_errors['store_list_url'] = '店舗一覧ページのURLが入力されていません。';
        }

        // store_detail_url のチェック。詳細ページが有効な場合は必須項目。
        if ($data['store_detail_page_enabled'] === 1 && empty($data['store_detail_url'])) {
                $this->view_form_errors['store_detail_url'] = '店舗詳細ページのURLが入力されていません。';
        }

        $wpdb->query("BEGIN");

        // item_is_display を詰める処理
        $display_item_ids = $wpdb->get_col("SELECT id FROM {$wpdb->prefix}docosl_template_display_items ORDER BY sort_order ASC");
        foreach($display_item_ids as $id) {
            // $_POST['item_is_display'] は各要素の存在チェックのみ行うためサニタイズは不要
            $data['item_is_display'][$id] = isset($_POST['item_is_display'][$id]) ? TemplateDisplayItem::IS_DISPLAY['ENABLED'] : TemplateDisplayItem::IS_DISPLAY['DISABLED'];
        }

        if (count($this->view_form_errors) > 0) {
            // エラーがある場合は再表示
            $this->view_alerts[] = array('level' => 'danger', 'message' => '入力内容にエラーがあります。');

            // DBの設定値にフォームの値を埋め込んで表示する
            $all_settings = self::get_all_settings();
            $this->all_settings = self::merge_form_data($all_settings, $data);
            return $this->render();
        }

        if (self::update_all_settings($data) === false) {
            $wpdb->query("ROLLBACK");
            $this->view_alerts[] = array('level' => 'danger', 'message' => '設定の更新に失敗しました。DBエラーが発生しました。');
            $this->all_settings = self::get_all_settings();
            return $this->render();
        }
        $wpdb->query("COMMIT");

        $this->view_alerts[] = array('level' => 'success', 'message' => '設定を更新しました。');
        $this->all_settings = self::get_all_settings();
        return $this->render();
    }

    public function render() {
        $view_alerts = $this->view_alerts;
        $view_form_errors = $this->view_form_errors;
        $global_settings = $this->all_settings['global_settings'];
        $template_types = $this->all_settings['template_types'];
        $available_design_templates = DesignTemplate::get_available_templates();

        wp_enqueue_style('docosl-bootstrap', DOCOSL_URL_PATH . 'admin/css/bootstrap.min.css', array(), DOCOSL_VERSION);
        include DOCOSL_PLUGIN_PATH . '/admin/views/display-setting.php';
    }

    /**
     * 設定を更新する。バリデーション、値のチェックは済ませておくこと。
     * 
     * @param array $data
     */
    public static function update_all_settings($data) {
        global $wpdb;

        // global_settings の更新
        $global_settings = array(
            'google_maps_apikey' => $data['google_maps_apikey'],
            'docodocojp_apikey'  => $data['docodocojp_apikey'],
            'docodocojp_enabled' => $data['docodocojp_enabled'],
            'design_template_id' => $data['design_template_id'],
            'template_color_id'  => $data['template_color_id'],
            'store_detail_page_enabled' => $data['store_detail_page_enabled'],
        );
        if($wpdb->update($wpdb->prefix . 'docosl_global_settings', $global_settings, array('id' => 1)) === false) {
            error_log('failed update global_settings record. [error=' . $wpdb->last_error . ']');
            return false;
        }

        // template_display_settings の更新
        $store_list_settings = array(
            'map_display_enabled' => $data['store_list_map_display_enabled'],
            'url' => $data['store_list_url'],
        );
        if ($wpdb->update($wpdb->prefix . 'docosl_template_display_settings', $store_list_settings, array('template_type_id' => 1)) === false) {
            error_log('failed update template_display_settings record. [error=' . $wpdb->last_error . ']');
            return false;
        }

        $store_detail_settings = array(
            'map_display_enabled' => $data['store_detail_map_display_enabled'],
            'url' => $data['store_detail_url'],
        );
        if ($wpdb->update($wpdb->prefix . 'docosl_template_display_settings', $store_detail_settings, array('template_type_id' => 2)) === false) {
            error_log('failed update template_display_settings record. [error=' . $wpdb->last_error . ']');
            return false;
        }

        // template_display_items の更新
        $display_item_ids = $wpdb->get_col("SELECT id FROM {$wpdb->prefix}docosl_template_display_items ORDER BY sort_order ASC");
        foreach($display_item_ids as $id) {
            if ($wpdb->update($wpdb->prefix . 'docosl_template_display_items', array('is_display' => $data['item_is_display'][$id]), array('id' => $id)) === false) {
                error_log('failed update template_display_items record. [error=' . $wpdb->last_error . ']');
                return false;
            }
        }

        return true;
    }

    /**
     * DBから現在の設定を取得する
     * 
     * @return array{global_settings: stdObject, template_types: stdObject}
     */
    public static function get_all_settings() {
        global $wpdb;

        $global_settings = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}docosl_global_settings AS gs JOIN {$wpdb->prefix}docosl_design_templates AS dt ON gs.design_template_id = dt.id");
        $template_types = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}docosl_template_type ORDER BY id ASC", OBJECT_K);
        foreach ($template_types as $id => $type) {
            $settings = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}docosl_template_display_settings WHERE template_type_id = %d", $id));
            $type->settings = $settings;

            $items = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}docosl_template_display_items WHERE template_type_id = %d ORDER BY sort_order ASC", $id), OBJECT_K);
            $type->items = $items;
        }
        unset($settings);
        unset($items);
        return array(
            'global_settings' => $global_settings,
            'template_types' => $template_types,
        );
    }

    /**
     * フォームの入力値を現在の設定にマージする（エラーがあって入力画面に戻る際にフォームに入力値を表示するために使う）
     * 
     * @param array $all_settings 現在の設定
     * @param array $form_data フォームの入力値
     * @return array マージ後の設定
     */
    public static function merge_form_data($all_settings, $form_data) {
        // global_settings のマージ
        $global_settings = $all_settings['global_settings'];
        $global_settings->google_maps_apikey = $form_data['google_maps_apikey'];
        $global_settings->docodocojp_apikey = $form_data['docodocojp_apikey'];
        $global_settings->docodocojp_enabled = $form_data['docodocojp_enabled'];
        $global_settings->design_template_id = $form_data['design_template_id'];
        $global_settings->store_detail_page_enabled = $form_data['store_detail_page_enabled'];

        // template_display_settings(リスト) のマージ
        $store_list = $all_settings['template_types'][1];
        $store_list->settings->map_display_enabled = $form_data['store_list_map_display_enabled'];
        $store_list->settings->url = $form_data['store_list_url'];

        // template_display_settings(詳細) のマージ
        $store_detail = $all_settings['template_types'][2];
        $store_detail->settings->map_display_enabled = $form_data['store_detail_map_display_enabled'];
        $store_detail->settings->url = $form_data['store_detail_url'];

        // template_display_items のマージ
        foreach($all_settings['template_types'] as $type) {
            foreach($type->items as $id => $item) {
                $item->is_display = $form_data['item_is_display'][$id];
            }
        }

        return $all_settings;
    }
}
