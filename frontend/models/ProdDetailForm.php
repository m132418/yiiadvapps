<?php
namespace frontend\models;

use yii\base\Model;
use common\models\User;

/**
 * Signup form
 */
class ProdDetailForm extends Model
{
    public $price;
    public $num;
    public $productid;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
//            ['username', 'trim'],
//            ['username', 'required'],
            ['num', 'integer'],
//            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.'],
//            ['username', 'string', 'min' => 2, 'max' => 255],
//
//            ['email', 'trim'],
//            ['email', 'required'],
//            ['email', 'email'],
//            ['email', 'string', 'max' => 255],
//            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],
//
//            ['password', 'required'],
//            ['password', 'string', 'min' => 6],
        ];
    }

}
