<?php
namespace frontend\modules\appsrv\controllers;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use common\models\SiteMessenger ; 
use common\models\Deposit ;
use common\models\Wish ;
use common\models\MessengerFriendlist ;
use common\models\Status;
use common\models\Relationship ;
use common\models\RelationshipUser ;
/**
 * Default controller for the `appsrv` module
 */
class SiteMessengerController extends AppSrvBase2Controller
{

    /**
     * list all site message sent to me
     */
    public function actionIndex()
    {
        $uid = parent::getU()->getId();        
        $db =  \Yii::$app->db ;      
      $sql =" SELECT".
            " s.id,".
            " s.frm_id,".
            " u.nickname,".
            " s.to_id,".
            " s.mtype,".
            " s.content,".
            " s.is_read,".
            " s.`status`,".
            " s.created_at,".
            " s.updated_at".
            " FROM".
            " site_messenger AS s".
            " INNER JOIN `user` AS u ON s.frm_id = u.id".
            " WHERE".
            " s.to_id =  " . $uid ;   
      $data = $db->createCommand($sql)->queryAll();
      header("Content-type: application/json;charset=utf-8");
   echo (Json::encode([
       'cd' => 1,'messages' =>$data 
   ]));
    }
    public function actionUdtRead()
    {
        $data = $data = parent::getJ() ;
        $data["mid"] ;
        $m = SiteMessenger::findOne(['id'=> $data["mid"] ]);
        $m->is_read = 1 ;
        $m->save(false) ;
        header("Content-type: application/json;charset=utf-8");
        echo (Json::encode([
            'cd' => 1
        ]));
    }

    public function actionShot1()
    {        
    
        $data = $data = parent::getJ() ; 
//         VarDumper::dump($data["to_id"]) ;
        
        $uid = parent::getU()->getId();
        $m = new SiteMessenger() ;
        $m->to_id = $data["to_id"] ;
        $m->frm_id = $uid ;
        $m->content = $data["content"] ;
        $m->save(false) ;
        header("Content-type: application/json;charset=utf-8");
        echo (Json::encode([
            'cd' => 1
        ]));
    }
    /**
     * {
   "is_agree" : "0", 
   "mid":1,
   "wishid":3,
    "deposits" : [
      {
         "amt" : 3000.0,
         "uid" : "1"
      },
      {
         "amt" : 3000.0,
         "uid" : "2"
      }
   ]
 
}

     */
    public function actionRespTfjs()
    {
       $req_j = parent::getJ() ;
       $uid = parent::getU()->getId();
       
//        VarDumper::dump($req_j) ;
       
       if ($req_j["is_agree"] == 1) {
       $d =   Deposit::findOne(['id'=>$req_j["depositid"] ]) ;
       $d->is_agree = 1 ;
       $d->save(false) ;
       
       $isall_agree =   Deposit::findOne(['wishid'=>$req_j["wishid"],'is_agree'=>0 ]) ;
        
       if ($isall_agree) {
           header("Content-type: application/json;charset=utf-8");
           echo (Json::encode([
               'cd' => -1 ,"msg"=> "还得等全部都同意了愿望就开始了"
           ]));
           return ;
       }else
       {
           $w =   Wish::findOne(['id'=>$req_j["wishid"] ]) ;
           $w->kickoff_at = time();
           $w->save(false);
           header("Content-type: application/json;charset=utf-8");
           echo (Json::encode([
               'cd' => 1
           ]));
           return ;
       }
       
       
       }else 
       {//不同意 得重发
           // update all other deposit is_agree to 0 , mine to 1
     
           foreach ($req_j["deposits"]  as $done) {
               $d =    Deposit::findOne(["wishid"=>$req_j["wishid"]  , "uid"=>$done["uid"]]) ;
               if ($done["uid"] == $uid) {             
              $d->is_agree = 1 ;
              $d->split_amt = $done["amt"];
              
               }else {
//                    $d2 =    Deposit::findOne(["wishid"=>$req_j["wishid"]  , "uid"=>$done["uid"]]) ;
                   $d->is_agree = 0 ;
                   $d->split_amt = $done["amt"];
                   $d->save(false) ;
                      
                 }
                 $d->save(false) ;
               }
               
               
               // disable all tfjs site message
               $ids_array = $this->all_r_ids($req_j["wishid"]);
               $this->clear_msg_before($req_j["wishid"], $req_j["frm_id"],$ids_array) ;
                
               // send tfjs request
               $uid_dids =   $this->all_uid_did_ids() ;
               foreach ($uid_dids  as $dsingle) {
                   if ($uid!=$dsingle["uid"]) {
                       $this->send_message_for_reconfirm($uid, $dsingle["uid"], $req_j["wishid"] ,  $dsingle["did"], $req_j["deposits"]);
                   }
               
               
           }
           header("Content-type: application/json;charset=utf-8");
           echo (Json::encode([
               'cd' => 1
           ]));
           
       }// end of 不同意得重发
       
      
      
    }   
    
