<?php
namespace sergmoro1\comment\models;

use Yii;
use yii\helpers\Html;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

use sergmoro1\rukit\behaviors\FullDateBehavior;
use sergmoro1\lookup\models\Lookup;
use sergmoro1\comment\Module;

use common\models\User;
use common\models\Comment;

/**
 * BaseComment model class.
 *
 * @author Seregey Morozov <sergey@vorst.ru>
 */

class BaseComment extends ActiveRecord
{
    /**
     * The followings are the available columns in table 'comment':
     * @var integer $id
     * @var integer $model
     * @var integer $parent_id
     * @var integer $user_id
     * @var string  $content
     * @var integer $status
     * @var string  $thread
     * @var boolean $last
     * @var integer $created_at
     * @var integer $updated_at
     */

    const MAX_COMMENT_LENGTH = 1024;
    
    const EVENT_JUST_ADDED = 'just_added_new_comment';

    const STATUS_PENDING = 1;
    const STATUS_APPROVED = 2;
    const STATUS_ARCHIVED = 3;

    public function init()
    {
        parent::init();
        $this->on(self::EVENT_JUST_ADDED, [$this, 'notifyResponsible']);
    }

    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return '{{%comment}}';
    }

    public function behaviors()
    {
        return [
            ['class' => TimestampBehavior::className()],
            ['class' => FullDateBehavior::className()],
         ];
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            [['model', 'parent_id', 'user_id', 'content'], 'required'],
            ['thread', 'string', 'max' => 32],
            ['status', 'in', 'range' => self::getStatuses()],
            ['status', 'default', 'value' => self::STATUS_PENDING],
            [['model', 'parent_id', 'user_id'], 'integer'],
            ['last', 'boolean'],
            ['last', 'default', 'value' => true],
            ['content', 'string', 'max' => self::MAX_COMMENT_LENGTH],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'model'         => Module::t('core', 'Model'),
            'parent_id'     => Module::t('core', 'Parent'),
            'user_id'       => Module::t('core', 'Name'),
            'thread'        => Module::t('core', 'Thread'),
            'status'        => Module::t('core', 'Status'),
            'content'       => Module::t('core', 'Content'),
            'last'          => Module::t('core', 'Last'),
        );
    }

    /**
     * Get statuses.
     * @return array
     */
    public static function getStatuses() {
        return [
            self::STATUS_PENDING,
            self::STATUS_APPROVED,
            self::STATUS_ARCHIVED, 
        ];
    }

    /**
     * Get Url for the model to this comment for.
     * 
     * @param model that this comment belongs to.
     * @return string the permalink URL for this comment
     */
    public function getUrl($model = null)
    {
        if($model === null) {
            $m = $this->parentModelName($this->model);
            $model = $this->$m;
        }
        return $model ? $model->url . '#c' . $this->id : '';
    }

    /**
     * Get link with title of the model to this comment for.
     * 
     * @return string
     */
    public function getTitleLink()
    {
		$m = $this->parentModelName($this->model);
		return $this->$m->getTitleLink();
    }

    /**
     * Approves a comment.
     */
    public function approve()
    {
        static::save(['status' => Comment::STATUS_APPROVED]);
    }
    
    /**
     * Comment can be answered if User is not a guest and
     * this is the last comment in a thread and
     * User is a commentator and
     * last comment not belongs to User
     * User begun the thread.
     * 
     * @return boolean
     */
    public function canBeAnswered() {
        $countInThread = Comment::find()
            ->where(['thread' => $this->thread, 'user_id' => Yii::$app->user->id])
            ->count();
        return !Yii::$app->user->isGuest && // you are not a guest
            $this->last && // this is a last comment in a thread
            Yii::$app->user->identity->group == User::GROUP_COMMENTATOR && // you are a commentator
            $this->user_id != Yii::$app->user->id && // last comment not yours
            $countInThread > 0; // you begin this thread
            
    }
    
    /**
     * Comment can be long and it not suits to output in a list of comments.
     * So, comment can be cutted down.
     * 
     * @param integer char count to cut down
     * @return string
     */
    public function getPartContent($limit = 500)
    {
        $out = '';
        $words = explode(' ', $this->content);
        mb_internal_encoding('UTF-8');
        foreach($words as $word) {
            if(mb_strlen($out) <= $limit)
                $out .= $word . ' ';
            else {
                $out .= ' ...';
                break;
            }
        }
        return $out;
    }

    /**
     * @return object the current comment's author (user)
     */
    public function getAuthor()
    {
        return User::findOne($this->user_id);
    }

    /**
     * @return string the hyperlink display for the current comment's author
     */
    public function getAuthorLink()
    {
        if(!empty($this->url))
            return Html::a($this->author->name, $this->url);
        else
            return $this->author->name;
    }

    /**
     * @return integer the number of comments that are pending approval
     */
    public function getPendingCommentCount()
    {
        return Comment::find()
            ->where(['status' => self::STATUS_PENDING])
            ->count();
    }

    /**
     * @param integer the maximum number of comments that should be returned
     * @return array the most recently added comments
     */
    public function findRecentComments($limit = 5)
    {
        return Comment::find()
            ->innerJoin('user', 'comment.user_id = user.id')
            ->where(['comment.status' => self::STATUS_APPROVED, 'group' => User::GROUP_COMMENTATOR])
            ->orderBy('created_at DESC')
            ->limit($limit)
            ->all();
    }

    /**
     * Get date as a phrase.
     * @return string.
     */
    public function getDate()
    {
        $now = time();
        if(($hours = floor(($now - $this->created_at) / 3600)) <= 24)
            return $hours == 1 ? Module::t('core', 'one hour ago') : $hours . Module::t('core', 'hours ago');
        elseif($hpurs <= 48)
            return Module::t('core', 'yesterday');
        else
            return $this->getFullDate('created_at');
    }

    /**
     * This is invoked before the record is saved.
     * @return boolean whether the record should be saved.
     */
    public function beforeSave($insert)
    {
        if(parent::beforeSave($insert))
        {
            if($insert)
            {
                // all comments in the thread can't be replied except the last, so change previous last to 0
                Yii::$app->db->createCommand("UPDATE {{%comment}} SET last=0 WHERE thread='{$this->thread}' AND last=1")->execute();
                $this->created_at = time();
            }
            return true;
        }
        else
            return false;
    }
    
    /**
     * Send email for responsible person - receiver. 
     * 
     * @param  integer event
     * @return boolean
     */
    public function notifyResponsible($event)
    {
        $from = [Yii::$app->params['email']['from'] => 'noreply'];
        $receiver = Yii::$app->params['email']['receiver']['comment'];
        $to = Yii::$app->params['email']['to'][$receiver];
        return Yii::$app->mailer->compose()
            ->setTo($to)
            ->setFrom($from)
            ->setSubject(
                $this->author->name . ' <' . $this->author->email . '>, ' .
                Module::t('core', 'new comment for') . ' - ' . Lookup::item('CommentFor', $this->model) . '.' 
            )
            ->setTextBody($this->content)
            ->send();
    }
}
