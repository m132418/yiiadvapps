<?php
namespace frontend\modules\appsrv\controllers;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Response;

/**
 * Default controller for the `appsrv` module
 */
class AppSrvBase1Controller extends \yii\rest\Controller
{

//    public function behaviors()
//    {
//        $behaviors = parent::behaviors();
//        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_HTML;
//        return $behaviors;
//    }

    protected function rtn_json($arr = ['cd'=>"1"]) {
//         \Yii::$app->response->statusCode = 200;
        header("Content-type: application/json;charset=utf-8");        
        echo( Json::encode($arr));
        exit()  ;
    }
    protected function getJ()
    {
        $post = file_get_contents("php://input");
        //decode json post input as php array:
       
        $data = Json::decode($post, true);

        return $data ;
    }
}
