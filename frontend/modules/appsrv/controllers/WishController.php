<?php
namespace frontend\modules\appsrv\controllers;
use common\components\Easemob;
use common\components\MyHelpers;
use common\models\ChargeOrd;
use common\models\Deposit;
use common\models\Status;
use common\models\Wish;
use common\models\WishDepositSolutions;
use frontend\modules\appsrv\services\DBService;
use frontend\modules\appsrv\services\UserService;
use frontend\modules\appsrv\services\UserProfileService;
use frontend\modules\appsrv\services\WishService;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
// use Qiniu\json_decode;
/**
 * Default controller for the `appsrv` module
 */
class WishController extends AppSrvBase2Controller
{
 /**
  * "deposit_type": 1,
      "achieve_span": 365,
"kickoff_at": 0  这三个 就是愿望达成时间
"deposit_type": 1, 周期单位 天
      "achieve_span": 365,  365 天
"kickoff_at": 0 开始的时间 
这样就推算出 愿望达成时间

  */
    public function actionIndex()
    {  $data = parent::getJ() ; $uid = parent::getU()->getId();
       $sql =
      " SELECT".
        " id,".
        " wish_name,".
        " title,".
        " content,".
        " pics,".
        " ytype,".
        " classify,".
        " deposit_type,".
        " achieve_span,".
        " achieve_way,".
        " is_lilimi_help,".
        " target_amt,".
        " rid,".
        " ref_uids,".
        " created_at,".
        " updated_at,".
        " `status`,".
        " created_by,".
        " updated_by,".
        " kickoff_at".
        " FROM wish WHERE".
        " ytype = ".$data["ytype"] ." and `status` = 1".
        " and kickoff_at > 0 and".
        " (  created_by = " .$uid . " or ref_uids LIKE '%". $uid . "%' ) "
       ;
      $result = DBService::q_with_native_sql($sql, 1) ;
   
       foreach ($result as $key => $value) {
   $result[$key]["pics"] =   explode ( "," ,$value["pics"])    ;
   $result[$key]["futrue_date"] =  MyHelpers::future_time_point($value["deposit_type"], $value["achieve_span"],  $value["kickoff_at"]) ;
   
   $result[$key]["deposit_sum"] = (string) WishService::sum_deposit($value["id"])  ;
   
       } 
//         var_dump($result) ;exit() ;
        
//        header("Content-type: application/json;charset=utf-8");
           parent::rtn_json ([
                'cd' => 1,'data' =>$result
            ]);
          
    }
    public function actionDetail()
    {
        $data = parent::getJ() ;
        $wishid = $data["wishid"] ;
        $now_time = time() ;
//         $wish = Wish::find()->where(['id'=> $wishid ,'status'=>Status::STATUS_ACTIVE])->asArray()->one() ;
        $wish_sql = 
        " SELECT".
        " wish.id,".
        " wish.wish_name,".
        " wish.title,".
        " wish.content,".
        " wish.pics,".
        " wish.deposit_type,".
        " wish.achieve_span,".
        " wish.achieve_way,".
        " wish.target_amt,".
        " wish.rid,".
        " wish.ref_uids,".
        " wish.created_at,".
        " wish.`status`,".
        " wish.kickoff_at,".
        " ref_wish_type.title AS wish_type,".
        " ref_wish_classify.title AS wish_classify,".
        " ref_time_unit.title as wish_deposit_period".
        " FROM".
        " wish".
        " LEFT JOIN ref_wish_type".
        " ON ref_wish_type.id = wish.ytype".
        " LEFT JOIN ref_wish_classify".
        " ON ref_wish_classify.id = wish.classify".
        " LEFT JOIN ref_time_unit".
        " ON ref_time_unit.id = wish.deposit_type".
        " where wish.id = " .$wishid                
            ;
            $wish = DBService::q_with_native_sql($wish_sql, 3) ;
        
//             var_dump($wish_sql) ;
//          var_dump($wish) ;exit() ;
        $wish["pics"] =   explode ( "," ,  $wish["pics"] )    ;
        $wish["should_progress"] =  MyHelpers::deposit_time_progress($wish["deposit_type"], $wish["achieve_span"],  $wish["kickoff_at"],$now_time); 
        $wish["futrue_date"] =  MyHelpers::future_time_point($wish["deposit_type"], $wish["achieve_span"],  $wish["kickoff_at"]) ;
//      $deposits =   Deposit::find()->where(['wishid' => $wishid])->asArray()->all() ;
      $insql =
      "SELECT                             ".
        "deposit.deposit,                   ".
        "`user`.nickname,                   ".
        "user_profile.portrait,             ".
        "deposit.id,                        ".
        "deposit.wishid,                    ".
        "deposit.ver,                       ".
        "deposit.uid,                       ".
        "deposit.step_amt,                  ".
        "deposit.split_amt,                 ".
        "deposit.is_agree,                  ".
        "deposit.created_at,                ".
        "deposit.updated_at                 ".
        "FROM                               ".
        "deposit                            ".
        "LEFT JOIN `user`                  ".
        "ON deposit.uid = `user`.id         ".
        "LEFT JOIN user_profile             ".
        "ON deposit.uid = user_profile.uid  ".
        "where deposit.wishid =" .$wishid
       ;
      $deposits = DBService::q_with_native_sql($insql, 1) ;
//       var_dump($deposits) ;exit() ;
//      $deposits =   Deposit::find()->where(['wishid' => $wishid])->asArray()->all() ;
     $already_depoists = 0;
     foreach ($deposits as $key => $value) {
         $already_depoists += $value["deposit"] ;
//          $deposits[$key]["split_done_process"] = $value["deposit"] / $value["split_amt"] ;
         
         if ($value["uid"] == 0) {
             unset($deposits[$key]) ;
         }
         
     }
     
     $wish["deposit_sum"] = (string)$already_depoists;
//      var_dump($deposits) ;exit() ;
   $deposits =  MyHelpers::re_sort_arr_key($deposits) ;

//      $key = array_search(0, array_column($deposits, 'uid'));
//         unset($deposits[$key]) ;

     header("Content-type: application/json;charset=utf-8");
        echo (Json::encode([
            'cd' => 1,'data' =>['wish'=>$wish , 'deposits'=>$deposits ,'now_time'=> $now_time]
        ]));
    }
    
