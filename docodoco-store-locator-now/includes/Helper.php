<?php

namespace DocodocoStoreLocator;

if ( ! defined( 'ABSPATH' ) ) exit;

class Helper {

    /**
     * キーを指定して配列から値を取得する。キーが存在しない場合は空文字を返す
     * テンプレートで配列の値を表示するとき、キーが存在しないと warn が出るので、抑制するために使用する
     * 
     * @param array $array
     * @param string $key
     * @return string
     */
    public static function arr_get($array, $key)
    {
        if (!is_array($array)) {
            return "";
        }
        if (array_key_exists($key, $array)) {
            return $array[$key];
        }
        return "";
    }

    /**
     * 文字列の先頭と末尾の全角・半角スペースを削除する
     * 
     * @param string|null $string 対象の文字列。
     * @return string|null 先頭と末尾の全角・半角スペースが削除された文字列。入力に null が渡された場合は null を返す
     */
    public static function trim($string) {
        if ($string === null) {
            return null;
        }
        return preg_replace('/^[\s　]+|[\s　]+$/u', '', $string);
    }

    /**
     * rollback 付きの unlink。rollback を実行すると削除したファイルを復元する。
     * rollback した際、元のファイルのパーミッションやタイムスタンプは維持されない。
     * 
     * @param string $filepath 削除するファイルのパス
     * @return array{success: bool, rollback: callable}
     */
    public static function unlink_with_rollback($filepath) {
        $result = array(
            'success' => false,
            'rollback' => function() {},
        );

        if (file_exists($filepath) === false) {
            $result['success'] = true;
            return $result;
        }

        $fh_for_rollback = fopen($filepath, 'rb');

        if (unlink($filepath) === false) {
            $result['success'] = false;
            fclose($fh_for_rollback);
            return $result;
        }

        $result['success'] = true;
        $result['rollback'] = function() use ($filepath, $fh_for_rollback) {
            $fh = fopen($filepath, 'wb');
            if ($fh === false) {
                fclose($fh_for_rollback);
                error_log("failed rollback unlink: [cause=failed open rollback file, filepath=$filepath]");
                return false;
            }

            if (ftruncate($fh, 0) === false) {
                fclose($fh_for_rollback);
                fclose($fh);
                error_log("failed rollback unlink: [cause=failed ftruncate rollback file, filepath=$filepath]");
                return false;
            }

            if (stream_copy_to_stream($fh_for_rollback, $fh) === false) {
                fclose($fh_for_rollback);
                fclose($fh);
                error_log("failed rollback unlink: [cause=failed copy rollback file, filepath=$filepath]");
                return false;
            }

            fclose($fh);
            fclose($fh_for_rollback);
        };

        return $result;
    }

    /**
     * ファイルアップロード時のエラーコードを文字列に変換する
     *
     * @param integer $error_code
     * @return string
     */
    public static function file_upload_error_string($error_code) {
        $msg = "";

        switch ($error_code) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $msg = "ファイルサイズが大きすぎます。サーバの設定を変更するか、ファイルサイズを小さくしてください。";
                break;
            case UPLOAD_ERR_PARTIAL:
                $msg = "ファイルが一部しかアップロードされませんでした。もう一度アップロードしてください。";
                break;
            case UPLOAD_ERR_NO_FILE:
                $msg = "ファイルが選択されていません。";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $msg = "一時フォルダが見つかりません。";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $msg = "ディスクへの書き込みに失敗しました。";
                break;
            case UPLOAD_ERR_EXTENSION:
                $msg = "PHPの拡張モジュールによってファイルのアップロードが中止されました。";
                break;
            default:
                $msg = "不明なエラーが発生しました。";
                break;
        }

        return $msg;
    }
}
