<?php

namespace backend\controllers;

use backend\models\Brand;
use backend\models\GoodsCategory;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use flyok666\uploadifive\UploadAction;
use flyok666\qiniu\Qiniu;

class BrandController extends \yii\web\Controller
{


    public function actionIndex()
    {

            $query = Brand::find()->where(['status'=>1]);
            //每页多少条
            //总条数
            //$total = $query->count();
            //当前页码(get参数)
            //实例化分页工具类
            $pager = new Pagination([
                'totalCount' => $query->count(),//总条数
                'defaultPageSize' => 2//每页多少条

            ]);
            $models=Brand::find()->where(['>','status',-1])->orderBy('sort desc')->offset($pager->offset)->limit($pager->limit)->all();


            return $this->render('index', ['models' => $models, 'pager' => $pager]);


    }

    //添加


    public function actionAdd()
    {
        $model = new Brand();
        $request = \Yii::$app->request;
        if($request->isPost){
            //加载数据
            $model->load($request->post());
            //处理上传文件实例化上传对象
            //$model->file=UploadedFile::getInstance($model,'file');

            if ($model->validate()) {
                //移动文件的路径，已经包含了文件名
                //$file = '/upload/' . uniqid() . '.' . $model->file->getExtension();
                //保存文件将文件存放的地址saveAs

                //$model->file->saveAs(\Yii::getAlias('@webroot') . $file, false);
                //把文件的地址赋值给photo字段
                //$model->logo = $file;
                //$model->create_time = time();
                $model->save(false);//save方法默认会再次执行验证 $model->validate()
                //var_dump($model->getErrors());exit;
                \Yii::$app->session->setFlash('info', '添加成功');
                return $this->redirect(['brand/index']);


            }

        }

        return $this->render('add', ['model' => $model]);

    }



    public function actionEdit($id)
    {
        $model = new Brand();
        $request = \Yii::$app->request;
        $model = Brand::findOne(['id'=>$id]);
        if($request->isPost){
            //加载数据
            $model->load($request->post());
            //处理上传文件实例化上传对象
            //$model->file=UploadedFile::getInstance($model,'file');

            if ($model->validate()) {
                //移动文件的路径，已经包含了文件名
                //$file = '/upload/' . uniqid() . '.' . $model->file->getExtension();
                //保存文件将文件存放的地址saveAs

                //$model->file->saveAs(\Yii::getAlias('@webroot') . $file, false);
                //把文件的地址赋值给photo字段
                //$model->logo = $file;
                //$model->create_time = time();
                $model->save(false);//save方法默认会再次执行验证 $model->validate()
                //var_dump($model->getErrors());exit;
                \Yii::$app->session->setFlash('info', '添加成功');
                return $this->redirect(['brand/index']);


            }

        }

        return $this->render('add', ['model' => $model]);

    }



    public function actions() {
        return [
            's-upload' => [
                'class' => UploadAction::className(),
                'basePath' => '@webroot/upload',
                'baseUrl' => '@web/upload',
                'enableCsrf' => true, // default
                'postFieldName' => 'Filedata', // default
                //BEGIN METHOD
                //'format' => [$this, 'methodName'],
                //END METHOD
                //BEGIN CLOSURE BY-HASH
                'overwriteIfExist' => true,
                //'format' => function (UploadAction $action) {
                    //$fileext = $action->uploadfile->getExtension();
                   // $filename = sha1_file($action->uploadfile->tempName);
                   // return "{$filename}.{$fileext}";
               // },
                //END CLOSURE BY-HASH
                //BEGIN CLOSURE BY TIME
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
        ];
    }


    //ajax删除
        public function actionDel(){
            $id = \Yii::$app->request->post('id');
            $model = Brand::findOne(['id'=>$id]);
            if($model){
                $model->status = -1;
                $model->save(false);
                return 'success';
            }
            return 'fail';
        }

}
