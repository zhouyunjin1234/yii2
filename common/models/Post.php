<?php

namespace common\models;

use Yii;
use yii\helpers\Html;
/**
 * This is the model class for table "post".  明确说明这是一个model class也就是对应着一张数据表
 *
 * @property integer $id
 * @property string $title
 * @property string $content
 * @property string $tags
 * @property integer $status
 * @property integer $create_time
 * @property integer $update_time
 * @property integer $author_id
 *
 * @property Comment[] $comments
 * @property Adminuser $author
 * @property Poststatus $status0
 */
class Post extends \yii\db\ActiveRecord
{
	private $_oldTags;
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'post';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'content', 'status', 'author_id'], 'required'],
            [['content', 'tags'], 'string'],
            [['status', 'create_time', 'update_time', 'author_id'], 'integer'],
            [['title'], 'string', 'max' => 128],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => Adminuser::className(), 'targetAttribute' => ['author_id' => 'id']],
            [['status'], 'exist', 'skipOnError' => true, 'targetClass' => Poststatus::className(), 'targetAttribute' => ['status' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '标题',
            'content' => '内容',
            'tags' => '标签',
            'status' => '状态',
            'create_time' => '创建时间',
            'update_time' => '修改时间',
            'author_id' => '作者',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['post_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(Adminuser::className(), ['id' => 'author_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatus0()
    {
        return $this->hasOne(Poststatus::className(), ['id' => 'status']);
    }
    
    public function beforeSave($insert)
    {
    	if(parent::beforeSave($insert))
    	{
    		if($insert)
    		{
    			$this->create_time = time();
    			$this->update_time = time();
    		}
    		else 
    		{
    			$this->update_time = time();
    		}
    		
    		return true;
    			
    	}
    	else 
    	{
    		return false;
    	}
    } 
    
    public function afterFind()
    {
    	parent::afterFind();
    	$this->_oldTags = $this->tags;
    }
    
    public function afterSave($insert, $changedAttributes)
    {
    	parent::afterSave($insert, $changedAttributes);
    	Tag::updateFrequency($this->_oldTags, $this->tags);
    }
    
    public function afterDelete()
    {
    	parent::afterDelete();
    	Tag::updateFrequency($this->tags, '');
    }
    //yii\web\UrlManager::createUrl(string|array $params) 
    public function getUrl()
    {
        return Yii::$app->urlManager->createUrl(      //Creates a URL using the given route and query parameters.
            ['post/detail','id'=>$this->id,'title'=>$this->title]);
    }
    public function getBeginning($length=288)
    {
        $tmpStr = strip_tags($this->content);      //Strip HTML and PHP tags from a string
        $tmpLen = mb_strlen($tmpStr);   
        $tmpStr = mb_substr($tmpStr,0,$length,'utf-8');
        return $tmpStr.($tmpLen>$length?'...':'');
    }
    
    public function  getTagLinks()
    {
        $links=array();
        foreach(Tag::string2array($this->tags) as $tag)
        {
            //yii\helpers\BaseHtml::a(string $text, array|string|null $url, array $options) 
            //Generates a hyperlink tag
            $links[]=Html::a(Html::encode($tag),array('post/index','PostSearch[tags]'=>$tag));
        }
        return $links;
    }
    
    public function getCommentCount()
    {
        //此时的$this->id 相当于视图中的$model->id,故可以用$this来显示相关文章数据
        return Comment::find()->where(['post_id'=>$this->id,'status'=>2])->count();
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
