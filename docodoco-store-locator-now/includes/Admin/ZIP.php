<?php

namespace DocodocoStoreLocator\Admin;

if ( ! defined( 'ABSPATH' ) ) exit;

use DocodocoStoreLocator\Models\Archive;
use DocodocoStoreLocator\Models\Store;
use DocodocoStoreLocator\Helper;

class ZIP {

    public function __construct() {
    }

    public function admin_menu_callback() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->post_import();
        } else {
            // GET は admin_init で処理するのでここに到達した場合は意図しないアクセスなのでエラーを返す
            wp_die("不正なアクセスです。");
        }
    }

    public function admin_init_callback() {
        // 関係ないページアクセスの場合は何もしない
        if (!isset($_GET['page']) || $_GET['page'] !== "docodoco-store-locator-zip") {
            return;
        }

        // エクスポート処理
        if (isset($_GET['action']) && $_GET['action'] === "export") {
            return $this->get_export();
        }
    }

    /**
     * zip インポート処理
     */
    public function post_import() {
        /** @var array{ title: string, msg: string }[] 全般的なエラーを格納 */
        $view_errors = array();

        /** @var string ビューで表示するCSVインポート結果のメッセージ */
        $view_upsert_result_msg = "";

        /** @var array CSV更新処理結果 */
        $view_upsert_result = null;

        /** @var string ビューで表示する画像更新結果のメッセージ */
        $view_update_store_images_result_msg = "";

        /** @var array 画像更新結果 */
        $view_update_store_images_result = null;

        /** @var bool ビューで使用する dry_run かどうかのフラグ */
        $view_dry_run = false;

        /**
         * アップロードされたファイルのチェック
         */
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'])), 'docodoco-store-locator-zip-import')) {
            wp_die('不正なリクエストです');
        }

        if (!isset($_FILES['file'])) {
            wp_die("ファイルが選択されていません。");
        }

        $uploaded_file = $_FILES['file'];

        if ($uploaded_file['error'] !== UPLOAD_ERR_OK) {
            $view_errors[] = array(
                'level' => "danger",
                'message' => "ファイルのアップロードに失敗しました。" . Helper::file_upload_error_string($uploaded_file['error']),
            );

            wp_enqueue_style('docosl-bootstrap', DOCOSL_URL_PATH . 'admin/css/bootstrap.min.css', array(), DOCOSL_VERSION);
            include DOCOSL_PLUGIN_PATH . '/admin/views/zip.php';
            return;
        }

        $allowed_mime_list = array(
            'zip'  => 'application/zip',
        );
        $r = wp_check_filetype_and_ext($uploaded_file['tmp_name'], $uploaded_file['name'], $allowed_mime_list);

        // ファイル拡張子が一致しているかのチェック
        if ($r['ext'] === false || $r['type'] === false) {
            $view_errors[] = array(
                'level' => "danger",
                'message' => "許可されていないファイル形式です。サポートされている形式: zip",
            );

            wp_enqueue_style('docosl-bootstrap', DOCOSL_URL_PATH . 'admin/css/bootstrap.min.css', array(), DOCOSL_VERSION);
            include DOCOSL_PLUGIN_PATH . '/admin/views/zip.php';
            return;
        }

        // 不正なファイル（zipを装ったファイルや、壊れたファイル）かチェック
        if ($r['proper_filename'] !== false) {
            $view_errors[] = array(
                'level' => "danger",
                'message' => "zipファイルが壊れているか、許可されていないファイル形式です。サポートされている形式: zip",
            );

            wp_enqueue_style('docosl-bootstrap', DOCOSL_URL_PATH . 'admin/css/bootstrap.min.css', array(), DOCOSL_VERSION);
            include DOCOSL_PLUGIN_PATH . '/admin/views/zip.php';
            return;
        }
        // アップロードされたファイルのチェックここまで

        $zip_file = $uploaded_file['tmp_name'];
        $dry_run = false;
        if (isset($_POST['dry_run']) && $_POST['dry_run'] === "1") {
            // dry_run が指定されていた場合は実際には更新処理を行わず、差分のみを表示する
            $dry_run = true;

            $view_errors[] = array(
                'level' => "info",
                'message' => "テスト実行のため、更新は反映していません。",
            );
        }
        $view_dry_run = $dry_run;

        try {
            $zip = Archive::read_from_file($zip_file);
        } catch (\Exception $e) {
            // アーカイブファイルがおかしかった場合
            $view_errors[] = array(
                'level' => "danger",
                'message' => "zipファイルの形式が一致しません。 " . $e->getMessage(),
            );
            error_log($e);

            wp_enqueue_style('docosl-bootstrap', DOCOSL_URL_PATH . 'admin/css/bootstrap.min.css', array(), DOCOSL_VERSION);
            include DOCOSL_PLUGIN_PATH . 'admin/views/zip.php';
            return;
        }

        /** @var array<string, bool> /uploads/ に既にある店舗画像のファイル名リスト */
        $uploaded_store_images = array();
        foreach(new \DirectoryIterator(DOCOSL_UPLOAD_DIR . "store-images/") as $k => $file_info) {
            if ($file_info->isDot()) {
                continue;
            }
            $filename = $file_info->getFilename();
            $uploaded_store_images[$filename] = true;
        }

        $has_csv = $zip->has_csv;

        $upsert_result = null;
        $update_store_images_result = null;

        // CSV 更新処理
        if ($has_csv) {
            $upsert_result = Store::upsert_from_archive($zip, $uploaded_store_images, $dry_run);

            if ($upsert_result['success']) {
                if (count($upsert_result['rows']) > 0) {
                    $view_upsert_result_msg = "店舗情報の更新が完了しました。";
                } else {
                    $view_upsert_result_msg = "差分がないため店舗情報の更新は行われませんでした。";
                }
            } else {
                $view_upsert_result_msg = "店舗情報の更新に失敗したため、店舗情報の更新は行われませんでした。";
            }
        } else {
            $view_upsert_result_msg = "CSVが含まれていないため店舗情報の更新は行われませんでした。";
        }

        // CSV 更新が失敗したらエラーを表示して終了
        if ($has_csv && $upsert_result['success'] === false) {
            $view_upsert_result = $upsert_result;
            $view_update_store_images_result = $update_store_images_result;

            // CSV 更新に失敗した場合は画像更新処理はスキップする
            $view_update_store_images_result_msg = "CSVインポートでエラーが発生したため、画像更新処理はスキップされました。";

            wp_enqueue_style('docosl-bootstrap', DOCOSL_URL_PATH . 'admin/css/bootstrap.min.css', array(), DOCOSL_VERSION);
            include DOCOSL_PLUGIN_PATH . '/admin/views/zip.php';
            return;
        }

        // 画像更新処理
        if ($zip->has_store_images) {
            // 画像ファイルの更新処理
            $update_store_images_result = self::update_store_images($zip, $uploaded_store_images, $dry_run);

            if ($update_store_images_result['success']) {
                if (count($update_store_images_result['changes']) > 0) {
                    $view_update_store_images_result_msg = "店舗画像の更新が完了しました。";
                } else {
                    $view_update_store_images_result_msg = "差分がないため店舗画像の更新は行われませんでした。";
                }
            } else {
                $view_update_store_images_result_msg = "店舗画像の更新に失敗しました。";
            }
        } else {
            $view_update_store_images_result_msg = "画像ファイルが含まれていないため店舗画像の更新は行われませんでした。";
        }

        $view_upsert_result = $upsert_result;
        $view_update_store_images_result = $update_store_images_result;
        wp_enqueue_style('docosl-bootstrap', DOCOSL_URL_PATH . 'admin/css/bootstrap.min.css', array(), DOCOSL_VERSION);
        include DOCOSL_PLUGIN_PATH . '/admin/views/zip.php';
    }

    /**
     * zip エクスポート処理
     */
    public function get_export() {
        /* @var string ブラウザで保存する際の zip ファイル名 */
        $zip_filename = "docosl-stores.zip";

        $zip_filepath = Archive::get_archive_from_db();

        $filesize = filesize($zip_filepath);

        // zip 出力処理
        header('Content-Type: application/zip');
        header('Content-Length: '.$filesize);
        header('Content-Disposition: attachment; filename="' . $zip_filename . '"');
        readfile($zip_filepath);
        // zip 出力終わり

        unlink($zip_filepath);
        exit;
    }

    /**
     * 画像ファイルの更新処理。新規画像の設置、変更がある画像の差し替え、未使用の画像の削除を行う。
     *
     * @param \DocodocoStoreLocator\Models\Archive $zip
     * @param array<string, bool> $uploaded_store_images /uploads/ に既にある店舗画像のファイル名リスト
     * @param bool $dry_run true の場合は実際には更新処理を行わない。デフォルト値: false
     * @return array{changes: array, errors: array, success: bool} 更新結果
     */
    public static function update_store_images($zip, $uploaded_store_images, $dry_run = false) {
        global $wpdb;

        /** @var array{changes: array, errors: array, success: bool} */
        $result = array(
            'changes' => array(),
            'errors' => array(),
            'success' => true,
        );

        /** @var array<int, string> 店舗で使われている画像ファイル名のリスト */
        $db_img_filenames = $wpdb->get_col("SELECT img_filename FROM {$wpdb->prefix}docosl_stores WHERE img_filename IS NOT NULL GROUP BY img_filename", 0);
        if ($wpdb->last_error !== "") {
            // DBエラー
            $result['errors'][] = '店舗画像ファイル名の重複チェックでDBエラーが発生しました。';
            error_log('failed check duplicate store names: [error=' . $wpdb->last_error . ']');
            $result['success'] = false;
            return $result;
        }

        /**
         * 新規画像登録
         */
        /** @var array<int, string> まだアップロードされていない画像ファイル名のリスト */
        $new_filenames = array_diff($db_img_filenames, array_keys($uploaded_store_images));

        foreach ($new_filenames as $k => $new_filename) {
            $result['changes'][$new_filename] = array(
                'type' => '新規追加',
                'filename' => $new_filename,
                'errors' => array(),
            );

            try {
                if (!$dry_run) {
                    $zip->extract_to(DOCOSL_UPLOAD_DIR . "store-images/" . $new_filename , $zip::STORE_IMAGES_DIR . $new_filename);
                }
            } catch (\Exception $e) {
                $result['changes'][$new_filename]['errors'][] = '画像ファイルの設置に失敗しました。';
                $result['success'] = false;
                error_log("failed zip import: add new img_file: [filename=" . $new_filename . ", error=" . $e->getMessage() . "]");
            }
        }

        /**
         * 画像差し替え
         */

        /** @var array<string, int> 現在使用されている店舗画像のファイル名のリスト */
        $db_img_filenames_key_filename = array_flip($db_img_filenames);

        foreach($zip->store_images as $filename => $zip_index) {
            // 新規画像登録で追加されたものはチェックする必要がないのでスキップする
            if (isset($result['changes'][$filename])) {
                continue;
            }

            // DBで使われていない画像ファイルだったらスルーする
            if (!isset($db_img_filenames_key_filename[$filename])) {
                continue;
            }

            // ダイジェストが一致しない場合は zip の画像ファイルで置き換え
            $uploaded_image_hash = md5_file(DOCOSL_UPLOAD_DIR . 'store-images/' . $filename);
            if ($uploaded_image_hash === false) {
                $result['errors'][] = '現在使用中の画像ファイル ' . $filename . 'を読み込めませんでした。';
                $result['success'] = false;
                continue;
            }

            try {
                $zip_image_hash = $zip->get_md5_store_image($filename);
            } catch (\Exception $e) {
                $result['errors'][] = 'zipファイル内の画像ファイル ' . $filename . 'を読み込めませんでした。';
                $result['success'] = false;
                continue;
            }

            if ($uploaded_image_hash === $zip_image_hash) {
                continue;
            }

            $result['changes'][$filename] = array(
                'type' => '差し替え',
                'filename' => $filename,
                'errors' => array(),
            );

            try {
                if (!$dry_run) {
                    $zip->extract_to(DOCOSL_UPLOAD_DIR . "store-images/" . $filename, $zip::STORE_IMAGES_DIR . $filename);
                }
            } catch (\Exception $e) {
                $result['changes'][$filename]['errors'][] = '画像ファイルの差し替えに失敗しました。';
                $result['success'] = false;
                error_log("failed zip import: replace img_file: [filename=" . $filename . ", error=" . $e->getMessage() . "]");
            }
        }

        /**
         * 未使用の画像ファイル削除
         */

        /** @var array<int, string> 未使用の画像ファイル名のリスト */
        $unused_filenames = array_diff(array_keys($uploaded_store_images), $db_img_filenames);

        foreach ($unused_filenames as $k => $unused_filename) {
            if (!$dry_run) {
                unlink(DOCOSL_UPLOAD_DIR . "store-images/" . $unused_filename);
            }
            $result['changes'][$unused_filename] = array(
                'type' => '削除',
                'filename' => $unused_filename,
                'errors' => array(),
            );
        }

        return $result;
    }
}
