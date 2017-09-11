<?php
namespace frontend\modules\appsrv\controllers;
use common\models\EasemobExt ;
use common\models\Status;
// use ;
/**
 * Default controller for the `appsrv` module
 */
class EasemobExtController extends AppSrvBase2Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
    echo 1 ;
    }
    
    public function actionReqFriend()
    {

        $uid = parent::getU()->getId();
        $data = EasemobExt::find()->where(['to_id'=>$uid , 'etype'=>EasemobExt::E_TYPE_REQ_FRIEND])->asArray()->all() ;    
        foreach ($data as $key => $value) {
            $data[$key]["ext"] = json_decode($value["ext"]) ;
        }
        parent::rtn_json(['cd'=>"1" ,"list"=>$data]) ;
    }
    
    public function actionReqBinding()
    {    
        $uid = parent::getU()->getId();
        $data = EasemobExt::find()->where(['to_id'=>$uid , 'etype'=>EasemobExt::E_TYPE_REQ_BINDING])->asArray()->all() ;
        
        foreach ($data as $key => $value) {
           $data[$key]["ext"] = json_decode($value["ext"]) ;
        }
        
        parent::rtn_json(['cd'=>"1" ,"list"=>$data]) ;
    }
    public function actionReqInteractMsg()
    {
        $uid = parent::getU()->getId();
        $data = EasemobExt::find()->where(['to_id'=>$uid , 'status'=>Status::STATUS_ACTIVE ,'etype'=>EasemobExt::E_TYPE_INTERACT])->asArray()->all() ;
        
        foreach ($data as $key => $value) {
            $data[$key]["ext"] = json_decode($value["ext"]) ;
        }
        
        parent::rtn_json(['cd'=>"1" ,"list"=>$data]) ;
    }
    public function actionReqSysMsg()
    {
        echo 9;exit() ;
        $uid = parent::getU()->getId();
        $data = EasemobExt::find()->where(['to_id'=>$uid , 'status'=>Status::STATUS_ACTIVE ,'etype'=>EasemobExt::E_TYPE_SYS])->asArray()->all() ;
    
        foreach ($data as $key => $value) {
            $data[$key]["ext"] = json_decode($value["ext"]) ;
        }
    
        parent::rtn_json(['cd'=>"1" ,"list"=>$data]) ;
    }
}
