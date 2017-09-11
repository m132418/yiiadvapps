<?php
namespace frontend\modules\appsrv\controllers;
use common\models\Relationship;
use yii\helpers\Json;
use common\models\RelationshipUser;
/**
 * Default controller for the `appsrv` module
 */
class RelationshipController extends AppSrvBase2Controller
{

    /**
     * list all relationship about me
     */
    public function actionIndex()    
    {
        $u = parent::getU();
        $db =  \Yii::$app->db ;
        $sql ='SELECT relationship.id,relationship.rtype,relationship_user.uid FROM relationship_user INNER JOIN relationship ON relationship.id = relationship_user.rid WHERE uid =' . $u->getId(); 
        
        
        $data = $db->createCommand($sql)->queryAll() ;
        header("Content-type: application/json;charset=utf-8");
         echo( Json::encode(['cd'=>1,'data'=>$data]));

    }
    
    /**
     * build 关系
     */
    public function actionBuildr($rtype)    
    {
        $u = parent::getU();        
        $r = new Relationship();      
        $r->rtype  = $rtype ;            
        $r->created_by = $u->getId() ;
        $r->save(false);
        
        $ru = new RelationshipUser();
        $ru->rid = $r->id ;
        $ru->uid = $u->getId() ;
        $ru->save(false);
        header("Content-type: application/json;charset=utf-8");
          echo( Json::encode(['cd'=>1,'rid'=> $r->id  ]));    
    } 
    /**
     * connect 关系
     */
    public function actionConnectr($rid ,$rtype,$uid )    
    {
     $db =  \Yii::$app->db ;
        if ($rtype === Relationship::RELATIONSHIP_QL) {
            
            $model = $db->createCommand('SELECT COUNT(1) FROM relationship_user where rid =' . $rid);           
            $users_count = $model->queryScalar();
            if ($users_count == 2 ) {
                header("Content-type: application/json;charset=utf-8");
               echo( Json::encode(['cd'=>-1,'msg'=>"already 2 body"]));
            }
             
        }
        
     $isExist =   RelationshipUser::findOne(['rid'=>$rid,'uid'=>$uid]) ;
        
     if ($isExist) {
         header("Content-type: application/json;charset=utf-8");
         echo( Json::encode(['cd'=>-1,'msg'=>"already exist don't need connect more"]));
     }
        
        $ru = new RelationshipUser();
        $ru->rid = $rid;        
        $ru->uid = $uid ;
        $ru->save(false);
        header("Content-type: application/json;charset=utf-8");
         echo( Json::encode(['cd'=>1]));
    
    }
}