    public function actionListMyWishs()
    {
        $uid = parent::getU()->getId();
//         var_dump($uid) ;
//       $rows =  Wish::findAll([
//             "status"=>Status::STATUS_ACTIVE,"created_by"=>$uid,
//             ['>', 'kickoff_at', 0]
//         ]) ;
      
      $rows = (new \yii\db\Query())
      ->select([
          "id",
          "wish_name",
          "title",
          "content",
          "pics",
          "ytype",
          "classify",
          "deposit_type",
          "achieve_span",
          "achieve_way",
          "is_lilimi_help",
          "target_amt",
//           "lilimi_target_amt",
          "rid",
          "ref_uids",
          "created_at",
          "updated_at",
          "`status`",
          "created_by",
          "updated_by",
          "kickoff_at"
      ])
      ->from('wish')
      ->where([ "status"=>Status::STATUS_ACTIVE,"created_by"=>$uid])
      ->andWhere(['>' , 'kickoff_at' ,'0'])
      ->all();
      
      
      foreach ($rows as $key => $value) {
       $rows[$key]["pics"] =explode(",",  $value["pics"] ) ;
      }
     
      echo (Json::encode([
          'cd' => 1,         
          'data' =>$rows         
      ]));
    }    

    public function actionPost()
    {
        $data = parent::getJ() ;
        $request = \Yii::$app->request;
        if (! $request->isPost)
            echo (Json::encode([
                'cd' => - 1
            ]));
        
        $u = parent::getU();
//         var_dump($data ['title']);
//         var_dump($data ['pics']);
//         var_dump($data ['content']) ;exit();
        
        
        $wish = new Wish();
        $wish->wish_name =$data ['wish_name'];
        $wish->title =$data ['title'];
        $wish->content = $data['content'];
        $wish->pics = implode(",", $data ['pics']) ;
        
        $wish->ytype = $data['ytype'];
        $wish->classify = $data['classify'];
        $wish->deposit_type = $data['deposit_type'];
        $wish->achieve_span = $data['achieve_span'];
        $wish->achieve_way = $data['achieve_way'];
        $wish->target_amt = $data['target_amt'];
        $wish->created_by = $u->getId();
        
        $wish->save(false);
//         header("Content-type: application/json;charset=utf-8");
//         echo (Json::encode([
//             'cd' => 1,
// //             'lilimi' => round($wish->target_amt * (5500 / 6000)),
//             'amount' => $wish->target_amt,
//             'wishid' => $wish->id
//         ]));
        
        parent::rtn_json(['cd'=>"1",
                        'amount' => $wish->target_amt,
            'wishid' => $wish->id
        ]) ;
        
    }
    
//     public function actionBindingr($wishid , $rid)
//     {
//         $request = \Yii::$app->request;
//         $u = parent::getU();         $db =  \Yii::$app->db ;  
//         // wish type have to match ralation type
//       $wish =  Wish::findOne(['id'=> $wishid]) ;
      
//       $sql ='SELECT id, rtype  ,created_by FROM relationship WHERE  id = ' .$rid ;      
//       $data = $db->createCommand($sql)->queryOne();
// //       var_dump($data ["rtype"]) ;exit() ;
//         if ((int)$data ["rtype"] === (int)$wish->ytype) 
//             ;
//        else
//        {
//            header("Content-type: application/json;charset=utf-8");
//            echo (Json::encode([
//                'cd' => - 1 ,'msg'=>'wish type have to match ralationship type'
//            ]));
//        }
         
        
//         // my wish my relationship match
// //     var_dump()     ;  var_dump($data ["created_by"] )  ;
//          if ((int)$wish->created_by != $u->getId() || (int)$data ["created_by"] != $u->getId() ) {
//              header("Content-type: application/json;charset=utf-8");
//              echo (Json::encode([
//                'cd' => - 1 ,'msg'=>'all items have to created by you!'
//            ]));
//          }           
//         // update wish binding ralationship together
        
//          $wish->rid = $rid ;
//          $wish->save(false) ;
         
//          echo (Json::encode([
//              'cd' => 1
//          ]));
         
//     }

