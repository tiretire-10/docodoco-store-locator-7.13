<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<!--========== テンプレート ==========-->
<article class="docodoco_store">
    <!--========== 詳細レイアウト03 ==========-->
    <div class="docodoco_store_detail03">
        <div class="docodoco_store_detail_info01">
            <h1 class="docodoco_store_detail_name01"><?php echo esc_html($store->name); ?></h1>
            
            <dl class="docodoco_detail_shop_info01">
                <?php foreach ($display_items as $item) : ?>
                    <?php if ($item->is_display == 1) : ?>
                        <?php
                            // $item->item_name に基づいて $storeの対応する項目を取得
                            $item_value = '';
                            switch ($item->item_name) {
                                case 'address':
                                    $postal_code = !empty($store->postal_code) ? '〒' . $store->postal_code : '';
                                    $address = !empty($store->address) ? ' ' . $store->address : '';
                                    $item_value = $postal_code . $address;
                                    break;
                                case 'tel':
                                    $item_value = $store->tel;
                                    break;
                                case 'fax':
                                    $item_value = $store->fax;
                                    break;
                                case 'url':
                                    $item_value = $store->url;
                                    break;
                                case 'email':
                                    $item_value = $store->email;
                                    break;
                                case 'open_hours':
                                    $item_value = $store->open_hours;
                                    break;
                                case 'regular_holiday':
                                    $item_value = $store->regular_holiday;
                                    break;
                                case 'parking':
                                    $item_value = $store->parking;
                                    break;
                                case 'remarks':
                                    $item_value = $store->remarks;
                                    break;
                            }
                        ?>
                        <dt><?php echo esc_html($item_labels[$item->item_name]); ?></dt>
                        <dd>
                            <?php if ($item->item_name === 'url') : ?>
                                <a href="<?php echo esc_url($item_value); ?>" target="_blank"><?php echo esc_html($item_value); ?></a>
                            <?php else : ?>
                                <?php echo esc_html($item_value); ?>
                            <?php endif; ?>
                        </dd>
                    <?php endif; ?>
                <?php endforeach; ?>
            </dl>
        </div>

        <div class="docodoco_store_detail_img03 docodoco_store_img01">
            <img src="<?php echo !empty($store->img_filename) ? esc_url(DOCOSL_UPLOAD_URL . 'store-images/' . $store->img_filename) : esc_url(DOCOSL_URL_PATH . 'public/images/store-dummy-img.png'); ?>" alt="<?php echo esc_attr($store->name); ?>">
        </div>

        <!--========== Map ==========-->
        <?php if ($is_map_display_enabled): ?>
            <div id="docodoco_store_map" class="docodoco_store_map map_size02"></div>
        <?php endif; ?>
        <!--========== /Map ==========-->

        <p class="docodoco_store_btn01 docodoco_store_btn01_back"><a href="<?php echo esc_url($list_page_url) ?>">一覧へ戻る</a></p>
    </div>
<!--========== /詳細レイアウト03 ==========-->
</article>
<!--========== /テンプレート ==========-->

<script>
    function docoslInitMap() {}

    document.addEventListener("DOMContentLoaded", function() {
        // Google Maps APIで使用する定数
        const MAX_ZOOM_LEVEL = 10; // 最大ズームレベル
        const MAP_DEFAULT_OPTIONS = {
                zoom: 6, // デフォルトのズームレベル（日本列島が収まるくらい）
                center: { lat: 36.2048, lng: 138.2529 }, // デフォルトの中心位置（日本の緯度経度）
            };
    
        // Google Maps API関連の変数
        let map; // Google Map オブジェクト
        let markers = []; // マーカーの配列
        let isMapDisplayEnabled = <?php echo $is_map_display_enabled ? 'true' : 'false'; ?>;
        
        /*
          初期表示時の処理
        */
        initMap();
        
        /*
          Googleマップの初期化
        */ 
        function initMap() {
            // Googleマップを有効化してない場合は処理を終了
            if (!isMapDisplayEnabled) return;
            
            map = new google.maps.Map(document.getElementById('docodoco_store_map'), {
                zoom: 10,
                center: { lat: <?php echo esc_js($store->lat); ?>, lng: <?php echo esc_js($store->lng); ?> },
            });
    
            // マーカーを作成
            makeInitMarkers();
    
            // 表示するマーカーがない場合、デフォルトの位置とズームを使用
            if (markers.length === 0) {
                setDefaultMapPositionAndZoom();
                return;
            }
    
            // マーカーに情報ウィンドウを追加
            markers.forEach(markerData => {
                const marker = new google.maps.Marker({
                    position: markerData.position,
                    map: map,
                });
    
                const infoWindow = new google.maps.InfoWindow({
                    content: markerData.content,
                });
    
                marker.addListener('click', () => {
                    infoWindow.open(map, marker);
                });
            });
        }
    
        /*
          初期表示時のマーカーを作成
        */
        function makeInitMarkers() {
            <?php if (!empty($store->lat) && !empty($store->lng)): ?>
                markers.push({
                    position: { lat: <?php echo esc_js($store->lat); ?>, lng: <?php echo esc_js($store->lng); ?> },
                    content: `
                        <div class="docodoco_index01_window">
                            <a href="<?php echo esc_url($store->url); ?>">
                                <?php echo esc_html($store->name); ?>
                            </a>
                        </div>
                    `,
                });
            <?php endif; ?>
        }
    
        /*
          デフォルトの位置とズームをセット
        */ 
        function setDefaultMapPositionAndZoom() {
            map.setCenter(MAP_DEFAULT_OPTIONS.center);
            map.setZoom(MAP_DEFAULT_OPTIONS.zoom);
        }
    });
</script>
