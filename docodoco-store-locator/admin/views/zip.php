<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<style>
    .docosl-bootstrap h1 {
        font-size: 23px !important;
    }

    .docosl-bootstrap h2 {
        font-size: 18px !important;
    }

    .docosl-bootstrap .record-count {
        /* font-size: 0.75rem; */
    }
</style>

<div class="wrap docosl-bootstrap">
    <?php foreach ($view_errors as $k => $error) : ?>
        <div class="alert <?php echo 'alert-' . esc_attr($error['level']); ?>" role="alert">
            <?php echo esc_html($error['message']); ?>
        </div>
    <?php endforeach ?>
    <h1>ZIP インポート結果 <?php echo $view_dry_run ? "（テスト実行）" : "" ?></h1>
    <div class="mt-3 mb-3">
        <div class="mb-3">
            <h2>店舗情報（CSV）</h2>
            <div class="mb-3">
                ステータス: <?php echo esc_html($view_upsert_result_msg); ?>
            </div>
            <?php if ($view_upsert_result !== null) : ?>
                <?php if (!$view_upsert_result['success']) : ?>
                    <div class="table-responsive mt-3">
                        <table class="table table-hover text-nowrap border table-striped table-bordered mb-0">
                            <thead>
                                <tr class="column-cb check-column">
                                    <th>エラー</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($view_upsert_result['errors'] as $error) : ?>
                                    <tr>
                                        <td><?php echo esc_html($error); ?></td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif ?>
                <div class="table-responsive mt-3">
                    <table class="table table-hover text-nowrap border table-striped table-bordered mb-0">
                        <thead>
                            <tr class="column-cb check-column">
                                <th>種別</th>
                                <th>ID</th>
                                <th>店舗名</th>
                                <th>変更内容</th>
                                <th>エラー</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($view_upsert_result['rows'] as $row) : ?>
                                <tr>
                                    <td><?php echo esc_html($row['changes']['type']); ?></td>
                                    <td><?php echo esc_html($row['changes']['id']); ?></td>
                                    <td><?php echo esc_html($row['changes']['name']); ?></td>
                                    <td>
                                        <?php if (isset($row['changes']['diff'])) :  ?>
                                            <?php foreach ($row['changes']['diff'] as $name => $value) : ?>
                                                <?php echo esc_html($name); ?>: <?php echo esc_html($value); ?><br>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                    </td>
                                    <td>
                                        <?php if (count($row['errors']) > 0) : ?>
                                            <?php echo implode("<br>", array_map('esc_html', $row['errors'])); ?>
                                        <?php else : ?>
                                            なし
                                        <?php endif ?>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            <?php endif ?>
        </div>
        <div class="mb-3">
            <h2>店舗画像</h2>
            <div class="mb-3">
                ステータス: <?php echo esc_html($view_update_store_images_result_msg); ?>
            </div>
            <?php if ($view_update_store_images_result !== null) : ?>
                <?php if (!$view_update_store_images_result['success']) : ?>
                    <div class="table-responsive mt-3">
                        <table class="table table-hover text-nowrap border table-striped table-bordered mb-0">
                            <thead>
                                <tr class="column-cb check-column">
                                    <th>エラー</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($view_update_store_images_result['errors'] as $error) : ?>
                                    <tr>
                                        <td><?php echo esc_html($error); ?></td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif ?>
                <div class="table-responsive mt-3">
                    <table class="table table-hover text-nowrap border table-striped table-bordered mb-0">
                        <thead>
                            <tr class="column-cb check-column">
                                <th>種別</th>
                                <th>ファイル名</th>
                                <th>エラー</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($view_update_store_images_result['changes'] as $file) : ?>
                                <tr>
                                    <td><?php echo esc_html($file['type']); ?></td>
                                    <td><?php echo esc_html($file['filename']); ?></td>
                                    <td>
                                        <?php echo implode("<br>", array_map('esc_html', $file['errors'])); ?>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            <?php endif ?>
        </div>
        <div class="mb-3">
            <a href="<?php echo esc_url(admin_url('admin.php?page=docodoco-store-locator-store-list')); ?>" class="btn btn-primary">店舗一覧ページへ戻る</a>
        </div>
    </div>
</div>