    public function actionKickOff1()
    {
        $data = parent::getJ() ;
        $request = \Yii::$app->request;
        if (! $request->isPost)
            echo (Json::encode([
                'cd' => - 1
            ]));
        
        $wishid = $data["wishid"];
//         $islilimi = $data["islilimi"];
        
        $wish = Wish::findOne($wishid);
        
        if ($wish->ytype == Wish::Y_TYPE_GR || $wish->ytype == Wish::Y_TYPE_ZY) {
            ;
        }else 
        {
            header("Content-type: application/json;charset=utf-8");
            echo (Json::encode([
                'cd' => -1 ,'msg'=>"kick-off1 给  个人或自营愿望调用滴"
            ]));
            return ;
        }
        
        
//         $wish->is_lilimi_help = $islilimi;
//         if ($islilimi)
//             $wish->lilimi_target_amt = $wish->target_amt * (5500 / 6000) ;
        
            $wish->kickoff_at = time() ;
            $wish->status = Status::STATUS_ACTIVE ;
            
        $wish->save(false);
        
//         if ($islilimi)
//         $split_amt = $wish->target_amt * (5500 / 6000) ;
//         else 
            $split_amt = $wish->target_amt ;
        
        $step_amt =   $split_amt / $wish->achieve_span;
        
        self::create_deposit1($wishid,$split_amt,$step_amt);
        
        header("Content-type: application/json;charset=utf-8");
        echo (Json::encode([
            'cd' => 1
        ]));
    }

