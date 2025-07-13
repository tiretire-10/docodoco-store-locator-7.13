<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<style>
	.docosl-bootstrap .wp-heading-inline {
		font-size: 23px !important;
	}
</style>

<div class="wrap docosl-bootstrap">
	<?php foreach ($view_alerts as $k => $alert) : ?>
		<div class="alert <?php echo 'alert-' . esc_attr($alert['level']); ?>" role="alert">
			<?php echo esc_html($alert['message']); ?>
		</div>
	<?php endforeach ?>
	<h1 class="wp-heading-inline">店舗一覧</h1>
	<a href="<?php echo esc_url(admin_url('admin.php?page=docodoco-store-locator-register-store')); ?>" class="page-title-action btn btn-success ms-3 btn-sm">
		＋ 新規追加
	</a>

	<div class="d-flex justify-content-end">
		<form id="zipUploadForm" action="<?php echo esc_url(admin_url('admin.php?page=docodoco-store-locator-zip')) ?>" method="POST" enctype="multipart/form-data">
			<?php wp_nonce_field('docodoco-store-locator-zip-import') ?>
			<input id="zipUploadDryRunFlag" type="hidden" name="dry_run" value="0">
			<button id="zipUploadButtonTest" type="button" class="button action me-3">
				ZIPインポート（テスト実行）
			</button>
			<button id="zipUploadButton" type="button" class="button action me-3">
				ZIPインポート
			</button>
		</form>
		<a href="<?php echo esc_url(admin_url('admin.php?page=docodoco-store-locator-zip&action=export')) ?>" class="button action me-3">
			ZIPエクスポート
		</a>
	</div>

	<div class="d-flex justify-content-between mt-3">
		<div class="d-flex">
			<label for="bulkActionSelector" class="screen-reader-text">一括操作を選択</label>
			<select name="bulkActionSelector" id="bulkActionSelector" class="bulk-action-input">
				<option value="-1">一括操作</option>
				<option value="published">公開</option>
				<option value="unpublished">非公開</option>
				<option value="delete">削除</option>
			</select>
			<input type="button" id="docoslBulkActionBtn" class="button action ms-2 bulk-action-input" value="適用">
		</div>
		<div class="d-flex">
			<label for="search-category" class="d-flex align-items-center me-2">検索項目</label>
			<select id="docoslSearchItem" name="category">
				<option value="name"><?php echo esc_html($labels['name']); ?></option>
				<option value="address"><?php echo esc_html($labels['address']); ?></option>
			</select>
			
			<label for="docoslSearchKeyword" class="d-flex align-items-center ms-3 me-2">キーワード</label>
			<input type="text" name="keyword" id="docoslSearchKeyword" placeholder="〇〇店">
			
			<button type="button" id="docoslSearchBtn" class="ms-4 btn btn-primary">検索</button>
		</div>
	</div>
	
	<div class="docodoco-store-no-results mt-5 ms-3" style="display: none;">
		条件に該当する店舗が見つかりませんでした。
	</div>
	
	<div id="docodocoStoreTableArea" class="table-responsive mt-2 ">
		<div class="mt-3 mb-2 ms-2 docodoco-store-count">
		</div>
		<form id="bulkActionForm" method="POST">
			<?php wp_nonce_field('docosl-store-list'); ?>
			<input id="bulkActionType" type="hidden" name="bulkActionType" value="-1">
			<table class="table table-hover text-nowrap border table-striped table-bordered mb-0">
				<thead>
					<tr>
						<td id="" class="column-cb check-column">
							<label class="label-covers-full-cell" for="cb-select-all-1">
								<span class="screen-reader-text">すべて選択</span>
							</label>
							<input id="cb-select-all-1" type="checkbox">
						</td>
						<th class=""></th>
						<th class=""><?php echo esc_html($labels['name']); ?></th>
						<th class=""><?php echo esc_html($labels['address']); ?></th>
						<th class=""><?php echo esc_html($labels['tel']); ?></th>
						<th class=""><?php echo esc_html($labels['fax']); ?></th>
						<th class=""><?php echo esc_html($labels['url']); ?></th>
						<th class=""><?php echo esc_html($labels['email']); ?></th>
						<th class=""><?php echo esc_html($labels['open_hours']); ?></th>
						<th class=""><?php echo esc_html($labels['regular_holiday']); ?></th>
						<th class=""><?php echo esc_html($labels['parking']); ?></th>
						<th class=""><?php echo esc_html($labels['publish_status']); ?></th>
						<th class=""><?php echo esc_html($labels['sort_order']); ?></th>
						<th class=""><?php echo esc_html($labels['created_at']); ?></th>
						<th class=""><?php echo esc_html($labels['updated_at']); ?></th>
						<th class=""><?php echo esc_html($labels['admin_remarks']); ?></th>
					</tr>
				</thead>
	
				<tbody id="docodocoStoreList">
				</tbody>

				<tbody id="tempDocodocoStoreList" style="display: none;">
					<?php foreach ($stores as $store) : ?>
						<tr>
							<th scope="row" class="check-column">
								<input type="checkbox" name="storeIds[]" value="<?php echo esc_attr( $store->id ); ?>">
							</th>
							<td class="">
                                <img src="
                                    <?php if (!empty($store->img_filename)) {
                                        echo esc_url(DOCOSL_UPLOAD_URL . 'store-images/' . $store->img_filename . '?date=' . crc32($store->updated_at));
                                    } else {
                                        echo esc_url(DOCOSL_URL_PATH . 'public/images/store-dummy-img.png');
                                    } ?>
                                " alt="" style="width: 100px; height: 80px;">
							</td>
							<td class="docosl-store-name">
								<strong>
									<a class="link-primary" href="<?php echo esc_url(admin_url('admin.php?page=docodoco-store-locator-register-store&storeId=' . $store->id)); ?>">
										<?php echo esc_html($store->name); ?>
									</a>
								</strong>
							</td>
							<td class="docosl-store-address">
								<?php if (!empty($store->postal_code)): ?>
									〒<?php echo esc_html($store->postal_code); ?> <?php echo esc_html($store->address); ?>
								<?php else: ?>
									<?php echo esc_html($store->address); ?>
								<?php endif; ?>
							</td>
							<td class="">
								<?php echo esc_html($store->tel); ?>
							</td>
							<td class="">
								<?php echo esc_html($store->fax); ?>
							</td>
							<td class="">
								<?php echo esc_html($store->url); ?>
							</td>
							<td class="">
								<?php echo esc_html($store->email); ?>
							</td>
							<td class="">
								<?php echo esc_html($store->open_hours); ?>
							</td>
							<td class="">
								<?php echo esc_html($store->regular_holiday); ?>
							</td>
							<td class="">
								<?php echo esc_html($store->parking); ?>
							</td>
							<td class="">
								<?php echo esc_html(($store->publish_status == 1) ? '非公開' : '公開'); ?>
							</td>
							<td class="">
								<?php echo esc_html($store->sort_order); ?>
							</td>
							<td class="">
								<?php echo esc_html(date('Y年m月d H:i', strtotime($store->created_at))); ?>
							</td>
							<td class="">
								<?php echo esc_html(date('Y年m月d H:i', strtotime($store->updated_at))); ?>
							</td>
							<td class="">
								<?php echo esc_html($store->admin_remarks); ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</form>
	</div>
