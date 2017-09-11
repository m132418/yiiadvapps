<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\shop\models\Category */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="category-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <label class="control-label" for="category-id">父节点</label>

    <select id="category-pid" class="form-control" name="Category[pid]" aria-invalid="false">
        <option value="0">根节点</option>

        <?php
        foreach ($rows as $item) {

        print_r( '<option value="' .  $item['key'] .   '">'  .  $item['val'] .  '</option>' ) ;

        }
        ?>



    </select>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
