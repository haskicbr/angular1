<?php

namespace app\models;

use Yii;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $login
 * @property string $password
 * @property integer $create
 * @property integer $update
 * @property string $email
 * @property string $password_hash
 * @property string $password_reset_token
 * @property integer $status
 * @property string $auth_key
 * @property string $access_token
 * @property bool $rememberMe
 */

class User extends ActiveRecord implements IdentityInterface
{

    public $rememberMe;
    public $password_repeat;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['login', 'password', 'create', 'update', 'email', 'password_hash', 'status', 'auth_key', 'access_token'], 'required'],
            [['create', 'update', 'status'], 'integer'],
            [['login', 'password', 'password_hash', 'password_reset_token', 'auth_key', 'access_token'], 'string', 'max' => 255],
            [['email'], 'string', 'max' => 50],
            [['password'], 'compare', 'compareAttribute' => 'password_repeat', 'message' => 'пароли должны совпадать'
                /* => 'MotherRelatedAttribute'*/, /*'on' => self::SCENARIO_CREATE_RST*/],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'login' => 'Login',
            'password' => 'Пароль',
            'create' => 'Create',
            'update' => 'Update',
            'email' => 'Email',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'status' => 'Status',
            'auth_key' => 'Auth Key',
            'access_token' => 'Access Token',
            'rememberMe' => 'оставаться в системе',
            'password_repeat' => 'повторите пароль'
        ];
    }

    /**
     * Finds an identity by the given ID.
     *
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface|null the identity object that matches the given ID.
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }


    /**
     * Finds an identity by the given token.
     *
     * @param string $token the token to be looked for
     * @return IdentityInterface|null the identity object that matches the given token.
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * @return int|string current user ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string current user auth key
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @param string $authKey
     * @return bool if auth key is valid for current user
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->auth_key = \Yii::$app->security->generateRandomString();
            }
            return true;
        }
        return false;
    }


    public static function findByUsername($username)
    {
        foreach (self::$users as $user) {
            if (strcasecmp($user['username'], $username) === 0) {
                return new static($user);
            }
        }

        return null;
    }


}
