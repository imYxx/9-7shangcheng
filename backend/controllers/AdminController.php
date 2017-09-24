<?php

namespace backend\controllers;

use backend\models\Admin;
use backend\models\RoleForm;
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
        $auth = \Yii::$app->authManager;
        $model= new Admin();
        $model->scenario='add';
        $request=\Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            $roles = $auth->getRole($model->roles);
            if ($model->validate()) {
                $model->save();
                //添加角色
                $id = $model->id;
                $auth->assign($roles,$id);
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
        //显示修改页面
        //根据ID查询数据库数据回显
        //修改数据提交数据
        //更新数据保存数据
        //跳转
        $model = Admin::findOne(['id'=>$id]);
        $auth = \Yii::$app->authManager;
        $model->scenario='add';
        $request=\Yii::$app->request;

        $roles = array_keys($auth->getRolesByUser($id));
        $model->roles= array_keys($auth->getRolesByUser($id));

        if($request->isPost){
            $model->load($request->post());
            $roles = $auth->getRole($model->roles);
            //var_dump($model->roles);exit;
            if ($model->validate()) {
                $auth->revokeAll($model->id);
                if($model->roles){
                    foreach($model->roles as $rolename){
                        $role = $auth->getRole($rolename);
                        $auth->assign($role,$id);
                    }
                }
                $model->save();
                $id = $model->id;
                //$auth->revoke($roles,$id);

                //$auth->assign($roles,$id);
                //var_dump($model);exit;
                \Yii::$app->session->setFlash('info', '添加成功');
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
                       'actions'=>['logout','add','edit','del','index','captcha','s-upload','pwd'],
                        'roles'=>['@'],
                    ],
                ],
            ]
        ];
    }

    //用户密码修改

    public function actionPwd(){
        $message=new LoginForm();
        //获取session里面的数据
        $identity=\Yii::$app->user->identity;
        //找到当前用户登录的ID
        $id=$identity->id;
        $request=\Yii::$app->request;
        //提交表单修改
        if($request->isPost){
            $message->load($request->post());
            //var_dump($message);exit;
            //验证提交的密码和用户密码是否一致
            $result=\Yii::$app->security->validatePassword($message->password_hash,$identity->password_hash);
            if($result){
                //输入的两次密码不一致
                if($message->newpassword != $message->reppassword){
                    $message->addError('reppassword','密码不一致');
                }else{
                    //密码一致
                    //查询出一条记录,并更新密码
                    $model=Admin::findOne(['id'=>$id]);
                    $model->password_hash=\Yii::$app->security->generatePasswordHash($message->reppassword);
                    //保存
                    $model->save(false);
                    //修改成功退出登录并跳转到登录页面
                    \Yii::$app->user->logout();
                    \Yii::$app->session->setFlash('success','密码修改成功,请重新登录');
                    return $this->redirect('login');
                }


            }else{
                $message->addError('password_hash','密码不正确');
            }

        }
        return $this->render('pwd',['message'=>$message,'id'=>$identity]);
    }




}
