<?php

if ( ! defined( 'ABSPATH' ) ) exit;

use \DocodocoStoreLocator\Helper;
use \DocodocoStoreLocator\Models\Store;
?>
<style type="text/css">
    .docosl-bootstrap h1 {
        font-size: 23px !important;
    }

    .docosl-bootstrap h2 {
        font-size: 18px !important;
    }

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

    .docosl-bootstrap .box-header-2 {
        font-weight: bold;
    }

    .docosl-bootstrap .radio-fix {
        height: 16px !important;
    }

    .docosl-bootstrap .checkbox-fix:before {
        content: none !important;
    }

    .docosl-bootstrap .radio-fix:before {
        content: none !important;
    }

    .docosl-bootstrap .checkbox-fix {
        height: 16px !important;
    }

    .docosl-bootstrap .form-control::placeholder {
        color: #c3c4c7 !important;
    }
</style>
<div class="wrap docosl-bootstrap">
    <?php foreach ($view_alerts as $k => $error) : ?>
        <div class="alert <?php echo esc_attr('alert-' . $error['level']); ?>" role="alert">
            <?php echo esc_html($error['message']); ?>
        </div>
    <?php endforeach ?>
    <h1 class="wp-heading-inline">表示設定</h1>
    <h2>全体設定</h2>
    <form class="form-group" id="display-setting" method="POST">
        <?php wp_nonce_field('docosl-display-setting'); ?>
        <div class="mb-3 box-with-border">
            <div class="p-2 box-header-2">どこどこJPを使った店舗一覧の表示切替</div>
            <div class="p-2">
                どこどこJPの位置情報を使って、アクセスしたユーザーの現在地から近い順に店舗を表示することができます。
            </div>
            <div class="p-2">
                <div class="form-check">
                    <input class="form-check-input radio-fix" type="radio" name="docodocojp_enabled" id="inputDocodocoJPEnabled1" value="1" <?php echo $global_settings->docodocojp_enabled == '1' ? 'checked' : '' ?>>
                    <label class="form-check-label" for="inputDocodocoJPEnabled1">使う</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input radio-fix" type="radio" name="docodocojp_enabled" id="inputDocodocoJPEnabled0" value="0" <?php echo $global_settings->docodocojp_enabled == '0' ? 'checked' : '' ?>>
                    <label class="form-check-label" for="inputDocodocoJPEnabled0">使わない</label>
                </div>
                <div class="form-error"><?php echo esc_html(Helper::arr_get($view_form_errors, 'docodocojp_enabled')) ?></div>
            </div>
            <div class="p-2">
                <label for="inputDocodocoJPAPIKey" class="form-label">
                    どこどこJP APIキー
                    <span id="DocodocoJPAPIKeyRequiredBadge" class="badge bg-danger" style="<?php echo $global_settings->docodocojp_enabled == '0' ? 'display: none;' : '' ?>">必須</span>
                </label>
                <input type="text" class="form-control" id="inputDocodocoJPAPIKey" name="docodocojp_apikey" placeholder="xxxx" value="<?php echo esc_attr($global_settings->docodocojp_apikey) ?>" <?php echo $global_settings->docodocojp_enabled == '1' ? 'required' : '' ?>>
                <div class="form-error"><?php echo esc_html(Helper::arr_get($view_form_errors, 'docodocojp_apikey')) ?></div>
            </div>
            <div class="p-2">
                ※ どこどこJPを使う場合はAPIキーの入力が必須になります。<br>
                ※ どこどこJPの詳細は <a href="https://www.docodoco.jp/" target="_blank">こちら</a> をご覧ください。
            </div>
        </div>
        <div class="mb-3 box-with-border">
            <div class="p-2 box-header-2">
                Google Maps APIキー
                <span id="GoogleMapsAPIKeysRequiredBadge" class="badge bg-danger" style="<?php echo ($template_types[1]->settings->map_display_enabled == '1' || $template_types[2]->settings->map_display_enabled == '1') ? '' : 'display: none;' ?>">必須</span>
            </div>
            <div class="p-2">
                地図を表示する際に使用するGoogle Maps APIキーを入力してください。
            </div>
            <div class="p-2">
                <input type="text" class="form-control" id="inputGoogleMapsAPIKey" name="google_maps_apikey" placeholder="xxxx" value="<?php echo esc_attr($global_settings->google_maps_apikey) ?>" <?php echo ($template_types[1]->settings->map_display_enabled == '1' || $template_types[2]->settings->map_display_enabled) ? 'required' : '' ?>>
                <div class="form-error"><?php echo esc_html(Helper::arr_get($view_form_errors, 'google_maps_apikey')) ?></div>
            </div>
        </div>
        <div class="mb-3 box-with-border">
            <div class="p-2 box-header-2">テンプレート設定</div>
            <div class="row">
                <div class="col">
                    <div class="p-2 box-header-2">使用するテンプレート</div>
                    <div class="p-2">
                        実際にWebサイトに表示される見た目のデザインをテンプレートから選択できます。一覧ページと詳細ページのテンプレートは共通です。
                    </div>
                    <div class="p-2">
                        <select id="inputDesignTemplate" class="form-select" aria-label="" name="design_template_id">
                            <?php foreach ($available_design_templates as $available_design_template_id => $available_design_template) : ?>
                                <option
                                    data-detail-preview-img="<?php echo esc_url(DOCOSL_URL_PATH . 'admin/images/design_template_preview/detail-' . $available_design_template->preview_img_filename) ?>"
                                    data-list-preview-img="<?php echo esc_url(DOCOSL_URL_PATH . 'admin/images/design_template_preview/list-' . $available_design_template->preview_img_filename) ?>"
                                    value="<?php echo esc_attr($available_design_template->id); ?>" <?php echo $global_settings->design_template_id == $available_design_template->id ? 'selected' : '' ?>
                                >
                                    <?php echo esc_html($available_design_template->template_name) ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                        <div class="form-error"><?php echo esc_html(Helper::arr_get($view_form_errors, 'design_template_id')) ?></div>
                    </div>
                    <div class="p-2 box-header-2">詳細ページの有効化</div>
                    <div class="p-2">
                        詳細ページを利用する場合は、一覧ページの店舗名をクリックすると詳細ページに遷移します。<br>
                        詳細ページを利用しない場合は、店舗情報のURLを新しいタブで開きます。<br>
                    </div>
                    <div class="p-2">
                        ※ 詳細ページを利用する場合は詳細ページのショートコードを必ず設置してください。
                    </div>
                    <div class="p-2">
                        <div class="form-check">
                            <input class="form-check-input radio-fix" type="radio" name="store_detail_page_enabled" id="inputStoreDetailPageEnabled1" value="1" <?php echo $global_settings->store_detail_page_enabled == '1' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="inputStoreDetailPageEnabled1">使う</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input radio-fix" type="radio" name="store_detail_page_enabled" id="inputStoreDetailPageEnabled0" value="0" <?php echo $global_settings->store_detail_page_enabled == '0' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="inputStoreDetailPageEnabled0">使わない</label>
                        </div>
                        <div class="form-error"><?php echo esc_html(Helper::arr_get($view_form_errors, 'store_detail_page_enabled')) ?></div>
                    </div>
                    <div class="p-2 box-header-2">一覧・詳細ページのURL</div>
                    <div class="p-2">
                        ショートコードを設置したページのURLを / から入力してください。<br>
                        詳細ページを使用しない場合は入力不要です。
                    </div>
                    <div class="p-2">
                        <label for="inputStoreListURL" class="form-label">一覧ページURL <span class="badge bg-danger url-required-badge" style="<?php echo $global_settings->store_detail_page_enabled == '0' ? 'display:none;' : '' ?>">必須</span></label>
                        <input type="text" class="form-control" id="inputStoreListURL" name="store_list_url" placeholder="/?page_id=21" value="<?php echo esc_attr($template_types[1]->settings->url); ?>" <?php echo $global_settings->store_detail_page_enabled == '0' ? 'disabled' : 'required' ?>>
                        <div class="form-error"><?php echo esc_html(Helper::arr_get($view_form_errors, 'store_list_url')) ?></div>
                    </div>
                    <div class="p-2">
                        <label for="inputStoreDetailURL" class="form-label">詳細ページURL <span class="badge bg-danger url-required-badge" style="<?php echo $global_settings->store_detail_page_enabled == '0' ? 'display:none;' : '' ?>">必須</span></label>
                        <input type="text" class="form-control" id="inputStoreDetailURL" name="store_detail_url" placeholder="/?page_id=22" value="<?php echo esc_attr($template_types[2]->settings->url); ?>" <?php echo $global_settings->store_detail_page_enabled == '0' ? 'disabled' : 'required' ?>>
                        <div class="form-error"><?php echo esc_html(Helper::arr_get($view_form_errors, 'store_detail_url')) ?></div>
                    </div>
                </div>
                <div class="col">
                    <div class="p-2 box-header-2">テンプレートのプレビュー: 一覧ページ</div>
                    <div class="p-2">
                        <img id="designTemplateListPreviewImg" src="<?php echo esc_url(DOCOSL_URL_PATH . 'admin/images/design_template_preview/list-' . $global_settings->preview_img_filename) ?>" class="img-fluid img-thumbnail">
                    </div>
                    <div class="p-2 box-header-2">テンプレートのプレビュー: 詳細ページ</div>
                    <div class="p-2">
                        <img id="designTemplateDetailPreviewImg" src="<?php echo esc_url(DOCOSL_URL_PATH . 'admin/images/design_template_preview/detail-' . $global_settings->preview_img_filename) ?>" class="img-fluid img-thumbnail">
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-3 row">
            <?php foreach ($template_types as $template_type_id => $template_type) : ?>
                <div class="col">
                    <h2><?php echo esc_html($template_type->name) ?></h2>
                    <div class="mb-3 box-with-border">
                        <div class="p-2 box-header-2">地図設定</div>
                        <div class="p-2">
                            地図設定を有効にすることで、店舗の場所をマッピングさせた地図を表示することができます。
                        </div>
                        <div class="p-2">
                            <div class="form-check">
                                <input type="radio" class="form-check-input radio-fix" id="<?php echo esc_attr('inputType' . $template_type_id); ?>MapDisplayEnabled1" name="<?php echo esc_attr('type' . $template_type_id); ?>_map_display_enabled" value="1" <?php echo $template_type->settings->map_display_enabled == '1' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="<?php echo esc_attr('inputType' . $template_type_id); ?>MapDisplayEnabled1">使う</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" class="form-check-input radio-fix" id="<?php echo esc_attr('inputType' . $template_type_id); ?>MapDisplayEnabled0" name="<?php echo esc_attr('type' . $template_type_id); ?>_map_display_enabled" value="0" <?php echo $template_type->settings->map_display_enabled == '0' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="<?php echo esc_attr('inputType' . $template_type_id); ?>MapDisplayEnabled0">使わない</label>
                            </div>
                            <div class="form-error"><?php echo esc_html(Helper::arr_get($view_form_errors, "type{$template_type_id}_map_display_enabled")) ?></div>
                        </div>
                        <div class="p-2">
                            ※ 使うを選択した場合は Google Maps API キーの入力が必須になります。
                        </div>
                    </div>
                    <div class="mb-3 box-with-border">
                        <div class="p-2 box-header-2"><?php echo esc_html($template_type->name); ?>で表示する情報の選択</div>
                        <div class="p-2">
                            <?php foreach ($template_type->items as $item_id => $item) : ?>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input checkbox-fix" name="<?php echo esc_attr("item_is_display[{$item->id}]") ?>" value="<?php echo esc_attr($item->id) ?>" id="<?php echo esc_attr('inputType' . $template_type_id . $item->item_name); ?>" <?php echo $item->is_display ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="<?php echo esc_attr('inputType' . $template_type_id . $item->item_name); ?>"><?php echo esc_html(Store::LABELS[$item->item_name]) ?></label>
                                </div>
                            <?php endforeach ?>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
        <div class="mb-3">
            <button type="submit" class="btn btn-primary">保存</button>
        </div>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectDesignTemplate = document.getElementById('inputDesignTemplate');
        const designTemplateListPreviewImg = document.getElementById('designTemplateListPreviewImg');
        const designTemplateDetailPreviewImg = document.getElementById('designTemplateDetailPreviewImg');
        selectDesignTemplate.addEventListener('change', async function(e) {
            designTemplateListPreviewImg.src = selectDesignTemplate.selectedOptions[0].dataset.listPreviewImg;
            designTemplateDetailPreviewImg.src = selectDesignTemplate.selectedOptions[0].dataset.detailPreviewImg;
        });


        const inputDocodocoJPEnabled1 = document.getElementById('inputDocodocoJPEnabled1');
        const inputDocodocoJPEnabled0 = document.getElementById('inputDocodocoJPEnabled0');
        const DocodocoJPAPIKeyRequiredBadge = document.getElementById('DocodocoJPAPIKeyRequiredBadge');
        const inputDocodocoJPAPIKey = document.getElementById('inputDocodocoJPAPIKey');
        const toggleDocodocoJP = async function(e) {
            let disabled = false;
            if (e.target.value === '0') {
                disabled = true;
            }
            DocodocoJPAPIKeyRequiredBadge.style.display = disabled ? 'none' : '';
            inputDocodocoJPAPIKey.required = !disabled;
        };
        inputDocodocoJPEnabled1.addEventListener('change', toggleDocodocoJP);
        inputDocodocoJPEnabled0.addEventListener('change', toggleDocodocoJP);


        const inputStoreDetailPageEnabled1 = document.getElementById('inputStoreDetailPageEnabled1');
        const inputStoreDetailPageEnabled0 = document.getElementById('inputStoreDetailPageEnabled0');
        const urlRequiredBadges = Array.from(document.getElementsByClassName('url-required-badge'));
        const inputStoreListURL = document.getElementById('inputStoreListURL');
        const inputStoreDetailURL = document.getElementById('inputStoreDetailURL');
        const toggleStoreURL = async function(e) {
            let disabled = false;
            if (e.target.value === '0') {
                disabled = true;
            }
            inputStoreListURL.disabled = disabled;
            inputStoreListURL.required = !disabled;
            inputStoreDetailURL.disabled = disabled;
            inputStoreDetailURL.required = !disabled;

            urlRequiredBadges.forEach(function(e) {
                e.style.display = disabled ? 'none' : '';
            });
        };
        inputStoreDetailPageEnabled1.addEventListener('change', toggleStoreURL);
        inputStoreDetailPageEnabled0.addEventListener('change', toggleStoreURL);


        const mapDisplayRadioBtns = [
            document.getElementById('inputType1MapDisplayEnabled1'),
            document.getElementById('inputType1MapDisplayEnabled0'),
            document.getElementById('inputType2MapDisplayEnabled1'),
            document.getElementById('inputType2MapDisplayEnabled0'),
        ];
        const GoogleMapsAPIKeysRequiredBadge = document.getElementById('GoogleMapsAPIKeysRequiredBadge');
        const toggleMapDisplay = async function(e) {
            let disabled = false;
            if (e.target.value === '0') {
                disabled = true;
            }
            document.getElementById('inputGoogleMapsAPIKey').required = !disabled;
            GoogleMapsAPIKeysRequiredBadge.style.display = disabled ? 'none' : '';
        };
        mapDisplayRadioBtns.forEach(function(element) {
            element.addEventListener('change', toggleMapDisplay);
        });

    });
</script>
