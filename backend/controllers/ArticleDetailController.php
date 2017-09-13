<?php
namespace backend\controllers;


use backend\models\ArticleDetail;
use yii\web\Controller;

class ArticleDetailController extends Controller{
    //文章内容显示
    public function actionIndex()
    {
        //获取文章内容信息
        $contents = ArticleDetail::find()->all();
        //分配试图
        return $this->render('index', ['contents' =>$contents]);
    }




}