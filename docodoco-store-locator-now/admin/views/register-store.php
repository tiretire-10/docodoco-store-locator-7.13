<?php
if ( ! defined( 'ABSPATH' ) ) exit;

use DocodocoStoreLocator\Helper;
?>
<style type="text/css">
    .docosl-bootstrap .wp-heading-inline {
        font-size: 23px !important;
    }

    .docosl-bootstrap .form-label-big {
        font-size: 1.3em;
    }

    .docosl-bootstrap #storeImagePreviewBox {
        width: auto;
    }

    .docosl-bootstrap #storeImagePreview {
        max-width: 100%;
        max-height: 300px;
    }

    .docosl-bootstrap .store-image-dummy {
        height: 300px;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #c3c4c7;
    }

    .docosl-bootstrap .form-error {
        color: #ff0000;
    }

    .docosl-bootstrap .box-with-border {
        border: 1px solid #c3c4c7;
        background-color: #ffffff;
    }

    .docosl-bootstrap .box-header {
        border-bottom: 1px solid #c3c4c7;
        font-size: 1.3em;
        font-weight: bold;
    }

    .docosl-bootstrap .radio-fix {
        height: 16px !important;
    }

    .docosl-bootstrap .form-control::placeholder {
        color: #c3c4c7 !important;
    }

    .docosl-bootstrap .delete-store-image {
        background: #000;
        color: #fff;
        opacity: 0.5;
        cursor: pointer;
    }
