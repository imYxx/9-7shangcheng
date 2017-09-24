<?php
namespace backend\models;

use yii\base\Model;

class PermissionForm extends Model{
    public $name;//权限名称
    public $description;//权限的描述

    public function rules()
    {
        return [
            [['name','description'],'required'],//设定一个场景
            ['name','validateName','on'=>'add'],
           // [['description'],'required','on'=>'add'],
        ];
    }
    //验证权限名称
    public function validateName(){
        if(\Yii::$app->authManager->getPermission($this->name)){
            $this->addError('name','权限已存在');
        }
    }

    public function attributeLabels()
    {
        return [
            'name'=>'权限名称',
            'description'=>'描述'
        ];
    }

}