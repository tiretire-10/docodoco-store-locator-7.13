<?php

namespace DocodocoStoreLocator;

if ( ! defined( 'ABSPATH' ) ) exit;

class Plugin {
    public $admin_menu_zip = null;
    public $admin_menu_store_list = null;
    public $admin_menu_display_setting = null;
    public $admin_menu_register_store = null;
    public $admin_menu_introduce = null;

    public $short_code_store_list = null;
    public $short_code_store_detail = null;

    /**
     * コンストラクタ
     */
    public function __construct() {
        if (is_admin()){
            $this->admin_menu_zip = new \DocodocoStoreLocator\Admin\ZIP();
            $this->admin_menu_store_list = new \DocodocoStoreLocator\Admin\StoreList();
            $this->admin_menu_display_setting = new \DocodocoStoreLocator\Admin\DisplaySetting();
            $this->admin_menu_register_store = new \DocodocoStoreLocator\Admin\RegisterStore();
            $this->admin_menu_introduce = new \DocodocoStoreLocator\Admin\Introduce();
            
            $this->setup_admin();
        }else{
            $this->short_code_store_list = new \DocodocoStoreLocator\ShortCode\StoreListTemplateHandler();
            $this->short_code_store_detail = new \DocodocoStoreLocator\ShortCode\StoreDetailTemplateHandler();

            $this->setup_public();
        }
    }

    /**
     * 管理画面のセットアップをする
     */
    public function setup_admin() {
        // メニューの追加
        add_action('admin_menu', array($this, 'register_admin_menu'));

        // admin_init 処理追加
        add_action('admin_init', array($this, 'register_admin_init'));
    }

    /**
     * 公開サイトのセットアップをする
     */
    public function setup_public() {
        // 店舗一覧ページテンプレートのショートコード定義
        add_shortcode('docosl_storelist', array($this->short_code_store_list, 'store_list_template_include'));

        // 店舗詳細ページテンプレートのショートコード定義
        add_shortcode('docosl_storedetail', array($this->short_code_store_detail, 'store_detail_template_include'));
    }

    /**
     * 管理画面にプラグインのメニューを追加する
     */
    public function register_admin_menu() {
        add_menu_page(
            '店舗管理',
            '店舗管理',
            'edit_pages',
            DOCOSL_PLUGIN,
            '',
            'dashicons-location-alt'
        );

        add_submenu_page(
            DOCOSL_PLUGIN,
            '店舗一覧',
            '店舗一覧',
            'edit_pages',
            DOCOSL_PLUGIN . '-store-list',
            array($this->admin_menu_store_list, 'admin_menu_callback')
        );

        add_submenu_page(
            DOCOSL_PLUGIN,
            '店舗登録',
            '店舗登録',
            'edit_pages',
            DOCOSL_PLUGIN . '-register-store',
            array($this->admin_menu_register_store, 'admin_menu_callback')
        );

        add_submenu_page(
            DOCOSL_PLUGIN,
            '表示設定',
            '表示設定',
            'edit_pages',
            DOCOSL_PLUGIN . '-display-setting',
            array($this->admin_menu_display_setting, 'admin_menu_callback')
        );

        add_submenu_page(
            DOCOSL_PLUGIN,
            '導入方法',
            '導入方法',
            'edit_pages',
            DOCOSL_PLUGIN . '-introduce',
            array($this->admin_menu_introduce, 'admin_menu_callback')
        );

        // メニューには表示しないページ

        add_submenu_page(
            null,
            'ZIPインポート・エクスポート',
            'ZIPインポート・エクスポート',
            'edit_pages',
            DOCOSL_PLUGIN . '-zip',
            array($this->admin_menu_zip, 'admin_menu_callback')
        );

        // 店舗管理のサブメニューは消したいのでここで消す
        remove_submenu_page(DOCOSL_PLUGIN, DOCOSL_PLUGIN);
    }

    public function register_admin_init() {
        // アップグレードチェックとアップグレード処理
        \DocodocoStoreLocator\Upgrader::upgrade();

        // ZIPインポート・エクスポートの初期化処理
        $this->admin_menu_zip->admin_init_callback();
    }

    /**
     * TODO: プラグインの実行処理
     */
    public function run() {

    }
}
