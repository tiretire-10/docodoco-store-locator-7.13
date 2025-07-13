<?php

namespace DocodocoStoreLocator\Admin;

if ( ! defined( 'ABSPATH' ) ) exit;

class Introduce {

    public function __construct() {
    }

    public function admin_menu_callback() {
        wp_enqueue_style('docosl-bootstrap', DOCOSL_URL_PATH . 'admin/css/bootstrap.min.css', array(), DOCOSL_VERSION);

        include DOCOSL_PLUGIN_PATH . '/admin/views/introduce.php';
    }
}
