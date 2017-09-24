<?php

namespace backend\controllers;

use backend\models\PermissionForm;
use backend\models\RoleForm;

class RbacController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $auth = \Yii::$app->authManager;
        $permissions = $auth->getPermissions();
        //var_dump($permissions);exit;
        return $this->render('index', ['permissions' => $permissions]);

    }

    //添加权限
    public function actionAdd()
    {
        $model = new PermissionForm();
        $model->scenario = 'add';
        $request = \Yii::$app->request;
        if ($request->isPost) {
            $model->load($request->post());
            if ($model->validate()) {
                $auth = \Yii::$app->authManager;
                //添加权限
                //创建权限
                $permission = $auth->createPermission($model->name);
                $permission->description = $model->description;
                //保存到数据表
                $auth->add($permission);

                return $this->redirect(['index']);
            }
        }
        return $this->render('add', ['model' => $model]);
    }

    //修改权限
    public function actionEdit()
    {
        $request = \Yii::$app->request;
        $name = $request->get('name');
        $auth = \Yii::$app->authManager;
        $permission = $auth->getPermission($name);//查询权限对象.
        //var_dump($permission);exit;
        $model = new PermissionForm();
        $model->name = $permission->name;
        $model->description = $permission->description;

        //通过name查询出来回显
        $model->description = $auth->getPermission($name)->description;

        if ($request->isPost) {
            $model->load($request->post());
                if($name == $model->name){
                    if ($model->validate()) {
                        //添加权限
                        //创建权限
                        //var_dump($request);exit;
                        $permission->name = $model->name;
                        $permission->description = $model->description;

                            $auth->update($name,$permission);

                        return $this->redirect(['index']);
                    }
                }else {
                    if ($model->validate()) {
                        //添加权限
                        //创建权限
                        //var_dump($request);exit;
                        $permission->name = $model->name;
                        $permission->description = $model->description;

//                        var_dump($permission);exit;
                        $auth->update($name, $permission);

                        return $this->redirect(['index']);
                    }
                }
        }
        return $this->render('add', ['model' => $model]);
    }

    public function YMC(){
        $request = \Yii::$app->request;
        $name = $request->get('name');
        $auth = \Yii::$app->authManager;
        $permission = $auth->getPermission($name);//查询权限对象.
        $model = new PermissionForm();
        $model->name = $permission->name;
        $model->description = $permission->description;



        $permission->name = $model->name;
        $permission->description = $model->description;
        $auth->update($permission->name,$permission);

    }

    //删除

    public function actionDel()
    {
        $request = \Yii::$app->request;
        $name = $request->get('name');
        $auth = \Yii::$app->authManager;
        $model = new PermissionForm();
        $model->name = $name;
        $permission= $auth->getPermission($name);
        $auth->remove($permission);
        //跳转
        return $this->redirect(['index']);
    }
    //角色显示列表
    public function actionRoleIndex(){
        $auth = \Yii::$app->authManager;
        $roles = $auth->getRoles();
        return $this->render('role-index',['roles'=>$roles]);
    }
    //角色添加
    public function actionAddRole(){
        $model = new RoleForm();
        $model->scenario = RoleForm::SCENARIO_ADD;
        $request = \Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                //保存角色
                $auth = \Yii::$app->authManager;
                //添加角色
                //创建新角色
                $role = $auth->createRole($model->name);
                $role->description = $model->description;
                //保存到数据表
                $auth->add($role);
                //给角色分配权限
                //$model->permissions = ['user/add','user/edit']; |  null
                if($model->permissions){
                    foreach ($model->permissions as $permissionName){
                        $permission = $auth->getPermission($permissionName);
                        $auth->addChild($role,$permission);//角色  权限
                    }
                }
                return $this->redirect(['role-index']);
            }
        }
        return $this->render('role-add',['model'=>$model]);
    }

    //删除角色

    public function actionDelRole()
    {
        $request = \Yii::$app->request;
        $name = $request->get('name');
        $auth = \Yii::$app->authManager;
        $model = new RoleForm();
        $model->name = $name;
        $role= $auth->getRole($name);
        //var_dump($name);exit;
        $auth->remove($role);
        //跳转
        return $this->redirect(['role-index']);
    }
    //修改角色
    public function actionEditRole()
    {
        $request = \Yii::$app->request;
        $name = $request->get('name');
        $auth = \Yii::$app->authManager;
        $permission = $auth->getPermission($name);
        //var_dump($permission);exit;
        $role = $auth->getRole($name);//查询角色对象.
        //$permissions = $auth->getPermission('rbac/role-add');
        $model = new RoleForm();
        $model->name = $role->name;
        $model->description = $role->description;
        $model->description = $role->description;

        //通过name查询出来回显
        $items = [];
        foreach ($auth->getPermissionsByRole($name) as $permission){
            $items[$permission->name] = $permission->description;
        }
        $model->permissions=array_keys($items);

        $model->description = $auth->getRole($name)->description;
        //$model->permissions = $auth->getPermission($name)->permissions        ;

        if ($request->isPost) {
            $model->load($request->post());
            if($name == $model->name){
                if ($model->validate()) {
                    //添加权限
                    //创建权限
                    //var_dump($request);exit;
                    $role->name = $model->name;
                    $role->description = $model->description;

                    $auth->update($name,$role);

                    return $this->redirect(['role-index']);
                }
            }else {
                if ($model->validate()) {
                    //添加权限
                    //创建权限
                    //var_dump($request);exit;
                    $role->name = $model->name;
                    $role->description = $model->description;

//                        var_dump($permission);exit;
                    $auth->update($name, $role);

                    return $this->redirect(['role-index']);
                }
            }
        }
        return $this->render('role-add', ['model' => $model]);
    }


}