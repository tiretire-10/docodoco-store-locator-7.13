<?php

namespace DocodocoStoreLocator\Models;

if ( ! defined( 'ABSPATH' ) ) exit;

use DocodocoStoreLocator\Validator;

class Store {

    // 定数
    const LABELS = [
        'sort_order' => '並び順',
        'name' => '店舗・支店名',
        'address' => '住所',
        'tel' => 'TEL',
        'fax' => 'FAX',
        'url' => 'URL',
        'email' => 'メールアドレス',
        'open_hours' => '営業時間',
        'regular_holiday' => '定休日',
        'open_hours' => '営業時間',
        'parking' => '駐車場',
        'publish_status' => '公開状態',
        'remarks' => '備考',
        'admin_remarks' => '管理画面備考',
        'department' => '担当部署',
        'contact' => '連絡',
        'age' => '受診年齢',
        'period' => '検診期間',
        'document' => '問診票・受診券の有無',
        'application' => '申し込み',
        'belongings' => '持ち物',
        'cost' => '費用',
        'created_at' => '登録日',
        'updated_at' => '更新日'
    ];

    const PUBLISH_STATUS = [
        'UNPUBLISHED' => 1,
        'PUBLISHED' => 2,
    ];

    const COLUMNS = array(
        'sort_order' => array(
            'label' => '並び順',
            'required' => true,
            'type' => 'int',
            'validation' => Validator::class . '::sort_order',
        ),
        'name' => array(
            'label' => '店舗・支店名',
            'required' => true,
            'type' => 'string',
            'validation' => Validator::class . '::store_name'
        ),
        'postal_code' => array(
            'label' => '郵便番号',
            'required' => false,
            'type' => 'string',
        ),
        'address' => array(
            'label' => '住所',
            'required' => false,
            'type' => 'string',
        ),
        'tel' => array(
            'label' => 'TEL',
            'required' => false,
            'type' => 'string',
        ),
        'fax' => array(
            'label' => 'FAX',
            'required' => false,
            'type' => 'string',
        ),
        'url' => array(
            'label' => 'URL',
            'required' => false,
            'type' => 'string',
            'validation' => Validator::class .  '::url',
        ),
        'email' => array(
            'label' => 'メールアドレス',
            'required' => false,
            'type' => 'string',
            'validation' => Validator::class . '::email',
        ),
        'open_hours' => array(
            'label' => '営業時間',
            'required' => false,
            'type' => 'string',
        ),
        'regular_holiday' => array(
            'label' => '定休日',
            'required' => false,
            'type' => 'string',
        ),
        'parking' => array(
            'label' => '駐車場',
            'required' => false,
            'type' => 'string',
        ),
        'lat' => array(
            'label' => '緯度',
            'required' => true,
            'type' => 'string',
            'validation' => Validator::class . '::lat',
        ),
        'lng' => array(
            'label' => '経度',
            'required' => true,
            'type' => 'string',
            'validation' => Validator::class . '::lng',
        ),
        'img_filename' => array(
            'label' => '店舗画像',
            'required' => false,
            'type' => 'string',
        ),
        'publish_status' => array(
            'label' => '公開状態',
            'required' => true,
            'type' => 'int',
            'options' => array(
                self::PUBLISH_STATUS['UNPUBLISHED'] => '非公開',
                self::PUBLISH_STATUS['PUBLISHED'] => '公開',
            ),
            'validation' => Validator::class . '::publish_status',
        ),
        'remarks' => array(
            'label' => '備考',
            'required' => false,
            'type' => 'string',
        ),
        'admin_remarks' => array(
            'label' => '管理画面備考',
            'required' => false,
            'type' => 'string',
        ),
        'department' => array(
            'label' => '担当部署',
            'required' => false,
            'type' => 'string',
        ),
        'contact' => array(
            'label' => '連絡',
            'required' => false,
            'type' => 'string',
        ),
        'age' => array(
            'label' => '受診年齢',
            'required' => false,
            'type' => 'string',
        ),
        'period' => array(
            'label' => '検診期間',
            'required' => false,
            'type' => 'string',
        ),
        'document' => array(
            'label' => '問診票・受診券の有無',
            'required' => false,
            'type' => 'string',
        ),
        'application' => array(
            'label' => '申し込み',
            'required' => false,
            'type' => 'string',
        ),
        'belongings' => array(
            'label' => '持ち物',
            'required' => false,
            'type' => 'string',
        ),
        'cost' => array(
            'label' => '費用',
            'required' => false,
            'type' => 'string',
        ),
    );

