<?php
namespace frontend\modules\appsrv\controllers;

use Qiniu\Auth;

/**
 * Default controller for the `appsrv` module
 */
class QiniuController extends AppSrvBase1Controller
{

    public function actionIndex()
    {
        echo 23;
    }

    public function actionRequestUploadToken()
    {
        $data = parent::getJ();
        // var_dump($data["bucket_name"]) ;
        
        $accessKey = \Yii::$app->params["qiniu"]['ak'];
        $secretKey = \Yii::$app->params["qiniu"]['sk'];
        $auth = new Auth($accessKey, $secretKey);
        $upToken = $auth->uploadToken($data["bucket_name"]);
        parent::rtn_json(['cd'=>"1" ,"uptoekn"=>$upToken]) ;
     
    }
}
