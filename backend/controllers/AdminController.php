<?php

namespace backend\controllers;

use backend\models\Admin;
use flyok666\qiniu\Qiniu;
use flyok666\uploadifive\UploadAction;
use backend\models\LoginForm;
use yii\data\Pagination;
use yii\filters\AccessControl;

class AdminController extends \yii\web\Controller
{
    public $enableCsrfValidation=false;

    public function actionIndex()
    {
        $query = Admin::find()->where(['>','status',0]);
        //每页多少条
        //总条数
        //$total = $query->count();
        //当前页码(get参数)
        //实例化分页工具类
        $pager = new Pagination([
            'totalCount' => $query->count(),//总条数
            'defaultPageSize' =>3//每页多少条

        ]);
        $models=Admin::find()->where(['>','status',-1])->offset($pager->offset)->limit($pager->limit)->all();


        return $this->render('index', ['models' => $models, 'pager' => $pager]);



    }
    //用户添加
    public function actionAdd(){

        $model= new Admin();
        $model->scenario='add';
        $request=\Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
           // var_dump($model);exit;
            if ($model->validate()) {
                $model->save();
                //var_dump($model);exit;
                \Yii::$app->session->setFlash('info', '添加成功');
                return $this->redirect(['admin/index']);


            }
        }
        return $this->render('add',['model'=>$model]);
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
            'upload' => [
                'class' => 'kucha\ueditor\UEditorAction',

            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                //设置验证码
                'minLength' =>4,
                'maxLength' =>4
            ],

        ];

    }


    //用户修改
    public function actionEdit($id){

        $model= new Admin();
        $request=\Yii::$app->request;
        $model = Admin::findOne(['id'=>$id]);
        if($request->isPost){
            $model->load($request->post());
            // var_dump($model);exit;
            if ($model->validate()) {
                $model->save();
                //var_dump($model);exit;
                \Yii::$app->session->setFlash('info', '修改成功');
                return $this->redirect(['admin/index']);


            }
        }
        return $this->render('add',['model'=>$model]);
    }

    public function actionDel($id){
        $model = Admin::findOne($id);
        $model->status=-1;
        $request = $model->save(false);
        if($request){
            \Yii::$app->session->setFlash('info','删除成功');
            return $this->redirect(['index']);
        }

    }


    public function actionLogin(){

        $model= new LoginForm();
        $request = \Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                //认证
               // var_dump($model);exit;
                $admin = Admin::findOne(['username'=>$model->username]);
                //用户存在，验证密码
                //var_dump($admin);exit;

                if($admin){
                    //验证密码
                    $pwd= \Yii::$app->security->validatePassword($model->password_hash,$admin->password_hash);
                    //var_dump($pwd);exit;
                    if($pwd){
                        //账户密码正确,保存信息到

                         $user = \Yii::$app->user;

                        //勾中记住我保存密码三天
                            $duration = $model->remember?259200:0;
                            \Yii::$app->user->login($admin,$duration);

                        //提示信息然后跳转
                        $admin->last_login_ip=$request->userIP;
                        $admin->last_login_time = time();
                        $admin->save(false);
                        //var_dump($last_login_time);exit;
                        \Yii::$app->session->setFlash('info', '登录成功');
                        //return $this->redirect(['aaa']);
                        return $this->redirect(['admin/index']);

                    }else {
                       $model->addError('password_hash','密码错误');
                }

                }else {
                    $model->addError('username', '用户不存在');
                }
            }
        }

        return $this->render('login',['model'=>$model]);
    }

    //退出
    public function actionLogout(){
        \Yii::$app->user->logout();
        \Yii::$app->session->setFlash('info', '退出成功');
        return $this->redirect(['admin/index']);
    }

    //设置权限 未登录不能操作
   public function behaviors()
  {
       return [
            'access'=>[
                'class'=>AccessControl::className(),
               'only'=>[],
               'rules'=>[
                   [
                       'allow'=>true,
                        'actions'=>['login','index','captcha'],
                        'roles'=>['?'],
                    ],
                    [
                       'allow'=>true,
                       'actions'=>['logout','add','edit','del','index','captcha','s-upload'],
                        'roles'=>['@'],
                    ],
                ],
            ]
        ];
    }



}
