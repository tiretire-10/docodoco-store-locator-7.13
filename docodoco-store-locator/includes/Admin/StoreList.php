<?php

namespace DocodocoStoreLocator\Admin;

if ( ! defined( 'ABSPATH' ) ) exit;

class StoreList {

    /** @var アラート */
    public $view_alerts = array();

    /** @var 店舗データ */
    public $stores = array();

    /** @var ラベルの一覧 */
    public $labels = array();

    /** @var 公開状態 */
    public $publish_status = array();

    public function __construct() {
        // 定数の取得
        $this->labels = \DocodocoStoreLocator\Models\Store::LABELS;
        $this->publish_status = \DocodocoStoreLocator\Models\Store::PUBLISH_STATUS;
    }

    public function admin_menu_callback() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->post_store_list();
        } else {
            $this->get_store_list();
        }
    }

    public function get_store_list()
    {
        // 全店舗データ取得
        $this->get_all_store_list();

        // 画面を表示
        $this->render();
    }

    public function post_store_list()
    {
        global $wpdb;

        // nonce チェック
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'])), 'docosl-store-list')) {
            wp_die('不正なリクエストです');
        }

        /**
         * WordPress が magic_quotes_gpc をエミュレートしてしまう (wp_magic_quotes()) ため wp_unslash をかける
         * 最初に入力文字をサニタイズする
         */
        $input_data['bulkActionType'] = sanitize_text_field(wp_unslash($_POST['bulkActionType']));
        $input_data['storeIds'] = array();
        if (isset($_POST['storeIds'])) {
            foreach ($_POST['storeIds'] as $store_id) {
                if (is_numeric($store_id)) {
                    $input_data['storeIds'][] = (int)sanitize_text_field(wp_unslash($store_id));
                }
            }
        }

        // 操作種類のチェック
        if (!in_array($input_data['bulkActionType'], array('delete', 'published', 'unpublished'))) {
            $this->view_alerts[] = array('level' => 'danger', 'message' => '無効な一括操作が選択されました。削除、公開、非公開のいずれかを選択してください。');
            $this->get_all_store_list();
            return $this->render();
        }

        // 選択店舗の空チェック
        if (empty($input_data['storeIds'])) {
            $this->view_alerts[] = array('level' => 'danger', 'message' => '操作対象の店舗が選択されていません。少なくとも一つの店舗を選択してから操作を実行してください。');
            $this->get_all_store_list();
            return $this->render();
        }

        // 更新有無のチェック
        $selectedStores = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}docosl_stores WHERE id IN (" . implode(',', array_fill(0, count($input_data['storeIds']), '%d')) . ")",
                $input_data['storeIds'],
            ),
        );

        if ($input_data['bulkActionType'] === 'published') {
            $filteredStores = array_filter($selectedStores, function ($store) {
                return $store->publish_status == $this->publish_status['PUBLISHED'];
            });
        
            // 選択された店舗が全て公開済みの場合は更新がないので、処理を中断
            if (count($filteredStores) === count($input_data['storeIds'])) {
                $this->view_alerts[] = array('level' => 'info', 'message' => '選択された店舗には更新が必要ないため、操作を実行しませんでした。');
                $this->get_all_store_list();
                return $this->render();
            }
        } elseif ($input_data['bulkActionType'] === 'unpublished') {
            $filteredStores = array_filter($selectedStores, function ($store) {
                return $store->publish_status == $this->publish_status['UNPUBLISHED'];
            });

            // 選択された店舗が全て公開済みの場合は更新がないので、処理を中断
            if (count($filteredStores) === count($input_data['storeIds'])) {
                $this->view_alerts[] = array('level' => 'info', 'message' => '選択された店舗には更新が必要ないため、操作を実行しませんでした。');
                $this->get_all_store_list();
                return $this->render();
            }
        }

        // 選択店舗の存在チェック
        if (count($selectedStores) !== count($input_data['storeIds'])) {
            // 1件でも存在しない店舗がある場合はエラー
            $this->view_alerts[] = array('level' => 'danger', 'message' => '選択された店舗が存在しないため、操作を実行できませんでした。画面をリロードして最新情報を取得してください。');
            $this->get_all_store_list();
            return $this->render();
        }

        // DB更新
        $result = true;
        if ($input_data['bulkActionType'] === 'delete') {
            // レコード削除
            $result = $wpdb->query(
                $wpdb->prepare(
                    "DELETE FROM {$wpdb->prefix}docosl_stores WHERE id IN (" . implode(',', array_fill(0, count($input_data['storeIds']), '%d')) . ")",
                    $input_data['storeIds'],
                ),
            );
            foreach($selectedStores as $store) {
                // 画像ファイル削除
                if (!empty($store->img_filename)) {
                    $image_path = DOCOSL_UPLOAD_DIR . 'store-images/' . $store->img_filename;
                    if (file_exists($image_path)) {
                        if(!unlink($image_path)) {
                            error_log("failed delete store image file in StoreList.php. [filepath=$image_path, id={$store->id}]");
                        }
                    }
                }
            }
        } elseif ($input_data['bulkActionType'] === 'published') {
            // 公開状態へ更新
            $result = $wpdb->query(
                $wpdb->prepare(
                    "UPDATE {$wpdb->prefix}docosl_stores SET publish_status = %d, updated_at = %s WHERE id IN (" .
                        implode(',', array_fill(0, count($input_data['storeIds']), '%d')) .
                        ")",
                    array_merge(
                        array($this->publish_status['PUBLISHED'], current_time('mysql')),   // publish_status = %d, updated_at = %s
                        $input_data['storeIds'],                                            // id IN ()
                    ),
                ),
            );
        } elseif ($input_data['bulkActionType'] === 'unpublished') {
            // 非公開状態へ更新
            $result = $wpdb->query(
                $wpdb->prepare(
                    "UPDATE {$wpdb->prefix}docosl_stores SET publish_status = %d, updated_at = %s WHERE id IN (" .
                        implode(',', array_fill(0, count($input_data['storeIds']), '%d')) .
                        ")",
                        array_merge(
                            array($this->publish_status['UNPUBLISHED'], current_time('mysql')), // publish_status = %d, updated_at = %s
                            $input_data['storeIds'],                                            // id IN ()
                        ),
                )
            );
        }
        
        // アラート設定
        if ($result === false) {
            error_log('failed insert store record. [error=' . $wpdb->last_error . ']');
            $this->view_alerts[] = array('level' => 'danger', 'message' => '一括操作に失敗しました。DBエラーが発生しました。');
        }else {
            $this->view_alerts[] = array('level' => 'success', 'message' => '一括操作が完了しました。');
        }

        // 画面再表示
        $this->get_all_store_list();
        return $this->render();
    }

    /**
     * 店舗データをすべて取得する
     */    
    public function get_all_store_list() {
        global $wpdb;

        // 全店舗のデータを取得
        $this->stores = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}docosl_stores ORDER BY sort_order, name ASC" );
    }

    /**
     * 店舗一覧画面を表示する
     */
    public function render() {
        // テンプレートで使用する変数を定義
        $view_alerts = $this->view_alerts;
        $stores = $this->stores;
        $labels = $this->labels;
        $publish_status = $this->publish_status;

        wp_enqueue_style('docosl-bootstrap', DOCOSL_URL_PATH . 'admin/css/bootstrap.min.css', array(), DOCOSL_VERSION);

        include DOCOSL_PLUGIN_PATH . '/admin/views/store-list.php';
    }
}