    private function clear_msg_before($wishid,$senderid ,$ids_array){
        $my_array =$ids_array;

        $to_remove = [$senderid] ;
        $result = array_diff($my_array, $to_remove);
      
        $indcondition  = "(" .  implode(",",$result). ")" ;
        
        
        $connection = \Yii::$app->db;
        $connection->createCommand()->update('site_messenger', ['status' => Status::STATUS_INACTIVE], 'frm_id =' . $senderid . " and " . 'mtype =' . SiteMessenger::MTYPE_REQ_TFJS . " and " . $indcondition )->execute();
        
    }
    
    private function all_uid_did_ids() {
        $db =  \Yii::$app->db ;
        $sql =" SELECT".
            " relationship_user.uid,".
            " deposit.id as did".
            " FROM".
            " wish".
            " INNER JOIN relationship ON wish.rid = relationship.id".
            " INNER JOIN relationship_user ON relationship_user.rid = relationship.id".
            " INNER JOIN deposit ON wish.id = deposit.wishid AND deposit.uid = relationship_user.uid".
            " WHERE".
            "	wish.id = 2";
        //         var_dump($sql);
        $data= $db->createCommand($sql)->queryAll();
    return $data ;
         
    }
    
    private function send_message_for_reconfirm( $from_id ,$to_id,$wishid,$depositid,$deposits) {
          $m = new SiteMessenger() ;
               $m->to_id = $to_id;
               $m->frm_id = $from_id ;
               $m->mtype = SiteMessenger::MTYPE_REQ_TFJS ;
               $m->content =Json::encode([  'wishid'=>$wishid, "your-deposit-id"=>$depositid,'deposits'=>$deposits]) ;
               $m->save(false) ;
    }
    
    
    
    /**
     * @param wishid
     */private function all_r_ids($wishid)
    {
        $db =  \Yii::$app->db ;
        $sql = "SELECT".
            " lilimiapp.relationship_user.uid".
            " FROM".
            " lilimiapp.wish".
            " INNER JOIN lilimiapp.relationship ON lilimiapp.wish.rid = lilimiapp.relationship.id".
            " INNER JOIN lilimiapp.relationship_user ON lilimiapp.relationship_user.rid = lilimiapp.relationship.id".
            " WHERE lilimiapp.wish.id = " . $wishid ;
        $model = $db->createCommand($sql);
        $data =  $model->queryColumn("uid");
       
        
        $my_array =$data;
        return $my_array;
    }

    
    public function actionReqAsFriend()
    {
        $uid = parent::getU()->getId();
        $data =  parent::getJ() ;
        
//      $this->add_in_my_friend_list($uid, $data["outerid"]);

       
       $smsg =new SiteMessenger() ;
       $smsg->frm_id = $uid ;
       $smsg->to_id = $data["outerid"] ;
       $smsg->mtype = SiteMessenger::MTYPE_REQ_IN_LIST ;
       $smsg->save(false) ;
       
       header("Content-type: application/json;charset=utf-8");
        echo (Json::encode([
            'cd' => 1
        ]));
    }
    public function actionRespAsFriend()
    {
        $data =  parent::getJ() ;
        $m = SiteMessenger::findOne(["id" => $data["mid"]]);
        $m->is_read = 1 ;
        $m->save(false) ;
        if ($data["is_agree"]==1) {
           $this->add_in_my_friend_list($data["frm_id"], $data["to_id"]);
        }else 
        {
            
            // $m->status = Status::STATUS_INACTIVE ;
        }
        header("Content-type: application/json;charset=utf-8");
        echo (Json::encode([
            'cd' => 1
        ]));
    }
    
