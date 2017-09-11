<?php
namespace frontend\modules\appsrv\controllers;
use common\components\Easemob;
use common\components\MyHelpers;
use common\models\EasemobExt;
use common\models\FriendList;
use common\models\Relationship;
use common\models\RelationshipUser;
use common\models\Status;
use common\models\Wish;
use frontend\modules\appsrv\services\DBService;
use frontend\modules\appsrv\services\UserProfileService;
use frontend\modules\appsrv\services\UserService;
use frontend\modules\appsrv\services\RelationshipService;
/**
 * Default controller for the `appsrv` module
 */
class CircleController extends AppSrvBase2Controller
{

    public function actionOnesWish()
    {
        $data = parent::getJ() ;
                $uid = parent::getU()->getId();
//                         var_dump($data) ;        
                        var_dump($uid) ;
                        
               $f_ids =    UserService::user_friendlist_ids($data["ones_id"]) ;
//               var_dump($f_ids) ;
            
              if (in_array("" . $uid, $f_ids))
              {
                $wishs =  Wish::findAll([
                      "created_by"=>$data["ones_id"]
                  ]) ;
                parent::rtn_json(['cd'=>"1" ,"data"=> $wishs]) ;
              }
              else
              {
                
                  parent::rtn_json(['cd'=>"1" ,"data"=> ""]) ;
              }
                        
                 
        
        
    }
    public function actionFindUser()
    {
        $data = parent::getJ() ;
//         $uid = parent::getU()->getId();
//                 var_dump($data["method"]) ;
//                 var_dump($data["val"]) ;
        $db =  \Yii::$app->db ;
          $sql = null ;     
                if ($data["method"] == 1) {
//                  $rows = (new \yii\db\Query())
//                 ->select(['id', 'nickname'])
//                 ->from('user')
//                 ->where([  'mobi' => $data["val"] ])
//                 ->all();
                    $sql = 
                  "SELECT `user`.id, `user`.username,       ".
"IFNULL(`user`.nickname,'') nickname,IFNULL(user_profile.age,0) age,".
"IFNULL(user_profile.gender,0) gender,IFNULL(user_profile.prov,0) prov, ".
"IFNULL(user_profile.city,0) city,IFNULL(user_profile.portrait,'') portrait,".
"IFNULL( user_profile.signature,'') signature ".
                        'FROM `user` LEFT JOIN user_profile       '.
                        'ON user_profile.uid = `user`.id          '.
                        "where `user`.mobi = '" .$data["val"] . "'" 
                            ;
                }else 
                {
//                     $rows = (new \yii\db\Query())
//                     ->select(['id', 'nickname'])
//                     ->from('user')
//                     ->where( ['like', 'nickname', $data["val"]  ])
//                     ->all();
                    
                    $sql =
                                    "SELECT `user`.id, `user`.username,       ".
"IFNULL(`user`.nickname,'') nickname,IFNULL(user_profile.age,0) age,".
"IFNULL(user_profile.gender,0) gender,IFNULL(user_profile.prov,0) prov, ".
"IFNULL(user_profile.city,0) city,IFNULL(user_profile.portrait,'') portrait,".
"IFNULL( user_profile.signature,'') signature ".
                    'FROM `user` LEFT JOIN user_profile       '.
                    'ON user_profile.uid = `user`.id          '.
                    "where `user`.nickname like '%" .$data["val"] . "%'"
                        ;
                    }
                    
                    $model = $db->createCommand($sql);
                    $flist = $model->queryAll() ;
              
//                     var_dump($flist) ;exit() ;

         
                parent::rtn_json(['cd'=>"1" ,"data"=> $flist]) ;
                
    }
    public function actionListFriendsGroupOnly1()
    {
        $uid = parent::getU()->getId();
        $jdata = parent::getJ() ;
        
      $data = $this->build_relation_users($uid, $jdata);
      parent::rtn_json(['cd'=>"1" ,"data"=> $data]) ;

    }
    /**
     * @param uid
     * @param jdata
     */private function build_relation_users($uid, $jdata)
    {
        //         var_dump($jdata["rtype"]) ;var_dump($uid) ;; exit();
                
                // (1) relationship_user inner join relationship : from  my uid and rtype -> rid
                $sql_in1=
                "SELECT".
                " relationship.id,".
                " relationship_user.uid,".
                " relationship_user.rid,".
                " relationship.rtype".
                " FROM relationship_user".
                " INNER JOIN relationship".
                " ON relationship_user.rid".
                " = relationship.id".
                " where relationship.rtype = ".$jdata["rtype"].
                " and relationship_user.uid = $uid"
                    ;
              $sql_in1_result =  DBService::q_with_native_sql($sql_in1, 1) ;
        //       var_dump(array_column($sql_in1_result, "rid")) ;exit() ;
                
                //(2)relationship_user: from rid ->target  uid exclude my uid
              $target_uid_array = (new \yii\db\Query())
              ->select([ 'uid' ])
              ->from('relationship_user')
              ->where([  'rid' => array_column($sql_in1_result, "rid") ])
              ->all();
              
             $target_uid_array = array_column($target_uid_array, "uid") ;      
            $target_uid_array = array_unique($target_uid_array);
            $key_target_uid_array = array_search($uid, $target_uid_array);
                unset($target_uid_array[$key_target_uid_array]);      
        //       var_dump($target_uid_array) ;
        //       exit();      
                // (3)query user portrait nickname with target  uid 
              $target_data =  UserProfileService::q_profile_from_uids_all($target_uid_array) ;
        //         var_dump($target_data) ;
                //
                $title = "" ;
                
            if ((int)$jdata["rtype"] == 3) {
                   $title = "情侣关系" ;
                }elseif ((int)$jdata["rtype"] == 4)
                {
                   $title = "亲友关系" ;
                }
               elseif ((int)$jdata["rtype"] == 5){
                    $title = "粒友关系" ;
                }
               
                
                $data= ["title"=>$title ,"rtype"=>$jdata["rtype"] ,"content"=>$target_data] ;
                return ($data) ;
    }

    
    
    
    
