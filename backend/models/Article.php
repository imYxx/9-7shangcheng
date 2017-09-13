<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "article".
 *
 * @property string $id
 * @property string $name
 * @property string $intro
 * @property integer $article_category_id
 * @property integer $sort
 * @property integer $status
 * @property integer $create_time
 */
class Article extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['intro'], 'string'],
            [['article_category_id', 'sort', 'status', 'create_time'], 'integer'],
            [['name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => '文章名称',
            'intro' => '文章简介',
            'article_category_id' => '文章分类',
            'content'=>'文章内容',
            'sort' => '文章排序',
            'status' => '是否上架',
            'create_time' => '创建时间',
        ];
    }
    public function getarticle_category(){
        return $this->hasOne(ArticleCategory::className(),['id'=>'article_category_id']);
    }
}
