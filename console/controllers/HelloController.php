<?php
namespace  console\controllers;

use yii\console\Controller;
use common\models\Post;
class HelloController extends Controller
{
    
                    public $rev;
	
	public function options()        //覆盖此方法，返回所有属性
	{
		return ['rev'];
	}
	
	public function optionAliases()             //选项别名与简化
	{
		return['r'=>'rev'];
	}
	
	public function actionIndex()
	{
		if($this->rev == 1)
		{
			echo strrev("Hello World!")."\n";
		}
		else
		{
			echo "Hello World!\n";	
		}
	}
          public function actionList()
	{
		$posts = Post::find()->all();
		
		foreach ($posts as $aPost)
		{
			echo ($aPost['id']. " - ".$aPost['title'] ."\n");
		}
	}
	
	public function actionWho($name)
	{
		echo ("Hello ". $name . "!\n");
	}
	
	public function actionBoth($name,$another)
	{
		echo ("Hello ".$name." and ". $another ."!\n");
	}
	
	public function actionAll(array $names)
	{
		var_dump($names);
	}
	
}