    public function actionListFriendsGroupOnly1_4del()
    {
        $uid = parent::getU()->getId(); 
        $jdata = parent::getJ() ;
        $db =  \Yii::$app->db ;
        $sql =
        "SELECT                                                       ".
        "`user`.id,                                                   ".
        "`user`.nickname,                                             ".
        "`user`.easemob_u,                                             ".
        
        "user_profile.portrait,                                       ".
        "user_profile.signature                                       ".
        "FROM                                                         ".
        "relationship_user                                            ".
        "INNER JOIN `user` ON relationship_user.uid = `user`.id       ".
        "LEFT JOIN user_profile ON user_profile.uid = `user`.id       ".
        "WHERE                                                        ".
        "relationship_user.rid ="
            ;
        
             
            $rids =   (new \yii\db\Query())
            ->select([
                'relationship_user.rid' , 'relationship.rtype'
            ])
            ->from('relationship_user')
            ->innerJoin("relationship" , "relationship_user.rid=relationship.id")
            ->where([  'relationship_user.uid' => $uid]  )
            ->all();
             
            //         $data = null ;
            //         var_dump($uid) ;
            //         var_dump($rids) ;exit() ;
        
            $uids_3 = [] ; $uids_4 = [] ; $uids_5 = [] ;
        
            foreach ($rids as  $value1) {
                //              var_dump($value1["rid"]) ;var_dump($value1["rtype"]) ;
        
                $section_data =    $db->createCommand($sql.$value1["rid"])->queryAll() ;
                //         var_dump($section_data) ;
                //         exit();
                if (strcmp ( $value1["rtype"] , "3" )  ) {
                    $uids_3 = array_merge($uids_3  ,  array_column($section_data, 'id') );
                }
                if (strcmp ( $value1["rtype"] , "4" )  ) {
                    $uids_4 = array_merge($uids_4  ,  array_column($section_data, 'id') );
                }
                if (strcmp ( $value1["rtype"] , "5" )  ) {
                    $uids_5 = array_merge($uids_5  ,  array_column($section_data, 'id') );
                }
        
                //           $uidkey = array_search($uid, array_column($section_data, 'id'));
                //           unset($section_data[$uidkey]) ;
        
                //         $section_data =  MyHelpers::re_sort_arr_key($section_data) ;
        
                //         $data[] = array_merge(["content" =>$section_data ] , [ "rtype"=>(int)$value1["rtype"] ."" ,"title"=> $this->translate_relationship_type($value1["rtype"])] ) ;
        
            }
            

//                     var_dump($uids_3) ;
//             var_dump($uids_4) ;var_dump($uids_5) ;
//             exit() ;
            
            $uids = null ; $title=null ;
            if ( $jdata["rtype"] ==3 ) {
                $uids = array_unique($uids_3) ;
//                 var_dump($uids) ; 
            $uidkey = array_search($uid, $uids); 
            unset($uids[$uidkey]) ;   
            $title = "情侣关系" ;
            }
            if ($jdata["rtype"] ==4 ) {
                 $uids = array_unique($uids_4) ;
            $uidkey = array_search($uid, $uids); unset($uids[$uidkey]) ;    
            $title = "亲友关系" ;
            }
            if ($jdata["rtype"] ==5 ) {
                $uids = array_unique($uids_5) ;
            $uidkey = array_search($uid, $uids); unset($uids[$uidkey]) ;   
            $title = "粒友关系" ;
            }
            
            
               //         var_dump($uids_5) ;
            // var_dump($uids_4) ;var_dump($uids_5) ;
//                      exit() ;
        
            $sec_data =  UserProfileService::q_profile_from_uids_all($uids) ;
            
             
             
            $data = [  ["content" =>$sec_data , "rtype"=>(int)$jdata["rtype"] , "title"=>$title] 
            ]
            ;
            //     $data =    array_merge( $data ,["content" =>$data_5 , "rtype"=>5 , "title"=>"粒友关系"]  ) ;
             
        
            parent::rtn_json(['cd'=>"1" ,"data"=> $data,"baseurl" =>\Yii::$app->params["qiniu"]['portraits'] ]) ;
        
  
             
    }
    public function actionListFriendsGroup()
    {
        $uid = parent::getU()->getId();
//         $jdata = parent::getJ() ;
        
      $data3 = $this->build_relation_users($uid, ["rtype"=>3]);
      $data4 = $this->build_relation_users($uid, ["rtype"=>4]);
      $data5 = $this->build_relation_users($uid, ["rtype"=>5]);
      
      parent::rtn_json(['cd'=>"1" ,"data"=> [$data3,$data4,$data5]]) ;
        
    }
    public function actionListFriendsGroup_4del()
    {
        $uid = parent::getU()->getId();
        $db =  \Yii::$app->db ;
        $sql = 
        "SELECT                                                       ".
            "`user`.id,                                                   ".
            "`user`.nickname,                                             ".
            "`user`.easemob_u,                                             ".
            
            "user_profile.portrait,                                       ".
            "user_profile.signature                                       ".
            "FROM                                                         ".
            "relationship_user                                            ".
            "INNER JOIN `user` ON relationship_user.uid = `user`.id       ".
            "LEFT JOIN user_profile ON user_profile.uid = `user`.id       ".
            "WHERE                                                        ".
            "relationship_user.rid ="        
        ;
        
   
     $rids =   (new \yii\db\Query())
        ->select([
            'relationship_user.rid' , 'relationship.rtype'
        ])
        ->from('relationship_user')
        ->innerJoin("relationship" , "relationship_user.rid=relationship.id")
        ->where([  'relationship_user.uid' => $uid]  )
        ->all();
       
//         $data = null ;
//         var_dump($uid) ;
//         var_dump($rids) ;exit() ;
        
        $uids_3 = [] ; $uids_4 = [] ; $uids_5 = [] ;
        
        foreach ($rids as  $value1) {
//              var_dump($value1["rid"]) ;var_dump($value1["rtype"]) ;

        $section_data =    $db->createCommand($sql.$value1["rid"])->queryAll() ;    
//         var_dump($section_data) ;
//         exit();
        if (strcmp ( $value1["rtype"] , "3" )  ) {
            $uids_3 = array_merge($uids_3  ,  array_column($section_data, 'id') );
        }
        if (strcmp ( $value1["rtype"] , "4" )  ) {
            $uids_4 = array_merge($uids_4  ,  array_column($section_data, 'id') );
        }
        if (strcmp ( $value1["rtype"] , "5" )  ) {
            $uids_5 = array_merge($uids_5  ,  array_column($section_data, 'id') );
        }
        
//           $uidkey = array_search($uid, array_column($section_data, 'id'));
//           unset($section_data[$uidkey]) ;
          
//         $section_data =  MyHelpers::re_sort_arr_key($section_data) ;
        
//         $data[] = array_merge(["content" =>$section_data ] , [ "rtype"=>(int)$value1["rtype"] ."" ,"title"=> $this->translate_relationship_type($value1["rtype"])] ) ;

        }
        $uids_3 = array_unique($uids_3) ;        
        $uidkey = array_search($uid, $uids_3); unset($uids_3[$uidkey]) ;
        
        
        $uids_4 = array_unique($uids_4) ;
        $uidkey = array_search($uid, $uids_4); unset($uids_4[$uidkey]) ;
        $uids_5 = array_unique($uids_5) ;
        $uidkey = array_search($uid, $uids_5); unset($uids_5[$uidkey]) ;
        
//         var_dump($uids_5) ;
// var_dump($uids_4) ;var_dump($uids_5) ;
//          exit() ;
        
       $data_3 =  UserProfileService::q_profile_from_uids_all($uids_3) ;
       $data_4 =  UserProfileService::q_profile_from_uids_all($uids_4) ;
       $data_5 =  UserProfileService::q_profile_from_uids_all($uids_5) ;
       
   
    $data = [  ["content" =>$data_3 , "rtype"=>3 , "title"=>"情侣关系"]  ,
       ["content" =>$data_4 , "rtype"=>4 , "title"=>"亲友关系"] ,
        ["content" =>$data_5 , "rtype"=>5 , "title"=>"粒友关系"]
    ]
    ;
//     $data =    array_merge( $data ,["content" =>$data_5 , "rtype"=>5 , "title"=>"粒友关系"]  ) ;
       
        
        parent::rtn_json(['cd'=>"1" ,"data"=> $data,"baseurl" =>\Yii::$app->params["qiniu"]['portraits'] ]) ;
       
       
       
    }
    private function translate_relationship_type($in_type)
    {
        switch ($in_type) {
            case 3:
           return "情侣关系" ;
            break;
            case 4:
                return "亲友关系" ;
                break;
                case 5:
                    return "朋友关系" ;
                    break;
            
            default:
                return "" ;
            break;
        }
    }
    
