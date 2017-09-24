<?php

namespace backend\controllers;

use backend\models\Menu;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

class MenuController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $query = Menu::find();
        //每页多少条
        //总条数
        //$total = $query->count();
        //当前页码(get参数)
        //实例化分页工具类
        $pager = new Pagination([
            'totalCount' => $query->count(),//总条数
            'defaultPageSize' =>10//每页多少条

        ]);
        $models=Menu::find()->offset($pager->offset)->orderBy('id asc')->limit($pager->limit)->all();


        return $this->render('index', ['models' => $models, 'pager' => $pager]);

    }
    public function  actionAdd(){
        $menu_name =Menu::find()->where(['=','parent_id',0])->asArray()->all();
        $data=ArrayHelper::map($menu_name,'id','name');
        $datas=ArrayHelper::merge([0=>'顶级菜单'],$data);
        $model = new Menu();
        $request = \Yii::$app->request;
        if($request->isPost){
            //加载数据
            $model->load($request->post());
            if ($model->validate()) {
                $model->save(false);//save方法默认会再次执行验证 $model->validate()
                \Yii::$app->session->setFlash('info', '添加成功');
                return $this->redirect(['menu/index']);

            }

        }

        return $this->render('add', ['model' => $model,'datas'=>$datas]);




    }

    public function  actionEdit($id){
        $menu_name =Menu::find()->asArray()->all();
        $data=ArrayHelper::map($menu_name,'id','name');
        $model = Menu::findOne(['id'=>$id]);
        $request = \Yii::$app->request;
        if($request->isPost){
            //加载数据
            $model->load($request->post());
            if ($model->validate()) {
                $model->save(false);//save方法默认会再次执行验证 $model->validate()
                \Yii::$app->session->setFlash('info', '修改成功');
                return $this->redirect(['menu/index']);

            }

        }

        return $this->render('add', ['model' => $model,'data'=>$data]);




    }
    //删除
    public function actionDel(){
        $id = \Yii::$app->request->post('id');
        $model = Menu::findOne(['id'=>$id]);
        if($model){
            $model->delete();

            return 'success';
        }
        return 'fail';
    }


}
