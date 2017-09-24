<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "menu".
 *
 * @property string $id
 * @property string $name
 * @property string $parent_id
 * @property string $url
 * @property string $sort
 */
class Menu extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'menu';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id'], 'required'],
            [['parent_id'], 'integer'],
            [['name', 'url', 'sort'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '名称',
            'parent_id' => '菜单',
            'url' => '路由',
            'sort' => '排序',
        ];
    }
//    public static function getData(){
//        $parents=self::find()->where(['=','parent_id',0])->asArray()->all();
//        $data=ArrayHelper::map($parents,'id','name');
//        $datas=ArrayHelper::merge([0=>'顶级菜单'],$data);
//        return $datas;
//    }

}
