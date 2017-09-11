<?php
namespace frontend\modules\appsrv\controllers;
use common\components\Easemob;
use common\components\MyHelpers;
use common\models\ChargeOrd;
use common\models\DdComent;
use common\models\DdComentChild;
use common\models\DdZan;
use common\models\Dongdan;
use common\models\EasemobExt;
use common\models\Pager;
use common\models\Status;
use frontend\modules\appsrv\services\CommentsService;
use frontend\modules\appsrv\services\DongdanService;
use frontend\modules\appsrv\services\UserProfileService;
use frontend\modules\appsrv\services\UserService;
use frontend\modules\appsrv\services\DBService;
/**
 * Default controller for the `appsrv` module
 */
class FriendQuanController extends AppSrvBase2Controller
{
    public function actionPushDongdan()
    {  $uid = parent::getU()->getId();
    $data = parent::getJ() ;
    
    $dd =  new Dongdan() ;
    $dd->pics =implode(",",  $data["pic"])  ;
    $dd->content = $data ["content"];
    $dd->gps_x = $data ["gps_x"];
    $dd->gps_y = $data ["gps_y"];
    $dd->d_name = $data ["d_name"];
    $dd->uid = (int)$uid ;
    $dd->wishid =  $data["wishid"] ;
    $dd->save(false) ;
    
    parent::rtn_json(['cd'=>"1"]) ;
    }
    
