<?php

namespace backend\models;

use creocoder\nestedsets\NestedSetsBehavior;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "goods".
 *
 * @property integer $id
 * @property string $name
 * @property string $sn
 * @property string $logo
 * @property integer $goods_category_id
 * @property integer $brand_id
 * @property string $market_price
 * @property string $shop_price
 * @property integer $stock
 * @property integer $is_on_sale
 * @property integer $status
 * @property integer $sort
 * @property integer $create_time
 * @property integer $view_times
 */
class Goods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */

    public static function tableName()
    {
        return 'goods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_category_id', 'brand_id', 'stock', 'is_on_sale', 'status', 'sort'], 'integer'],
            [['market_price', 'shop_price'], 'number'],
            [['name', 'sn', 'logo'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '品牌',
            'name' => '商品名',
            'sn' => '货号',
            'logo' => '图片',
            'goods_category_id' => '商品分类',
            'brand_id' => '品牌分类',
            'market_price' => '市场售价',
            'shop_price' => '商场售价',
            'stock' => '库存',
            'is_on_sale' => '是否上架',
            'status' => '状态',
            'sort' => '排序',
            'create_time' => '添加时间',
            'view_times' => '阅览次数',
        ];
    }
    //获取商品分类的ztree数据
//    public static function getZNodes(){
//        $model =new GoodsCategory();
//        $goodsCategories =  GoodsCategory::find()->select(['id','parent_id','name'])->asArray()->all();
//
//    }
//    public function behaviors() {
//        return [
//            'tree' => [
//                'class' => NestedSetsBehavior::className(),
//                'treeAttribute' => 'tree',//这里必须打开,支持多棵树,因为分类有多个一级分类
//                // 'leftAttribute' => 'lft',
//                // 'rightAttribute' => 'rgt',
//                // 'depthAttribute' => 'depth',
//            ],
//        ];
//    }
    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }
    public static function find()
    {
        return new CategoryQuery(get_called_class());
    }

    public function getBrand(){
        return $this->hasOne(Brand::className(),['id'=>'brand_id']);
    }

    public function getGoodsCategory(){
        return $this->hasOne(GoodsCategory::className(),['id'=>'goods_category_id']);
    }


}
