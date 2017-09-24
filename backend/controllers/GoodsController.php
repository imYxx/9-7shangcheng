<?php

namespace backend\controllers;

use backend\models\Brand;
use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsDayCount;
use backend\models\GoodsGallery;
use backend\models\GoodsIntro;
use flyok666\qiniu\Qiniu;
use flyok666\uploadifive\UploadAction;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

class GoodsController extends \yii\web\Controller

{
    public $enableCsrfValidation=false;

    public function actionIndex()
    {
        $query =Goods::find()->where(['status'=>1]);
        //每页多少条
        //总条数
        //$total = $query->count();
        //当前页码(get参数)
        //实例化分页工具类
        $where = \Yii::$app->request->get();
        //var_dump($where);exit;
        $name = isset($where['name'])?$where['name']:'';
        $sn = isset($where['sn'])?$where['sn']:'';
        $cprice = isset($where['cprice'])?$where['cprice']:'';
        $dprice = isset($where['dprice'])?$where['dprice']:'';


        $pager = new Pagination([
            'totalCount' => $query->andFilterWhere(['like','name',$name])->andFilterWhere(['like','sn',$sn])->andFilterWhere(['between','shop_price',$cprice,$dprice])->count(),//总条数
            'defaultPageSize' =>4//每页多少条

        ]);
        $models=Goods::find()->where(['>','status',-1])->andFilterWhere(['like','name',$name])->andFilterWhere(['like','sn',$sn])->andFilterWhere(['between','shop_price',$cprice,$dprice])->orderBy('sort desc')->offset($pager->offset)->limit($pager->limit)->all();


        return $this->render('index', ['models' => $models, 'pager' => $pager]);

    }
    //商品添加
    public function actionAdd(){
            $brand= Brand::find()->asArray()->all();
            $data=ArrayHelper::map($brand,'id','name');

            $gts=GoodsCategory::find()->select(['id','parent_id','name'])->asArray()->all();
            $model= new Goods();
            $content = new GoodsIntro();
            $request=\Yii::$app->request;
            if($request->isPost){
                $model->load($request->post());
                $content->load($request->post());

                //var_dump($model);exit;
                if ($model->validate()) {
                    $date=date('Ymd');
                    $count=GoodsDayCount::findOne(['day'=>$date]);
                // print_r($count);die;
                    if($count){
                        if($count->count <9){

                            $model->sn=$date.'000'.($count->count +1);

                        }elseif ($count->count<98){

                            $model->sn=$date.'00'.($count->count +1);
                        }


                        $count->count=$count->count+1;
                        $count->day=$date;
                        $count->save();

                    }else{
                        $model->sn=$date.'0001';

                        $count=new GoodsDayCount();
                        $count->count=1;
                        $count->day=$date;
                        $count->save();
                    }
                    $model->create_time=time();
                    $model->save();

                    $content->goods_id=$model->id;
                    $content->save(false);


                    \Yii::$app->session->setFlash('info', '添加成功');
                    return $this->redirect(['goods/index']);


                }
            }
            return $this->render('add',['model'=>$model,'topCategory'=>json_encode($gts),'data'=>$data,'content'=>$content]);
        }

