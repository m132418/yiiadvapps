<?php

namespace common\models;

use Yii;

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
            'productid' => 'Productid',
            'cateid' => 'Cateid',
            'title' => 'Title',
            'descr' => 'Descr',
            'num' => 'Num',
            'price' => 'Price',
            'cover' => 'Cover',
            'pics' => 'Pics',
            'issale' => 'Issale',
            'ishot' => 'Ishot',
            'istui' => 'Istui',
            'saleprice' => 'Saleprice',
            'ison' => 'Ison',
            'createtime' => 'Createtime',
        ];
    }
}
