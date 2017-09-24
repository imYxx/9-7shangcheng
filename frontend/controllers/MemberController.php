<?php

namespace frontend\controllers;

use frontend\models\Address;
use frontend\models\Member;
use frontend\models\SmsDemo;

class MemberController extends \yii\web\Controller
{
    public $enableCsrfValidation = false;

    public function actionUser()
    {
        //var_dump(\Yii::$app->user->identity);

        return $this->renderPartial('user');
    }

    //首页
    public function actionIndex()
    {


        return $this->renderPartial('index');
    }

    public function actionRegist()
    {

        $model = new Member();
        $request = \Yii::$app->request;
        if ($request->isPost) {
            $model->load($request->post(), '');
            if ($model->validate()) {
                //var_dump($model);die;
                $model->password_hash = \Yii::$app->security->generatePasswordHash($model->password);
                $model->status = 1;
                $model->created_at = time();

                $model->save(false);
                \Yii::$app->session->setFlash('info', '注册成功，可以登录啦');
                return $this->redirect(['login']);


            }
        }
        return $this->renderPartial('regist');
    }

    public function actionLogin()
    {
        $this->layout = false;
        $model = new Member();
        $request = \Yii::$app->request;
        if ($request->isPost) {
            $model->load($request->post(), '');
            if ($model->validate()) {
                $member = Member::findOne(['username' => $model->username]);
                //用户存在继续验证密码
                if ($member) {
                    $pwd = \Yii::$app->security->validatePassword($model->password, $member->password_hash);
                    //var_dump($pwd);die;
                    if ($pwd) {
                        //密码正确返回true

                        $member->auth_key = \Yii::$app->security->generateRandomString();
                        $member->last_login_ip = $request->userIP;
                        $member->last_login_time = time();
                        $member->save(false);

                        //记住密码保存信息到sesson中
                        $duration = $model->remember ? 259200 : 0;
                        \Yii::$app->user->login($member, $duration);

                        \Yii::$app->session->setFlash('info', '登录成功');
                        GoodsController::actionTong();
                        return $this->redirect(['user']);
                    } else {
                        $model->addError('password', '密码错误');
                    }
                } else {
                    $model->addError('username', '用户不存在');
                }
            }
        }
        return $this->renderPartial('login');
    }

    //添加收货地址
    public function actionAddress()
    {

        $model = new Address();
        $request = \Yii::$app->request;
        if ($request->isPost) {
            $model->load($request->post(), '');
            //验证
            if ($model->validate()) {
                //省份
                $model->province = $model->location_p;
                //市级
                $model->city = $model->location_c;
                //区县
                $model->area = $model->location_a;
                $model->address = $model->location_b;
                //登录用户id
                $model->member_id = \Yii::$app->user->id;
                //保存
                $model->save();
                return $this->redirect(['member/address']);
            }
        }

        return $this->renderPartial('address');
    }

    //删除地址
    public function actionDel($id)
{
    $model = Address::findOne($id);
    $request = $model->delete();
    if ($request) {
        \Yii::$app->session->setFlash('info', '删除成功');
        return $this->redirect(['address']);
    }


}
    //编辑功能
    public function actionEdit($id)
    {
        $model = Address::findOne(['id'=>$id]);
        $request = \Yii::$app->request;
        if ($request->isPost) {
            $model->load($request->post(), '');
            //验证
            if ($model->validate()) {
                //省份
                $model->province = $model->location_p;
                //市级
                $model->city = $model->location_c;
                //区县
                $model->area = $model->location_a;
                $model->address = $model->location_b;
                //登录用户id
                $model->member_id = \Yii::$app->user->id;
                //保存
                $model->save();
                return $this->redirect(['member/address']);
            }
        }

        return $this->renderPartial('editaddress',['model'=>$model]);
    }
    //测试发送短信
    public function actionSms(){
        $tel = \Yii::$app->request->post('tel');
            var_dump($tel);
        $code = rand(1000,9999);
        \Yii::$app->session->set('code_'.$tel,$code);
        $demo = new SmsDemo(
            "LTAIaGKxVSdRcAH6",
            "vOcZwQ76qPPLaXHvo0zD9RdHImpu6z"
        );

        echo "SmsDemo::sendSms\n";
        $response = $demo->sendSms(
            "波胖商城欢迎你", // 短信签名
            "SMS_97960025", // 短信模板编号
            $tel, // 短信接收者
            Array(  // 短信模板中字段的值
                "code"=>$code,
                "product"=>"dsd"
            )

        );
        print_r($response);
        echo $code;


    }

    public function actionMessage($tel,$checkcode1){
        $code = \Yii::$app->session->get('code_'.$tel);
        if($code==null || $code!=$checkcode1){
            return 'false';
        }
            return 'true';
    }
    //ajax验证用户唯一性
    public function actionValidateUser($username){

        //用户已存在
        //return 'false';
        //可以注册
        return 'true';
    }

    //注销登录

    public function actionLogout(){
        \Yii::$app->user->logout();
        \Yii::$app->session->setFlash('info', '退出成功');
        return $this->redirect(['index']);
    }
}