    public function actionListFriends()
    {      
        $uid = parent::getU()->getId(); 
        $data = parent::getJ() ; 
        $db =  \Yii::$app->db ;
        $sql =
        "SELECT user.id, user.nickname , user.easemob_u ," .
        " IFNULL(user_profile.portrait,'')  portrait, " . " IFNULL( user_profile.signature,'') signature " .
        " FROM user  ".
            "INNER JOIN friend_list ON user.id = friend_list.outterid ".
            " LEFT JOIN user_profile ON friend_list.outterid = user_profile.uid " .
            " WHERE friend_list.myid = " . $uid .
            " and friend_list.status = " .Status::STATUS_ACTIVE
        ;
       
                $model = $db->createCommand($sql);
            $flist = $model->queryAll() ;
    
   
            parent::rtn_json(['cd'=>"1","friend_list"=> $flist,"baseurl" =>\Yii::$app->params["qiniu"]['portraits']]) ;
   
    }
    public function actionRespBindingReq()
    {
        $data = parent::getJ() ;
        $uid = parent::getU()->getId();
//         var_dump($data["from_id"]) ;

//         var_dump($data["yesorno"]) ;

       $r = Relationship::findOne([
           "rtype" =>(int)$data["r_type"] ,
           "created_by" =>     $data["from_id"]       
       ]) ;
       
       
       
       
       if (!$r) 
       {
           $r =  new Relationship() ;   
           $r->rtype = (int)$data["r_type"] ;
           $r->created_by = $data["from_id"]  ;
           $r->save(false) ;
       }
       
       
       
       
//        $r_u_me =  RelationshipUser::findOne(
//            [ "rid"=> $r->id ,"uid"=>$uid ]
//            ) ;
       
         
       if ((int) $data["r_type"]  == Relationship::RELATIONSHIP_QL) {
         $is_exists =  RelationshipUser::findOne(
               [ "uid"=> $uid ]
               ) ;
         
         if ($is_exists) {
              parent::rtn_json(['cd'=>"-1" , "msg"=>"情侣关系已经存在了"]) ;
         }else 
         {
             $this->connect_r_u1_u2($r->id  , $uid,(int)$data["from_id"]) ;
             $easemobext =  EasemobExt::findOne(["frm_id"=>(int)$data["from_id"],"to_id"=>$uid,"etype"=>2]) ;
             if ($easemobext) {
                 $easemobext->delete() ;
             }
             parent::rtn_json(['cd'=>"1" ]) ;
         }
         
            
         
         
       }  else 
       {
          $this->connect_r_u1_u2($r->id  , $uid,(int)$data["from_id"]) ;
          $easemobext =  EasemobExt::findOne(["frm_id"=>(int)$data["from_id"],"to_id"=>$uid,"etype"=>2]) ;
          if ($easemobext) {
              $easemobext->delete() ;
          }
             parent::rtn_json(['cd'=>"1" ]) ;
       }
        
        
        
    }
    
