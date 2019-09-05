<?php
/* @var $this yii\web\View */
/* @var $model models\Comment */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

use sergmoro1\lookup\models\Lookup;
use sergmoro1\blog\Module;

?>

<div class="comment-form">

<?php $form = ActiveForm::begin([
    'id' => 'comment-form',
    'layout' => 'horizontal',
    'fieldConfig' => [
        'horizontalCssClasses' => [
            'label' => 'col-sm-4',
            'wrapper' => 'col-sm-6',
        ],
    ],
]); ?>

    <?php echo $form->errorSummary($model); ?>

    <div class="form-group">
        <label class="control-label col-sm-4">
            <?= Module::t('core', 'Author') ?>
        </label>
        <div class="col-sm-6">
            <div class="well well-sm">
                <?= $model->author->username ?>
            </div>
        </div>
    </div>

    <?= $form->field($model, 'content')
        ->textArea([
            'rows' => 6,
            'maxlength' => 512,
        ]
    ) ?>

    <?= $form->field($model, 'status')->dropdownList(
        Lookup::items('CommentStatus')
    ) ?>

    <?= Html::submitButton(Module::t('core', 'Submit'), ['id' => 'submit-btn', 'style' => 'display: none']) ?>

    <?php ActiveForm::end(); ?>

</div>