    private function create_deposit1($wishid,$split_amt,$step_amt)
    {
       $uid = parent::getU()->getId() ;
       $d = new Deposit() ;
       $d->wishid = $wishid ;
       $d->split_amt = $split_amt ;
       $d->step_amt = $step_amt ;
       $d->uid = $uid ;
       $d->is_agree = 1 ;
       $d->save(false) ;       
       
   
       $d = new Deposit() ;
       $d->wishid = $wishid ;
       $d->split_amt = $split_amt ;
       $d->step_amt = $step_amt ;
       $d->uid = 0 ;
       $d->is_agree = 1 ;
       $d->save(false) ;
    }
    public function actionTfjsResp()
    {
        $redis = \Yii::$app->redis;
        $data = parent::getJ() ;  $uid = parent::getU()->getId() ;
        $data["wishid"] ;
//         var_dump($data) ;

        // if key exists? if not over 3 days! return error
        
           $key_isexist =    $redis->keys("wish:" . $data["wishid"]  . ":" .$data["req_ver"]);
    
    if (!$key_isexist) {
        parent::rtn_json(['cd'=>"-1" , "msg"=>"有人反对，或者超过三天失效了,让发起者重发一个"]) ;
    }

        // 如果答复  N 则删除 这个版本的收集和相应的存钱方案,并数据库wish rid ++
      $wish = Wish::findOne(['id' => $data["wishid"]]) ;
      
      if ($data["req_ver"] != $wish->rid  ) {
          parent::rtn_json(['cd'=>"-1" , "msg"=>"本轮发起已经失效"]) ;
      }
    
      
    if ( strcmp( $data["yesorno"] , "N") == 0) {
       $wish->rid =   $wish->rid  + 1 ;
       $wish->save(false) ;
       
       $redis->del ("wish:" . $data["wishid"]  . ":" .$data["req_ver"]) ;
       
       $w_d_s = WishDepositSolutions::findOne(["wishid"=>$data["wishid"],"ver"=>$data["req_ver"]]) ;
       
       if ($w_d_s) {
          $w_d_s->delete() ;
       }
       
       parent::rtn_json(['cd'=>"1" , "msg"=>"恭喜你本次发起被你搅了"]) ;
    }
      
       

      
        
        //如果Y 添加到redis 里        
        
   
        $redis->hset("wish:" . $data["wishid"]  . ":" .$data["req_ver"]  , $uid ,$data["yesorno"]);
//         var_dump("wish:" . $data["wishid"]  . ":" .$data["req_ver"]  ) ;
//         var_dump($data["uid"] ) ; var_dump($data["yesorno"] ) ;
         $obj = $redis->hgetall("wish:" . $data["wishid"]  . ":" .$data["req_ver"] ) ;
        
        //  和数据库中 ref_uids比较是否全员了,如果全员 还没有N 那就让愿望生效了,愿望status 愿望开始时间,每个人按照配额建储蓄账户,多加一个接受帮存的uid = 0
          
    $arr_fromdb = explode(",", $wish->ref_uids)     ;
    $arr_from_redis = array_diff($obj, [$wish->created_by . "" , "Y" ,"N"])  ;
    $if_completed = array_diff($arr_fromdb, $arr_from_redis)   ;
    
   if (count($if_completed) == 0  && $wish->rid == $data["req_ver"] ) { //"凑齐了" . "版本也对" 
     $wish_d_solution = WishDepositSolutions::findOne(['wishid'=>$data["wishid"] ,'ver'=>$data["req_ver"] ]) ;     
     $solutions = json_decode( $wish_d_solution->solution)  ;
     
     
     $split_deposit = new Deposit() ;
     $split_deposit->wishid = $data["wishid"] ;
     $split_deposit->ver = $data["req_ver"]  ;
     $split_deposit->save(false) ;
      
      foreach ($solutions as $value) {
//          var_dump($value->uid) ; var_dump($value->amt) ;
         
         
         $split_deposit_item = new Deposit() ;
         $split_deposit_item->wishid = $data["wishid"] ;
         $split_deposit_item->uid = $value->uid ;
         $split_deposit_item->split_amt = $value->amt ;
         $split_deposit_item->step_amt = ceil((float) $value->amt /(int) $wish->achieve_span);
         $split_deposit_item->ver = $data["req_ver"]  ;
         $split_deposit_item->save(false) ;
         
      }
      $wish->kickoff_at = time() ;
      $wish->status = Status::STATUS_ACTIVE ;
      $wish->save(false) ;
      
   } 
   
   parent::rtn_json(['cd'=>"1" ]) ;
   
    }
    public function actionTfjsReqVer()
    {
        $data = parent::getJ() ;
        $data["wishid"] ;
//      var_dump(   $data["wishid"]);

        $rows = (new \yii\db\Query())
        ->select(['id', 'rid']) //rid以前放置 关系id 现在用来 放请求版本
        ->from('wish')
        ->where([  'id' => $data["wishid"] ])
        ->one();
        
//         var_dump($rows) ;
        
        parent::rtn_json(['cd'=>"1" , "ver"=>$rows["rid"]]) ;
        
    }
    public function actionTfjsReq()
    {
        $data = parent::getJ() ;
        $uid = parent::getU()->getId();
//         var_dump($data["wishid"]) ;


        
         ;
        $key_is_exists = array_search(0, array_column($data["deposits"], 'amt'));
//         var_dump( is_int($key_is_exists) ) ;
        if (is_int($key_is_exists)) {
           parent::rtn_json(['cd'=>"-1" , "msg"=>"所有参与者都要设个存钱金额"]) ;
        }
        
        $key_is_exists2 = array_search($uid, array_column($data["deposits"], 'uid'));
        //         var_dump( is_int($key_is_exists) ) ;
        if (!is_int($key_is_exists2)) {
            parent::rtn_json(['cd'=>"-1" , "msg"=>"你自己存多少呢?"]) ;
        }
        
        
        $w =  Wish::findOne(["id"=>$data["wishid"]]) ;
         
        if (!$w) {
        
            parent::rtn_json(['cd'=>"-1" , "msg"=>"没有这个愿望"]) ;
        }
         
       $deposits_sum  = array_column($data["deposits"], 'amt') ;

//        var_dump($deposits_sum) ;
//        var_dump(array_sum($deposits_sum) ); 
//        var_dump($w->target_amt) ;
//        var_dump(  
//           ! ( (float)   array_sum($deposits_sum)  ==(float) $w->target_amt )
//            );
       
       
//        exit() ;
       
        
        if (! ((float)   array_sum($deposits_sum)  ==(float) $w->target_amt)) {
           parent::rtn_json(['cd'=>"-1" , "msg"=>"重分,每个人存款加和不等于愿望总金额"]) ;
        }
        
        if ($w->status == Status::STATUS_ACTIVE && $w->kickoff_at > 0) {
            parent::rtn_json(['cd'=>"-1" , "msg"=>"愿望都已经开始了,别再发请求了"]) ;
        }        
        
        
        
        $uid_all  = array_column($data["deposits"], 'uid') ;
        $my_uid_key = array_search( $uid,$uid_all) ;
        unset($uid_all[$my_uid_key]) ;
        $w->ref_uids = implode( ",",$uid_all) ;
        $w->save(false) ;
        
//         exit();
                $db =  \Yii::$app->db ;
                $sql1 = "SELECT title, content,pics, rid ,ref_uids FROM wish where id = " . $data["wishid"] ;
                $q1_result = $db->createCommand($sql1)->queryOne() ;
//     var_dump(   $q1_result["title"] ); var_dump(   $q1_result["pics"] ); 
//     exit() ;
//     $uids =    str_replace ("me" , "" , $w->ref_uids) ;
//         //查询相关用户 的环信 账号

//                 var_dump( ) ;exit() ;
                $sql2 = null ;
                if (strlen($q1_result["ref_uids"] ) == 0) {
                     $sql2 = "SELECT id,nickname,easemob_u,easemob_p FROM `user` where id in ("  . $uid .   ")" ;
                }else 
                {
                    $sql2 = "SELECT `user`.id,`user`.nickname,`user`.easemob_u,IFNULL(user_profile.portrait,'')	portrait FROM `user` LEFT JOIN user_profile ON user_profile.uid = `user`.id where `user`.id  in ("  . $uid . "," . $q1_result["ref_uids"] .  ")" ;

                    $sql3 = "SELECT `user`.id,`user`.nickname,`user`.easemob_u,`user`.easemob_p,IFNULL(user_profile.portrait,'')	portrait  FROM `user` LEFT JOIN user_profile ON user_profile.uid = `user`.id where `user`.id  in ("  . $q1_result["ref_uids"] .  ")" ;
                }
       

//         var_dump($sql2) ; exit() ;
        $q2_result = $db->createCommand($sql2)->queryAll();  
        $q3_result = $db->createCommand($sql3)->queryAll();
//         var_dump($q2_result) ;
        
        $uidkey = array_search($uid, array_column($q2_result, 'id'));
        
//     var_dump  (  $q2_result[$uidkey]["easemob_u"]) ;
        
      $all_easemob_u =  ArrayHelper::getColumn($q2_result, "easemob_u") ;
//       var_dump($all_easemob_u)  ;exit();
//       $send_target = array_diff($all_easemob_u,  [$q2_result[$uidkey]["easemob_u"] ]) ;
//       var_dump($send_target) ;
//       $target = [] ;
      
//       foreach ($send_target as $value) {
//           $target[] = $value ;
//       }
//       var_dump( $target) ;
//      var_dump(  ArrayHelper::getColumn($data["deposits"], 'uid')) ;

        $r = UserService::translate_nickname( ArrayHelper::getColumn($data["deposits"], 'uid')) ;
//        var_dump($r ) ;   
//     var_dump($this->tr_nickname($r,$uid) );
//        exit() ; 
       
        $easemob_options = \Yii::$app->params["easemob"] ;
        $e = new Easemob($easemob_options);  
//    var_dump(     $q2_result[$uidkey]["easemob_u"]);exit() ;
//     $re =     $e->sendText(  $q2_result[$uidkey]["easemob_u"],"users",
//             $target,  "那个" . $this->tr_nickname($r,$uid) ."发起了个愿望,摊分存钱比例如下,你同意四还是不同意?"
//             , ['key'=>"y_tfjs", "req_ver"=> $q1_result["rid"], "uid"=>$uid,"wishid"=>$data["wishid"], "wish_title"=>$q1_result["title"] ,"wish_content"=>$q1_result["content"] ,"wish_pics"=>$q1_result["pics"],"tfjs"=>$data["deposits"] ,"nicknames"=>$r
//             ,   "fromNickname"=>$q2_result[$uidkey]["nickname"],"fromPortrait"=>$q2_result[$uidkey]["portrait"]
//             ]) ;
    
    foreach ($q3_result as $key => $value) {
//         var_dump($q2_result) ;
//         var_dump($uidkey) ; exit() ;
//         var_dump($q2_result[$uidkey]) ;
//         var_dump($q2_result[$uidkey]["easemob_u"]) ;
//         var_dump($value ["easemob_u"],  "那个" . $this->tr_nickname($r,$uid) ."发起了个愿望,摊分存钱比例如下,你同意四还是不同意?") ;
//         var_dump(
//             ['key'=>"y_tfjs", "req_ver"=> $q1_result["rid"], "uid"=>$uid,"wishid"=>$data["wishid"], "wish_title"=>$q1_result["title"] ,"wish_content"=>$q1_result["content"] ,"wish_pics"=>$q1_result["pics"],"tfjs"=>$data["deposits"]
//             ,   "fromNickname"=>$q2_result[$uidkey]["nickname"],"fromPortrait"=>$q2_result[$uidkey]["portrait"]
//             ,"toNickname"=>$value["nickname"],"toPortrait"=>$value["portrait"]
//             ]
//             ); exit() ;
      $r =  $e->sendText( $q2_result[$uidkey]["easemob_u"],"users",
             [$value ["easemob_u"]] ,  "那个" . $this->tr_nickname($r,$uid) ."发起了个愿望,摊分存钱比例如下,你同意四还是不同意?"
            , ['key'=>"y_tfjs", "req_ver"=> $q1_result["rid"], "uid"=>$uid,"wishid"=>$data["wishid"], "wish_title"=>$q1_result["title"] ,"wish_content"=>$q1_result["content"] ,"wish_pics"=>$q1_result["pics"],"tfjs"=>$data["deposits"] 
            ,   "fromNickname"=>$q2_result[$uidkey]["nickname"],"fromPortrait"=>$q2_result[$uidkey]["portrait"]
                ,"toNickname"=>$value["nickname"],"toPortrait"=>$value["portrait"]
            ]) ; // "req_ver"=> $q1_result["rid"], rid以前放置 关系id 现在用来 放请求版本
      
      
//       var_dump($r) ; exit() ;
      
    }
    
    
    
    
//     var_dump($q3_result) ;
//     var_dump( $send_target,  "那个" . $this->tr_nickname($r,$uid) ."发起了个愿望,摊分存钱比例如下,你同意四还是不同意?") ;
//             var_dump($re) ;
//             exit() ;
    
    $w_solution =   new  WishDepositSolutions() ;
    $w_solution->wishid = $data["wishid"];
    $w_solution->ver = $q1_result["rid"] ;
    $w_solution->solution = json_encode($data["deposits"]) ;
    $w_solution->created_by = $uid ;$w_solution->updated_by = $uid ;
    $w_solution->save(false) ;
    
    $redis = \Yii::$app->redis;
     
    $redis->hset("wish:" . $data["wishid"] . ":" . $q1_result["rid"]   ,"" . $uid ,"Y");
    $redis->expire("wish:" . $data["wishid"] . ":" . $q1_result["rid"]  , 24*3 * 3600) ;
    
    parent::rtn_json(['cd'=>"1"]) ;
    }
    private function tr_nickname($r,$id)
    {
        //         var_dump($r) ;
        $key = array_search($id, array_column($r, 'id'));
        return ( is_null ($r[$key]["nickname"] ) ? "未命名" :  $r[$key]["nickname"]  ) ;
    }
    public function actionBinduids()
    {
        $data = parent::getJ() ;
//         $uid = parent::getU()->getId();
        
        
//         var_dump($data["uids"]) ;
//      var_dump(   $uid) ;
     
//    $arr =  ArrayHelper::merge($data["uids"] ) ;
     
//    var_dump(implode( ",",$arr));
     
   $w =  Wish::findOne(["id"=>$data["wishid"]]) ;
   
   if (!$w) {
              
   parent::rtn_json(['cd'=>"-1"]) ;
   }
   
   
   $w->ref_uids = implode( ",",$data["uids"]) ;
   $w->save(false) ;
        
   parent::rtn_json(['cd'=>"1"]) ;
        
    }

