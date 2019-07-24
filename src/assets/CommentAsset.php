<?php
/**
 * @author <sergmoro1@ya.ru>
 * @license MIT
 */

namespace sergmoro1\comment\assets;

use yii\web\AssetBundle;

class CommentAsset extends AssetBundle
{
    public $sourcePath = '@vendor/sergmoro1/yii2-comment/src/assets';

    public $css = [
    ];
    public $js = [
        'js/comment.min.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
