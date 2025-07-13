<?php

namespace DocodocoStoreLocator;

if ( ! defined( 'ABSPATH' ) ) exit;

class WPFilter
{
    public static function storeimages_upload_dir($dir)
    {
        $new_dir = array(
            'path'   => $dir['basedir'] . '/' . DOCOSL_PLUGIN . '/store-images',
            'url'    => $dir['baseurl'] . '/' . DOCOSL_PLUGIN . '/store-images',
            'subdir' => '/' . DOCOSL_PLUGIN . '/store-images',
        );

        return $new_dir + $dir;
    }

    public static function storeimages_unique_filename($dir, $filename, $ext)
    {
        return $filename;
    }
}
