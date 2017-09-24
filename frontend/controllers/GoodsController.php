<?php

namespace frontend\controllers;

use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsGallery;
use backend\models\GoodsIntro;
use frontend\components\SphinxClient;
use frontend\models\Cart;
use yii\helpers\ArrayHelper;
use yii\web\Cookie;
use yii\web\NotFoundHttpException;

class GoodsController extends \yii\web\Controller
{
    public $layout = false;
    public $enableCsrfValidation = false;


    //商品列表页

    public function actionList($category_id)
    {   //商品分类$id
        $models = GoodsCategory::findOne(['id' => $category_id]);
        if ($models->depth == 2) {
            $goods = Goods::find()->where(['goods_category_id' => $category_id])->all();
        } else {
            $ids = $models->leaves()->asArray()->column();
            //var_dump($ids);exit;
            $goods = Goods::find()->where(['in', 'goods_category_id', $ids])->all();
        }

        $models = \frontend\models\GoodsCategory::find()->where('parent_id=0')->all();
        return $this->render('list', ['goods' => $goods, 'models' => $models]);
    }


    //商品根据id的详情页

    public function actionGoods($id)
    {
        $goods = \frontend\models\Goods::findOne($id);
        if (!$goods) {
            throw new NotFoundHttpException('你要找的商品不存在!');
        }
        $category_id = $goods->goods_category_id;

        $models = \frontend\models\GoodsCategory::find()->where('parent_id=0')->all();

        $goods_category2 = GoodsCategory::findOne(["id" => $category_id]);
        //var_dump($goods_category2);exit;
        $goods_category1 = GoodsCategory::findOne(["id" => $goods_category2->parent_id]);
        //var_dump($goods_category1);exit;
        $goods_id = $id;
        //var_dump($goods_id);exit;
        $goods_intro = GoodsIntro::findOne(['goods_id' => $goods_id]);
        //var_dump($goods_intro);exit;
        $goods_gallery = \frontend\models\GoodsGallery::find()->where(['goods_id' => $id])->all();
        //var_dump($goods_gallery);exit;
        return $this->render('goods', ['goods' => $goods, 'goods_intro' => $goods_intro, 'goods_gallery' => $goods_gallery, 'goods_category2' => $goods_category2, 'goods_category1' => $goods_category1, 'models' => $models]);
    }


    //从详情页添加到购物车

    public function actionToCart($goods_id, $amount)
    {
        //未登录的购物车
        if (\Yii::$app->user->isGuest) {
            //如果没有登录就存放在cookie中
            $cookies = \Yii::$app->request->cookies;
            //获取cookie中的购物车数据
            $cart = $cookies->get('cart');
            //var_dump($cart);exit;
            if ($cart == null) {
                $carts = [$goods_id => $amount];
            } else {
                $carts = unserialize($cart->value);
                //var_dump($carts);exit;
                if (isset($carts[$goods_id])) {
                    //购物车中如果已经有该商品数量累加
                    $carts[$goods_id] += $amount;
                } else {
                    //购物车中没有该商品
                    $carts[$goods_id] = $amount;
                }
            }
            //将商品id和商品数量写入cookie
            $cookies = \Yii::$app->response->cookies;
            $cookie = new Cookie([
                'name' => 'cart',
                'value' => serialize($carts),
                'expire' => 7 * 24 * 3600 + time(),
            ]);
            $cookies->add($cookie);
        } else {
            //用户已登录，操作购物车数据表
            $model = new Cart();
            $member_id = \Yii::$app->user->identity->id;
            $cart = Cart::findOne(['goods_id' => $goods_id, 'member_id' => $member_id]);

            //var_dump($cart);exit;
            if ($cart) {
                //存在商品，合并商品数量
                $cart->amount += $amount;
                $cart->save();

            } else {
                $model->member_id = $member_id;
                $model->goods_id = $goods_id;
                $model->amount = $amount;
                if ($model->validate()) {
                    $model->save();
                } else {

                }
            }
        }
        return $this->redirect(['show-cart']);
    }

