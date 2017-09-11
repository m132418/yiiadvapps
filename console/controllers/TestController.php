<?php
namespace console\controllers;
use yii\console\Controller ;
$path = \Yii::getAlias("@vendor/hongshuo/yii2-v-player/VideoJsWidget.php");
require_once($path);
$path2 = \Yii::getAlias("@vendor/hongshuo/yii2-v-player/VideoJsAsset.php");
require_once($path2);
use hongshuo\vpalyer\VideoJsWidget ;
use hongshuo\vpalyer\VideoJsAsset ;
/**
 * Site controller
 */
class TestController extends Controller
{
    /**
     * 调用 ./yii test/hey
     */
    public function actionHey()
    {
       echo 777 ;
    }

    public function actionIndex()
    {
        $v = new VideoJsWidget();

    }

}