    public function actionListDongdan()
    {
        $uid = parent::getU()->getId();
        $jdata = parent::getJ() ;
        //         var_dump($data["page"]) ;exit() ;
        $sql_in =
        "SELECT".
        " outterid".
        " FROM".
        " friend_list".
        " WHERE".
        " myid = " . $uid
            ;
        $outerids = DBService::q_with_native_sql($sql_in, 1) ;
        $outerids = array_column($outerids, "outterid");
//         var_dump($outerids) ;exit();
       
        
        
        $rows = (new \yii\db\Query())
        ->select([
            'dongdan.id',
            'dongdan.wishid',
            'dongdan.uid',
            'dongdan.pics',
            'dongdan.content',
            'dongdan.created_at'
        ])
        ->from('dongdan')
                ->where([  'dongdan.uid' => $outerids ])
        ->orderBy("dongdan.created_at desc ")
        ->limit((int)$jdata["page"] * Pager::PAGER_SPAN_M,Pager::PAGER_SPAN_M)
        ->all();
        //         var_dump($rows) ;exit() ;
        $data = [];$ddone=[];
    
        foreach ($rows as $key => $value) {
    
            $ddone["did"]=$value["id"] ;
            $ddone["uid"]=$value["uid"] ;
            $ddone["content"]=$value["content"] ;
            $ddone["wishid"]=$value["wishid"] ;
            $ddone["pics"]=
            explode(",",  $value["pics"] ) ;
            $d_profile =  UserProfileService::q_profile_from_uid_all($value["uid"]) ;
            $ddone["nickname"] = $d_profile["nickname"];
            $ddone["signature"] = $d_profile["signature"];
            //          var_dump($d_profile) ;
            //             var_dump($value) ;
            //             var_dump($ddone) ;
            //             exit() ;
    
    
    
    
            $nickname =   UserService::nickname_only1($value["uid"]) ;
            //         $rows[$key]["nickname"] = $nickname["nickname"] ;
            $value["nickname"] = $nickname["nickname"] ;
            $portrait_row =   UserProfileService::q_profile_from_uid($value["uid"]) ;
            //         $rows[$key]["portrait"] = $portrait_row["portrait"] ;
            $ddone["portrait"] = $portrait_row["portrait"] ;
            $ddone["created_at"] =  $value["created_at"];
    
            $coments = (new \yii\db\Query())->select([
                'dd_coment.id',
                'dd_coment.dongdanid',
                'dd_coment.content',
                'dd_coment.uid',
                'user.nickname',
                'user_profile.portrait',
                'user_profile.signature',
                'dd_coment.created_at',
                'dd_coment.updated_at'
            ])
            ->from('dd_coment')
            ->leftJoin("user" , "dd_coment.uid = user.id")
            ->leftJoin("user_profile","user_profile.uid = dd_coment.uid")
            ->where([
                'dongdanid' => $value["id"] , "cm_type"=>0
            ])
            ->all();
            //                $value["pics"] =explode(",", $value["pics"])    ;
    
            //                 $value["pics"] = explode(",",  $value["pics"])    ;
            foreach ($coments as $key => $value) {
                $c_rows =  CommentsService::retrieve_re_comments($value["id"]) ;
                $coments[$key]["re-comments"] = $c_rows ;
            }
    
            //             $rows[$key]["coments"] = $coments;
            $ddone["coments"] = $coments;
    
    
            //            $coments_count = (new \yii\db\Query())
            //             ->select([
            //                 'COUNT(1) coments_count'
            //             ])
            //             ->from('dd_coment')
            //             ->where([  'dongdanid' => $value["id"] ])
            //             ->count();
            //             $rows[$key]["coments_count"] =  (int) $coments_count ;
            $ddone["coments_count"] = count($coments) ;
    
    
    
            $zan_count = (new \yii\db\Query())
            ->select([
                'COUNT(1) zan_coount'
            ])
            ->from('dd_zan')
            ->where([  'did' =>$value["id"]])
            ->count();
    
            //             $rows[$key]["zan_count"] = (int)$zan_count ;
            $ddone["zan_count"] = (int)$zan_count ;
            //             var_dump($value["id"]) ;exit() ;
            $bancun_count =   DongdanService::bangcun_count_with_did($value["id"]) ;
            //          $rows[$key]["cun_count"] =  (int)$bancun_count ;
            $ddone["cun_count"] = (int)$bancun_count ;
            //             var_dump($bancun_count) ;exit() ;
    
            $data[] = array_merge($data ,$ddone) ;
        }
    
        parent::rtn_json(['cd'=>"1","data"=> $data]) ;
         
    }
    
    
    public function actionCommentDongdan()
    {
        $uid = parent::getU()->getId();
        $data = parent::getJ() ;
    
        $coment = new DdComent() ;
    
        $coment->uid = $uid ;
        $coment->dongdanid = $data[ "did"] ;
        $coment->content =  $data[ "content"] ;
        $coment->save(false) ;
    
        $dd =  DongdanService::q_dongdan_with_id($data["did"] ) ;
        $easemob_arr= UserService::fromuid_4easemob($dd["uid"]) ;
        $nickname = UserService::nickname_only1($uid) ;
        $u_profile =  UserProfileService::q_profile_from_uid_all($uid) ;
         
        $easemob_options = \Yii::$app->params["easemob"] ;
        $e = new Easemob($easemob_options);
        $easemob_result=  $e->sendText( 'interact-message',
            "users",
            [$easemob_arr [0]["easemob_u"]],
            $u_profile ["nickname"] . "评论了你的动态",
            [
                //                 "fromNickname"=> $u_profile ["nickname"] ,"fromPortrait"=> $u_profile["portrait"] ,"dd-title"=>$dd["title"],"sent-date"=>date("Y-m-d")
                "key"=>"interact-message"
            ]
            ) ;
        $e_ext = new EasemobExt();
        $e_ext->frm_id = $uid ;
        $e_ext->to_id = $dd["uid"] ;
        $e_ext->content = $u_profile ["nickname"] . "评论了你的动态";
        $e_ext->ext = json_encode(
            [
                "fromNickname"=> $u_profile ["nickname"] ,"fromPortrait"=> $u_profile["portrait"] ,"dd-title"=>$dd["title"],"sent-date"=>date("Y-m-d")
    
            ]
            );
        $e_ext->etype = EasemobExt::E_TYPE_INTERACT ;
        $e_ext->save(false) ;
    
    
    
    
        parent::rtn_json(['cd'=>"1"]) ;
    }
    public function actionReComment()
    {
        $uid = parent::getU()->getId();
        $data = parent::getJ() ;
        //         var_dump($uid) ;
        //         var_dump($data);
        $c = new DdComent() ;
        $c->content = $data["content"] ;
        $c->uid = $uid ;
        $c->dongdanid =  $data["dd_id"] ;
        $c->cm_type = 1;
        $c->save(false) ;
    
        $c_child = new DdComentChild() ;
        $c_child->cid = $c->id ;
        $c_child->pid = $data["comment_id"] ;
        $c_child->save() ;
        parent::rtn_json(['cd'=>"1"]) ;
    }
    public function actionZanDongdanYesorno()
    {
    
    }
    public function actionZanDongdan()
    {
        $uid = parent::getU()->getId();
        $data = parent::getJ() ;
        //         var_dump($data["dd_id"]) ;
    
    
        $is_exists=    DdZan::findOne([
            "did"=> $data["did"] ,
            "uid" =>$uid
        ]) ;
    
        if ($is_exists) {
            parent::rtn_json(['cd'=>"-1", "msg"=>"赞过"]) ;
        }
    
        $dd_z = new DdZan() ;
        $dd_z->uid = $uid ;
        $dd_z->did = $data["did"] ;
        $dd_z->save(false) ;
    
        $dd =  DongdanService::q_dongdan_with_id($data["did"] ) ;
        $easemob_arr= UserService::fromuid_4easemob($dd["uid"]) ;
        $nickname = UserService::nickname_only1($uid) ;
        $u_profile =  UserProfileService::q_profile_from_uid_all($uid) ;
         
        $easemob_options = \Yii::$app->params["easemob"] ;
        $e = new Easemob($easemob_options);
        $easemob_result=  $e->sendText( 'interact-message',
            "users",
            [$easemob_arr [0]["easemob_u"]],
            $u_profile ["nickname"] . "赞了你的动态",
            [
            //                 "fromNickname"=> $u_profile ["nickname"] ,"fromPortrait"=> $u_profile["portrait"] ,"dd-title"=>$dd["title"],"sent-date"=>date("Y-m-d")
                "key"=>"interact-message"
            ]
            ) ;
        $e_ext = new EasemobExt();
        $e_ext->frm_id = $uid ;
        $e_ext->to_id =$dd["uid"];
        $e_ext->content = $u_profile ["nickname"] . "赞了你的动态";
        $e_ext->ext = json_encode(
            [
                "fromNickname"=> $u_profile ["nickname"] ,"fromPortrait"=> $u_profile["portrait"] ,"dd-title"=>$dd["title"],"sent-date"=>date("Y-m-d")
    
            ]
            );
        $e_ext->etype = EasemobExt::E_TYPE_INTERACT ;
        $e_ext->save(false) ;
    
        parent::rtn_json(['cd'=>"1"]) ;
    }
    public function actionHelpCunDongdan()
    {
        $uid = parent::getU()->getId();
        $data = parent::getJ() ;
        //         var_dump($data["dd_id"]) ;
    
        // dd_id -> wishid & deposit_id
        $db =  \Yii::$app->db ;
        $sql =
        "SELECT wish.id AS wish_id,                      ".
        "deposit.id AS deposit_id,                       ".
        "deposit.uid FROM dongdan                        ".
        "INNER JOIN wish ON dongdan.wishid = wish.id     ".
        "INNER JOIN deposit ON deposit.wishid = wish.id  ".
        "where deposit.uid = 0 and   dongdan.id =" . $data["dd_id"]
        ;
        $model = $db->createCommand($sql);
        $one = $model->queryOne() ;
        if (!$one) {
            parent::rtn_json(['cd'=>"-1","msg"=>"数据不完善还不能存"]) ;
        }
    
        //--------easemob_ext----------------------
        $dd =  DongdanService::q_dongdan_with_id($data["did"] ) ;
        $easemob_arr= UserService::fromuid_4easemob($dd["uid"]) ;
        $nickname = UserService::nickname_only1($uid) ;
        $u_profile =  UserProfileService::q_profile_from_uid_all($uid) ;
         
        //               $easemob_options = \Yii::$app->params["easemob"] ;
        //               $e = new Easemob($easemob_options);
        //               $easemob_result=  $e->sendText( 'interact-message',
        //                   "users",
        //                   [$easemob_arr [0]["easemob_u"]],
        //                   $u_profile ["nickname"] . "赞了你的动态",
        //                   [
        //                   //                 "fromNickname"=> $u_profile ["nickname"] ,"fromPortrait"=> $u_profile["portrait"] ,"dd-title"=>$dd["title"],"sent-date"=>date("Y-m-d")
        //                       "key"=>"interact-message"
        //                   ]
        //                   ) ;
        $e_ext = new EasemobExt();
        $e_ext->frm_id = $uid ;
        $e_ext->to_id =$dd["uid"];
        $e_ext->content = $u_profile ["nickname"] . "帮你存了一笔";
        $e_ext->ext = json_encode(
            [
                "fromNickname"=> $u_profile ["nickname"] ,"fromPortrait"=> $u_profile["portrait"] ,"dd-title"=>$dd["title"],"sent-date"=>date("Y-m-d")
    
            ]
            );
        $e_ext->etype = EasemobExt::E_TYPE_INTERACT ;
        $e_ext->status = Status::STATUS_INACTIVE ;
        $e_ext->save(false) ;
    
        //-------------------------------
    
        $ord = new ChargeOrd ();
        $ord->sn =  date('YmdHis') .  MyHelpers::gen_random_cd(6) ;
        $ord->uid = $uid ;
        $ord->wish_id = $one ["wish_id"] ;
        $ord->deposit_id = $one ["deposit_id"];
    
        $ord->easemob_ext_id = $e_ext->id; // if alipy notify sucess update easemob_ext status 1 and send easemob message
    
        $ord->chennel = ChargeOrd::CHENNEL_ALIPAY ;
        $ord->save(false);
    
        parent::rtn_json(['cd'=>"1","ord"=>$ord]) ;
         
    }
    
