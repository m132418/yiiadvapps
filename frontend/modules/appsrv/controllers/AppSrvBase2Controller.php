<?php
namespace frontend\modules\appsrv\controllers;
use common\models\User;
use yii\filters\auth\HttpBasicAuth;
use yii\web\Controller;
use yii\web\Response;
use yii\helpers\Json;
/**
 * Default controller for the `appsrv` module
 */
class AppSrvBase2Controller extends \yii\rest\Controller
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_HTML;
            $behaviors['authenticator'] = [
                'class' => HttpBasicAuth::className(),
            ];
        return $behaviors;
    }
    
    protected function getU()
    {
        $request = \Yii::$app->request;
        $u_token =  $request->getAuthUser();    
        return  User::findIdentityByAccessToken($u_token) ;
    }
    
    protected function getJ()
    {
        $post = file_get_contents("php://input");
        //decode json post input as php array:
        $data = Json::decode($post, true);
        return $data ;
    }
    
    protected function rtn_json($arr = ['cd'=>"1"]) {
        header("Content-type: application/json;charset=utf-8");
             echo( Json::encode($arr));
             exit()  ;
    }

}