</div>

<script>

document.addEventListener('DOMContentLoaded', function() {
	// 表示モード
	const DISPLAY_MODE = {
		INIT: 'init',
		SEARCH: 'search',
	} 

	/*
		初期表示時の処理
	*/
	displayStoreData();

	// 店舗一覧の表示
	function displayStoreData(mode = DISPLAY_MODE.INIT) {
		const storeListElement = document.getElementById('docodocoStoreList');
		const tempStoreListElement = document.querySelectorAll('#tempDocodocoStoreList tr');
		const tableAreaElement = document.getElementById('docodocoStoreTableArea');
		const noResultsMessageElement = document.querySelector('.docodoco-store-no-results');
		const bulkActionInputElement = document.querySelectorAll('.bulk-action-input');
		
		// 店舗リストのクリア
		storeListElement.innerHTML = '';

		// 店舗リストの追加
		tempStoreListElement.forEach(trElement => {
			const cloneTrElement = trElement.cloneNode(true);
			if (mode === DISPLAY_MODE.SEARCH) {              
				const keyword = document.getElementById('docoslSearchKeyword').value;
				const searchItem = document.getElementById('docoslSearchItem').value;
				const searchClassName = '.docosl-store-' + searchItem;

				// 検索キーワードが含まれない場合、スキップ
				const text = trElement.querySelector(searchClassName).textContent;
				if (!text.includes(keyword)) {
					return;
				}
			}
			storeListElement.appendChild(cloneTrElement);
		});

		// レコード数に応じて表示を切り替え
		const recordCount = storeListElement.querySelectorAll('tr').length;
		if (recordCount === 0) {
			tableAreaElement.style.display = 'none';
			bulkActionInputElement.forEach(element => element.style.display = 'none');
			noResultsMessageElement.style.display = 'block';
		} else {
			tableAreaElement.style.display = 'block';
			bulkActionInputElement.forEach(element => element.style.display = 'block');
			noResultsMessageElement.style.display = 'none';
		}

		// レコード数の表示
		const storeCountElement = document.querySelector('.docodoco-store-count');
		storeCountElement.textContent = recordCount + '件表示中';
	}

	/*
	  イベント処理
	*/ 
   const uploadButton = document.getElementById('zipUploadButton');
   const uploadButtonTest = document.getElementById('zipUploadButtonTest');
   const zipUploadInput = document.createElement('input');
   const zipUploadDryRunFlag = document.getElementById('zipUploadDryRunFlag');
   const searchBtn = document.getElementById('docoslSearchBtn');

   zipUploadInput.type = 'file';
   zipUploadInput.style = 'display: none;';
   zipUploadInput.accept = '.zip';
   zipUploadInput.name = 'file';

	// キーワード検索
	searchBtn.addEventListener('click', function() {
		displayStoreData(DISPLAY_MODE.SEARCH);
	});

	// zipインポート・エクスポート
	uploadButton.addEventListener('click', function() {
		zipUploadDryRunFlag.value = '0';
		zipUploadInput.click();
	});

	uploadButtonTest.addEventListener('click', function() {
		zipUploadDryRunFlag.value = '1';
		zipUploadInput.click();
	});

	zipUploadInput.addEventListener('change', async function(e) {
		const file = zipUploadInput.files[0];

		if (file.type !== 'application/zip' && file.type !== 'application/x-zip-compressed') {
			zipUploadInput.value = '';
			alert('アップロードされたファイルはZIPファイルではないようです。ZIPファイルを選択してください。');
			return;
		}

		const uploadForm = document.getElementById("zipUploadForm");

		uploadForm.appendChild(zipUploadInput);
		uploadForm.submit();
	});

	// 一括操作の処理
	const bulkActionSelector = document.getElementById('bulkActionSelector');
	const bulkActionBtn = document.getElementById('docoslBulkActionBtn');
	const bulkActionType = document.getElementById('bulkActionType');
	const bulkActionForm = document.getElementById('bulkActionForm');

	bulkActionBtn.addEventListener('click', function() {
		bulkActionType.value = bulkActionSelector.value;
		if (bulkActionType.value === 'delete') {
			const confirmResult = confirm('選択した店舗を削除してもよろしいですか？');
			
			// 確認がキャンセルされた場合、フォーム送信を中止
			if (!confirmResult) {
				return;
			}
		}
		bulkActionForm.submit();
	});
});

</script>
