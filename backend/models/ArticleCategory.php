<?php

namespace backend\models;


use yii\db\ActiveRecord;

/**
 * This is the model class for table "brand".
 *
 * @property integer $id
 * @property string $name
 * @property string $intro
 * @property string $logo
 * @property integer $sort
 * @property integer $status
 */
class ArticleCategory extends ActiveRecord
{


    public static function tableName()
    {
        return 'brand';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return[
            [['name','intro','sort','status'],'required'],//不能为空

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => '商品名',
            'intro' => '介绍',
            'sort' => '排序',
            'status' => '是否上架',
        ];
    }
}
