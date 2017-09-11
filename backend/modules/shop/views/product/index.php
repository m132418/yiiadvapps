<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Products';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Product', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'productid',
            'cateid',
            'title',
            'descr:ntext',
            'num',
            // 'price',
            // 'cover',
            // 'pics:ntext',
            // 'issale',
            // 'ishot',
            // 'istui',
            // 'saleprice',
            // 'ison',
            // 'createtime',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
