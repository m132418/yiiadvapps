<?php
namespace frontend\controllers;
use common\components\Category;
use common\components\MyHelpers;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\models\LoginForm;
use  yii\helpers\Json ;
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


    public function beforeAction($action){
        if( $action->id == 'index') {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

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
//        MyHelpers::p(Category::unlimitedForLevel1($rows));


        $cate = array(
    0 => array('id' => 1, 'pid' => 0, 'name' => '江西省'),
    1 => array('id' => 2, 'pid' => 0, 'name' => '浙江省'),
    2 => array('id' => 3, 'pid' => 1, 'name' => '上饶市'),
    3 => array('id' => 4, 'pid' => 3, 'name' => '广丰县'),
    4 => array('id' => 5, 'pid' => 2, 'name' => '杭州市'),
    5 => array('id' => 6, 'pid' => 5, 'name' => '西湖'),
    6 => array('id' => 7, 'pid' => 6, 'name' => '断桥'),
);
//        MyHelpers::p(Category::unlimitedForLayer($cate));

  echo      Json::encode($rows) ;
//        return $this->render('index');
    }
    public function actionT1(){
//        $collection = new \Ds\Vector([1, 2, 3]);
//
//        var_dump($collection->toArray());

    }
    public function actionT2(){
//        $v= new VideoJsWidget();
        ArrayHelper::map(CmsCatalog::get(0, CmsCatalog::find()->where(['status' => \funson86\cms\models\Status::STATUS_ACTIVE, 'page_type' => CmsCatalog::PAGE_TYPE_LIST])->asArray()->all()), 'id', 'label') ;
        return $this->render('t2');
    }
    public function actionT3(){
        return $this->render('t3');
    }
    public function actionT4(){
        $request = Yii::$app->request;
        if($request->isPost) {
         $var =   \Yii::$app->request->post() ;
//            var_dump($var['prodid']) ; var_dump($var['num']) ;


        }
    }
    public function actionT5(){
//        $redis = Yii::$app->redis10;
//        $result = $redis->executeCommand('hmset', ['test_collection', 'key1', 'val1', 'key2', 'val2']);
//        var_dump($result);
//        $id = Yii::$app->user->id;
//        var_dump($id) ;
return $this->render('incart') ;
    }

}