    private  function connect_r_u1_u2( $rid ,$uid1 ,$uid2  ){
        
       $r_u1 = RelationshipUser::findOne(['rid'=>$rid,'uid'=>$uid1]);
        
       if (!$r_u1) {
        $r_u1 =new RelationshipUser();
        $r_u1->rid =  $rid;
        $r_u1->uid =$uid1;
        $r_u1->save(false) ;
       }
       
       $r_u2 = RelationshipUser::findOne(['rid'=>$rid,'uid'=>$uid2]);
       if (!$r_u2) {
        $r_u2 =new RelationshipUser();
        $r_u2->rid =  $rid;
        $r_u2->uid =$uid2;
        $r_u2->save(false) ;
       }
    }
    
    public function actionRespFriendReq()
    {
        $data = parent::getJ() ;
        $uid = parent::getU()->getId();
        
//         var_dump($data["reqp_id"]) ;
//         var_dump($data["source_id"]) ;
//         var_dump($data["yesorno"]) ;
    
        if (strcmp($data["yesorno"], 'Y')==0) {
            
            if ((int)$data["from_id"]  == $uid) {
                parent::rtn_json(['cd'=>"-1","msg"=>"自己不能加自己"]) ;
            }
            
            
             $flist = new FriendList();      
      $flist->myid =(int)$data["from_id"]  ;
      $flist->outterid =$uid ;              
       $flist->status = Status::STATUS_ACTIVE ;   
       $flist->save(false) ;
       $flist2 = new FriendList();
       $flist2->outterid =(int)$data["from_id"]  ;
       $flist2->myid =$uid ;
       $flist2->status = Status::STATUS_ACTIVE ;
       $flist2->save(false) ;
       
     $easemobext =  EasemobExt::findOne(["frm_id"=>(int)$data["from_id"],"to_id"=>$uid,"etype"=>1]) ;
     if ($easemobext) {
        $easemobext->delete() ;
     }
            
        parent::rtn_json(['cd'=>"1"]) ;
        }
       
        
        parent::rtn_json(['cd'=>"-1" , "msg"=>"不同意就算了"]) ;
    
      
        
    }
    public function actionRemoveBinding()
    {
        $jdata = parent::getJ() ;
        $uid = parent::getU()->getId();
//         var_dump($jdata["target_id"]) ;
//         var_dump($jdata["rtype"]) ;
//         exit() ;
        // (1) relationship_user inner join relationship : from  my uid and rtype -> rid
        $sql_in1=
        "SELECT".
        " relationship.id,".
        " relationship_user.uid,".
        " relationship_user.rid,".
        " relationship.rtype".
        " FROM relationship_user".
        " INNER JOIN relationship".
        " ON relationship_user.rid".
        " = relationship.id".
        " where relationship.rtype = ".$jdata["rtype"].
        " and relationship_user.uid = $uid"
        ;
        $sql_in1_result =  DBService::q_with_native_sql($sql_in1, 1) ;
        $target_rids = array_column($sql_in1_result, "rid") ;
//               var_dump($target_rids) ;exit() ;
             $build_str4_sql_in_cond = implode(",", $target_rids) ;
        
              $sql_del_relationship_user =
              "DELETE FROM relationship_user ".
              " WHERE rid in (". $build_str4_sql_in_cond . ")" .
              " and uid = " .$jdata["target_id"]
                  ;
//               var_dump($sql_del_relationship_user) ;exit() ;

                  DBService::q_with_native_sql($sql_del_relationship_user, 5) ;
              
                  parent::rtn_json(['cd'=>"1"]) ;
    }
   
