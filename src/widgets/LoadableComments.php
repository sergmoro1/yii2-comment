<?php

namespace sergmoro1\comment\widgets;

use yii\base\Widget;

use sergmoro1\blog\Module;
use sergmoro1\comment\assets\CommentAsset;

/**
 * Loadable comments widget.
 * 
 * Both views - views/loadableComments and views/_comments can be used as is
 * but can be rediclared if needed.
 * 
 * @author Sergey Morozov <sergey@vorst.ru>
 */

class LoadableComments extends Widget
{
    public $viewFile = 'loadableComments';
    public $need_js = true; 
    public $model;
    public $comment;
    public $replyButtonClass = 'btn btn-default';

    public function init()
    {
        parent::init();
        if($this ->need_js) {
            CommentAsset::register($this->view);
        }
    }

    public function run()
    {
        echo $this->render($this->viewFile, [
            'model' => $this->model,
            'comment' => $this->comment,
            'replyButtonClass' => $this->replyButtonClass,
        ]);
    }
}
