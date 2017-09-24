<?php
namespace backend\models;

use yii\base\Model;

class LoginForm extends Model{
    public $username;
    public $password_hash;
    public $code;
    public $remember;
    public $reppassword;
    public $newpassword;

    public function rules(){
        return[
            [['password_hash','username'],'required'],
            [['reppassword','newpassword'],'string'],
            ['code','captcha','captchaAction'=>'admin/captcha'],
            [['remember'],'string'],
           // ['newpassword', 'compare', 'compareAttribute'=>'reppassword'],
        ];



    }
    public  function attributeLabels(){
        return[
            'username'=>'用户名',
            'password_hash'=>'密码',
            'remember'=>'记住密码',
            'newpassword'=>'新密码',
            'reppassword'=>'再次输入新密码',
            //'code'=>'验证码',

        ];

    }


}