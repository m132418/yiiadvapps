<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Categories';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Category', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            'id',
//            'name',

            [
                'attribute' => 'name',
                'label'=>'title',
                'value'=>function($model) {

                    if ($model["level"] -1 > 0)
                        return   $model["html"] . $model["name"] ;
//                    elseif ($model["level"] ==2 )
//                        return      '|_' .  $model["name"] ;
                    else
                        return $model["name"];
                }
            ],

            'pid',
            'level',
//            'createtime',
//        'html',
//            [
//                'attribute' => 'level',
//                'label'=>'树位置',
//                'value'=>function($model) {
////            var_dump($model) ;die();
//             if ($model["level"] -1 > 0)
//           return      '|' . str_repeat("—",$model["level"] -1) ;
//                 else
//return '';
//                }
//            ],

//            ['class' => 'yii\grid\ActionColumn'],


//            [
//                'class' => 'yii\grid\ActionColumn',
//                'template' => '{view}',
//                'buttons' => [
//                    'view' => function ($url, $model, $key) {
//                        return Html::a('显示', ['view', 'id' => $key+1], ['class'=>'btn btn-sm btn-info']);
//                    }
//                ],
//                'options' => [
//                    'width' => 5
//                ]
//            ],


            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{user-view} {user-update} {user-delete}',
                'buttons' => [
                    // 下面代码来自于 yii\grid\ActionColumn 简单修改了下
                    'user-view' => function ($url, $model, $key) {
                        $options = [
                            'title' => 'View',
                            'aria-label' => 'View',
                            'data-pjax' => '0',
                        ];
//                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, $options);
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['view', 'id' => $key+1], $options);
                    },
                    'user-update' => function ($url, $model, $key) {
                        $options = [
                            'title' =>  'Update',
                            'aria-label' =>  'Update',
                            'data-pjax' => '0',
                        ];
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['update', 'id' => $key+1], $options);
                    },
                    'user-delete' => function ($url, $model, $key) {
                        $options = [
                            'title' =>  'Delete',
                            'aria-label' =>  'Delete',
                            'data-confirm' =>  'Are you sure you want to delete this item?',
                            'data-method' => 'post',
                            'data-pjax' => '0',
                        ];
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['delete', 'id' => $key+1], $options);
                    },
                ]
            ],



                    ],
    ]); ?>
</div>
