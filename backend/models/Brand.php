<?php

namespace backend\models;

use Yii;

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
class Brand extends \yii\db\ActiveRecord
{
    public $file;


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
            ['file','file','extensions'=>['jpg','png','gif']],
            ['logo','string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '商品名',
            'intro' => '介绍',
            'file' => '图标',
            'sort' => '排序',
            'status' => '是否上架',
        ];
    }
}
