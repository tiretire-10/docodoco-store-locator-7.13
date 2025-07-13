<?php

namespace DocodocoStoreLocator;

if ( ! defined( 'ABSPATH' ) ) exit;

use DocodocoStoreLocator\Models\Store;

class Validator {

    /**
     * 店舗情報のバリデーション
     * 
     * @param string $store_name
     * @return bool|string true: OK, string: error message
     */
    public static function store_name($store_name) {
        if (empty($store_name)) {
            return '店舗名は必須です';
        }
        return true;
    }

    /**
     * 並び順のバリデーション
     * 
     * @param string $sort_order
     * @return bool|string true: OK, string: error message
     */
    public static function sort_order($sort_order) {
        if (empty($sort_order)) {
            return '並び順は必須です';
        }
        if (!preg_match('/\A[0-9]{1,}\Z/', $sort_order)) {
            return '並び順は半角数字で入力してください。';
        }
        return true;
    }

    /**
     * メールアドレスのチェック
     * 
     * @param string $email
     * @return bool|string true: OK, string: error message
     */
    public static function email($email) {
        if ($email !== null && is_email($email) === false) {
            return 'メールアドレスは正しい形式で入力してください';
        }
        return true;
    }

    /**
     * URLのチェック
     * 
     * @param string $url
     * @return bool|string true: OK, string: error message
     */
    public static function url($url) {
        if ($url !== null && (filter_var($url, FILTER_VALIDATE_URL) === false || !preg_match('#\Ahttps?://#', $url))) {
            return '正しいURLを入力してください';
        }
        return true;
    }

    /**
     * 緯度のチェック
     * 
     * @param string $lat
     * @return bool|string true: OK, string: error message
     */
    public static function lat($lat) {
        if ($lat === null) {
            return '緯度は必須です';
        }
        if (!preg_match('/\A-?[0-9]{1,2}\.[0-9]{1,}\Z/', $lat)) {
            return '緯度は半角数字の小数点で入力してください。';
        }
        return true;
    }

    /**
     * 経度のチェック
     * 
     * @param string $lng
     * @return bool|string true: OK, string: error message
     */
    public static function lng($lng) {
        if ($lng === null) {
            return '経度は必須です';
        }
        if (!preg_match('/\A-?[0-9]{1,3}\.[0-9]{1,}\Z/', $lng)) {
            return '経度は半角数字の小数点で入力してください。';
        }
        return true;
    }

    /**
     * 公開状態のチェック
     * 
     * @param string $publish_status
     * @return bool|string true: OK, string: error message
     */
    public static function publish_status($publish_status) {
        if ($publish_status === null) {
            return '公開状態は必須です';
        }
        if (!in_array($publish_status, Store::PUBLISH_STATUS)) {
            return '公開状態は 1 か 2 を指定してください';
        }
        return true;
    }

    /**
     * 画像ファイルのチェック
     * $store_id が埋まっている場合は、その店舗の画像を上書きする場合と判断する。
     * 既存のファイル名と重複するファイルは弾かれるが、
     * 既存店舗の場合は更新対象の店舗に設定されているファイル名に限り重複していても許可される。
     * 
     * @param array $file $_FILES['file'] の形式
     * @param int|null $store_id 既存店舗の更新をする場合はその店舗のIDを指定する。
     * @return bool|string true: OK, string: error message
     */
    public static function store_image($file, $store_id = null) {
        // ファイルサイズチェック 暫定で 1MB
        if ($file['size'] > 1 * 1024 * 1024) {
            return "ファイルサイズが大き過ぎます。1MB 以下のファイルをアップロードしてください。";
        }

        // 悪意のあるファイル名でないかチェック
        if (validate_file($file['name']) > 0) {
            return "ファイル名に使用できない文字が含まれています";
        }

        // ファイル拡張子チェック, 画像ファイルかチェック
        $allowed_mime_list = array(
            'jpg|jpeg'  => 'image/jpeg',
            'png'       => 'image/png',
        );
        $r = wp_check_filetype_and_ext($file['tmp_name'], $file['name'], $allowed_mime_list);

        // ファイル拡張子が一致しているかのチェック
        if ($r['ext'] === false || $r['type'] === false) {
            return "許可されていないファイル形式です。サポートされている形式: jpeg, png";
        }

        // 不正なファイル（画像ファイルを装ったファイルなど）かチェック
        if ($r['proper_filename'] !== false) {
            return "画像が壊れているか、許可されていないファイル形式です。サポートされている形式: jpeg, png";
        }

        global $wpdb;

        // WordPress はアップロードしたファイル名をサニタイズするため、バリデーション時もサニタイズされたファイル名でチェックを行う。
        $sanitized_filename = wp_unique_filename(DOCOSL_UPLOAD_DIR, $file['name'], array('\DocodocoStoreLocator\WPFilter', 'storeimages_unique_filename'));

        // ファイルの重複チェック
        if ($store_id !== null) {
            // 既存店舗の場合は更新対象の店舗に設定されているファイル名に限り重複していても許可する
            $duplicate_file = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}docosl_stores WHERE img_filename = %s AND id != %d", $sanitized_filename, $store_id));
        } else {
            // 新規店舗の場合は重複しているファイル名は許可しない
            $duplicate_file = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}docosl_stores WHERE img_filename = %s", $sanitized_filename));
        }

        if ($duplicate_file > 0) {
            return "既に同じファイル名の画像が登録されています。 ファイル名:" . $sanitized_filename;
        }
        return true;
    }
}
