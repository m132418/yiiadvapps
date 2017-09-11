<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Product */
?>
<div class="product-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'productid',
            'cateid',
            'title',
            'descr:ntext',
            'num',
            'price',
            'cover',
            'pics:ntext',
            'issale',
            'ishot',
            'istui',
            'saleprice',
            'ison',
            'createtime',
        ],
    ]) ?>

</div>
