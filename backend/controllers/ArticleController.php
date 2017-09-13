<?php
namespace backend\controllers;


use backend\models\Article;
use backend\models\ArticleCategory;
use backend\models\ArticleDetail;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\web\Controller;


class ArticleController extends Controller{

        public  function actionIndex(){

            $query =Article::find()->where(['status'=>1]);
            //每页多少条
            //总条数
            //$total = $query->count();
            //当前页码(get参数)
            //实例化分页工具类
            $pager = new Pagination([
                'totalCount' => $query->count(),//总条数
                'defaultPageSize' => 3//每页多少条

            ]);
            $models=Article::find()->where(['>','status',-1])->orderBy('sort desc')->offset($pager->offset)->limit($pager->limit)->all();


            return $this->render('index', ['models' => $models, 'pager' => $pager]);






        }
//添加


    public function actionAdd()
    {
        $article_category=ArticleCategory::find()->asArray()->all();
        $data=ArrayHelper::map($article_category,'id','name');
        $model = new Article();
        $content = new ArticleDetail();
        $request = \Yii::$app->request;
        if($request->isPost){
            //加载数据
            $model->load($request->post());
            $content->load($request->post());

            if ($model->validate()) {
                $model->create_time = time();
                $model->save(false);//save方法默认会再次执行验证 $model->validate()
                $content->article_id=$model->id;
                $content->save(false);
                \Yii::$app->session->setFlash('info', '添加成功');
                //echo "11";exit;
               return $this->redirect(['article/index']);

            }

        }

        return $this->render('add', ['model' => $model,'data'=>$data,'content'=>$content]);

    }

    public function actionDel1($id)
    {


        $model= Article::findOne($id);
        $model->status=-1;
        $request=$model->save(false);
        if($request){
            \Yii::$app->session->setFlash('info','删除成功！');

            return $this->redirect(['article/index']);
        }


    }
    //修改
    public function actionEdit($id)
    {
        $article_category=ArticleCategory::find()->asArray()->all();
        $data=ArrayHelper::map($article_category,'id','name');
        $model = new Article();
        $content= new ArticleDetail();
        $request = \Yii::$app->request;
        $model = Article::findOne(['id'=>$id]);
        $content = ArticleDetail::findOne(['article_id'=>$id]);
        if($request->isPost){
            //加载数据
            $model->load($request->post());
            $content->load($request->post());

            if ($model->validate()) {
                $model->create_time = time();
                $model->save(false);//save方法默认会再次执行验证 $model->validate()
                $content->article_id=$model->id;
                $content->save(false);
                if($content->validate()) {
                    \Yii::$app->session->setFlash('info', '修改成功');
                    //echo "11";exit;
                    return $this->redirect(['article/index']);
                }
            }

        }

        return $this->render('add', ['model' => $model,'data'=>$data,'content'=>$content]);

    }

//ajax删除
   public function actionDel(){
           $id = \Yii::$app->request->post('id');
           $model = Article::findOne(['id'=>$id]);
           if($model){
                   $model->status = -1;
                    $model->save(false);
                    return 'success';
        }
        return 'fail';
   }



}