    public function actionReqBinding()
    {
        $data = parent::getJ() ;
        $uid = parent::getU()->getId();

        
        $relation_name = "" ;
        
       switch ( (int)$data["r_type"]) {
           case Relationship::RELATIONSHIP_QL:
           $relation_name = "情侣关系";
           break;
           
           case Relationship::RELATIONSHIP_QY:
               $relation_name = "亲友关系";
               break;
               
               case Relationship::RELATIONSHIP_LY:
                   $relation_name = "好友关系";
                   break;
           
           default:
               ;
           break;
       }

        
       $target_arr = UserService::fromuid_4easemob([ 
          $uid,
           $data["target_id"]]) ;
        
       
       $from_arr = UserService::fromuid_4easemob_assist($uid, $target_arr) ;
       $to_arr = UserService::fromuid_4easemob_assist($data["target_id"], $target_arr) ;
       

       
        $easemob_options = \Yii::$app->params["easemob"] ;
        $e = new Easemob($easemob_options);
//         $e->sendText( $from_arr["easemob_u"],
           $ease_result= $e->sendText( 'req-binding',
            "users", [$to_arr["easemob_u"]],            
            $from_arr["nickname"] . ",请求和你建立" . $relation_name  . "关系", ["key"=>"req-binding"]
//             [  "from_id" => $from_arr["id"] ,
//             "to_id" => $to_arr["id"] ,'key'=>"binding",
//             "portrait" => "common.png",
//                 "sender_nickname"=> $from_arr["nickname"]
//             ] 
            ) ;
            
//             var_dump($ease_result) ;exit() ;
            
            $e_ext = new EasemobExt();
            $e_ext->frm_id = $uid ;
            $e_ext->to_id = $to_arr["id"] ;
            $e_ext->content =  $from_arr["nickname"] . ",请求和你建立" . $relation_name  . "关系" ;
            $e_ext->ext = json_encode(
                [  "from_id" => $uid ,
                "to_id" =>  $to_arr["id"] ,
                "portrait" => "common.png",
                "nickname"=> $from_arr["nickname"],
                    "r_type"=> (int)$data["r_type"]
                ]
                );
            $e_ext->etype = EasemobExt::E_TYPE_REQ_BINDING ;
            $e_ext->save(false) ;
            
            parent::rtn_json(['cd'=>"1"]) ;
    }
    
