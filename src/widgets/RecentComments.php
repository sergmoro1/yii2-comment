<?php

namespace sergmoro1\comment\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use sergmoro1\comment\Module;

use common\models\Comment;

class RecentComments extends Widget
{
    public $viewFile = 'recentComments';
    public $title;

    public function init()
    {
        $this->title = $this->title
            ? $this->title
            : Module::t('core', 'Recent Comments');
        parent::init();
    }

    public function getRecentComments()
    {
        return Comment::findRecentComments(Yii::$app->params['recentCommentCount']);
    }

        public function run()
    {
        echo $this->render($this->viewFile, [
            'title' => $this->title,
            'comments' => $this->getRecentComments(),
        ]);
    }
}
