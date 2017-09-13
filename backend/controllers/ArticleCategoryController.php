<?php
namespace backend\controllers;

use backend\models\ArticleCategory;
use yii\data\Pagination;
use yii\web\Controller;


class ArticleCategoryController extends Controller{

    public function actionIndex(){

        $query =ArticleCategory::find()->where(['status'=>1]);
        //每页多少条
        //总条数
        //$total = $query->count();
        //当前页码(get参数)
        //实例化分页工具类
        $pager = new Pagination([
            'totalCount' => $query->count(),//总条数
            'defaultPageSize' => 2//每页多少条

        ]);
        $models=ArticleCategory::find()->where(['>','status',-1])->orderBy('sort desc')->offset($pager->offset)->limit($pager->limit)->all();


        return $this->render('index', ['models' => $models, 'pager' => $pager]);


    }

    //添加

    public function actionAdd()
    {
        $model = new ArticleCategory();
        $request = \Yii::$app->request;
        if($request->isPost){
            //加载数据
            $model->load($request->post());

            if ($model->validate()) {
                $model->save(false);//save方法默认会再次执行验证 $model->validate()
                //var_dump($model->getErrors());exit;
                \Yii::$app->session->setFlash('info', '添加成功');
                return $this->redirect(['article-category/index']);


            }

        }

       return $this->render('add', ['model' => $model]);

    }

    public function actionDel($id)
    {


        $model= ArticleCategory::findOne($id);
        $model->status=-1;
        $request=$model->save(false);
        if($request){
            \Yii::$app->session->setFlash('info','删除成功！');

            return $this->redirect(['article-category/index']);
        }


    }

    public function actionEdit($id)
    {
        $model = new ArticleCategory();
        $request = \Yii::$app->request;
        $model = ArticleCategory::findOne(['id'=>$id]);
        if($request->isPost){
            //加载数据
            $model->load($request->post());

            if ($model->validate()) {
                $model->save(false);//save方法默认会再次执行验证 $model->validate()
                //var_dump($model->getErrors());exit;
                \Yii::$app->session->setFlash('info', '修改   成功');
                return $this->redirect(['article-category/index']);


            }

        }

        return $this->render('add', ['model' => $model]);

    }


}