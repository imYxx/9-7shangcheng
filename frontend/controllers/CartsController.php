<?php

namespace frontend\controllers;

use frontend\models\Address;
use frontend\models\Cart;
use frontend\models\Goods;
use frontend\models\Order;
use frontend\models\OrderGoods;
use yii\console\Exception;
use yii\filters\AccessControl;

class CartsController extends \yii\web\Controller
{
    public $enableCsrfValidation=false;
    public function actionIndex()
    {
        return $this->render('index');
    }

    public  function actionOrder(){
            //收货人信息
        $member_id = \Yii::$app->user->identity->id;
        $address = Address::find()->where(['member_id'=>$member_id])->all();
        //送货方式
        $deliveries =Order::$deliveries;
        //付款方式
        $payments=Order::$payments;
        //商品信息从数据库中读取
        $model = Cart::find()->where(['member_id'=>$member_id])->all();
        //var_dump($model);die;
        $goods_id = [];
        $carts = [];
        foreach($model as $cart){
            $goods_id[]=$cart['goods_id'];
            $carts[$cart['goods_id']]=$cart['amount'];
        }
        //var_dump($carts);exit;
        $goods=Goods::find()->where(['in','id',$goods_id])->asArray()->all();
        return $this->renderPartial('order',['address'=>$address,'goods'=>$goods,'carts'=>$carts,'deliveries'=>$deliveries,'payments'=>$payments]);
    }


    //提交订单
    public function actionAddOrder($address_id,$delivery_id,$payment_id){
        //实例化模型
        $model=new Order();
        //开始事物
        $transaction=\Yii::$app->db->beginTransaction();
        $user_id=\Yii::$app->user->identity->id;
        $carts=Cart::find()->where(['member_id'=>$user_id])->all();
        if($carts==null){
            return json_encode('NULL');
        }
        try{
            //处理数据获取地址信息保存到数据表
            $cart=Cart::findOne(['member_id'=>$user_id]);
            $goods_id= $cart->goods_id;
            $logo=Goods::findOne(['id'=>$goods_id]);
            $address=Address::findOne(['member_id'=>$user_id,'id'=>$address_id]);
            $model->logo=$logo->logo;
            $model->member_id=$user_id;
            $model->name=$address->name;
            $model->province=$address->province;
            $model->city=$address->city;
            $model->area=$address->area;
            $model->address=$address->address;
            $model->tel=$address->tel;
            //获取配送方式
            $model->delivery_id=$delivery_id;
            $model->delivery_name = Order::$deliveries[$delivery_id]['name'];
            $model->delivery_price = Order::$deliveries[$delivery_id]['price'];
            //付款方式
            $model->payment_id=$payment_id;
            $model->payment_name=Order::$payments[$payment_id]['name'];
            $model->total=0;
            $model->status=1;
            $model->create_time=time();
            $model->save(false);
            //获取购物车数据处理订单商品表数据
            $total=0;
            foreach($carts as $cart){
                $goods=Goods::findOne(['id'=>$cart->goods_id]);
                $order_goods=new OrderGoods();
                if($cart->amount<=$goods->stock){
                    $order_goods->order_id=$model->id;
                    $order_goods->goods_id=$goods->id;
                    $order_goods->goods_name=$goods->name;
                    $order_goods->logo=$goods->logo;
                    $order_goods->price=$goods->shop_price;
                    $order_goods->amount=$cart->amount;
                    $order_goods->total=$cart->amount*$goods->shop_price;
                    //保存
                    $order_goods->save();
                    //改变订单表的统计金额,将购买的每个商品的价钱相加
                    $total+=$order_goods->total;
                    //下单成功后改变商品库存
                    $goods->stock-=$cart->amount;
                    $goods->save();
                    //下单成功后清除购物车数据
                    $cart->delete();
                }else{
                    //库存状况，抛出异常
                    throw new Exception('商品库存不足，无法继续下单，请修改购物车商品数量');
                }
            }
            //订单生成成功后，计算订单表总金额
            $model->total=$total;
            $model->update(false);
            //提交事务
            $transaction->commit();
            return json_encode('success');
        }catch(Exception $e){//捕获异常
            //如果异常回滚数据
            $transaction->rollBack();
        }
        var_dump($model->getErrors());exit;

    }


         //结算成功
    public function actionEnd(){
        return $this->renderPartial('scuess');
    }


         //查看订单状态
    public function actionShowOrder()
    {
        $member_id=\Yii::$app->user->id;
        $orders =Order::find()->where(['member_id'=>$member_id])->all();
        return $this->renderPartial('check',['orders'=>$orders]);
    }


         //判断是否登陆
    public function behaviors()
    {
        return [
            'ACF'=>[
                'class'=>AccessControl::className(),
                'only'=>['order','add-order','show-order'],//哪些操作需要使用该过滤器
                'rules'=>[
                    [
                        'allow'=>true,//判断是否允许true允许
                        'actions'=>['order','add-order','show-order'],//授权的操作
                        'roles'=>['@'],//@表示已认证用户(已登录的用户)
                    ],
                ]
            ]
        ];
    }

}