    public function actionReqBindingMulti()
    {
        $data = parent::getJ() ;
        $uid = parent::getU()->getId();
        
        
        $relation_name = "" ;
        
        switch ( (int)$data["r_type"]) {
            case Relationship::RELATIONSHIP_QL:
                $relation_name = "情侣关系";
                break;
                 
            case Relationship::RELATIONSHIP_QY:
                $relation_name = "亲友关系";
                break;
                 
            case Relationship::RELATIONSHIP_LY:
                $relation_name = "好友关系";
                break;
                 
            default:
                ;
                break;
        }
        
        
        $target_arr = UserService::fromuid_4easemob(array_merge($data["target_ids"] ,[$uid])) ;
        
//         var_dump($target_arr)  ;
        
        $from_arr = UserService::fromuid_4easemob_assist($uid, $target_arr) ;
//         var_dump($from_arr)  ;
        $esemob_us_arr =   array_column($target_arr, "easemob_u") ;
//         var_dump($esemob_us_arr) ; 
     unset(  $esemob_us_arr [ array_search( $from_arr["easemob_u"] , $esemob_us_arr) ]);
//      var_dump($esemob_us_arr) ;
//         exit();
        $to_arr = UserService::fromuid_4easemob_assist($data["target_ids"], $target_arr) ;
         
        
        
         
        $easemob_options = \Yii::$app->params["easemob"] ;
        $e = new Easemob($easemob_options);
        //         $e->sendText( $from_arr["easemob_u"],
        $easemob_result = $e->sendText( 'req-binding',
            "users", $esemob_us_arr,
            $from_arr["nickname"] . ",请求和你建立" . $relation_name  . "关系",[ "key"=>'req-binding']
//             [  "source_id" => $uid ,
//                 "target_ids" =>$data["target_ids"] ,
//                 "portrait" => 'common.png',
//                 "nickname"=> $from_arr["nickname"]
//             ]
            ) ;
//         var_dump($easemob_result) ;exit();
        
        foreach ($data["target_ids"] as $value_id) {
                       $e_ext = new EasemobExt();
            $e_ext->frm_id = $uid ;
            $e_ext->to_id = $value_id ;
            $e_ext->content =   $from_arr["nickname"] . ",请求和你建立" . $relation_name  . "关系" ;
            $e_ext->ext = json_encode(
                [  "from_id" => $uid ,
                "to_id" =>  $value_id,
                "portrait" => "common.png",
                "nickname"=> $from_arr["nickname"],  "r_type"=> (int)$data["r_type"]
                ]
                );
            $e_ext->etype = EasemobExt::E_TYPE_REQ_BINDING ;
            $e_ext->save(false) ;
        }
        
        parent::rtn_json(['cd'=>"1"]) ;
        
    }   
    public function actionReqFriend()
    {
        $data = parent::getJ() ;
        $uid = parent::getU()->getId();
        
//         var_dump($data["target_id"]) ;
//         var_dump($uid) ;


         $islaready_friend = RelationshipService::is_already_friend($uid ,$data["target_id"]) ;
        
        if ($islaready_friend) {
              parent::rtn_json(['cd'=>"-1","msg"=>"别再加了"]) ;
        }
        
       
        // send a request & wait
        $target_arr = UserService::fromuid_4easemob([ $uid  ,$data["target_id"]]) ;
     
        
       $from_arr = UserService::fromuid_4easemob_assist($uid, $target_arr) ;
       $to_arr = UserService::fromuid_4easemob_assist($data["target_id"], $target_arr) ;
        
//     var_dump($from_arr);  var_dump($to_arr); 
//     print_r (json_encode( ["from_id"=>$from_arr["id"] ,'from-nickname'=>$from_arr["nickname"]  ,"to_id"=>$to_arr["id"], 'nickname'=>"好友消息",'portrait'=>"common.png" ])) ;
//     exit();
        
        $easemob_options = \Yii::$app->params["easemob"] ;
        $e = new Easemob($easemob_options);
//         $e->sendText($from_arr["easemob_u"] 
            $e->sendText('req-friend'
            , "users", [$to_arr["easemob_u"]], $from_arr["nickname"] ."求加好友",['key'=>'req-friend'] 
//             ["from_id"=>$from_arr["id"] ,'from-nickname'=>$from_arr["nickname"]  ,"to_id"=>$to_arr["id"], 'nickname'=>"好友消息",'portrait'=>"common.png" ]
            );
           $e = new EasemobExt();
           $e->frm_id = $uid ;
           $e->to_id = $to_arr["id"] ;
           $e->content =  $from_arr["nickname"] ."求加好友" ;
           $e->ext = json_encode( ["from_id"=>$from_arr["id"] ,'from-nickname'=>$from_arr["nickname"]  ,"to_id"=>$to_arr["id"], 'fromNickname'=>"好友消息",'fromPortrait'=>"common.png" ]);
           $e->etype = EasemobExt::E_TYPE_REQ_FRIEND ;
           $e->save(false) ;

        parent::rtn_json(['cd'=>"1"]) ;
    }
    
