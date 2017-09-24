<?php
namespace backend\models;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class RoleForm extends Model{
    const SCENARIO_EDIT = 'edit';
    const SCENARIO_ADD = 'add';
    public $name;
    public $description;
    public $permissions;
    public function rules()
    {
        return [
            [['name','description'],'required'],
            ['permissions','safe'],
            ['name','validateName','on'=>self::SCENARIO_ADD],
            ['name','validateEditName','on'=>self::SCENARIO_EDIT],
        ];
    }
    //如果定义的场景在rules中没有配置,需要通过scenarios方法申明,否则会提示场景不存在
    /*public function scenarios()
    {
        return [
            self::SCENARIO_EDIT =>[],//指定该场景下需要验证哪些字段(空数组表示所有字段)
        ];
    }*/
    public function validateName(){
        $auth = \Yii::$app->authManager;
        if($auth->getRole($this->name)){
            $this->addError('name',' 该角色已存在');
        }
    }
    public function validateEditName(){
        $auth = \Yii::$app->authManager;
        //没有修改名称(主键)
        //修改了名称,新名称不能存在
        //怎么判断名称修改没有?通过get参数获取旧名称
        if(\Yii::$app->request->get('name') != $this->name){
            if($auth->getRole($this->name)){
                $this->addError('name',' 该角色已存在');
            }
        }
    }

    public static function getPermissionItems(){
        $permissions = \Yii::$app->authManager->getPermissions();
        $items = [];
        foreach ($permissions as $permission){
            $items[$permission->name] = $permission->description;
        }

        return $items;
    }
    public static function getPermissionItems2(){
        $permissions = \Yii::$app->authManager->getPermissions();
        $items = [];
        foreach ($permissions as $permission){
            $items[$permission->name] = $permission->name;
            $data=ArrayHelper::merge([0=>'请选择路由'],$items);
        }

        return $data;
    }
    //角色
    public static function getRole(){
        $roles = \Yii::$app->authManager->getRoles();
        $items = [];
        foreach ($roles as $role){
            $items[$role->name] = $role->description;
        }

        return $items;
    }
}