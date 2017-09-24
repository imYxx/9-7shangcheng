<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "cart".
 *
 * @property string $id
 * @property integer $goods_id
 * @property integer $amout
 * @property integer $member_id
 */
class Cart extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cart';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id', 'amount', 'member_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'goods_id' => 'Goods ID',
            'amount' => 'Amount',
            'member_id' => 'Member ID',
        ];
    }
    public static function delCart($goods_id)
    {
        if (Yii::$app->user->isGuest) {
            //删除cookie中数据
            $cookies = Yii::$app->request->cookies;
            $cookie = $cookies->get('cart');
            //var_dump($cookie);exit;
            if ($cookie !== null) {
                //有cookie
                $date = unserialize($cookie->value);
                if (isset($date[$goods_id])) {
                    //有对应的商品
                    unset($date[$goods_id]);
                    $cookie->value = serialize($date);
                    Yii::$app->response->cookies->add($cookie);
                   //var_dump($cookie);exit;
                    return true;
                }
            }
        } else {//删除数据库中的数据
            $model = self::findOne(['goods_id' => $goods_id, 'member_id' => Yii::$app->user->id]);
            if ($model !== null) {
                return $model->delete();
            }
        }
        return false;
    }

}
