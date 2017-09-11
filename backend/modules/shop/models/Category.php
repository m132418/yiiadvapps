<?php

namespace backend\modules\shop\models;

use Yii;

/**
 * This is the model class for table "{{%shop_category}}".
 *
 * @property string $id
 * @property string $title
 * @property string $pid
 * @property string $createtime
 */
class Category extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shop_category}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pid', 'createtime'], 'integer'],
            [['title'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Cateid',
            'title' => '节点名称',
            'pid' => 'Parentid',
            'createtime' => 'Createtime',
        ];
    }
}
