<?php
namespace backend\models;

use yii\base\Model;

class LoginForm extends Model{
    public $username;
    public $password_hash;
    public $code;
    public $remember;

    public function rules(){
        return[
            [['password_hash','username'],'required'],
            ['code','captcha','captchaAction'=>'admin/captcha'],
            [['remember'],'string'],

        ];


    }
    public  function attributeLabels(){
        return[
            'username'=>'用户名',
            'password_hash'=>'密码',
            'remember'=>'记住密码',
            //'code'=>'验证码',

        ];

    }


}