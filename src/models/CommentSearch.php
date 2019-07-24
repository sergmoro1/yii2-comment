<?php

namespace sergmoro1\comment\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use common\models\User;
use common\models\Post;
use common\models\Comment;

/**
 * CommentSearch class.
 *
 * @author Seregey Morozov <sergey@vorst.ru>
 *    
 */
class CommentSearch extends Comment
{
    public function rules()
    {
        // only fields in rules() are searchable
        return [
            [['id', 'model', 'status'], 'integer'],
            [['content'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Comment::find()
            ->orderBy(['thread' => SORT_DESC, 'created_at' => SORT_ASC]);
        if(Yii::$app->user->identity->group == User::GROUP_AUTHOR)
        {
            // only comments for User's posts
            $userPosts = [];
            foreach(Post::find()
                ->select(['id'])
                ->where(['user_id' => Yii::$app->user->id])
                ->all() as $post)
                $userPosts[] = $post->id;
            // post_id IN $userPosts
            $query->andFilterWhere(['parent_id' => $userPosts]);
        }
 
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => \Yii::$app->params['recordsPerPage'],
            ],
            'sort' => false,
        ]);

        // load the search form data and validate
        if (!($this->load($params) && $this->validate())) {
            if(isset($_GET['author'])) {
                $this->author = $_GET['author'];
            } else
                return $dataProvider;
        }
        
        // adjust the query by adding the filters
        $query->andFilterWhere(['id' => $this->id])
            ->andFilterWhere(['model' => $this->model])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['status' => $this->status]);

        return $dataProvider;
    }
}
