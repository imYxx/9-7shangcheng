<?php
namespace backend\controllers;


use backend\models\ArticleDetail;
use yii\web\Controller;

class ArticleDetailController extends Controller{
    //����������ʾ
    public function actionIndex()
    {
        //��ȡ����������Ϣ
        $contents = ArticleDetail::find()->all();
        //������ͼ
        return $this->render('index', ['contents' =>$contents]);
    }




}