<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "order".
 *
 * @property string $id
 * @property integer $member_id
 * @property string $name
 * @property string $province
 * @property string $city
 * @property string $area
 * @property string $address
 * @property string $tel
 * @property integer $delivery_id
 * @property string $delivery_name
 * @property double $delivery_price
 * @property integer $payment_id
 * @property string $payment_name
 * @property string $total
 * @property integer $status
 * @property string $trade_no
 * @property integer $create_time
 */
class Order extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    //自定义送货方式
    public static $deliveries=[
        1=>['name'=>'顺丰快递','price'=>'25','detail'=>'速度快，一般情况下隔天可以到到，偏远乡镇不支持配送','default'=>'1'],
        2=>['name'=>'圆通快递','price'=>'12','detail'=>'速度比较快，收货2-5天可以到达，偏远乡镇不支持配送','default'=>'0'],
        3=>['name'=>'中通快递','price'=>'10','detail'=>'速度一般，价格便宜，偏远乡镇不支持配送','default'=>'0'],
        4=>['name'=>'EMS','price'=>'15','detail'=>'速度一般，网点多，乡镇可达','default'=>'0'],



    ];
    //自定义支付方式
    public static $payments=[
      1=>['name'=>'货到付款','detail'=>'送货上门后再收款，支持现金、POS机刷卡、支票支付','default'=>'1'],
      2=>['name'=>'微信支付','detail'=>'即使到帐','default'=>'0'],
      3=>['name'=>'支付宝支付','detail'=>'即使到帐','default'=>'0'],
      4=>['name'=>'快捷支付','detail'=>'银行卡，信用卡支付','default'=>'0'],
      5=>['name'=>'朋友代付','detail'=>'将连接发送给好友，由好友为您支付，连接有效期5天','default'=>'0'],

    ];
    public static function tableName()
    {
        return 'order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'delivery_id', 'payment_id', 'status', 'create_time'], 'integer'],
            [['delivery_price', 'total'], 'number'],
            [['name', 'province', 'city', 'area', 'address', 'delivery_name', 'payment_name', 'trade_no'], 'string', 'max' => 255],
            [['tel'], 'string', 'max' => 11],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => 'Member ID',
            'name' => 'Name',
            'province' => 'Province',
            'city' => 'City',
            'area' => 'Area',
            'address' => 'Address',
            'tel' => 'Tel',
            'delivery_id' => 'Delivery ID',
            'delivery_name' => 'Delivery Name',
            'delivery_price' => 'Delivery Price',
            'payment_id' => 'Payment ID',
            'payment_name' => 'Payment Name',
            'total' => 'Total',
            'status' => 'Status',
            'trade_no' => 'Trade No',
            'create_time' => 'Create Time',
        ];
    }
}
