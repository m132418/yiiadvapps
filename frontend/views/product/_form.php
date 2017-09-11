<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
require_once(Yii::getAlias('@vendor')."/mihaildev/yii2-ckeditor/CKEditor.php");
require_once(Yii::getAlias('@vendor')."/mihaildev/yii2-ckeditor/Assets.php");
use mihaildev\ckeditor\CKEditor ;
use mihaildev\ckeditor\Assets;
/* @var $this yii\web\View */
/* @var $model common\models\Product */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'cateid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>




    <?= $form->field($model, 'descr')->widget(CKEditor::className(),[
        'editorOptions' => [
            'preset' => 'full',
            'inline' => false,
        ],
    ]); ?>

    <?= $form->field($model, 'num')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cover')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pics')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'issale')->dropDownList([ '0', '1', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'ishot')->dropDownList([ '0', '1', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'istui')->dropDownList([ '0', '1', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'saleprice')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ison')->dropDownList([ '0', '1', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'createtime')->textInput(['maxlength' => true]) ?>

  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
