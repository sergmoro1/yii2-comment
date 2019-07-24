<?php 

use yii\helpers\Html;
use sergmoro1\comment\Module;

/**
 * Show ricent comments.
 */
?>

<p><?php echo $title; ?></p>
<?php if(count($comments) > 0): ?>
    <div class='post-preview'>
    <?php foreach($comments as $comment): ?>
        <div class='post-meta'>
            <?= date('d.m h:i', $comment->created_at); ?>, <?= $comment->authorLink; ?><br>
            <?= $comment->post->getTitle(); ?> 
        </div>
        &laquo;<?= Html::a(Html::encode($comment->getShortContent()), $comment->getUrl()); ?>&raquo;
    <?php endforeach; ?>
    </div>
<?php else: ?>
    <p><?= Module::t('core', 'There are no comments yet.'); ?></p>
<?php endif; ?>
