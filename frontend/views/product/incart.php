<?php
//use common\widgets\Alert ;
use frontend\models\Cart ;
use common\models\Product ;
//$alert = new Alert();
//Alert::widget();
?>


<table class="table table-bordered">

<thead>


    <tr>

    <th>缩略图</th>
    <th>题目</th>
    <th>订购数量</th>
    <th>单价</th>
        <th>小计</th>
    </tr>
</thead>

<tbody>
    <tr>
        <td>
            <div class="col-xs-6 col-md-3">
                <a href="#" class="thumbnail">
                    <img class="img-responsive" src="http://placehold.it/75x50" alt="">
                </a>
            </div>
        </td>
        <td><?=$prod->title?></td>
        <td><?=$cart->productnum?></td>
        <td><?=$prod->price?></td>
        <td><?=$cart->productnum *  $prod->price?></td>
    </tr>

</tbody>


</table>

<a class="btn btn-default pull-right" href="#" role="button">结帐去</a>