    public function actionDdComments()
    {
        $uid = parent::getU()->getId();
        $data = parent::getJ() ;
        //         var_dump($data) ;
        $coments = (new \yii\db\Query())
        ->select([
            'dd_coment.id',
            'dd_coment.dongdanid',
            'dd_coment.content',
            'dd_coment.uid',
            'user.nickname',
            //                       'user_profile.portrait',
            "IFNULL(portrait,'') portrait",
            'dd_coment.created_at',
            'dd_coment.updated_at'
        ])
        ->from('dd_coment')
        ->leftJoin("user" , "dd_coment.uid = user.id")
        ->leftJoin("user_profile","user_profile.uid = dd_coment.uid")
        ->where([  'dd_coment.dongdanid' => $data["did"]  ,"cm_type"=>0 ])
        ->all();
    
        foreach ($coments as $key => $value) {
            $rows =  CommentsService::retrieve_re_comments($value["id"]) ;
            $coments[$key]["re-comments"] = $rows ;
        }
    
    
        parent::rtn_json(['cd'=>"1","comments"=>$coments]) ;
    }
    
    public function actionDdZans()
    {
        $uid = parent::getU()->getId();
        $data = parent::getJ() ;
        //                 var_dump($data["did"]) ;
    
        $db =  \Yii::$app->db ;
        $sql =
        " SELECT                           ".
        " dd_zan.uid,                      ".
        " nickname,                        ".
        " IFNULL(portrait,'') portrait,    ".
        " dd_zan.created_at                ".
        " FROM                             ".
        " dd_zan                           ".
        " LEFT JOIN `user`                 ".
        " ON dd_zan.uid = `user`.id        ".
        " LEFT JOIN user_profile           ".
        " ON dd_zan.uid = user_profile.uid ".
        " WHERE did =  "        . $data["did"] ;
        $zans= $db->createCommand($sql)->queryAll();
         
        parent::rtn_json(['cd'=>"1","zans"=>$zans]) ;
    }
    public function actionDdBangCun()
    {
        $uid = parent::getU()->getId();
        $data = parent::getJ() ;
        //                         var_dump($data) ;
    
        // dd_id ->wishid -> diposit id which is uid = 0
    
        $db =  \Yii::$app->db ;
        $sql =
        "SELECT wish.id wish_id,                        ".
        "deposit.id deposit_id                          ".
        "FROM wish                                      ".
        "INNER JOIN deposit ON deposit.wishid = wish.id ".
        "INNER JOIN dongdan ON dongdan.wishid = wish.id ".
        "WHERE deposit.uid = 0 AND                      ".
        "dongdan.id = ". $data["did"] ;
        $ids= $db->createCommand($sql)->queryOne();
        //          var_dump($ids) ;
        //sel charge_ord where uid wish_id deposit_id
        if (!$ids) {
            parent::rtn_json(['cd'=>"-1","msg"=>"数据不全"]) ;
        }
    
        $sql2 =
        "SELECT                                                  ".
        "charge_ord.uid,                                         ".
        "charge_ord.totfee,                                      ".
        "charge_ord.created_at,                                  ".
        "`user`.nickname,                                        ".
        "IFNULL(user_profile.portrait,'')  portrait              ".
        "FROM                                                    ".
        "charge_ord                                              ".
        "INNER JOIN `user` ON `user`.id = charge_ord.uid         ".
        "LEFT JOIN user_profile ON user_profile.uid = `user`.id  ".
        "WHERE                                                   ".
        "charge_ord.ispayed = 1                                  ".
        " and charge_ord.wish_id = ".$ids["wish_id"] .
        " and charge_ord.deposit_id = " .$ids["deposit_id"]
    
        ;
        $help_cuns= $db->createCommand($sql2)->queryAll();
        parent::rtn_json(['cd'=>"1","help_cuns"=>$help_cuns]) ;
    }
    
    
}

