<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<!--========== テンプレート ==========-->
<div class="docodoco_store">
    <!--========== 一覧レイアウト02 ==========-->
    <div class="docodoco_store_index02">
        <!--========== 検索条件 ==========-->
        <div class="docodoco_index_search01">
            <div class="index_search_box01">
                <p class="index_search_box01_title">キーワード検索</p>
                <div class="index_search_item01">
                    <input type="text" name="keyword" value="" class="index_search_keyword01" placeholder="店舗名・住所・郵便番号">
                    <button type="submit" class="index_search_btn01">
                        <span class="material-symbols-sharp">search</span>
                    </button>
                </div>
            </div>
        </div>
        <!--========== /検索条件 ==========-->

        <!--========== Map ==========-->
        <?php if ($is_map_display_enabled): ?>
            <div id="docodoco_store_map" class="docodoco_store_map map_size01"></div>
        <?php endif; ?>
        <!--========== /Map ==========-->

        <!--========== 店舗リスト ==========-->
        <div id="docodoco_store_list" class="docodoco_index_shop02">
        </div>
            
        <div class="docodoco_store_no_results docodoco_index_shop_item02" style="display: none;">
            条件に該当する店舗が見つかりませんでした。
        </div>

        <div id="temp_docodoco_store_list" style="display: none;">
            <?php foreach ($stores as $store) : ?>
                <div class="docodoco_index_shop_item02 js_docodoco_store_row" data-distance="999999" data-lat="<?php echo esc_attr($store->lat); ?>" data-lng="<?php echo esc_attr($store->lng); ?>" >
                    <div class="docodoco_index_shop_box02">
                        <div class="docodoco_index_shop_img02 docodoco_store_img01">
                            <img src="<?php echo !empty($store->img_filename) ? esc_url(DOCOSL_UPLOAD_URL . 'store-images/' . $store->img_filename) : esc_url(DOCOSL_URL_PATH . 'public/images/store-dummy-img.png'); ?>" alt="<?php echo esc_attr($store->name); ?>">
                        </div>
                        <div class="docodoco_index_shop_inner02">
                            <p class="docodoco_index_shop_name02 docodoco_store_name">
                                <?php
                                    if ($store_detail_page_enabled) {
                                        $url = add_query_arg('storeId', $store->id, $detail_page_url);
                                        echo '<a href="' . esc_url($url) . '">' . esc_html($store->name) . '</a>';
                                    } else if (!empty($store->url)) {
                                        $url = $store->url;
                                        echo '<a href="' . esc_url($url) . '">' . esc_html($store->name) . '</a>';
                                    } else {
                                        echo esc_html($store->name);
                                    }
                                ?>
                            </p>
                            <dl class="docodoco_index_shop_info02">
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
                                        <dd class="<?php echo 'docodoco_store_' . esc_attr($item->item_name); ?>"><?php echo esc_html($item_value); ?></dd>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </dl>
                        </div>
                        <?php if ($store_detail_page_enabled) : ?>
                            <p class="docodoco_store_btn01 docodoco_store_btn01_type01"><a href="<?php echo esc_url(add_query_arg('storeId', $store->id, $detail_page_url)); ?>">店舗詳細を見る</a></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>    
        </div>
        <!--========== /店舗リスト ==========-->
    </div>
    <!--========== /一覧レイアウト0２ ==========-->
</div>
<!--========== /テンプレート ==========-->