    public function actions() {
        return [
            's-upload' => [
                'class' => UploadAction::className(),
                'basePath' => '@webroot/upload',
                'baseUrl' => '@web/upload',
                'enableCsrf' => true, // default
                'postFieldName' => 'Filedata', // default
                'overwriteIfExist' => true,
                'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filehash = sha1(uniqid() . time());
                    $p1 = substr($filehash, 0, 2);
                    $p2 = substr($filehash, 2, 2);
                    return "{$p1}/{$p2}/{$filehash}.{$fileext}";
                },
                //END CLOSURE BY TIME
                'validateOptions' => [
                    'extensions' => ['jpg', 'png'],
                    'maxSize' => 1 * 1024 * 1024, //file size
                ],
                'beforeValidate' => function (UploadAction $action) {
                    //throw new Exception('test error');
                },
                'afterValidate' => function (UploadAction $action) {},
                'beforeSave' => function (UploadAction $action) {},
                'afterSave' => function (UploadAction $action) {
                    $action->output['fileUrl'] = $action->getWebUrl();//输出图片的路径
                    // $action->getFilename(); // "image/yyyymmddtimerand.jpg"
                    //$action->getWebUrl(); //  "baseUrl + filename, /upload/image/yyyymmddtimerand.jpg"

                    //$action->getSavePath(); // "/var/www/htdocs/upload/image/yyyymmddtimerand.jpg"
                    //将图片上传到七牛云,并且返回七牛云的图片地址
                    /*$config = [
                        'accessKey'=>'hqNnJqiC0r7xoCcroZKMbqgbmELaZPyYmrbnNIDg',
                        'secretKey'=>'KwzsOiQ7UbAesjwXKh5fMblJCbbrOHuN6grCQxzq',
                        'domain'=>'http://ovy7x7fft.bkt.clouddn.com/',
                        'bucket'=>'0516php',
                        'area'=>Qiniu::AREA_HUADONG   //华东
                    ];*/

                    $qiniu = new Qiniu(\Yii::$app->params['qiniuyun']);
                    $key = $action->getWebUrl();
                    //上传文件到七牛云  同时指定一个key(名称,文件名)
                    $file = $action->getSavePath();
                    $qiniu->uploadFile($file,$key);
                    //获取七牛云上文件的url地址
                    $url = $qiniu->getLink($key);

                    $action->output['fileUrl'] = $url;//输出图片的路径

                },
            ],
            'upload' => [
                'class' => 'kucha\ueditor\UEditorAction',
            ]
        ];
    }

    //商品修改
    public function actionEdit($id){
        $brand= Brand::find()->asArray()->all();
        $data=ArrayHelper::map($brand,'id','name');

        $gts=GoodsCategory::find()->select(['id','parent_id','name'])->asArray()->all();
        $model= new Goods();
        $content = new GoodsIntro();
        $request=\Yii::$app->request;
        $model = Goods::findOne(['id'=>$id]);
        $content= GoodsIntro::findOne(['goods_id'=>$id]);
        if($request->isPost){
            $model->load($request->post());
            $content->load($request->post());

            //var_dump($model);exit;
            if ($model->validate()) {
                // print_r($model);exit;

                $model->create_time = time();
                // var_dump($model);exit;
                $model->save();
                $content->goods_id=$model->id;
                $content->save(false);
                \Yii::$app->session->setFlash('info', '修改成功');
                return $this->redirect(['goods/index']);


            }
        }
        return $this->render('add',['model'=>$model,'topCategory'=>json_encode($gts),'data'=>$data,'content'=>$content]);
    }

    public function actionDel($id){
        $model = Goods::findOne($id);
        $model->status=-1;
        $request = $model->save(false);
        if($request){
            \Yii::$app->session->setFlash('info','删除成功');
            return $this->redirect(['index']);
        }

    }

    public function actionCheck(){
        $request=\Yii::$app->request;
        $id=$request->get('id');
        //查看相册
        $model=GoodsGallery::find()->where(['goods_id'=>$id])->asArray()->all();
        //接收goods_id保存

        $path=$request->get('fileUrl');
        if($path){
            $goods_id=$request->get('goods_id');
            $model=new GoodsGallery();
            $model->goods_id=$goods_id;
            $model->path=$path;
            $model->save(false);
            return json_encode(['success'=>true,'msg'=>'保存成功']);
        }
        //接收图片的URL
        return $this->render('check',['model'=>$model]);
    }

    public function actionAjax(){
        //接收数据,删除图片
        $request=\Yii::$app->request;
        $path=$request->get('fileUrl');
        $result=GoodsGallery::findOne(['path'=>$path])->delete();
        if($result){
            echo "{'success':true,'msg':'恭喜删除成功'}";
        }
    }



}
