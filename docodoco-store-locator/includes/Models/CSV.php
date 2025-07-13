<?php

namespace DocodocoStoreLocator\Models;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * CSV ファイルを読み込むクラス
 *
 * CSV の列は順序がバラバラでも良い。
 * UPDATE の際は id が必須。
 * INSERT の際は name, lat, lng, publish_status のみ必須。
 *
 * @property array<int, string> $fields 列のIDをキー、フィールド名を値にもつ連想配列
 * @property resource $handler CSVのファイルハンドラ
 * @property array $ALLOWED_COLUMNS CSV ファイルで読み込むカラム。これ以外は無視される
 *
 * @method __construct($resource, $fields) CSV インスタンスを作成する
 * @method create_from_handler($resource) CSV インスタンスを作成する
 */
class CSV {

    /** @var array<int, string> 列のIDをキー、フィールド名を値にもつ連想配列 */
    public $fields = array();

    /** @var resource CSVのファイルハンドラ */
    public $handler = null;

    /** @var array CSV ファイルで読み込むカラム。これ以外は無視される */
    const ALLOWED_COLUMNS = array(
        'sort_order',
        'id',
        'name',
        'postal_code',
        'address',
        'prefecture',         // ← 追加
        'municipality_name',
        'tel',
        'fax',
        'url',
        'email',
        'open_hours',
        'regular_holiday',
        'parking',
        'lat',
        'lng',
        'img_filename',
        'remarks',
        'admin_remarks',
        'publish_status',
        'department',
        'contact',
        'age',
        'period',
        'document',
        'application',
        'belongings',
        'cost',
        'category',
        'medical_service_url',  // ← 追加
        'female_doctor',        // ← 追加
        'sunday_hours',         // ← 追加
        'online_booking', 
    );

    /**
     * CSV インスタンスを作成する
     * @param resource $resource CSVのファイルハンドラ（ストリームでも良い）
     * @param array<int, string> $fields 列のIDをキー、フィールド名を値にもつ連想配列
     */
    public function __construct($resource, $fields) {
        $this->handler = $resource;
        $this->fields = $fields;
        return $this;
    }

    /**
     * stream もしくはファイルハンドラから CSV インスタンスを作成する。
     *
     * @param resource $resource CSVのファイルハンドラ（ストリームでも良い）
     * @return DocodocoStoreLocator\Models\CSV
     * @throws \Exception
     */
    public static function create_from_handler($resource) {
        $columns = fgetcsv($resource);
        if ($columns === false || $columns === null) {
            throw new \Exception("CSV のカラム名が取得できませんでした");
        }
        // BOM がついていたらBOMだけ消す
        if (strncmp($columns[0], "\xEF\xBB\xBF", 3) === 0) {
            $columns[0] = substr($columns[0], 3);
        }

        $column_name_ids = array_flip($columns);

        // CSV の行から規定のフィールドのみを抜き出す（関係ないフィールドを無視するための処理）
        $field_ids = array();
        foreach(self::ALLOWED_COLUMNS as $field_name) {
            if (isset($column_name_ids[$field_name])) {
                $field_ids[$field_name] = $column_name_ids[$field_name];
            }
        }
        $fields = array_flip($field_ids);

        $csv = new CSV($resource, $fields);

        // 必須のカラムが存在するかチェック
        $required_columns = Store::get_required_columns();

        $missing_columns_list = array();
        foreach($required_columns as $required_column_name) {
            if (!isset($field_ids[$required_column_name])) {
                $missing_columns_list[] = $required_column_name;
            }
        }
        if (count($missing_columns_list) > 0) {
            throw new \Exception("CSV ファイルに必須の列 " . esc_html(implode(", ", $missing_columns_list)) ." が存在しません。");
        }

        return $csv;
    }

    /**
     * CSV のファイルハンドラ/ストリームから1行読み込み、フィールド名をキーとする連想配列を返す。
     *
     * @return array|false|null 1行分のデータ。ファイルの終端に達した場合は false を返す。nullは空行の場合。
     */
    public function read() {
        if ($this->handler === null) {
            throw new \Exception("CSV のファイルハンドラがありません");
        }
        $row = fgetcsv($this->handler);
        if ($row === false || $row === null) {
            return $row;
        }

        $result = array();
        foreach($row as $k => $v) {
            if (isset($this->fields[$k])) {
                $column_name = $this->fields[$k];
                $trimmed_v = \DocodocoStoreLocator\Helper::trim($v);
                $result[$column_name] = $trimmed_v === "" ? null : $trimmed_v;
            }
        }

        if(count($result) === 0) {
            return null;
        }

        return $result;
    }

}
