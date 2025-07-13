<?php

namespace DocodocoStoreLocator\Models;

if ( ! defined( 'ABSPATH' ) ) exit;

class Archive {

    /** @var bool アーカイブがCSVファイル（店舗情報シート）を含むか */
    public $has_csv = false;

    /** @var bool アーカイブが店舗画像を含むか */
    public $has_store_images = false;

    /** @var string|null アーカイブ内でのCSVファイル名（パスを含む） */
    public $csv_filepath = null;

    /** @var DocodocoStoreLocator\Models\CSV */
    public $csv = null;

    /** @var array{ $filename: int } 店舗画像のファイル名をキー、アーカイブの index を値とする配列 */
    public $store_images = array();

    public const ARCHIVE_DIR = "docosl-stores/";
    public const STORE_IMAGES_DIR = "docosl-stores/store-images/";
    public const STORE_LIST_FILEPATH = "docosl-stores/store-list.csv";

    /** @var \ZipArchive|null \ZipArchive のインスタンス */
    public $zip_handler = null;
    
    public function __construct() {
    }

    /**
     * zip ファイルを読み込んでインスタンスを作成
     * 
     * @param $filepath string zip ファイルのパス
     * @return DocodocoStoreLocator\Models\Archive
     * @throws \Exception
     */
    public static function read_from_file($filepath) {
        $my = new self();

        $zip = new \ZipArchive();
        $my->zip_handler = $zip;

        try {
            if ($zip->open($filepath) !== true) {
                throw new \Exception("zip ファイルのオープンに失敗しました。");
            }
        } catch (\Throwable $e) {
            throw new \Exception("zip ファイルのオープンに失敗しました。", 1, $e);
        }

        // CSV
        if ($zip->locateName(self::STORE_LIST_FILEPATH) !== false) {
            $my->has_csv = true;
            $my->csv_filepath = self::STORE_LIST_FILEPATH;

            $csv_h = $zip->getStream($my->csv_filepath);
            if ($csv_h === false) {
                throw new \Exception("CSV ファイルを開けませんでした。");
            }

            $csv = CSV::create_from_handler($csv_h);
            $my->csv = $csv;
        } else {
            // CSV がないパターン
            //echo "CSV ファイルがありませんでした。";
        }

        // 画像ファイルの一覧作成
        if ($zip->locateName(self::STORE_IMAGES_DIR) !== false) {
            $my->has_store_images = true;

            $imagefile_prefix = self::STORE_IMAGES_DIR;
            $imagefile_prefix_len = strlen($imagefile_prefix);

            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);

                if (strncmp($filename, $imagefile_prefix, $imagefile_prefix_len) === 0 && $filename !== $imagefile_prefix) {
                    // 画像ファイルの場合はここに来る
                    $my->store_images[mb_substr($filename, $imagefile_prefix_len)] = $i;
                }
            }
        } else {
            // 画像ファイルがない場合に何かする必要あれば
            //echo "画像ディレクトリがありませんでした。";
        }

        // 店舗情報シートも画像ファイルもないzipアーカイブはエラー
        if ($my->has_csv === false && $my->has_store_images === false) {
            $zip->close();
            throw new \Exception(
                "zip ファイルには CSVファイル (" . esc_html(self::STORE_LIST_FILEPATH) . ") と店舗画像を入れるディレクトリ (" . esc_html(self::STORE_IMAGES_DIR) . ") のどちらも含まれていません。");
        }

        return $my;
    }

    /**
     * DBの店舗情報と uploads/ の店舗画像を使って zip アーカイブを作成する。
     * このメソッドは作成した zip ファイルパスを返す。
     * 
     * @param $filepath string zip ファイルのパス
     * @return string zip ファイルのパス
     */
    public static function get_archive_from_db() {
        // exports ディレクトリに一時ファイルをおくのでなければ作成しておく。
        // 通常はプラグインインストール時に作成されるためここで作成されることはない。
        if (!file_exists(DOCOSL_PLUGIN_PATH . 'exports')) {
            wp_mkdir_p(DOCOSL_PLUGIN_PATH . 'exports');
        }

        $csv_filepath = DOCOSL_PLUGIN_PATH . "exports/store-list.csv";

        if (file_exists($csv_filepath)) { // 処理が失敗したか何かでCSVファイルが残っている場合は削除する
            unlink($csv_filepath);
        }

        $fh = fopen($csv_filepath, "w");
        if ($fh === false) {
            throw new \Exception("一時保存用のCSVファイルを開けませんでした");
        }

        fwrite($fh, "\xEF\xBB\xBF"); // BOM を書き込む（Excel で開いたときに文字化けしないようにする

        $columns = [
            'sort_order',
            'id',
            'name',
            'postal_code',
            'address',
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
        ];
        fputcsv($fh, $columns);

        global $wpdb;
        $stores = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}docosl_stores ORDER BY sort_order, name ASC");

        foreach ($stores as $store) {
            $row = [
                $store->sort_order,
                $store->id,
                $store->name,
                $store->postal_code,
                $store->address,
                $store->tel,
                $store->fax,
                $store->url,
                $store->email,
                $store->open_hours,
                $store->regular_holiday,
                $store->parking,
                $store->lat,
                $store->lng,
                $store->img_filename,
                $store->remarks,
                $store->admin_remarks,
                $store->publish_status,
            ];
            fputcsv($fh, $row);
        }
        fclose($fh);

        $zip_filename = "docosl-stores.zip";
        $zip_filepath = DOCOSL_PLUGIN_PATH . 'exports/' . $zip_filename;

        if(file_exists($zip_filepath)) {
            unlink($zip_filepath);
        }

        $zip = new \ZipArchive();
        $zip->open($zip_filepath, \ZipArchive::CREATE);

        $zip->addEmptyDir(self::ARCHIVE_DIR);
        $zip->addEmptyDir(self::STORE_IMAGES_DIR);

        // CSVファイルを追加
        $zip->addFile($csv_filepath, self::STORE_LIST_FILEPATH);

        // 画像ファイルを追加
        $dh = opendir(DOCOSL_UPLOAD_DIR . 'store-images');
        while(true) {
            $entry = readdir($dh);
            if ($entry === false) {
                break;
            }
            if ($entry === "." || $entry === "..") {
                continue;
            }
            $zip->addFile(DOCOSL_UPLOAD_DIR . 'store-images/' . $entry, self::STORE_IMAGES_DIR . $entry);
        }
        closedir($dh);

        $zip->close();

        // ZipArchive::close() 後にファイルに書き出されるので、CSVファイル削除は ZipArchive::close() 後に実施
        unlink($csv_filepath);

        return $zip_filepath;
    }

    /**
     * 画像ファイルの md5 ハッシュを返す。ファイル名のみを指定する。
     * 
     * @param string $store_image_filename 画像ファイルのファイル名
     * @return string md5 ハッシュ値
     * @throws \Exception ファイルを開けなかった場合
     */
    public function get_md5_store_image($store_image_filename) {
        return $this->get_md5(self::STORE_IMAGES_DIR . $store_image_filename);
    }

    /**
     * アーカイブ内に存在するファイルの md5 ハッシュを返す。パスを含めたファイル名を指定する。
     * 
     * @param string $filepath パスを含めたファイル名
     * @return string md5 ハッシュ値
     * @throws \Exception ファイルを開けなかった場合
     */
    public function get_md5($filepath) {
        $fh = $this->zip_handler->getStream($filepath);
        if ($fh === false) {
            throw new \Exception("ファイルを開けませんでした。");
        }

        $hash = hash_init('md5');
        while (!feof($fh)) {
            $data = fread($fh, 64 * 1024);
            hash_update($hash, $data);
        }
        fclose($fh);

        return hash_final($hash);
    }

    /**
     * アーカイブ内のファイルを指定した場所に展開する。
     * ZipArchive::extractTo() とは違い、アーカイブ内のディレクトリ構造は保持せず、展開先のパスの直下にファイルを展開する。
     * @param string $dest_path 展開先のパス（ファイル名まで指定すること）
     * @param string $filepath アーカイブ内のファイルパス
     * @return bool 成功したら true
     */
    public function extract_to($dest_path, $filepath) {
        $fh = $this->zip_handler->getStream($filepath);
        if ($fh === false) {
            throw new \Exception("アーカイブ内の画像ファイルを開けませんでした。"
            . "エラーコード: " . esc_html($this->zip_handler->status)
            . " システムエラーコード: " . esc_html($this->zip_handler->statusSys)
            . "エラーメッセージ: " . esc_html($this->zip_handler->getStatusString()));
        }

        $dest_fh = fopen($dest_path, "w");
        if ($dest_fh === false) {
            fclose($fh);
            throw new \Exception("ファイルを開けませんでした。");
        }

        $truncate_r = ftruncate($dest_fh, 0);
        if ($truncate_r === false) {
            fclose($fh);
            fclose($dest_fh);
            throw new \Exception("ファイルのクリアに失敗しました。");
        }

        if(stream_copy_to_stream($fh, $dest_fh) === false) {
            fclose($fh);
            fclose($dest_fh);
            throw new \Exception("ファイルのコピーに失敗しました。");
        }

        fclose($fh);
        fclose($dest_fh);
        return true;
    }
}
