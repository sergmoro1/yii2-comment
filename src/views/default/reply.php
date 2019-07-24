<?php
/* @var $this yii\web\View */
/* @var $model models\Comment */

use yii\helpers\Html;
use sergmoro1\blog\Module;

$this->title = Module::t('core', 'Reply');
$this->params['breadcrumbs'][] = ['label' => Module::t('core', 'Comments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="comment-reply">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <div class="row"><div class="form-group">
        <label class="control-label col-sm-4 text-right">
            <?= $comment->author->username . ' ' . Module::t('core', 'wrote') ?>
        </label>
        <div class="col-sm-6">
            <div class=" well well-sm">
                <?= $comment->content ?>
            </div>
        </div>
    </div>
    </div>
    
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

