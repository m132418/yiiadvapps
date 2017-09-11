<?php
namespace frontend\modules\appsrv\controllers;
use common\components\Easemob;
use common\models\FriendList;
use frontend\modules\appsrv\services\UserService;
use common\models\Status;
use common\models\Relationship;
use common\models\RelationshipUser;
/**
 * Default controller for the `appsrv` module
 */
class RegionController extends AppSrvBase2Controller
{

   
    
    public function actionQProv()
    {      
           
        
        $db =  \Yii::$app->db ;
        $sql =
        "SELECT name,id,parent_id,grade FROM region where grade =2" 
        ;
                $model = $db->createCommand($sql);
            $flist = $model->queryAll() ;
    
   
            parent::rtn_json(['cd'=>"1","prov"=> $flist]) ;
   
    }
    
    public function actionQCity()
    {
        $data = parent::getJ() ;
        var_dump($data["prov"]) ;
    
        $db =  \Yii::$app->db ;
        $sql =
        "SELECT name,id,parent_id,grade FROM region where grade =3 and parent_id =" . $data["prov"]  
            ;
            $model = $db->createCommand($sql);
            $flist = $model->queryAll() ;
    
             
            parent::rtn_json(['cd'=>"1","city"=> $flist]) ;
             
    }
    
    

    
 
  
    
  
  
}