    public function actionRespAsQy()
    {
     $data = parent::getJ();        
     $s = new  \frontend\modules\appsrv\services\SiteMessengerService();
     $r = new  \frontend\modules\appsrv\services\RelationshipService();
     $s->mark_as_read($data["mid"]) ;
     
     if ($data["is_agree"]) {
         $r->relationship_connect($data["rid"], $data["to_id"]) ;
     }else
     {
         $s->send_msg($data["to_id"], $data["frm_id"], 0 , "那个谁决绝了你加亲友的请求") ;
     }
     header("Content-type: application/json;charset=utf-8");
     echo (Json::encode([
         'cd' => 1
     ]));
    }
    public function actionRespAsLy()
    {
        $data = parent::getJ();
        $s = new  \frontend\modules\appsrv\services\SiteMessengerService();
        $r = new  \frontend\modules\appsrv\services\RelationshipService();
        $s->mark_as_read($data["mid"]) ;
         
        if ($data["is_agree"]) {
            $r->relationship_connect($data["rid"], $data["to_id"]) ;
        }else
        {
            $s->send_msg($data["to_id"], $data["frm_id"], 0 , "那个谁决绝了你加亲友的请求") ;
        }
        header("Content-type: application/json;charset=utf-8");
        echo (Json::encode([
            'cd' => 1
        ]));
    }
    public function actionRespAsQl()
    {
        $data = parent::getJ();
        $m = SiteMessenger::findOne([
            "id" => $data["mid"]
        ]);
        $m->is_read = 1;
        $m->save(false);
        if ($data["is_agree"] == 1) {
            // 连接建立情侣关系
            if ($this->is_already_in_ql($data["to_id"])) {
                 echo (Json::encode(['cd' => -1,"msg"=>"已经存在情侣关系了" ])); return ;
            }else 
            {// 建立情侣关系
                $r = new Relationship();
                $r->rtype  = Relationship::RELATIONSHIP_QL ;
                $r->created_by =$data["frm_id"] ;
                $r->save(false);
                
                $ru = new RelationshipUser();
                $ru->rid = $r->id ;
                $ru->uid = $data["frm_id"] ;
                $ru->save(false);
                
                $ru2 = new RelationshipUser();
                $ru2->rid = $r->id ;
                $ru2->uid = $data["to_id"] ;
                $ru2->save(false);
            }
            
        }else 
        {
            // xxx 已经拒绝了你的建立情侣关系的申请
            
            $smsg =new SiteMessenger() ;
            $smsg->frm_id =["to_id"] ;
            $smsg->to_id = ["frm_id"]  ;
            $smsg->content = "刚刚拒绝了建立情侣关系的申请" ;
            $smsg->save(false) ;
            
        }
        header("Content-type: application/json;charset=utf-8");
        echo (Json::encode([
            'cd' => 1
        ]));
    
    }
    private function is_already_in_ql($uid){
        $db =  \Yii::$app->db ;
        $sql = "SELECT".
            " relationship.id,".
            " relationship_user.uid,".
            " rtype".
            " FROM".
            " relationship_user".
            " INNER JOIN relationship ON".
            " relationship_user.rid = relationship.id".
            " where rtype = 3 AND uid = " .$uid
                ;
                $model = $db->createCommand($sql);
                $ifexist = $model->queryScalar(); 
                return $ifexist ;
    }
    
    public function actionReqAsQl()
    {
       $uid = parent::getU()->getId();
        $data =  parent::getJ() ;
        
//      $this->add_in_my_friend_list($uid, $data["outerid"]);

       
       $smsg =new SiteMessenger() ;
       $smsg->frm_id = $uid ;
       $smsg->to_id = $data["outerid"] ;
       $smsg->mtype = SiteMessenger::MTYPE_REQ_R_QL ;
       $smsg->save(false) ;
       
       header("Content-type: application/json;charset=utf-8");
        echo (Json::encode([
            'cd' => 1
        ]));
    }
  
    
    public function actionReqAsQy()
    {
        $uid = parent::getU()->getId();
        $data =  parent::getJ() ;        
        $service = new \frontend\modules\appsrv\services\RelationshipService() ;
    $rid =    $service->is_some_type_relationship_exists(Relationship::RELATIONSHIP_QY, $uid) ;
    
//     VarDumper::dump($rid) ;
    
    if (!$rid) {
        header("Content-type: application/json;charset=utf-8");
         echo (Json::encode([
            'cd' => -1 ,"msg"=>"先建立一个亲友关系再申请"
        ]));
    }
    
    $service2 = new \frontend\modules\appsrv\services\SiteMessengerService();
    $service2->send_msg($uid, $data["outerid"], SiteMessenger::MTYPE_REQ_R_QY, Json::encode(["rid"=>$rid])) ;
    header("Content-type: application/json;charset=utf-8");
    echo (Json::encode([
        'cd' => 1 
    ]));
    
    }
    public function actionReqAsLy()
    {
        $uid = parent::getU()->getId();
        $data =  parent::getJ() ;
        $service = new \frontend\modules\appsrv\services\RelationshipService() ;
        $rid =    $service->is_some_type_relationship_exists(Relationship::RELATIONSHIP_LY, $uid) ;
    
        //     VarDumper::dump($rid) ;
    
        if (!$rid) {
            header("Content-type: application/json;charset=utf-8");
            echo (Json::encode([
                'cd' => -1 ,"msg"=>"先建立一个粒友关系再申请"
            ]));
        }
    
        $service2 = new \frontend\modules\appsrv\services\SiteMessengerService();
        $service2->send_msg($uid, $data["outerid"], SiteMessenger::MTYPE_REQ_R_LY, Json::encode(["rid"=>$rid])) ;
        header("Content-type: application/json;charset=utf-8");
        echo (Json::encode([
            'cd' => 1
        ]));
    
    } 
    
    
private function add_in_my_friend_list($uid, $outerid)
    {
      
           $flist = new MessengerFriendlist() ;
           $flist->myid = $uid ;
           $flist->outerid = $outerid ;
           $flist->save(false) ;
       
    }

}
