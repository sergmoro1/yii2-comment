<?php

namespace sergmoro1\comment\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\ForbiddenHttpException;

use common\models\User;
use common\models\Comment;
use sergmoro1\comment\Module;
use sergmoro1\modal\controllers\ModalController;
use sergmoro1\comment\models\CommentSearch;

/**
 * DefaultController implements the CRUD actions for Comment model.
 * 
 * @author Seregey Morozov <sergey@vorst.ru>
 */
class DefaultController extends ModalController
{
    public function newModel() { return new Comment(); }
    public function newSearch() { return new CommentSearch(); }

    public function init()
    {
        parent::init();

        Yii::$app->mailer->setViewPath('@vendor/sergmoro1/yii2-comment/src/mail');
    }

    /**
     * Reply on a comment.
     * If replay is successful, the browser will be redirected to the page from witch was request.
     * @param integer $id
     * @return mixed
     */
    public function actionReply($id)
    {
        $comment = $this->findModel($id);
        if (!Yii::$app->user->can('replyStranger', ['comment' => $comment]))
            return $this->alert(Yii::t('app', 'Access denied.'));
        
        // fill in a new Comment
        $model = $this->newModel();
        $model->model = $comment->model;
        // this is reply to an existing comment
        $model->parent_id = $comment->parent_id;
        // and it is the same thread
        $model->thread = $comment->thread;
        // comment of a current user
        $model->user_id = Yii::$app->user->id;
        // comment approved by default
        $model->status = Comment::STATUS_APPROVED;
        // only the last comment in the thread can be replied
        $model->last = 1;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // the comment to which we reply must be approved
            if ($comment->status == Comment::STATUS_PENDING) {
                $comment->status = Comment::STATUS_APPROVED;
                $comment->save(false);
            }
            return YII_DEBUG 
                ? $this->redirect(['index'])
                : $this->redirect(Yii::$app->request->referrer);
        } else {
            return $this->renderAjax('reply', [
                'model' => $model,
                'comment' => $comment,
            ]);
        }
    }

    /**
     * Deletes an existing model.
     * If deletion is successful, the browser will be redirected to the page from witch was request.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('delete'))
            throw new ForbiddenHttpException(Module::t('core', 'Access denied.'));

        $model = $this->findModel($id);
        $model->delete();
        // mark last comment in a thread
        Yii::$app->db->createCommand("UPDATE {{%comment}} SET last=1 WHERE thread='{$model->thread}' ORDER BY created_at DESC LIMIT 1")->execute();

        return YII_DEBUG 
            ? $this->redirect(['index'])
            : $this->redirect(\Yii::$app->request->referrer);
    }
}
