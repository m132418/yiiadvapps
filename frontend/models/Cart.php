<?php
namespace frontend\models;
use Yii;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "shop_cart".
 *
 * @property string $cartid
 * @property string $productid
 * @property string $productnum
 * @property string $price
 * @property string $userid
 * @property string $createtime
 */
class Cart extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_cart';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['productid', 'productnum', 'userid', 'createtime'], 'integer'],
            [['price'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'cartid' => 'Cartid',
            'productid' => 'Productid',
            'productnum' => 'Productnum',
            'price' => 'Price',
            'userid' => 'Userid',
            'createtime' => 'Createtime',
        ];
    }
}