    public static function create_table() {
        global $wpdb;

        $sql = "CREATE TABLE `{$wpdb->prefix}docosl_stores` (
            `id` int unsigned NOT NULL AUTO_INCREMENT,
            `sort_order` int unsigned NOT NULL,
            `name` varchar(255) NOT NULL,
            `postal_code` varchar(50) NULL,
            `address` varchar(255) NULL,
            `tel` varchar(50) NULL,
            `fax` varchar(50) NULL,
            `url` varchar(255) NULL,
            `email` varchar(100) NULL,
            `open_hours` varchar(255) NULL,
            `regular_holiday` varchar(255) NULL,
            `parking` varchar(255) NULL,
            `lat` varchar(50) NOT NULL,
            `lng` varchar(50) NOT NULL,
            `img_filename` varchar(100) NULL,
            `remarks` text NULL,
            `admin_remarks` text NULL,
            `department` varchar(255) NULL,
            `contact` varchar(255) NULL,
            `age` varchar(255) NULL,
            `period` varchar(255) NULL,
            `document` varchar(255) NULL,
            `application` text NULL,
            `belongings` varchar(255) NULL,
            `cost` varchar(255) NULL,
            `publish_status` int NOT NULL DEFAULT '1',
            `created_at` datetime NOT NULL,
            `updated_at` datetime NOT NULL,
            PRIMARY KEY (`id`)
        ) DEFAULT CHARSET=UTF8MB4;";