    public function actionBlockFriend()
    {
        $data = parent::getJ() ;
        $uid = parent::getU()->getId();
//         var_dump($data["from_id"]) ;
//         var_dump($data["to_id"]) ;

        
        
        
        
   $flist =     FriendList::findOne([
            "myid"=>$data["from_id"] ,
            "outterid"=>$data["to_id"] ,
        ]) ;
   $flist->status = Status::STATUS_INACTIVE ;
   $flist->save(false) ;
   
   parent::rtn_json(['cd'=>"1"]) ;
    }
    public function actionDelFriend()
    {
        $data = parent::getJ() ;
        $uid = parent::getU()->getId();
        //         var_dump($data["from_id"]) ;
        //         var_dump($data["to_id"]) ;
        $sql_in =
        "SELECT ru1.rid FROM".
            " relationship_user AS ru1".
            " INNER JOIN relationship_user AS ru2".
            " ON ru1.rid = ru2.rid".
            " where ru1.uid = ".$data["from_id"].
            " and ru2.uid =" .$data["to_id"]
                ;
        $is_exists= DBService::q_with_native_sql($sql_in, 1) ;
        if ($is_exists) {
            parent::rtn_json(['cd'=>"-1","msg"=>"清楚关系后才可以删好友"]) ;
        }
    
        $flist =     FriendList::findOne([
            "myid"=>$data["from_id"] ,
            "outterid"=>$data["to_id"] ,
        ]) ;
//         $flist->status = Status::STATUS_DELETED ;
//         $flist->save(false) ;
         
        if ($flist) {
           $flist->delete() ;
        }
       
        
        parent::rtn_json(['cd'=>"1"]) ;
    }
}