</style>
<div class="wrap docosl-bootstrap">
    <?php foreach ($view_errors as $k => $error) : ?>
        <div class="alert alert-<?php echo esc_attr($error['level']); ?>" role="alert">
            <?php echo esc_html($error['message']); ?>
        </div>
    <?php endforeach ?>
    <h1 class="wp-heading-inline"><?php if ($view_edit_mode) : ?>店舗編集<?php else : ?>店舗登録<?php endif ?></h1>
    <form method="post" enctype="multipart/form-data">
        <?php wp_nonce_field('docosl-register-store'); ?>
        <?php if (Helper::arr_get($data, 'id')) : ?>
            <input type="hidden" name="storeId" value="<?php echo esc_attr($data['id']) ?>">
        <?php endif ?>
        <div class="row g-3">
            <div class="col-8">
                <div class="mb-3">
                    <label for="inputName" class="form-label form-label-big">店舗名 <span class="badge bg-danger">必須</span></label>
                    <input type="text" class="form-control" id="inputName" name="name" placeholder="店舗・支店名" value="<?php echo esc_attr(Helper::arr_get($data, 'name')) ?>" required>
                    <div class="form-error"><?php echo esc_html(Helper::arr_get($view_form_errors, 'name')) ?></div>
                </div>
                <div class="mb-3">
                    <label for="inputPostalCode" class="form-label form-label">郵便番号</label>
                    <input type="text" class="form-control" id="inputPostalCode" name="postal_code" placeholder="000-0000" value="<?php echo esc_attr(Helper::arr_get($data, 'postal_code')) ?>">
                    <div class="form-error"><?php echo esc_html(Helper::arr_get($view_form_errors, 'postal_code')) ?></div>
                </div>
                <div class="mb-3">
                    <label for="inputAddress" class="form-label">住所</label>
                    <input type="text" class="form-control" id="inputAddress" name="address" placeholder="静岡県三島市 xxx-x" value="<?php echo esc_attr(Helper::arr_get($data, 'address')) ?>">
                    <div class="form-error"><?php echo esc_html(Helper::arr_get($view_form_errors, 'address')) ?></div>
                </div>
                <div class="mb-3">
                    <label for="inputTel" class="form-label">電話番号</label>
                    <input type="tel" class="form-control" id="inputTel" name="tel" placeholder="055-xxxx-xxxx" value="<?php echo esc_attr(Helper::arr_get($data, 'tel')) ?>">
                    <div class="form-error"><?php echo esc_html(Helper::arr_get($view_form_errors, 'tel')) ?></div>
                </div>
                <div class="mb-3">
                    <label for="inputFax" class="form-label">FAX番号</label>
                    <input type="tel" class="form-control" id="inputFax" name="fax" placeholder="055-xxxx-xxxx" value="<?php echo esc_attr(Helper::arr_get($data, 'fax')) ?>">
                    <div class="form-error"><?php echo esc_html(Helper::arr_get($view_form_errors, 'fax')) ?></div>
                </div>
                <div class="mb-3">
                    <label for="inputURL" class="form-label">URL</label>
                    <input type="url" class="form-control" id="inputURL" name="url" placeholder="https://example.jp/" value="<?php echo esc_attr(Helper::arr_get($data, 'url')) ?>" pattern="https?://.+">
                    <div class="form-error"><?php echo esc_html(Helper::arr_get($view_form_errors, 'url')) ?></div>
                </div>
                <div class="mb-3">
                    <label for="inputEmail" class="form-label">メールアドレス</label>
                    <input type="email" class="form-control" id="inputEmail" name="email" placeholder="info@example.jp" value="<?php echo esc_attr(Helper::arr_get($data, 'email')) ?>">
                    <div class="form-error"><?php echo esc_html(Helper::arr_get($view_form_errors, 'email')) ?></div>
                </div>
                <div class="mb-3">
                    <label for="inputOpenHours" class="form-label">営業時間</label>
                    <input type="text" class="form-control" id="inputOpenHours" name="open_hours" placeholder="09:00 ~ 18:00" value="<?php echo esc_attr(Helper::arr_get($data, 'open_hours')) ?>">
                    <div class="form-error"><?php echo esc_html(Helper::arr_get($view_form_errors, 'open_hours')) ?></div>
                </div>
                <div class="mb-3">
                    <label for="inputRegularHoliday" class="form-label">定休日</label>
                    <input type="text" class="form-control" id="inputRegularHoliday" name="regular_holiday" placeholder="土日祝、第2水曜日" value="<?php echo esc_attr(Helper::arr_get($data, 'regular_holiday')) ?>">
                    <div class="form-error"><?php echo esc_html(Helper::arr_get($view_form_errors, 'regular_holiday')) ?></div>
                </div>
                <div class="mb-3">
                    <label for="inputParking" class="form-label">駐車場</label>
                    <input type="text" class="form-control" id="inputParking" name="parking" placeholder="大型車1台、普通車10台" value="<?php echo esc_attr(Helper::arr_get($data, 'parking')) ?>">
                    <div class="form-error"><?php echo esc_html(Helper::arr_get($view_form_errors, 'parking')) ?></div>
                </div>
                <div class="mb-3 row">
                    <div class="col">
                        <label for="inputLat" class="form-label">緯度 </label>
                        <input type="text" class="form-control" id="inputLat" name="lat" placeholder="35.63667739" value="<?php echo esc_attr(Helper::arr_get($data, 'lat')) ?>" pattern="-?[0-9]{1,3}\.[0-9]{1,}">
                        <div class="form-error"><?php echo esc_html(Helper::arr_get($view_form_errors, 'lat')) ?></div>
                    </div>
                    <div class="col">
                        <label for="inputLng" class="form-label">経度 </label>
                        <input type="text" class="form-control" id="inputLng" name="lng" placeholder="139.7632321" value="<?php echo esc_attr(Helper::arr_get($data, 'lng')) ?>" pattern="-?[0-9]{1,3}\.[0-9]{1,}">
                        <div class="form-error"><?php echo esc_html(Helper::arr_get($view_form_errors, 'lng')) ?></div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="inputRemarks" class="form-label">備考</label>
                    <textarea class="form-control" id="inputRemarks" name="remarks" placeholder="向かいの神社が目印です。"><?php echo esc_html(Helper::arr_get($data, 'remarks')) ?></textarea>
                    <div class="form-error"><?php echo esc_html(Helper::arr_get($view_form_errors, 'remarks')) ?></div>
                </div>
                <div class="mb-3">
                    <label for="inputAdminRemarks" class="form-label">管理画面備考</label>
                    <textarea class="form-control" id="inputAdminRemarks" name="admin_remarks" placeholder="管理画面でのみ確認可能なメモ欄"><?php echo esc_html(Helper::arr_get($data, 'admin_remarks')) ?></textarea>
                    <div class="form-error"><?php echo esc_html(Helper::arr_get($view_form_errors, 'admin_remarks')) ?></div>
                </div>
            </div>
                <h3 class="mt-4">健診関連情報</h3>

                <div class="mb-3">
                    <label for="inputDepartment" class="form-label">担当部署</label>
                    <input type="text" class="form-control" id="inputDepartment" name="department"  placeholder="健康管理センター"  value="<?php echo esc_attr(Helper::arr_get($data, 'department')) ?>">
                    <div class="form-error"><?php echo esc_html(Helper::arr_get($view_form_errors, 'department')) ?></div>
                </div>
                <div class="mb-3">
                    <label for="inputContact" class="form-label">連絡</label>
                    <input type="text" class="form-control" id="inputContact" name="contact"  placeholder="要予約（電話またはWeb）"  value="<?php echo esc_attr(Helper::arr_get($data, 'contact')) ?>">
                    <div class="form-error"><?php echo esc_html(Helper::arr_get($view_form_errors, 'contact')) ?></div>
                </div>
                <div class="mb-3">
                    <label for="inputAge" class="form-label">受診年齢</label>
                    <input type="text" class="form-control" id="inputAge" name="age"  placeholder="40歳以上"  value="<?php echo esc_attr(Helper::arr_get($data, 'age')) ?>">
                    <div class="form-error"><?php echo esc_html(Helper::arr_get($view_form_errors, 'age')) ?></div>
                </div>
                <div class="mb-3">
                    <label for="inputPeriod" class="form-label">検診期間</label>
                    <input type="text" class="form-control" id="inputPeriod" name="period"  placeholder="4月1日～3月31日"  value="<?php echo esc_attr(Helper::arr_get($data, 'period')) ?>">
                    <div class="form-error"><?php echo esc_html(Helper::arr_get($view_form_errors, 'period')) ?></div>
                </div>
                <div class="mb-3">
                    <label for="inputDocument" class="form-label">問診票・受診券の有無</label>
                    <input type="text" class="form-control" id="inputDocument" name="document"  placeholder="受診券必要"  value="<?php echo esc_attr(Helper::arr_get($data, 'document')) ?>">
                    <div class="form-error"><?php echo esc_html(Helper::arr_get($view_form_errors, 'document')) ?></div>
                </div>
                <div class="mb-3">
                    <label for="inputApplication" class="form-label">申し込み</label>
                    <textarea class="form-control" id="inputApplication" name="application"  placeholder="電話予約：平日9:00～17:00&#10;Web予約：24時間受付"  rows="2"><?php echo esc_html(Helper::arr_get($data, 'application')) ?></textarea>
                    <div class="form-error"><?php echo esc_html(Helper::arr_get($view_form_errors, 'application')) ?></div>
                </div>
                <div class="mb-3">
                    <label for="inputBelongings" class="form-label">持ち物</label>
                    <textarea class="form-control" id="inputBelongings" name="belongings"  placeholder="保険証、受診券、問診票"  rows="2"><?php echo esc_html(Helper::arr_get($data, 'belongings')) ?></textarea>
                    <div class="form-error"><?php echo esc_html(Helper::arr_get($view_form_errors, 'belongings')) ?></div>
                </div>
                <div class="mb-3">
                    <label for="inputCost" class="form-label">費用</label>
                    <input type="text" class="form-control" id="inputCost" name="cost"  placeholder="無料（市の助成あり）"  value="<?php echo esc_attr(Helper::arr_get($data, 'cost')) ?>">
                    <div class="form-error"><?php echo esc_html(Helper::arr_get($view_form_errors, 'cost')) ?></div>
                </div>
            <div class="col-4">
                <div class="mb-3 box-with-border">
                    <div class="p-2 box-header">公開設定</div>
                    <div class="p-2">
                        <div class="form-check">
                            <input type="radio" class="form-check-input radio-fix" id="inputPublishStatus1" name="publish_status" value="1" <?php echo empty($data['publish_status']) || $data['publish_status'] === 1 ? 'checked' : '' ?>>
                            <label class="form-check-label" for="inputPublishStatus1">非公開</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" class="form-check-input radio-fix" id="inputPublishStatus2" name="publish_status" value="2" <?php echo Helper::arr_get($data, 'publish_status') === 2 ? 'checked' : '' ?>>
                            <label class="form-check-label" for="inputPublishStatus2">公開</label>
                        </div>
                        <div class="form-error"><?php echo esc_html(Helper::arr_get($view_form_errors, 'publish_status')) ?></div>
                    </div>
                    <div class="p-2">
                        <div class="">
                            <label for="inputSortOrder" class="form-label">並び順</label>
                            <input type="text" class="form-control" id="inputSortOrder" name="sort_order" placeholder="1" value="<?php echo esc_attr(Helper::arr_get($data, 'sort_order')) ?>" pattern="[0-9]*">
                            <div class="form-error"><?php echo esc_html(Helper::arr_get($view_form_errors, 'sort_order')) ?></div>
                        </div>
                    </div>
                </div>
                <div class="mb-3 box-with-border">
                    <div class="p-2 box-header">
                        <label for="inputStoreImage">店舗画像</label>
                    </div>
                    <div class="p-2">
                        <input type="file" class="form-control store-image" id="inputStoreImage" name="store_image" placeholder="店舗画像">
                        <div class="form-error"><?php echo esc_html(Helper::arr_get($view_form_errors, 'store_image')) ?></div>
                    </div>
                    <input hidden id="inputDeleteStoreImage" type="text" name="delete_store_image" value="0">
                    <div class="text-center">
                        <div id="storeImagePreviewBox" class="p-1">
                            <?php if ($view_edit_mode && !empty($data['img_filename'])) : ?>
                                <div id="uploadedStoreImagePreview" class="position-relative">
                                    <div class="position-absolute top-50 start-50 translate-middle">
                                        <span id="storeImageDeleteBtn" class="rounded delete-store-image p-2">現在の店舗画像を削除</span>
                                    </div>
                                    <div class="">
                                        <img src="<?php echo esc_url(DOCOSL_UPLOAD_URL . 'store-images/' . $data['img_filename'] . '?date=' . crc32($data['updated_at'])) ?>" id="storeImagePreview">
                                    </div>
                                </div>
                            <?php else : ?>
                                <div class="store-image-dummy p-3">店舗一覧・詳細画面に表示される店舗画像をアップロードしてください</div>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
                <div class="mb-3 p-2 text-end">
                        <input type="submit" class="btn btn-primary" value="登録">
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    jQuery(document).ready(function($) {
        $(document).on("change", ".store-image", function() {
            let fh = new FileReader();
            fh.readAsDataURL(this.files[0]);
            fh.onload = (function(e) {
                $("#storeImagePreviewBox").html('<img src="' + e.target.result + '" id="storeImagePreview">');
            });
        })

        $(document).on("click", "#storeImageDeleteBtn", function() {
            $("#inputDeleteStoreImage").val(1);
            $("#storeImagePreviewBox").html('<div class="store-image-dummy p-3">店舗一覧・詳細画面に表示される店舗画像をアップロードしてください</div>');
        });
    });
</script>
