<?php

use yii\helpers\Url;
use yii\helpers\Html;

use sergmoro1\comment\Module;
use sergmoro1\user\widgets\SocialCredentials;

/**
 * Comments block working example.
 * Use it for making your own.
 * 
 * Comments count, comments list, more comments button and new comment form.
 * 
 * @author Sergey Morozov <sergey@vorst.ru>
 */
?>

<div id="comments" class="post-comments">
    
    <!-- A first of comments -->
	<?php if(($commentCount = $model->commentCount) >= 1): ?>
	<div class="comment-section">
		<h3>
			<?= $commentCount > 1 
				? Module::t('core', 'Comments') 
				: Module::t('core', 'One comment'); 
			?>
		</h3>

        <ul class="comment-list">
            <?= $this->render('_comments', [
                'post' => $model,
                'comments' => $model->comments,
                'replyButtonClass' => $replyButtonClass,
            ]); ?>
        </ul>
        
	</div>
	<?php endif; ?>
    
    <!-- Place "More comments" button if more comments exist -->
	<?php if($commentCount > Yii::$app->params['commentsPerPage']): ?>
	<p class="text-right">
		<a href="javascript:;" class="pure-button load-more-btn" onclick=""
            data-href="<?= Url::to(['post/more-comments']) ?>" 
            data-slug="<?= $model->slug ?>"
            data-offset="<?= Yii::$app->params['commentsPerPage'] ?>">
            <span title="<?= Module::t('core', 'More comments') ?>">
                <i class="fas fa-comments"></i> ...
            </span>
		</a>
	</p>
	<?php endif; ?>
    
    <a name="leave-comment">
	<div class="leave-comment"> <!--leave comment-->
		<h3 class="reply-heading"><?= Module::t('core', 'Leave a comment'); ?></h3>

        <?php if(!Yii::$app->user->isGuest): ?>
		<div class="answer">
			<h4></h4>
			<div class="apponent"></div>
			<h3><?= Module::t('core', 'Your answer') ?></h3>
		</div>

		<?= $this->render('@frontend/views/comment/_form', [
            'parent' => common\models\Comment::parentModelName($comment->model),
            'model' => $comment,
        ]); ?>
        <?php endif; ?>
	</div>
	</a>
    <?php if(Yii::$app->user->isGuest): ?>
        <p class=="need-to register">
            <?= Module::t('core', 'Only authorized users can leave comments. Please {log-in} or pass a {registration}.' , [
                'log-in' => Html::a(Module::t('core', 'log in'), ['site/login']),
                'registration' => Html::a(Module::t('core', 'registration'), ['site/signup']),
            ]) ?>

            <?= SocialCredentials::widget() ?>
        </p>
    <?php endif; ?>

</div><!-- comments -->
