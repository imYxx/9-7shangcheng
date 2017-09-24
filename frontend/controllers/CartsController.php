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
            //�ջ�����Ϣ
        $member_id = \Yii::$app->user->identity->id;
        $address = Address::find()->where(['member_id'=>$member_id])->all();
        //�ͻ���ʽ
        $deliveries =Order::$deliveries;
        //���ʽ
        $payments=Order::$payments;
        //��Ʒ��Ϣ�����ݿ��ж�ȡ
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


    //�ύ����
    public function actionAddOrder($address_id,$delivery_id,$payment_id){
        //ʵ����ģ��
        $model=new Order();
        //��ʼ����
        $transaction=\Yii::$app->db->beginTransaction();
        $user_id=\Yii::$app->user->identity->id;
        $carts=Cart::find()->where(['member_id'=>$user_id])->all();
        if($carts==null){
            return json_encode('NULL');
        }
        try{
            //�������ݻ�ȡ��ַ��Ϣ���浽���ݱ�
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
            //��ȡ���ͷ�ʽ
            $model->delivery_id=$delivery_id;
            $model->delivery_name = Order::$deliveries[$delivery_id]['name'];
            $model->delivery_price = Order::$deliveries[$delivery_id]['price'];
            //���ʽ
            $model->payment_id=$payment_id;
            $model->payment_name=Order::$payments[$payment_id]['name'];
            $model->total=0;
            $model->status=1;
            $model->create_time=time();
            $model->save(false);
            //��ȡ���ﳵ���ݴ�������Ʒ������
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
                    //����
                    $order_goods->save();
                    //�ı䶩�����ͳ�ƽ��,�������ÿ����Ʒ�ļ�Ǯ���
                    $total+=$order_goods->total;
                    //�µ��ɹ���ı���Ʒ���
                    $goods->stock-=$cart->amount;
                    $goods->save();
                    //�µ��ɹ���������ﳵ����
                    $cart->delete();
                }else{
                    //���״�����׳��쳣
                    throw new Exception('��Ʒ��治�㣬�޷������µ������޸Ĺ��ﳵ��Ʒ����');
                }
            }
            //�������ɳɹ��󣬼��㶩�����ܽ��
            $model->total=$total;
            $model->update(false);
            //�ύ����
            $transaction->commit();
            return json_encode('success');
        }catch(Exception $e){//�����쳣
            //����쳣�ع�����
            $transaction->rollBack();
        }
        var_dump($model->getErrors());exit;

    }


         //����ɹ�
    public function actionEnd(){
        return $this->renderPartial('scuess');
    }


         //�鿴����״̬
    public function actionShowOrder()
    {
        $member_id=\Yii::$app->user->id;
        $orders =Order::find()->where(['member_id'=>$member_id])->all();
        return $this->renderPartial('check',['orders'=>$orders]);
    }


         //�ж��Ƿ��½
    public function behaviors()
    {
        return [
            'ACF'=>[
                'class'=>AccessControl::className(),
                'only'=>['order','add-order','show-order'],//��Щ������Ҫʹ�øù�����
                'rules'=>[
                    [
                        'allow'=>true,//�ж��Ƿ�����true����
                        'actions'=>['order','add-order','show-order'],//��Ȩ�Ĳ���
                        'roles'=>['@'],//@��ʾ����֤�û�(�ѵ�¼���û�)
                    ],
                ]
            ]
        ];
    }

}
