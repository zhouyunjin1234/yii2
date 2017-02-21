<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Post;

/**
 * PostSearch represents the model behind the search form about `common\models\Post`.
 */
class PostSearch extends Post
{
    //可覆盖(重写) yii\base\Model::attributes() 来定义属性(常用来增加属性)
	public function attributes()
	{
	    //array_merge(array $array1, array $_)   合并数组
		return array_merge(parent::attributes(),['authorName']);
	}
    /**
     * @inheritdoc
     */
	//必须覆盖此方法，不然就会执行post中的rules,也可以采用场景(感觉比较麻烦)
    public function rules()
    {
        return [
            [['id', 'status', 'create_time', 'update_time', 'author_id'], 'integer'],          
            [['title', 'content', 'tags','authorName'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Post::find();            //此处提供所有数据

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'pagination' => ['pageSize'=>5],
        	'sort'=>[
        			'defaultOrder'=>[
        					'id'=>SORT_DESC,        			
        			],
        			//'attributes'=>['id','title'],
        	],
        ]);
  
        //块赋值
        $this->load($params);

        if (!$this->validate()) {
             // uncomment the following line if you do not want to return any records when validation fails
             //$query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        //Adds an additional WHERE condition to the existing one but ignores [[isEmpty()|empty operands]].
        $query->andFilterWhere([
            //'id' => $this->id,
        	'post.id' => $this->id,
            'status' => $this->status,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
            'author_id' => $this->author_id,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'tags', $this->tags]);
        //为增加的属性添加搜索功能
        $query->join('INNER JOIN','adminuser','post.author_id = adminuser.id');
        $query->andFilterWhere(['like','adminuser.nickname',$this->authorName]);
        //为增加的属性增加排序功能
        $dataProvider->sort->attributes['authorName'] = 
        [
        	'asc'=>['Adminuser.nickname'=>SORT_ASC],
        	'desc'=>['Adminuser.nickname'=>SORT_DESC],
        ];                             
        return $dataProvider;
    }
}

