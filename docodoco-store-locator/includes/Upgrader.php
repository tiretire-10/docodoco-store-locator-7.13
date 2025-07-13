<?php

namespace DocodocoStoreLocator;

if ( ! defined( 'ABSPATH' ) ) exit;

class Upgrader {
    public static function upgrade() {
        $stored_version = get_option('docosl_version');

        // 現在のバージョンが保存されているバージョンと同じ場合は何もしない
        if ($stored_version === DOCOSL_VERSION) {
            return;
        }

        // Models で実行する dbDelta() を使うために upgrade.php を読み込む必要がある
        // Models はアップグレード時やインストール時以外でも読み込むため upgrade.php の読み込みはここで行う
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        // マイグレーションが必要な場合にはここに処理を記述

        update_option('docosl_version', DOCOSL_VERSION);

        add_action('admin_notices', array('DocodocoStoreLocator\Upgrader', 'upgrade_success_notice'));
    }

    /**
     * アップデート完了の通知を表示する
     *
     * @return void
     */
    public static function upgrade_success_notice() {
        echo '<div class="notice notice-success is-dismissible">';
        echo '    <p>Docodoco Store Locator のアップデートが完了しました。</p>';
        echo '</div>';
    }
}
