<?php
use frontend\models\ProdDetailForm;
use yii\widgets\ActiveForm;
$this->registerCssFile('css/jquery.bsPhotoGallery.css', ['depends' => ['frontend\assets\AppAsset'], 'position' => $this::POS_HEAD]);
$this->registerJsFile('js/jquery.bsPhotoGallery.js', ['depends' => ['frontend\assets\AppAsset'], 'position' => $this::POS_END]);
$this->registerJsFile('js/prod_photos.js', ['depends' => ['frontend\assets\AppAsset'], 'position' => $this::POS_END]);
?>

<!-- Portfolio Item Heading -->
<div class="row">
    <div class="">
        <h3 class="page-header"><?= $model->title ?>
            <!--            <small>Item Subheading</small>-->
        </h3>
    </div>
</div>
<!-- /.row -->

<!-- Portfolio Item Row -->
<div class="row">

    <div class="col-md-8">
        <img class="img-responsive" src="http://placehold.it/750x500" alt="">
    </div>

    <div class="col-md-4">

        <div class="well">

            <span><?= $model->price ?></span>

        </div>

        <div class="gcIpt">
            <span class="guT">数量</span>
            <input id="min" name="" type="button" value="-">
            <input id="text_box" name="" type="text" value="0" style="width:30px; text-align: center; color: #0F0F0F;"
                   disabled="true">
            <input id="add" name="" type="button" value="+">

        </div>

        <div>
            <?php $form = ActiveForm::begin(); ?>




            <input type="hidden" id="proddetailform-num" class="form-control" name="ProdDetailForm[num]">

            <input id="add_cart" name="" type="submit" value="加入购物车">
            <input id="buy_it" name="" type="submit" value="一键购买">
            <?php ActiveForm::end(); ?>

        </div>

    </div>

</div>
<!-- /.row -->

<!-- Related Projects Row -->
<div class="row">

    <div class="col-lg-12">

        <ul class="row first" style="list-style-type:none">
            <li>
                <img alt="Rocking the night away" src="http://placehold.it/500x300">
                <!--<div class="text">Consectetur adipiscing elit</div>-->
            </li>
            <li>
                <img alt="Yellow sign" src="http://placehold.it/500x300">
                <!--<div class="text">Lorem ipsum dolor sit amet, labore et dolore magna aliqua. Ut enim ad minim veniam</div>-->
            </li>

            <li>
                <img alt="Spaghetti bitch" src="http://placehold.it/500x300">
                <!--<div class="text">Lorem ipsum dolor sit amet, labore et dolore magna aliqua. Ut enim ad minim veniam</div>-->
            </li>
            <li>
                <img alt="Budget this" src="http://placehold.it/500x300">
                <!--<div class="text">Adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam</div>-->
            </li>
            <li>
                <img src="http://placehold.it/500x300">
                <!--<div class="text">Consectetur adipiscing elit, re magna aliqua. Ut enim ad minim veniam</div>-->
            </li>

        </ul>

    </div>


</div>
<!-- /.row -->

<?php
$script = <<< JS
    $(document).ready(function(){
        $("#min").click(function(){
        var  num = $("#text_box").val() ;
          var numInt = parseInt(num);
          numInt = numInt -1 ;
          
          if (numInt > 0)
             // console.log(numInt) ;
             $("#text_box").val("" + numInt) ;
             else 
                $("#text_box").val("0" ) ;
            
        });
        
        $("#add").click(function(){
        var  num = $("#text_box").val() ;
          var numInt = parseInt(num);
            numInt =numInt +1 ;
              if (numInt > 0)
             // console.log(numInt) ;
             $("#text_box").val("" + numInt) ;
             else 
                   $("#text_box").val("0" ) ;
        });
        
       $("#add_cart").click(function(){
        var num =  $("#text_box").val();
        $("#proddetailform-num").val(num) ;
        console.log(num) ;
        });
        
        
        
    });
JS;
$this->registerJs($script);
?>