        dbDelta($sql);
    }

    public static function set_default_value() {
        global $wpdb;

        // レコード数が0件の場合のみ初期データをセットする
        $record_count = $wpdb->get_results("SELECT COUNT(*) as count FROM {$wpdb->prefix}docosl_stores");

        if ($record_count && $record_count[0]->count === '0') {
            // 初期データを非公開でセットする
            $wpdb->insert($wpdb->prefix . "docosl_stores", array(
                'sort_order' => 1,
                'name' => '店舗例1',
                'postal_code' => '0001111',
                'address' => '東京都渋谷区xxx',
                'tel' => '00011112222',
                'fax' => '00011112223',
                'url' => 'https://test1.com',
                'email' => 'store1@example.com',
                'open_hours' => '10:00〜19:00',
                'regular_holiday' => '月曜日、火曜日',
                'parking' => '有り10台',
                'lat' => '35.65816463',
                'lng' => '139.7016143',
                'remarks' => '備考例1です',
                'admin_remarks' => '管理画面の備考例1です',
                'created_at' => '2023-09-26 10:00:00',
                'updated_at' => '2023-09-26 10:00:00',
            ));

            $wpdb->insert($wpdb->prefix . "docosl_stores", array(
                'sort_order' => 2,
                'name' => '店舗例2',
                'postal_code' => '0002222',
                'address' => '東京都新宿区xxx',
                'tel' => '00022223333',
                'fax' => '00022223334',
                'url' => 'https://test2.com',
                'email' => 'store2@example.com',
                'open_hours' => '9:00〜18:00',
                'regular_holiday' => '土曜日、日曜日',
                'parking' => '無し',
                'lat' => '35.68969771',
                'lng' => '139.7005693',
                'remarks' => '備考例2です',
                'admin_remarks' => '管理画面の備考例2です',
                'created_at' => '2023-09-26 10:00:01',
                'updated_at' => '2023-09-26 10:00:01',
            ));
        }
    }

    public static function drop_table() {
        global $wpdb;

        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}docosl_stores");
    }

    /**
     * zip ファイルを使って店舗テーブルを更新する
     * 
     * @param DocodocoStoreLocator\Models\Archive $zip
     * @param array<int, string> $uploaded_store_images 既存の画像ファイル名のリスト（/uploads/docodoco-store-locator/store-images/ 以下のファイル名のリスト）
     * @param bool $dry_run true の場合は実際には更新処理を行わない。デフォルト値: false
     * @return array{changes: array, errors: array} 更新結果
     * @throws \Exception
     */
    public static function upsert_from_archive($zip, $uploaded_store_images, $dry_run = false) {
        /** @var string 現在時刻 */
        $now = current_time('mysql');

        /** @var array{changes: array, errors: array} */
        $result = array(
            'rows' => array(), // CSV 各行の処理結果
            'errors' => array(), // CSV の行に関係ない情報
            'success' => true,
        );
        /** @var array{ id: int, name: string, type: string }[] */
        $changes = array();

        /** @var array{ id: int, name: string, msg: string }[] 更新に失敗したレコードと原因の詳細 */
        $errors = array();

        /** @var array 画像ファイル存在チェック用: アップロード済みの画像とzipにある画像のリストをマージしたもの */
        $exists_images = $uploaded_store_images;

        foreach($zip->store_images as $filename => $archive_index) {
            $exists_images[$filename] = true;
        }

        global $wpdb;

        if ($wpdb->query("BEGIN") === false) {
            throw new \Exception("トランザクションの開始に失敗しました。");
        }

        $csv = $zip->csv;
        $columns = $csv->fields;

        // CSVにidがなかった場合も考慮する。
        // id が最初に来るようにする。（OBJECT_K で id をキーにした連想配列にするため）
        $query_columns = array_filter($columns, function ($v) {
            return $v !== 'id';
        });
        array_unshift($query_columns, 'id');

        /** @var array<int, \stdClass> 更新前のDBの店舗情報のリスト。キーに stores.id が入る array。 */
        // TODO: カラムを動的指定しているところどうにかする
        $db_store_rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT " . implode(",", array_fill(0, count($query_columns), "%i")) . " FROM {$wpdb->prefix}docosl_stores ORDER BY id ASC FOR UPDATE",
                $query_columns,
            ),
            OBJECT_K,
        );
        if ($wpdb->last_error !== "") {
            throw new \Exception("現在の店舗情報の取得に失敗しました。");
        }

        // CSVの行を読み込み。ヘッダは読み込み済みなので 2から始める
        for ($csv_line = 2; ; $csv_line++) {
            $csv_row = $csv->read();
            if ($csv_row === null) continue; // 空行はスキップ
            if ($csv_row === false) break; // EOF

            // フィルター処理。update/insert の直前に実施するものだが、DBとの差分を取る必要があるのでここで実施する
            $csv_row = self::record_filter($csv_row);

            $error = array();
            $errors = array();
            // START
            if (isset($csv_row['id']) && isset($db_store_rows[$csv_row['id']])) {
                /**
                 * UPDATE 処理: CSV の行に id があり、DB に一致する行がある時
                 */

                /** @var stdObject 更新対象のDBレコードのデータ */
                $target_row = $db_store_rows[$csv_row['id']];

                // 差分の抽出
                $diff = self::diff($target_row, $csv_row);

                if (count($diff) === 0) continue; // 差分がない場合（更新がない行の場合）はスキップ

                // ユーザ報告用の変更を記録
                $changes = array(
                    'id' => $csv_row['id'],
                    'name' => $csv_row['name'],
                    'type' => 'UPDATE',
                    'diff' => $diff,
                );

                //バリデーション
                $error = self::get_validator_for_update($diff);
                // エラーがあればUPDATEせずに次の処理へ
                if(count($error) > 0) {
                    $result['success'] = false;
                    $errors[] = implode(", ", $error);

                    $result['rows'][$csv_line] = array(
                        'changes' => $changes,
                        'errors' => $errors,
                    );
                    continue;
                }

                // DB UPDATE
                if (!self::update_by_id($csv_row['id'], $diff)) {
                    // UPDATE 失敗
                    $result['success'] = false;
                    $errors[] = '店舗情報の更新に失敗しました';
                }
            } else {
                /**
                 * INSERT 処理: CSV の行に id がないか、あっても DB に一致する行がない時
                 */

                // ユーザ報告用の変更を記録
                $changes = array(
                    'id' => isset($csv_row['id']) ? $csv_row['id'] : null,
                    'name' => $csv_row['name'],
                    'type' => 'INSERT',
                );

                // 新規レコードの場合は id = "" が入っている場合があるので削除する
                if (isset($csv_row['id']) && empty($csv_row['id'])) {
                    unset($csv_row['id']);
                }

                // バリデーション
                $error = self::get_validator_for_insert($csv_row);
                if(count($error) > 0) {
                    $result['success'] = false;
                    $errors[] = implode(", ", $error);

                    $result['rows'][$csv_line] = array(
                        'changes' => $changes,
                        'errors' => $errors,
                    );
                    continue;
                }

                $new_store_record = $csv_row;
                $new_store_record['created_at'] = $now;
                $new_store_record['updated_at'] = $now;

                if (!$wpdb->insert($wpdb->prefix . 'docosl_stores', $new_store_record)) {
                    // INSERT 失敗
                    $result['success'] = false;
                    $errors[] = 'DBエラー: インサートに失敗しました';
                }
            }
            $result['rows'][$csv_line] = array(
                'changes' => $changes,
                'errors' => $errors,
            );
        }

        // 店舗名重複チェック
        $dupliate_store_names = $wpdb->get_col("SELECT name, COUNT(*) as count FROM {$wpdb->prefix}docosl_stores GROUP BY name HAVING count > 1", 0);
        if ($wpdb->last_error !== "") {
            // DBエラー
            $result['errors'][] = '店舗名の重複チェックでDBエラーが発生しました。';
            $result['success'] = false;
            error_log('failed check duplicate store names: [error=' . $wpdb->last_error . ']');
        }

        if (count($dupliate_store_names) > 0) {
            // 店舗名重複してるのでエラー
            $result['errors'][] = '店舗名が重複しているため、更新処理を中断しました。重複している店舗名: ' . implode(", ", $dupliate_store_names);
            $result['success'] = false;
        }

        // 店舗画像ファイル名重複チェック
        $dupliate_img_filenames = $wpdb->get_col("SELECT img_filename, COUNT(*) as count FROM {$wpdb->prefix}docosl_stores WHERE img_filename IS NOT NULL GROUP BY img_filename HAVING count > 1", 0);
        if ($wpdb->last_error !== "") {
            // DBエラー
            $result['errors'][] = '店舗画像ファイル名の重複チェックでDBエラーが発生しました。';
            error_log('failed check duplicate store names: [error=' . $wpdb->last_error . ']');
            $result['success'] = false;
        }

        if (count($dupliate_img_filenames) > 0) {
            // 店舗画像ファイル名重複してるのでエラー
            $result['errors'][] = '店舗画像ファイル名が重複しているため更新処理を中断しました。重複している画像ファイル名: ' . implode(", ", $dupliate_img_filenames);
            $result['success'] = false;
        }

        // 画像ファイルの存在チェック
        $db_img_filenames = $wpdb->get_col("SELECT img_filename FROM {$wpdb->prefix}docosl_stores WHERE img_filename IS NOT NULL GROUP BY img_filename", 0);
        if ($wpdb->last_error !== "") {
            // DBエラー
            $result['errors'][] = '店舗画像ファイルの存在チェックでDBエラーが発生しました。';
            error_log('failed check duplicate store names: [error=' . $wpdb->last_error . ']');
            $result['success'] = false;
        }
        foreach($db_img_filenames as $filename) {
            if (!isset($exists_images[$filename])) {
                // DBにある画像ファイルがzipにない場合はエラー
                $result['errors'][] = '店舗画像ファイルが見つからないため、更新処理を中断しました。画像ファイル名: ' . $filename;
                $result['success'] = false;
            }
        }

        if (!$result['success']) {
            // エラーがあった場合はロールバックして終了
            $wpdb->query("ROLLBACK");
            $result['success'] = false;
            $result['errors'][] = "更新処理に失敗しました。";
            return $result;
        }

        if (!$dry_run) {
            if ($wpdb->query("COMMIT") === false) {
                throw new \Exception("トランザクションのコミットに失敗しました。");
            }
        }

        return $result;
    }

    /**
     * 既存のレコードと更新内容の差分を取る
     * 
     * @param \stdClass $src 既存のレコードの配列
     * @param array $data 更新内容の配列
     * @return array<string, string> 更新のあるレコードの配列とバリデーションエラーの配列
     */
    public static function diff($src, $data) {
        $diff = array();

        // 差分取り出し
        $diff = array();
        foreach ($data as $k => $v) {
            // CSVとDBレコードでは型が違うので != で比較
            if ($src->{$k} != $v) {
                $diff[$k] = $v;
            }
        }

        // バリデーションは本体に写す
        return $diff;
    }

    /**
     * CSVでのUPDATE処理用のバリデーション
     * 
     * @param array<string, string> $data 更新するデータ
     * @return array<string, string> バリデーションエラーの配列。エラーがない場合は空の配列。
     */
    public static function get_validator_for_update($data) {
        $errors = array();
        $columns = self::COLUMNS;

        foreach ($data as $name => $value) {
            if(!isset($columns[$name]['validation'])) {
                continue;
            }

            $ok = $columns[$name]['validation']($value);
            if ($ok !== true) {
                $errors[$name] = $ok;
            }
        }
        return $errors;
    }

    /**
     * CSVでのINSERT処理用のバリデーション
     * 
     * @param array<string, string> $data 更新するデータ
     * @return array<string, string> バリデーションエラーの配列。エラーがない場合は空の配列。
     */
    public static function get_validator_for_insert($data) {
        $errors = array();

        foreach (self::COLUMNS as $column => $column_info) {
            if (isset($column_info['validation'])) {
                $val = isset($data[$column]) ? $data[$column] : null;
                $ok = $column_info['validation']($val);
                if ($ok !== true) {
                    $errors[$column] = $ok;
                }
            }
        }
        return $errors;
    }

    /**
     * 店舗テーブルを更新する。 $data['updated_at'] がない場合は現在時刻を入れる。
     * 
     * @param int   $id 更新対象の店舗ID
     * @param array<string, string> $data 更新内容
     * @return int|false 成功時は更新した行数、失敗時はfalse
     */
    public static function update_by_id($id, $data) {
        global $wpdb;

        if (!isset($data['updated_at'])) {
            $data['updated_at'] = current_time('mysql');
        }
        return $wpdb->update($wpdb->prefix . 'docosl_stores', $data, array('id' => $id));
    }

    /**
     * 必須のカラムを返す
     * @return array<int, string> 必須のカラムの配列
     */
    public static function get_required_columns() {
        $required_columns = array_filter(self::COLUMNS, function ($v) {
            return $v['required'];
        });
        return array_keys($required_columns);
    }

    /**
     * 登録時に値をフィルタするメソッド
     * 今の所 lat, lng を丸める処理のみ
     * 
     * @param array<string, string> $data フィルタ前のデータ
     * @return array<string, string> フィルタ後のデータ
     */
    public static function record_filter($data) {
        if (!empty($data['lat']) && Validator::lat($data['lat']) === true) {
            $data['lat'] = (string)round($data['lat'], 8);
        }
        if (!empty($data['lng']) && Validator::lng($data['lng']) === true) {
            $data['lng'] = (string)round($data['lng'], 7);
        }
        return $data;
    }
}
