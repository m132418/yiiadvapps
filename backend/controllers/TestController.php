<?php
namespace backend\controllers;
use common\components\Category;
use common\components\MyHelpers;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\models\LoginForm;
//
//$path = \Yii::getAlias("@vendor/hongshuo/yii2-v-player/VideoJsWidget.php");
//require_once($path);
//$path2 = \Yii::getAlias("@vendor/hongshuo/yii2-v-player/VideoJsAsset.php");
//require_once($path2);
//use hongshuo\vpalyer\VideoJsWidget ;
//use hongshuo\vpalyer\VideoJsAsset ;

/**
 * Site controller
 */
class TestController extends Controller
{


    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $rows = (new \yii\db\Query())
            ->select(['id', 'pid' ,'title as name'])
            ->from('shop_category')
            ->all();
//        MyHelpers::p($rows) ;
//        Category::unlimitedForLayer($rows);
        MyHelpers::p(Category::unlimitedForLevel1($rows));


        $cate = array(
    0 => array('id' => 1, 'pid' => 0, 'name' => '江西省'),
    1 => array('id' => 2, 'pid' => 0, 'name' => '浙江省'),
    2 => array('id' => 3, 'pid' => 1, 'name' => '上饶市'),
    3 => array('id' => 4, 'pid' => 3, 'name' => '广丰县'),
    4 => array('id' => 5, 'pid' => 2, 'name' => '杭州市'),
    5 => array('id' => 6, 'pid' => 5, 'name' => '西湖'),
    6 => array('id' => 7, 'pid' => 6, 'name' => '断桥'),
);
        MyHelpers::p(Category::unlimitedForLayer($cate));

//        return $this->render('index');
    }
    public function actionT1(){
        $collection = new \Ds\Vector([1, 2, 3]);

        var_dump($collection->toArray());
    }
    public function actionT2(){
//        $v= new VideoJsWidget();

        return $this->render('t2');
    }
    public function actionT3(){


        return $this->renderPartial('t3');
    }

}