    public function actionInitOrd()
    {
        $data = parent::getJ() ;
        $uid = parent::getU()->getId();
    
        $sql =
        " SELECT".
        " id,".
        " wishid,".
        " ver,".
        " uid,".
        " deposit,".
        " step_amt,".
        " split_amt,".
        " is_agree,".
        " created_at,".
        " updated_at,".
        " `status`".
        " FROM deposit".
        " WHERE wishid = ". $data["wishid"] .
        " and uid = " . $uid
        ;
        $d_row =   DBService::q_with_native_sql($sql,3) ;
        $d_row["id"] ;
         
        if ( is_null( $d_row["id"] ) )
            parent::rtn_json(['cd'=>"-1" ,"msg"=>"没有对应的储蓄账户"]) ;
    
    
    
            $ord = new ChargeOrd ();
            $ord->sn =  date('YmdHis') .  MyHelpers::gen_random_cd(6) ;
            $ord->uid = $uid ;
            $ord->wish_id = $data["wishid"] ;
            $ord->deposit_id = $d_row["id"];
            $ord->chennel = ChargeOrd::CHENNEL_ALIPAY ;
            $ord->save(false);
    
            parent::rtn_json(['cd'=>1,'ord'=>$ord]) ;
    
    }
    public function actionRankList()
    {
        $data = parent::getJ() ;
        $uid = parent::getU()->getId();
        $sql =
       "SELECT".
        " charge_ord.uid,".
        " SUM(charge_ord.totfee) total".
        " FROM".
        " charge_ord".
        " INNER JOIN deposit".
        " ON deposit.id = charge_ord.deposit_id".
        " where charge_ord.wish_id = ".$data["wishid"].
        " and deposit.uid = 0".
        " GROUP BY charge_ord.uid"
        ;
       $rows = DBService::q_with_native_sql($sql, 1) ;
//        var_dump($rows) ;
       foreach ($rows as $key => $row) {
           $volume[$key]  = $row['total'];
           $edition[$key] = $row['uid'];
       }
       
       // Sort the data with volume descending, edition ascending
       // Add $data as the last parameter, to sort by the common key
       array_multisort($volume, SORT_DESC, $edition, SORT_ASC, $rows);
//        var_dump($rows) ;
       
       foreach ($rows as $key => $row) {
        $u_profile = UserProfileService::q_profile_from_uid_all($row['uid']) ;
        $rows[$key]["nickname"] = $u_profile["nickname"] ;
        $rows[$key]["portrait"] = $u_profile["portrait"] ;
        
       }
//        var_dump($rows) ;

       parent::rtn_json(['cd'=>1,'list'=>$rows]) ;
    }
    
}