<script>
    // どこどこJP APIの処理
    let isDocodocojpEnabled = <?php echo $is_docodocojp_enabled ? 'true' : 'false'; ?>;
    var docodoco_key = '<?php echo esc_js($docodocojp_apikey); ?>&used_by=docodoco-store-locator';
    var geolocationapi_op = {
        enableHighAccuracy: true,
        timeout: 5000,
        maximumAge: 300000
    };

    // どこどこJPのcallback関数
    function callback_docodoco(SURFPOINT_latlon){
        docodocoStoreLocator(SURFPOINT_latlon);
    };

    // GoogleMapAPIのcallback関数
    function callback_docoslInitMap() {
        // どこどこJPを利用する場合はどこどこJPのcallback関数内で呼び出す
        if (!isDocodocojpEnabled) {
            docodocoStoreLocator();
        };
    }

    let isDocoslMapDisplayEnabled = <?php echo $is_map_display_enabled ? 'true' : 'false'; ?>;
    if (!isDocodocojpEnabled && !isDocoslMapDisplayEnabled) {
        docodocoStoreLocator();
    }

    // DocoDoco Store Locatorテンプレートの処理
    function docodocoStoreLocator(surfpoint = {}) {
        // 表示モード
        const DISPLAY_MODE = {
            INIT: 'init',
            SEARCH: 'search',
        } 
    
        // 表示項目
        const DISPLAY_ITEMS = [
            <?php foreach ($display_items as $item): ?>
                <?php if ($item->is_display == 1): ?>
                    '<?php echo esc_js($item->item_name); ?>',
                <?php endif; ?>
            <?php endforeach; ?>
        ];
    
        // Google Maps APIで使用する定数
        const MAX_ZOOM_LEVEL = 10; // 最大ズームレベル
        const MAP_DEFAULT_OPTIONS = {
            zoom: 6, // デフォルトのズームレベル（日本列島が収まるくらい）
            center: { lat: 36.2048, lng: 138.2529 }, // デフォルトの中心位置（日本の緯度経度）
        };
    
        // Google Maps API関連の変数
        let map; // Google Map オブジェクト
        let markers = []; // マーカーの配列
    
        /*
          初期表示時の処理
        */
        sortTableRows();
        displayStoreData();
        initMap();

        /*
          店舗テーブルの並び替え
        */ 
        function sortTableRows() {
            if (!isDocodocojpEnabled || !isDjApiResValid()) return;  

            // テーブルの行要素を取得
            const tableBody = document.getElementById('temp_docodoco_store_list');
            
            // 現在地の緯度と経度
            const currentLat = surfpoint['CityLatitude'];
            const currentLon = surfpoint['CityLongitude'];

            // テーブル内の各行を処理
            const rows = Array.from(tableBody.querySelectorAll('.js_docodoco_store_row'));
            rows.forEach((row) => {
                // 各店舗の緯度と経度を取得
                const storeLat = parseFloat(row.getAttribute('data-lat'));
                const storeLon = parseFloat(row.getAttribute('data-lng'));

                // 現在地から店舗までの距離を計算（単位：メートル）
                const distance = calculateDistance(currentLat, currentLon, storeLat, storeLon);

                // 距離をdata-distance属性に設定
                row.setAttribute('data-distance', distance);
            });

            // data-distance属性で昇順にソート
            rows.sort(compareDistance);

            // ソート後の行をテーブルに追加
            rows.forEach((row) => {
                tableBody.appendChild(row);
            });
        }

        // 2つの緯度経度から距離を計算する関数（単位：キロメートル）
        function calculateDistance(lat1, lon1, lat2, lon2) {
            const radLat1 = lat1 * Math.PI / 180.0;
            const radLon1 = lon1 * Math.PI / 180.0;
            const radLat2 = lat2 * Math.PI / 180.0;
            const radLon2 = lon2 * Math.PI / 180.0;
            const radLatAve = (radLat1 + radLat2) / 2.0;
            const radLatDiff = radLat1 - radLat2;
            const radLonDiff = radLon1 - radLon2;
            const sinLat = Math.sin(radLatAve);
            const temp = 1.0 - 0.00669438 * (sinLat * sinLat);
            const dvrad = 6378137.0 / Math.sqrt(temp);  // 卯酉線曲率半径
            const meridianRad = 6335439.0 / Math.sqrt(temp * temp * temp);  // 子午線曲率半径
            const t1 = meridianRad * radLatDiff;
            const t2 = dvrad * Math.cos(radLatAve) * radLonDiff;
            const dist = Math.sqrt((t1 * t1) + (t2 * t2));
            return dist;
        }

        // data-distance属性を基準に昇順でソートする関数
        function compareDistance(a, b) {
            const distanceA = parseFloat(a.getAttribute('data-distance'));
            const distanceB = parseFloat(b.getAttribute('data-distance'));
            return distanceA - distanceB;
        }

        /*
          Googleマップの初期化
        */ 
        function initMap() {
            // Googleマップを有効化してない場合は処理を終了
            if (!isDocoslMapDisplayEnabled) return;
    
            map = new google.maps.Map(document.getElementById('docodoco_store_map'));
    
            // マーカーを作成
            makeInitMarkers();
    
            // 表示するマーカーがない場合、デフォルトの位置とズームを使用
            if (markers.length === 0) {
                setDefaultMapPositionAndZoom();
                return;
            }
    
            // マーカーに情報ウィンドウを追加
            markers.forEach(markerData => {
                const infoWindow = new google.maps.InfoWindow({
                    content: markerData.content,
                });
    
                markerData.marker.addListener('click', () => {
                    infoWindow.open(map, markerData.marker);
                });
            });
    
            // 表示位置とズームレベルを設定
            if (isDocodocojpEnabled && isDjApiResValid()) {
                // どこどこJP利用時は現在地を表示位置に設定する
                map.setCenter({ lat: Number(surfpoint['CityLatitude']), lng: Number(surfpoint['CityLongitude']) });
                map.setZoom(10);
            }else{
                // マーカーに合わせてズームレベルと表示位置を調整
                fitMapBoundsToMarkers(markers);
            }
        }
    
        /*
          どこどこJP APIのレスポンスが正常かチェック
        */
        function isDjApiResValid() {
            // ハッシュ型でない場合
            if (typeof surfpoint !== 'object' || Array.isArray(surfpoint)) {
                return false;
            }

            // ハッシュが空のケース
            if (Object.keys(surfpoint).length === 0) {
                return false;
            }

            // 緯度経度のキーがない場合
            if (!('CityLatitude' in surfpoint) || !('CityLongitude' in surfpoint)) {
                return false;
            }

            // 緯度経度の値が存在しない場合
            if (surfpoint['CityLatitude'] === null || surfpoint['CityLongitude'] === null) {
                return false;
            }

            // keyというキーの値がdummyの場合
            if ('key' in surfpoint && surfpoint['key'] === 'dummy') {
                return false;
            }

            // すべての条件を満たす場合は正常な値とみなす
            return true;
        }
        
        /*
          初期表示時のマーカーを作成
        */
        function makeInitMarkers() {
            markers = [
                <?php foreach ($stores as $store): ?>
                    <?php if (empty($store->lat) || empty($store->lng)) continue; ?>
                    {
                        marker: new google.maps.Marker({
                            position: { lat: <?php echo esc_js($store->lat); ?>, lng: <?php echo esc_js($store->lng); ?> },
                            map: map,
                        }),
                        content: `
                            <div class="docodoco_index_window01">
                                <div class="docodoco_index_window01_inner">
                                    <p class="docodoco_index_window01_name">
                                        <a href="<?php echo esc_url($store->url); ?>">
                                            <?php echo esc_html($store->name); ?>
                                        </a>
                                    </p>
                                    ${DISPLAY_ITEMS.includes('address') ? `
                                        <p class="docodoco_index_window01_add">
                                            <?php
                                                if ($store->postal_code) {
                                                    echo esc_html('〒' . $store->postal_code . ' ' . $store->address);
                                                } else {
                                                    echo esc_html($store->address);
                                                }
                                            ?>
                                        </p>` : ''
                                    }
                                </div>
                            </div>
                        `,
                        storeName: '<?php echo esc_html($store->name); ?>',
                        storePostalCode: '<?php echo esc_html($store->postal_code); ?>',
                        storeAddress: '<?php echo esc_html($store->address); ?>',
                    },
                <?php endforeach; ?>
            ];
        }
    
        /*
          検索ボタンクリック時の処理
        */ 
        document.querySelector('.index_search_btn01').addEventListener('click', () => {
            const keyword = document.querySelector('.index_search_keyword01').value;
            searchStores(keyword);
        });

        /*
          キーワード入力エリアでEnterキーが押された時の処理
        */ 
        document.querySelector('.index_search_keyword01').addEventListener('keydown', (event) => {
            if (event.keyCode === 13) {
                const keyword = document.querySelector('.index_search_keyword01').value;
                searchStores(keyword);
            }
        });
    
        /*
          キーワード検索
        */ 
        function searchStores(keyword) {
            // 店舗リストの再表示
            displayStoreData(keyword, DISPLAY_MODE.SEARCH);
    
            // Googleマップを有効化してない場合は処理を終了
            if (!isDocoslMapDisplayEnabled) return;
    
            // キーワードが空白の場合、全マーカーを表示
            if (keyword === "") {
                showTargetMarkers(markers);
                return;
            }
            
            const filteredMarkers = markers.filter(markerData => {
                return (
                    markerData.storeName.includes(keyword) ||
                    (DISPLAY_ITEMS.includes('address') && markerData.storePostalCode.includes(keyword)) ||
                    (DISPLAY_ITEMS.includes('address') && markerData.storeAddress.includes(keyword))
                );
            });
    
            // すべてのマーカーを非表示
            hideAllMarkers();
    
            // 表示するマーカーがない場合、デフォルトの位置とズームを使用
            if (filteredMarkers.length === 0) {
                setDefaultMapPositionAndZoom();
                return;
            }
    
            // 検索結果のマーカーのみを表示
            showTargetMarkers(filteredMarkers);
        }
    
        /*
          店舗リストの表示
        */ 
        function displayStoreData(keyword = '', mode = DISPLAY_MODE.INIT) {
            const storeListElement = document.getElementById('docodoco_store_list');
            const tempStoreListElement = document.querySelectorAll('#temp_docodoco_store_list .docodoco_index_shop_item02');
            const noResultsMessageElement = document.querySelector('.docodoco_store_no_results');
    
            // 店舗リストのクリア
            storeListElement.innerHTML = '';
    
            // 店舗リストの追加
            tempStoreListElement.forEach(rowElement => {
                const cloneRowElement = rowElement.cloneNode(true);
                if (mode === DISPLAY_MODE.SEARCH) {
                    // 店舗名に検索キーワードが含まれるかチェック
                    const storeName = rowElement.querySelector('.docodoco_store_name').textContent;
                    let isStoreNameMatched = storeName.includes(keyword);
    
                    // 住所・郵便番号に検索キーワードが含まれるかチェック
                    let isAddressMatched = false;
                    if (DISPLAY_ITEMS.includes('address')) {
                        const storeAddress = rowElement.querySelector('.docodoco_store_address').textContent;
                        isAddressMatched = storeAddress.includes(keyword);
                    } 
                    
                    // 検索キーワードが含まれない場合、スキップ
                    if (!isStoreNameMatched && !isAddressMatched) {
                        return;
                    }
                }
                storeListElement.appendChild(cloneRowElement);
            });
    
            // 表示店舗がない場合、メッセージを表示
            const recordCount = storeListElement.querySelectorAll('.docodoco_index_shop_item02').length;
            if (recordCount === 0) {
                const cloneNoResultsElement = noResultsMessageElement.cloneNode(true);
                cloneNoResultsElement.style.display = 'block';
                storeListElement.appendChild(cloneNoResultsElement);
            }
        }
    
        /*
          指定のマーカーを表示
        */ 
        function showTargetMarkers(targetMarkers) {
            targetMarkers.forEach(markerData => {
                markerData.marker.setMap(map);
            });
    
            // マーカーに合わせてズームレベルと表示位置を調整
            fitMapBoundsToMarkers(targetMarkers);
        }
    
        /*
          マーカーに合わせてズームレベルと表示位置を調整
        */ 
        function fitMapBoundsToMarkers(markers) {
            const resultBounds = new google.maps.LatLngBounds();
            markers.forEach(markerData => {
                resultBounds.extend(markerData.marker.getPosition());
            });
            map.fitBounds(resultBounds);
            
            // ズームレベルを制限
            const zoom = map.getZoom();
            if (map.getZoom() > MAX_ZOOM_LEVEL) {
                map.setZoom(MAX_ZOOM_LEVEL);
            }
        }
    
        /*
          全店舗のマーカーを非表示
        */ 
        function hideAllMarkers() {
            markers.forEach(markerData => {
                markerData.marker.setMap(null);
            });
        }
    
        /*
          デフォルトの位置とズームをセット
        */ 
        function setDefaultMapPositionAndZoom() {
            map.setCenter(MAP_DEFAULT_OPTIONS.center);
            map.setZoom(MAP_DEFAULT_OPTIONS.zoom);
        }
    };
</script>
