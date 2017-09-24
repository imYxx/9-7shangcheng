<?php

namespace backend\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "admin".
 *
 * @property integer $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $last_login_time
 * @property string $last_login_ip
 */
class Admin extends \yii\db\ActiveRecord implements IdentityInterface
{
    public $password;//定义一个明文的密码
    public $roles;
    //常量定义场景
    //const SCENARIO_ADD = 'add';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admin';
    }

    public function beforeSave($insert){
        if($insert){//添加
            //保存之前添加船舰时间
            $this->created_at=time();
            //保存之前对密码进行hash加密
            $this->password_hash=\Yii::$app->security->generatePasswordHash($this->password_hash);
            //保存之前生成一个随机数
            $this->auth_key=\Yii::$app->security->generateRandomString();
        }else{//修改
            $this->updated_at=time();
            //判断有密码才执行
            if($this->password) {
                $this->password_hash = \Yii::$app->security->generatePasswordHash($this->password);
                //修改之前生成一个随机数
                $this->auth_key = \Yii::$app->security->generateRandomString();
            }
        }

        return parent::beforeSave($insert);

    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username','email'], 'required'],
            [['password'],'required','on'=>'add'],//on定义场景只有在添加的时候场景生效密码不能为空 修改可以为空
            [['status', 'created_at', 'updated_at', 'last_login_time'], 'integer'],
            [['username', 'password_hash', 'password_reset_token', 'email', 'last_login_ip'], 'string', 'max' => 255],
            [['logo'], 'string'],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
            [['password'],'string'],
            [['roles'],'safe','on'=>'add'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'last_login_time' => 'Last Login Time',
            'last_login_ip' => 'Last Login Ip',
            'roles' => '选择角色',

        ];
    }

    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        return self::findOne(['id'=>$id]);
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: Implement findIdentityByAccessToken() method.
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
       return $this->auth_key;
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }


    //获取用户的菜单
    public function getMenus(){
        $menuItems = [];
        //获取所有一级菜单
        $menus = Menu::find()->where(['parent_id'=>0])->all();
        foreach($menus as $menu){
            //获取一级菜单的所有子菜单
            $children = Menu::find()->where(['parent_id'=>$menu->id])->all();
            $items = [];
            foreach ($children as $child){
                //判断当前用户是否有该路由的权限
                if(Yii::$app->user->can($child->url)){
                    $items[] = ['label' => $child->name, 'url' => [$child->url]];
                }
            }

            $menuItems[] = ['label' => $menu->name, 'items'=>$items];
        }
        return $menuItems;

    }

}
