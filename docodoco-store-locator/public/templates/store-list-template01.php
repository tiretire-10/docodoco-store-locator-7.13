<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<!--========== テンプレート ==========-->
<article class="docodoco_store">
    <!--========== 一覧レイアウト01 ==========-->
    <div class="docodoco_store_index01">
        <!--========== 検索条件 ==========-->
        <div class="docodoco_index_search01">
            <div class="index_search_box01">
                <?php 
                $search_title = (isset($atts['category']) && $atts['category'] === 'hospital') ? 'お近くの病院名を入力して受診方法を調べましょう' : 'あなたがお住まいの市区町村名を入力して受診方法を調べましょう';
                ?>
                <p class="index_search_box01_title"><?php echo esc_html($search_title); ?></p>
                <div class="index_search_item01">
                    <?php 
                    $placeholder = (isset($atts['category']) && $atts['category'] === 'hospital') ? '例：○○クリニック、千代田区' : '例：新宿区、横浜市';
                    ?>
                    <input type="text" name="keyword" value="" class="index_search_keyword01" placeholder="<?php echo esc_attr($placeholder); ?>">
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
        <div class="docodoco_index_shop01">
            <div class="docodoco_store_no_results">
                条件に該当する自治体が見つかりませんでした。
            </div>

            <table id="docodoco_store_table">
                <thead>
                    <tr>
                        <th>自治体名</th>
                        <?php foreach ($display_items as $item) : ?>
                            <?php if ($item->is_display == 1) : ?>
                                <th <?php echo ($item->item_name === 'tel' || $item->item_name === 'fax') ? 'class="tel"' : ''; ?><?php echo ($item->item_name === 'open_hours') ? 'class="hours"' : ''; ?>>
                                    <?php echo esc_html($item_labels[$item->item_name]); ?>
                                </th>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody id="docodoco_store_list">
                </tbody>
            </table>

            <table style="display: none;">
                <tbody id="temp_docodoco_store_list">
                    <?php foreach ($stores as $store) : ?>
                        <tr class="js_docodoco_store_row" data-distance="999999" data-lat="<?php echo esc_attr($store->lat); ?>" data-lng="<?php echo esc_attr($store->lng); ?>" >
                            <td class="docodoco_store_name">
                                <?php
                                    if ($store_detail_page_enabled) {
                                        $url = add_query_arg('storeId', $store->id, $detail_page_url);
                                        echo '<a href="' . esc_url($url) . '" target="_blank">' . esc_html($store->name) . '</a>';
                                    } else if (!empty($store->url)) {
                                        $url = $store->url;
                                        echo '<a href="' . esc_url($url) . '" target="_blank">' . esc_html($store->name) . '</a>';
                                    } else {
                                        echo esc_html($store->name);
                                    }
                                ?>
                            </td>
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
                                            case 'department':
                                                $item_value = $store->department;
                                                break;
                                            case 'contact':
                                                // 既存のtel とemail を組み合わせて表示
                                                $contact_parts = array();
                                                if (!empty($store->tel)) {
                                                     $contact_parts[] = 'TEL: ' . $store->tel;
                                                }
                                                if (!empty($store->email)) {
                                                    $contact_parts[] = 'Email: ' . $store->email;
                                                }
                                                $item_value = !empty($contact_parts) ? implode(' / ', $contact_parts) : $store->contact;
                                                break;
                                            case 'age':
                                                $item_value = $store->age;
                                                break;
                                            case 'period':
                                                $item_value = $store->period;
                                                break;
                                            case 'document':
                                                $item_value = $store->document;
                                                break;
                                            case 'application':
                                                $item_value = $store->application;
                                                break;
                                            case 'belongings':
                                                $item_value = $store->belongings;
                                                break;
                                            case 'cost':
                                                $item_value = $store->cost;
                                                break;
                                        }
                                    ?>
                                    <td class="<?php echo 'docodoco_store_' . esc_attr($item->item_name); ?>">
                                        <?php if ($item->item_name === 'url') : ?>
                                            <a href="<?php echo esc_url($item_value); ?>" target="_blank"><?php echo esc_html($item_value); ?></a>
                                        <?php else : ?>
                                            <?php echo esc_html($item_value); ?>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <!--========== /店舗リスト ==========-->
    </div>
    <!--========== /一覧レイアウト01 ==========-->
</article>
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

        sortTableRows();
        displayStoreData();
        initMap();
        */


        // 初期状態でテーブルと地図を非表示にする（新規追加）
        document.getElementById('docodoco_store_table').style.display = 'none';
        if (document.getElementById('docodoco_store_map')) {
            document.getElementById('docodoco_store_map').style.display = 'none';
        }


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
                            position: { lat: <?php echo esc_html($store->lat); ?>, lng: <?php echo esc_html($store->lng); ?> },
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
            const tempStoreListElement = document.querySelectorAll('#temp_docodoco_store_list tr');
            const tableElement = document.getElementById('docodoco_store_table');
            const noResultsMessageElement = document.querySelector('.docodoco_store_no_results');
    
            // 店舗リストのクリア
            storeListElement.innerHTML = '';
    
            // 店舗リストの追加
            tempStoreListElement.forEach(trElement => {
                const cloneTrElement = trElement.cloneNode(true);
                if (mode === DISPLAY_MODE.SEARCH) {                   
                    // 店舗名に検索キーワードが含まれるかチェック
                    const storeName = trElement.querySelector('.docodoco_store_name').textContent;
                    let isStoreNameMatched = storeName.includes(keyword);
    
                    // 住所・郵便番号に検索キーワードが含まれるかチェック
                    let isAddressMatched = false;
                    if (DISPLAY_ITEMS.includes('address')) {
                        const storeAddress = trElement.querySelector('.docodoco_store_address').textContent;
                        isAddressMatched = storeAddress.includes(keyword);
                    } 
                    
                    // 検索キーワードが含まれない場合、スキップ
                    if (!isStoreNameMatched && !isAddressMatched) {
                        return;
                    }
                }
                storeListElement.appendChild(cloneTrElement);
            });
    
            // レコード数に応じて表示を切り替え
            const recordCount = storeListElement.querySelectorAll('tr').length;
            if (recordCount === 0) {
                tableElement.style.display = 'none';
                noResultsMessageElement.style.display = 'block';
            } else {
                tableElement.style.display = 'table';
                noResultsMessageElement.style.display = 'none';
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