    //购物车显示页面
    public function actionShowCart()
    {

        //用户未登录信息从cookie中取出
        if (\Yii::$app->user->isGuest) {
            $cookies = \Yii::$app->request->cookies;
            //var_dump(unserialize($cookies->getValue('cart')));
            $cart = $cookies->get('cart');
            //var_dump($cart);
            if ($cart == null) {
                $carts = [];

            } else {
                $carts = unserialize($cart->value);
                //var_dump($carts);
            }
            //获取商品的数据
            $models = \frontend\models\Goods::find()->where(['in', 'id', array_keys($carts)])->asArray()->all();
            //var_dump($models);exit;
        } else {
            //用户已经登录，数据从数据库中读取
            $cart = Cart::find()->select(['goods_id', 'amount'])->where(['member_id' => \Yii::$app->user->identity->id])->asArray()->all();
            //var_dump($cart);exit;
            $carts = [];
            foreach ($cart as $car) {
                $carts[$car['goods_id']] = $car['amount'];
            }
            //var_dump($carts);exit;
            $models = Goods::find()->where(['in', 'id', array_keys($carts)])->asArray()->all();

        }
        return $this->render('cart', ['models' => $models, 'carts' => $carts]);
    }


     //Ajax删除购物车的数据

    public function actionAjaxDel()
    {
        $goods_id = \Yii::$app->request->post('goods_id');
        return Cart::delCart(intval($goods_id));
    }

    // Ajax修改购物车的数据
    public function actionAjaxCart()
    {
        $goods_id = \Yii::$app->request->post('goods_id');
        $amount = \Yii::$app->request->post('amount');
        if (\Yii::$app->user->isGuest) {
            //未登录
            $cookies = \Yii::$app->request->cookies;
            //获取cookie中的购物车数据
            $cart = $cookies->get('cart');
            if ($cart == null) {
                $carts = [$goods_id => $amount];
            } else {
                $carts = unserialize($cart->value);
                if (isset($carts[$goods_id])) {
                    //购物车中已经有该商品，更新数量
                    $carts[$goods_id] = $amount;
                } else {
                    //购物车中没有该商品
                    $carts[$goods_id] = $amount;
                }
            }
            //var_dump($carts);exit;
            //将商品id和商品数量写入cookie
            $cookies = \Yii::$app->response->cookies;
            $cookie = new Cookie([
                'name' => 'cart',
                'value' => serialize($carts),
                'expire' => 7 * 24 * 3600 + time()
            ]);
            $cookies->add($cookie);
           //var_dump($cookies);exit;
            return 'success';
        } else {
            //用户登录时
            $member_id = \Yii::$app->user->identity->id;
            $cart = Cart::findOne(['goods_id' => $goods_id, 'member_id' => $member_id]);
           //var_dump($cart);exit;
            $cart->amount = $amount;
            $cart->save();
        }
    }

    //同步购物车数据
    public static function actionTong()
    {
        //获取cookie中的购物车数据
        $cookies = \Yii::$app->request->cookies;
        $carts = $cookies->get('cart');
        //var_dump($cart);exit;
        if ($carts) {
            $carts = unserialize($carts);
            foreach($carts as $goods_id=>$amount){
                $goods = Cart::findOne(['goods_id'=>$goods_id,'member_id'=>\Yii::$app->user->id]);
                if($goods){
                   $goods->amount =$goods->amount+=$amount;
                    $goods->save();
                }else{
                    $model = new Cart();
                    $model->goods_id = $goods_id;
                    $model->amount = $amount;
                    $model->member_id = \Yii::$app->user->id;
                    $model->save();

                }
            }

        }
        //清除cookies
        $cookies = \Yii::$app->response->cookies;
        $cookies->remove('cart');

    }
}