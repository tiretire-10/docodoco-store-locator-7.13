<?php

namespace DocodocoStoreLocator\Admin;

if ( ! defined( 'ABSPATH' ) ) exit;

use DocodocoStoreLocator\Models\Store;
use DocodocoStoreLocator\Validator;

class RegisterStore {

    /** @var 全体的なエラー（フォームの入力値に紐づかないものなど） */
    public $view_errors = array();

    /** @var フォームの各入力値のエラー */
    public $view_form_errors = array();

    /** @var フォームの入力値 */
    public $data = array();

    /** @var bool 既存店舗の編集モードの場合は true */
    public $edit_mode = false;

    public function __construct() {
    }

    /**
     * 管理画面のメニューから「店舗登録」にアクセスした際に呼ばれるコールバック
     */
    public function admin_menu_callback() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->post_register_store();
        } else {
            $this->get_register_store();
        }
    }

    /**
     * 店舗登録画面 GET リクエスト時の処理
     */
    public function get_register_store() {
        // フォームの入力値を初期化
        if(isset($_GET['storeId']) && is_numeric($_GET['storeId'])) {
            $store_id =(int)sanitize_text_field(wp_unslash($_GET['storeId']));
            $form_data = self::init_form_data($store_id);
            if (count($form_data) === 0) {
                // 指定された店舗が存在しない場合はエラーを表示
                wp_die("指定された店舗は存在しませんでした。店舗一覧ページに戻って最新の情報を確認してください。");
            }

            $this->data = $form_data;
            $this->edit_mode = true;
        }
        return $this->render();
    }

    /**
     * 店舗登録/編集画面 POST リクエスト時の処理
     */
    public function post_register_store() {
        global $wpdb;

        $form_data = array();

        /** @var array DBから取得した店舗データ */
        $store = null;

        /** @var array 登録用のデータ */
        $db_data = array();

        /** @var string 登録か更新かの文字列が入る。エラーメッセージ生成用 */
        $action_name = '登録';

        // nonce チェック
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'])), 'docosl-register-store')) {
            wp_die('不正なリクエストです');
        }

        // 既存の店舗データ取得
        if(isset($_REQUEST['storeId']) && is_numeric(sanitize_text_field(wp_unslash($_REQUEST['storeId'])))) {
            $store_id = (int)sanitize_text_field(wp_unslash($_REQUEST['storeId']));
            $store = self::init_form_data($store_id);
            if (count($store) === 0) {
                // 指定された店舗が存在しない場合はエラーを表示
                wp_die("指定された店舗は存在しませんでした。店舗一覧ページに戻って最新の情報を確認してください。");
            }
            $this->edit_mode = true;
            $action_name = '更新';
            $form_data['id'] = $store_id;
        }

        /**
         * 最初に入力文字をサニタイズする
         * WordPress が magic_quotes_gpc をエミュレートしてしまう (wp_magic_quotes()) ため wp_unslash をかける
         */
        $input_data['sort_order']       = sanitize_text_field(wp_unslash($_POST['sort_order']));
        $input_data['name']             = sanitize_text_field(wp_unslash($_POST['name']));
        $input_data['postal_code']      = sanitize_text_field(wp_unslash($_POST['postal_code']));
        $input_data['address']          = sanitize_text_field(wp_unslash($_POST['address']));
        $input_data['tel']              = sanitize_text_field(wp_unslash($_POST['tel']));
        $input_data['fax']              = sanitize_text_field(wp_unslash($_POST['fax']));
        $input_data['url']              = sanitize_url(wp_unslash($_POST['url']), array('http', 'https'));
        $input_data['email']            = sanitize_email(wp_unslash($_POST['email']));
        $input_data['open_hours']       = sanitize_text_field(wp_unslash($_POST['open_hours']));
        $input_data['regular_holiday']  = sanitize_text_field(wp_unslash($_POST['regular_holiday']));
        $input_data['parking']          = sanitize_text_field(wp_unslash($_POST['parking']));
        $input_data['lat']              = sanitize_text_field(wp_unslash($_POST['lat']));
        $input_data['lng']              = sanitize_text_field(wp_unslash($_POST['lng']));
        $input_data['remarks']          = sanitize_textarea_field(wp_unslash($_POST['remarks']));
        $input_data['admin_remarks']    = sanitize_textarea_field(wp_unslash($_POST['admin_remarks']));
        $input_data['department']       = sanitize_text_field(wp_unslash($_POST['department']));
        $input_data['contact']          = sanitize_text_field(wp_unslash($_POST['contact']));
        $input_data['age']              = sanitize_text_field(wp_unslash($_POST['age']));
        $input_data['period']           = sanitize_text_field(wp_unslash($_POST['period']));
        $input_data['document']         = sanitize_text_field(wp_unslash($_POST['document']));
        $input_data['application']      = sanitize_textarea_field(wp_unslash($_POST['application']));
        $input_data['belongings']       = sanitize_textarea_field(wp_unslash($_POST['belongings']));
        $input_data['cost']             = sanitize_text_field(wp_unslash($_POST['cost']));
        $input_data['publish_status']   = sanitize_text_field(wp_unslash($_POST['publish_status']));

        // 入力値が空かどうかはサニタイズした後の値でチェックする
        // 数値は int 型にキャストする
        $form_data['sort_order']        = $input_data['sort_order'] === '' ? null : (int)$input_data['sort_order'];
        $form_data['name']              = $input_data['name'] === '' ? null : $input_data['name'];
        $form_data['postal_code']       = $input_data['postal_code'] === '' ? null : $input_data['postal_code'];
        $form_data['address']           = $input_data['address'] === '' ? null : $input_data['address'];
        $form_data['tel']               = $input_data['tel'] === '' ? null : $input_data['tel'];
        $form_data['fax']               = $input_data['fax'] === '' ? null : $input_data['fax'];
        $form_data['url']               = $input_data['url'] === '' ? null : $input_data['url'];
        $form_data['email']             = $input_data['email'] === '' ? null : $input_data['email'];
        $form_data['open_hours']        = $input_data['open_hours'] === '' ? null : $input_data['open_hours'];
        $form_data['regular_holiday']   = $input_data['regular_holiday'] === '' ? null : $input_data['regular_holiday'];
        $form_data['parking']           = $input_data['parking'] === '' ? null : $input_data['parking'];
        $form_data['lat']               = $input_data['lat'] === '' ? null : $input_data['lat'];
        $form_data['lng']               = $input_data['lng'] === '' ? null : $input_data['lng'];
        $form_data['remarks']           = $input_data['remarks'] === '' ? null : $input_data['remarks'];
        $form_data['admin_remarks']     = $input_data['admin_remarks'] === '' ? null : $input_data['admin_remarks'];
        $form_data['department']        = $input_data['department'] === '' ? null : $input_data['department'];
        $form_data['contact']           = $input_data['contact'] === '' ? null : $input_data['contact'];
        $form_data['age']               = $input_data['age'] === '' ? null : $input_data['age'];
        $form_data['period']            = $input_data['period'] === '' ? null : $input_data['period'];
        $form_data['document']          = $input_data['document'] === '' ? null : $input_data['document'];
        $form_data['application']       = $input_data['application'] === '' ? null : $input_data['application'];
        $form_data['belongings']        = $input_data['belongings'] === '' ? null : $input_data['belongings'];
        $form_data['cost']              = $input_data['cost'] === '' ? null : $input_data['cost'];
        $form_data['publish_status']    = $input_data['publish_status'] === '' ? null : (int)$input_data['publish_status'];

        $db_data = $form_data;

        // 編集モードの時のみ存在する/必要なデータ
        if(isset($_POST['delete_store_image'])) {
            $input_data['delete_store_image'] = sanitize_text_field(wp_unslash($_POST['delete_store_image']));
            $form_data['delete_store_image'] = $input_data['delete_store_image'] === '1';
        } else {
            $form_data['delete_store_image'] = null;
        }
        $form_data['img_filename'] = isset($store['img_filename']) ? $store['img_filename'] : null;

        $wpdb->query('BEGIN');

        // 並び順チェック（任意項目）
        if (empty($form_data['sort_order']) || $form_data['sort_order'] < 1) {
            // sort_order が空の場合か、不正な値（1未満の整数か int で 0 になってしまう文字列など）の場合は自動採番にする
            $r = $wpdb->get_var("SELECT MAX(sort_order) + 1 AS next_sort_order FROM {$wpdb->prefix}docosl_stores FOR UPDATE");
            if ($r === null) {
                $r = 1; // まだ登録されていない場合は 1 から採番する
            }
            $db_data['sort_order'] = $r;
        }

        $form_upload_store_image = null;
        if (!empty($_FILES['store_image']) && $_FILES['store_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $form_upload_store_image = $_FILES['store_image'];
        }

        // バリデーション実施
        if (!$this->validate($form_data, $form_upload_store_image, $this->edit_mode)) {
            $this->view_errors[] = array('level' => 'danger', 'message' => "店舗の{$action_name}に失敗しました。フォームの入力項目を確認してください。");
            $this->data = $form_data;
            return $this->render();
        }

        // 画像アップロード処理
        /** @var callable 画像ファイルの変更を元に戻す関数。画像更新後にDB更新を中断するときに実行 */
        $store_image_rollback = function () {};

        if ($form_upload_store_image !== null || $this->edit_mode && $form_data['delete_store_image']) {
            $store_image_handle_result = $this->handle_upload_image($form_upload_store_image, $form_data['img_filename'], $form_data['delete_store_image']);
            $store_image_rollback = $store_image_handle_result['rollback'];
            if ($store_image_handle_result['error'] !== null) {
                // 画像アップロードに失敗したらエラーを表示して終了
                $store_image_rollback();
                $this->view_form_errors['store_image'] = $store_image_handle_result['error'];
                $this->view_errors[] = array('level' => 'danger', 'message' => "店舗の{$action_name}に失敗しました。");
                $this->data = $form_data;
                return $this->render();
            } else {
                $db_data['img_filename'] = $store_image_handle_result['filename'];
            }
        }

        $now = current_time('mysql');
        if ($this->edit_mode) {
            // update 実行
            $db_data['updated_at'] = $now;
            $query_result = $wpdb->update($wpdb->prefix . 'docosl_stores', Store::record_filter($db_data), array('id' => $store['id']));
            $this->data = $form_data;
        } else {
            // insert 実行
            $db_data['created_at'] = $now;
            $db_data['updated_at'] = $now;
            $query_result = $wpdb->insert($wpdb->prefix . 'docosl_stores', Store::record_filter($db_data));
            $store_id = $wpdb->insert_id;
        }

        if ($query_result === false) {
            // DB登録が失敗したら画像の変更を元に戻してエラーを表示して終了
            $store_image_rollback();
            error_log('failed update store record. [id=' . $this->data['id'] . ', error=' . $wpdb->last_error . ']');
            $this->view_errors[] = array('level' => 'danger', 'message' => "店舗の{$action_name}に失敗しました。DBエラーが発生しました。");
            $this->data = $form_data;
            return $this->render();
        }

        $commit_result = $wpdb->query('COMMIT');
        if ($commit_result === false) {
            // DB登録が失敗したら画像の変更を元に戻してエラーを表示して終了
            $store_image_rollback();
            error_log('failed commit store record. [error=' . $wpdb->last_error . ', id=' . $this->data['id'] . ']');
            $this->view_errors[] = array('level' => 'danger', 'message' => "店舗の{$action_name}に失敗しました。DBエラーが発生しました。");
            $this->data = $form_data;
            return $this->render();
        }

        // 店舗登録/更新が成功したら DB からデータを取得して 店舗編集画面 を表示する。
        $this->data = self::init_form_data($store_id);
        $this->edit_mode = true;
        $this->view_errors[] = array('level' => 'success', 'message' => "店舗の{$action_name}に成功しました。");
        return $this->render();
    }

    /**
     * 店舗登録画面を表示する
     */
    public function render() {
        // テンプレートで使用する変数を定義
        $data = $this->data;
        $view_form_errors = $this->view_form_errors;
        $view_errors = $this->view_errors;
        $view_edit_mode = $this->edit_mode;

        wp_enqueue_style('docosl-bootstrap', DOCOSL_URL_PATH . 'admin/css/bootstrap.min.css', array(), DOCOSL_VERSION);

        include DOCOSL_PLUGIN_PATH . '/admin/views/register-store.php';
    }

    /**
     * 画像アップロード処理
     * 返り値の配列の error が null でない場合はエラーが発生しているので error キーをチェックすること。
     * 結果的にファイルアップロードを無かったことにしたい場合は rollback キーに入っている関数を実行すること。
     * handle_upload_image() 処理自体が失敗した際は途中の処理を元に戻すため、内部で rollback を実行する。
     *
     * @param array $file $_FILES から取得したファイル情報
     * @param string|null $old_filename 既存のファイル名。店舗編集時且つ店舗画像のある店舗の場合は必ず指定する。
     * @param bool $delete_store_image 既存のファイルを削除するかどうか
     * @return array{error: string|null, filename: string|null, url: string|null, rollback: callable}
     *     error: エラーの場合は文字列が、成功の場合は null が入る
     *     filename: アップロードされたファイル名。失敗した場合は常に null
     *     rollback: ファイルを削除するための関数。物理ファイルの変更を元に戻す際に実行する。
     */
    public function handle_upload_image($file, $old_filename = null, $delete_store_image = false) {
        $rollback_funcs = array();
        $rollback = function() use (&$rollback_funcs) {
            foreach(array_reverse($rollback_funcs) as $func) {
                $func();
            }
            unset($func);

            // rollback は 一回のみ実施するため、実行後は空にする
            $rollback_funcs = array();
        };
        $return = array(
            'error' => null,
            'filename' => null,
            'rollback' => $rollback,
        );

        if ($old_filename !== null) {
            $old_filepath = DOCOSL_UPLOAD_DIR . 'store-images' . '/' . $old_filename;
        }

        if($file !== null) {
            $to_filepath = DOCOSL_UPLOAD_DIR . 'store-images' . '/' . $file['name'];

            // 新規店舗登録 or 店舗編集時の画像アップロード処理
            if ($old_filename) {
                // 店舗編集時
                $unlink_result = \DocodocoStoreLocator\Helper::unlink_with_rollback($old_filepath);
                $rollback_funcs[] = $unlink_result['rollback'];
                if ($unlink_result['success'] === false) {
                    $rollback();
                    $return['error'] = '既存のファイルの削除に失敗しました';
                    return $return;
                }
            }

            add_filter('upload_dir', array('\DocodocoStoreLocator\WPFilter', 'storeimages_upload_dir'));
            $upload_result = wp_handle_upload($file, [
                'test_form' => false,
                'unique_filename_callback' => array('\DocodocoStoreLocator\WPFilter', 'storeimages_unique_filename'),
            ]);
            remove_filter('upload_dir', array('\DocodocoStoreLocator\WPFilter', 'storeimages_upload_dir'));

            if (isset($upload_result['error'])) {
                $rollback();
                $return['error'] = "ファイルのアップロードに失敗しました。 " . $upload_result['error'];
                return $return;
            }
            $return['filename'] = $file['name'];
            $return['filename'] = basename($upload_result['file']);

            // ファイルアップロードに成功したときだけファイルを削除するロールバック用の関数を返す
            $rollback_funcs[] = function () use ($to_filepath) {
                unlink($to_filepath);
            };
        } else if ($delete_store_image === true) {
            // 店舗更新の時に画像を削除する場合の処理
            $unlink_result = \DocodocoStoreLocator\Helper::unlink_with_rollback($old_filepath);
            $rollback_funcs[] = $unlink_result['rollback'];
            if ($unlink_result['success'] === false) {
                $rollback();
                $return['error'] = 'ファイルの削除に失敗しました';
            }
            $return['filename'] = null;
        }
        return $return;
    }

    /**
     * フォームの入力値をチェックする
     * 
     * @return bool チェック結果。エラーがなければ true、エラーがあれば false
     */
    public function validate($data, $form_upload_store_image, $edit_mode = false) {
        $validate_ok = true;
        
        global $wpdb;
        // 並び順チェック（任意項目）
        if (empty($data['sort_order']) || $data['sort_order'] < 1) {
            // sort_order が空の場合か、不正な値（1未満の整数か int で 0 になってしまう文字列など）の場合は自動採番にする
            $r = $wpdb->get_var("SELECT MAX(sort_order) + 1 AS next_sort_order FROM {$wpdb->prefix}docosl_stores FOR UPDATE");
            if ($r === null) {
                $r = 1; // まだ登録されていない場合は 1 から採番する
            }
            $data['sort_order'] = $r;
        }

        // 店舗名チェック（必須項目）
        if (empty($data['name'])) {
            $validate_ok = false;
            $this->view_form_errors['name'] = '店舗名は必須です';
        } else {
            if ($this->edit_mode) {
                // 既存の店舗を編集するときは自分自身の名前は重複チェックしない
                $duplicate_store = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}docosl_stores WHERE name = %s AND id != %d", $data['name'], $data['id']));
            } else {
                $duplicate_store = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}docosl_stores WHERE name = %s", $data['name']));
            }
            if ($duplicate_store > 0) {
                $validate_ok = false;
                $this->view_form_errors['name'] = 'すでに登録されている店舗名です';
            }
        }

        // メールアドレスチェック（任意項目）
        if ($data['email'] !== null && is_email($data['email']) === false) {
            $validate_ok = false;
            $this->view_form_errors['email'] = 'メールアドレスは正しい形式で入力してください';
        }

        // url チェック（任意項目）
        if (
            $data['url'] !== null
            && (
                filter_var($data['url'], FILTER_VALIDATE_URL) === false
                || !preg_match('#\Ahttps?://#', $data['url'])
            )
        ) {
            $validate_ok = false;
            $this->view_form_errors['url'] = '正しいURLを入力してください';
        }

        // 緯度経度チェック（必須項目）
        if ($data['lat'] === null) {
            $validate_ok = false;
            $this->view_form_errors['lat'] = '緯度は必須です';
        } else {
            if (!preg_match('/\A-?[0-9]{1,2}\.[0-9]{1,}\Z/', $data['lat'])) {
                $validate_ok = false;
                $this->view_form_errors['lat'] = '緯度は半角数字の小数点で入力してください。';
            }
        }

        if ($data['lng'] === null) {
            $validate_ok = false;
            $this->view_form_errors['lng'] = '経度は必須です';
        } else {
            if (!preg_match('/\A-?[0-9]{1,3}\.[0-9]{1,}\Z/', $data['lng'])) {
                $validate_ok = false;
                $this->view_form_errors['lng'] = '経度は半角数字の小数点で入力してください。';
            }
        }

        // 画像ファイルチェック
        if ($form_upload_store_image !== null) {
            // 店舗の編集モードの時は店舗IDを入れる
            $ok = Validator::store_image($form_upload_store_image, $edit_mode ? $data['id'] : null);
            if ($ok !== true) {
                $validate_ok = false; 
                $this->view_form_errors['store_image'] = $ok;
            }
        }

        // 公開状態の値チェック
        if ($data['publish_status'] === null || !in_array($data['publish_status'], Store::PUBLISH_STATUS)) {
            $validate_ok = false;
            $this->view_form_errors['publish_status'] = '公開状態の値が不正です';
        }

        return $validate_ok;
    }

    /**
     * フォームの入力値を既存の店舗情報で初期化する
     * 
     * @param int $store_id 店舗の ID
     * @return array|false 店舗データをフォームに埋め込んだもの。店舗が見つからなかった場合は false
     */
    public static function init_form_data($store_id) {
        global $wpdb;

        $store = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}docosl_stores WHERE id = %d", $store_id));
        if ($store === false || $store === null) {
            return array();
        }

        // フォームの入力値を初期化
        $data = array(
            'id'                => (int)$store->id,
            'sort_order'        => (int)$store->sort_order,
            'name'              => $store->name,
            'postal_code'       => $store->postal_code,
            'address'           => $store->address,
            'tel'               => $store->tel,
            'fax'               => $store->fax,
            'url'               => $store->url,
            'email'             => $store->email,
            'open_hours'        => $store->open_hours,
            'regular_holiday'   => $store->regular_holiday,
            'parking'           => $store->parking,
            'lat'               => $store->lat,
            'lng'               => $store->lng,
            'img_filename'      => $store->img_filename,
            'remarks'           => $store->remarks,
            'admin_remarks'     => $store->admin_remarks,
            'publish_status'    => (int)$store->publish_status,
            'department'        => $store->department,
            'contact'           => $store->contact,
            'age'               => $store->age,
            'period'            => $store->period,
            'document'          => $store->document,
            'application'       => $store->application,
            'belongings'        => $store->belongings,
            'cost'              => $store->cost,

            // フォーム入力値では使わないが店舗画像の cache busting に使うので詰める
            'updated_at'        => $store->updated_at,
        );
        return $data;
    }
}
