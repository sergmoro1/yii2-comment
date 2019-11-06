<?php
/* @var $this yii\web\View */
/* @var $model models\Comment */

use yii\helpers\Html;
use sergmoro1\blog\Module;

$this->title = Module::t('core', 'Update');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Comments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="comment-update">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
