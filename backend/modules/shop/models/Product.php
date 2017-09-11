<?php
namespace backend\modules\shop\models;
use Yii;
//use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "shop_product".
 *
 * @property string $productid
 * @property string $cateid
 * @property string $title
 * @property string $descr
 * @property string $num
 * @property string $price
 * @property string $cover
 * @property string $pics
 * @property string $issale
 * @property string $ishot
 * @property string $istui
 * @property string $saleprice
 * @property string $ison
 * @property string $createtime
 */
class Product extends \yii\db\ActiveRecord
{
//    public function behaviors()
//    {
//        return [
//            TimestampBehavior::className(),
//        ];
//    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cateid', 'num', 'createtime'], 'integer'],
            [['descr', 'pics', 'issale', 'ishot', 'istui', 'ison'], 'string'],
            [['price', 'saleprice'], 'number'],
            [['title', 'cover'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'productid' => '产品id',
            'cateid' => '分类id',
            'title' => '产品题目',
            'descr' => '产品描述',
            'num' => '库存数量',
            'price' => '价格',
            'cover' => '封面',
            'pics' => '图片',
            'issale' => '是否促销',
            'ishot' => '是否热卖',
            'istui' => '是否热推',
            'saleprice' => '促销价格',
            'ison' => '上线',
            'createtime' => '创建时间',
        ];
    }
}
