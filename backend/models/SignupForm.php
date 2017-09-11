<?php
namespace backend\models;

use common\components\MyHelpers;
use yii\base\Model;
use backend\models\CommonAdmin;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\backend\models\CommonAdmin', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 30],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 128],
            ['email', 'unique', 'targetClass' => '\backend\models\CommonAdmin', 'message' => 'This email address has already been taken.'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {

//            MyHelpers::p($this->getErrors());
//            die();

            return null;
        }
        
        $user = new CommonAdmin();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->status = CommonAdmin::STATUS_ACTIVE ;
//        var_dump($user->attributes) ;die() ;

        return $user->save(false) ? $user : null;
    }
}
