/*
 * Comments are linked in the thread. 
 * Last comment in a thread can be replied. 
 * 
 * @author sergmoro1@ya.ru
 * @license - MIT
 * 
 * Somewhere in a views/index.php
 * 
 * $app = [
 *   'actions' => [
 *       'getContent' => Url::toRoute(['post/get-content']),
 *   ],
 *   'buttons' => [],
 *   'comment' => [
 *       'message' => Yii::t('app', 'Please, fill in field "{field}".', ['field' => $model->getAttributeLabel($attribute)]),
 *   ],
 * ];
 * $this->registerJS('var appCanComments=' . json_encode($app), \yii\web\View::POS_READY);
 *
 * Approximate DOM structure.
 * 
 * <div id="comments">
 *    <div class="comment-section">
 *        <ul class="comment-list">
 *            <li class="comment">
 *                <span class="indention">--</span>
 *                <img src="...">
 *                <span class="content">
 *                    <span class="avatar-name"></span>
 *                 </span>
 *                 <a href="#leave-comment" data-comment-thread="" class="reply-btn"></a>
 *            </li>
 *            ...
 *        </ul>
 *    </div>
 *    <a class="load-more-btn" data-href="" data-slug="" data-offset=""></a>
 *    <a name="leave-comment">
 *        <div class="leave-comment">
 *           <h3 class="reply-heading">Leave a comment</h3>
 *           <div class="answer">
 *               <h4></h4>
 *               <div class="apponent"></div>
 *               <h3>Your answer</h3>
 *           </div>
 *           <?= $this->render('@frontend/views/comment/_form', [
 *               'model' => $comment,
 *           ]);?>
 *        </div>
 *    </a>
 * </div>
 */

var appCanComments = appCanComments || {buttons:{}};

/*
 * Place comment content that need to be answered
 * before an answer form.
 * 
 */
 $(function () {

    var app = appCanComments;
    app.buttons.reply = function () {
        var that = $(this);
        // change the thread
        $('#comment-form #comment-thread').val(that.attr('data-comment-thread'));
        
        // find comment that need replying to
        var comment = that.closest('li.comment');
        // find content of the comment
        var content = comment.find('.content');
        // copy the comment content before an answer
        var answer = $('.leave-comment .answer');
        answer.find('.apponent').html(content.html());

        $('.leave-comment .reply-heading').hide();
        $('.leave-comment .answer').show();
    }
    $('#comments .reply-btn').on('click', app.buttons.reply);

    /* 
     * Load more comments and place it after loaded before.
     * @param appCanComments.comment.limit
     */
    app.buttons.loadMore = function() {
        var that = $(this);
        var btn = that;
        // if no more comments then button was disabled
        if(btn.attr('disabled') === 'disabled') {
            return false;
        }
        $.ajax({
            // url and params were saved in a link
            url: that.data('href'),
            data: {slug: that.data('slug'), offset: that.data('offset')},
            async: false,
            success: function(response) {
                if(response == false) {
                    // disable button
                    btn.attr('disabled', 'disabled')
                } else {
                    // append next comments to the list
                    $('.comment-section .comment-list').append(response);
                    // change offset to the next portion
                    btn.data('offset', Number(btn.data('offset')) + Number(btn.data('step')));
                }
            },
            error: function(e) {
                // disable button
                btn.attr('disabled', 'disabled');
            }
        });
        // bind new reply buttons
        $('#comments .reply-btn').on('click', app.buttons.reply);
        return false;
    };
    $('#comments .load-more-btn').on('click', app.buttons.loadMore);
    appCanComments = app;
});
