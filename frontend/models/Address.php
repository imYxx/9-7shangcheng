<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "address".
 *
 * @property string $id
 * @property string $name
 * @property integer $member_id
 * @property string $tel
 * @property integer $status
 */
class Address extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public $location_p;
    public $location_c;
    public $location_a;
    public $location_b;
   // public $name;
    public static function tableName()
    {
        return 'address';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'status','tel'], 'integer'],
            [['address','province','city','area'], 'string', 'max' => 255],
            [['location_a','location_p','location_c','name','location_b'], 'string', 'max' => 11],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'address' => '地址',
            'member_id' => 'Member ID',
            'tel' => 'Tel',
            'status' => 'Status',
            'name' => 'name',
            'province' => '省份',
            'city' => '市级',
            'area' => '区县',
        ];
    }

    public  static function getAddress(){
        $id = Yii::$app->user->id;
        $address = self::find()->where(['member_id'=>$id])->all();
        return $address;
    }
}
