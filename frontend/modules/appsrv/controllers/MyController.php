<?php
namespace frontend\modules\appsrv\controllers;
use frontend\modules\appsrv\services\DongdanService;
use frontend\modules\appsrv\services\UserProfileService;
use frontend\modules\appsrv\services\DBService;
use common\models\UserBankAccts;
use common\models\UserAlipay;
use common\models\UserBalance;
use common\models\FeedBack;
use common\models\Wish;
use common\models\Deposit;
use common\models\Status;
use common\models\CashApply;
use yii\base\Exception;
use common\models\ChargeOrd;
use common\components\MyHelpers;
/**
 * Default controller for the `appsrv` module
 */
class MyController extends AppSrvBase2Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        parent::rtn_json(['cd'=>"1" ,"uptoekn"=>"sdsd"]) ;
    }
    public function actionWishQuan()
    {
        $data = parent::getJ();
//         var_dump($data) ;
        $uid = parent::getU()->getId();
        
      $q_result =  UserProfileService::q_profile_from_uid_all($uid) ;
     $q_dd = DongdanService::q_dongdan_with_uid($uid) ;
     
     foreach ($q_dd as $key => $value) {
         $q_dd[$key]["pics"] = explode(",", $value["pics"]) ;
     }   
     
     
        parent::rtn_json(['cd'=>"1" ,"profile"=>$q_result ,
//             "baseurl" =>\Yii::$app->params["qiniu"]['portraits' ] ,
            "dongdans"=>$q_dd ,
//             "baseurl_dd" =>\Yii::$app->params["qiniu"]['dongdan-pics'] 
            
        ]) ;
    }
    public function actionProfile()
    {
        $data = parent::getJ();
        //         var_dump($data) ;
        $uid = parent::getU()->getId();
        
        $q_result =  UserProfileService::q_profile_from_uid_all($uid) ;
      

         
        
        parent::rtn_json(['cd'=>"1" ,"profile"=>$q_result ,
//             "baseurl" =>\Yii::$app->params["qiniu"]['portraits' ] ,
//             "dongdans"=>$q_dd ,"baseurl_dd" =>\Yii::$app->params["qiniu"]['dongdan-pics']
        
        ]) ;
    }
    public function actionListBank()
    {
        $uid = parent::getU()->getId();
        $sql_in = 
        "SELECT                     ".
        "user_bank_accts.id,        ".
        "user_bank_accts.uid,       ".
        "user_bank_accts.cname,     ".
        "user_bank_accts.idcard,    ".
        "user_bank_accts.bank_name, ".
        "user_bank_accts.bank_no    ".
        "FROM user_bank_accts       ".
        "where user_bank_accts.uid = ". 
          $uid  ;
//         var_dump($sql_in) ;exit() ;
      $data =  DBService::q_with_native_sql($sql_in, 1) ;
      parent::rtn_json(['cd'=>"1","data"=>$data]) ;
    }
    public function actionBindingBank()
    {
        $data = parent::getJ();
//                 var_dump($data) ;
        $uid = parent::getU()->getId();
        
   $is_exists =     UserBankAccts::findOne([
            'uid'=>$uid ,
            'bank_no'=>$data["bank_no"]
        ]) ;
        
   if ($is_exists) {
        parent::rtn_json(['cd'=>"-1" ,"msg"=>"已经有了不用再存了"]) ;
   }
     $u_b = new UserBankAccts();
     $u_b->uid = $uid ;
     $u_b->cname = $data["cname"] ;
     $u_b->bank_name = $data["bank_name"] ;
     $u_b->bank_no = $data["bank_no"] ;
     $u_b->save(false) ;
     parent::rtn_json(['cd'=>"1"]) ;
    }
    
    public function actionBindingAlipay()
    {
        $data = parent::getJ();       
        $uid = parent::getU()->getId();
        
        $is_exists =     UserBankAccts::findOne([
            'uid'=>$uid ,
            'alipay'=>$data["alipay"]
        ]) ;
        
        if ($is_exists) {
            parent::rtn_json(['cd'=>"-1" ,"msg"=>"已经有了不用再存了"]) ;
        }
        $u_b = new UserAlipay();
        $u_b->uid = $uid ;
        $u_b->cname = $data["cname"] ;
        $u_b->alipay = $data["alipay"] ;      
        $u_b->save(false) ;
        parent::rtn_json(['cd'=>"1"]) ;
//         
    }

    public function actionShowAlipay()
    {
        $uid = parent::getU()->getId();
        $data = UserAlipay::find()->where([
            "uid" => $uid
        ])
            ->asArray()
            ->one();
        
        if ($data) {
            parent::rtn_json([
                'cd' => "1",
                "data" => $data
            ]);
        } else
            parent::rtn_json([
                'cd' => "-1"
            ]);
    }
    public function actionShowBalance()
    {
        $uid = parent::getU()->getId();
        $data = UserBalance::find()->where([
            "uid" => $uid
        ])
        ->asArray()
        ->one();
        
        if ($data) {
           
        } else
        {
          $data = new  UserBalance() ;
          $data->uid =$uid ;
          $data->save(false);
        }
        $sql_in =
        "SELECT COUNT(1) cnt".
            " FROM user_bank_accts".
            " where uid  = " .$uid
                ;
        
       $cnt =  DBService::q_with_native_sql($sql_in, 4) ;
//         var_dump($cnt) ;exit();
        
       $data = array_merge($data , ["bank_accts_count"=>$cnt]);
       
        parent::rtn_json([
            'cd' => "1",
            "data" => $data
        ]);
    }
    public function actionWish2balance()
    {
        $uid = parent::getU()->getId();
        $data = parent::getJ();
        //change wish & deposits status -> inactive
        $w=Wish::findOne(["id"=>$data["wishid"]]);
        if (!$w) {
            parent::rtn_json(['cd' => "-1","msg"=>"愿望数据不存在"]);
        }
        $deposits = Deposit::findAll(["wishid"=>$data["wishid"]]) ;
        if (!$w) {
            parent::rtn_json(['cd' => "-1","msg"=>"愿望下存款不存在"]);
        }
        
        $w->status = Status::STATUS_DELETED ;$w->save(false) ;
        $deposits_sum = 0 ;
        foreach ($deposits as $value) {
           $value->status =Status::STATUS_DELETED ;
            $deposits_sum += $value->deposit ;
           $value->save(false) ;
        }
        //deposits -> balance 
        $u_b = UserBalance::findOne(["uid"=>$uid]) ;
        if (!$u_b) {
           $u_b = new UserBalance();$u_b->uid = $uid ;
        }
        $u_b->amt += $deposits_sum ;
        parent::rtn_json(['cd' => "1"]);
        
    }
    public function actionBalance2alipay()
    {   
        $uid = parent::getU()->getId();
        $data = parent::getJ();$db = \Yii::$app->db;
//         var_dump($uid);exit();
        $u_b = UserBalance::findOne(["uid"=>$uid]) ;
        if (!$u_b) {
            $u_b = new UserBalance();
            $u_b->uid = $uid ;
            $u_b->save(false);
            parent::rtn_json(['cd' => "-1","msg"=>"余额不足"]);
        }
        
        if ((int)$data["amt2transfer"] > $u_b->amt) {
             parent::rtn_json(['cd' => "-1","msg"=>"余额不足"]);
        }
        // 插入一个申请记录
        $u_p = UserAlipay::findOne(["uid"=>$uid]) ;
        
        $c_apply = new CashApply() ;        
        $transaction = $db->beginTransaction();
        try {
            $u_b->amt -= (int)$data["amt2transfer"] ; 
            $c_apply->amt2transfer += (int)$data["amt2transfer"] ; 
            $c_apply->target_alipay = $u_p->id ;            
            $u_b->save(false) ;$c_apply->save(false);            
            $db->commit();
            parent::rtn_json(['cd' => "1"]);
        } catch (Exception $e) {
            $db->rollback();
            parent::rtn_json(['cd' => "-1","msg"=>"交易失败"]);
        }
        

    }
    public function actionBalance2bankacct()
    {
         $uid = parent::getU()->getId();
        $data = parent::getJ();$db = \Yii::$app->db;
//         var_dump($uid);exit();
        $u_b = UserBalance::findOne(["uid"=>$uid]) ;
        if (!$u_b) {
            $u_b = new UserBalance();
            $u_b->uid = $uid ;
            $u_b->save(false);
            parent::rtn_json(['cd' => "-1","msg"=>"余额不足"]);
        }
        
        if ((int)$data["amt2transfer"] > $u_b->amt) {
             parent::rtn_json(['cd' => "-1","msg"=>"余额不足"]);
        }
        // 插入一个申请记录
        $u_b_acct = UserBankAccts::findOne([ "id"=>(int)$data["bankacct_id"],"uid"=>$uid]) ;
        
      
        
        if (!$u_b_acct) {
          parent::rtn_json(['cd' => "-1","msg"=>"查不到你的银行账户信息"]);
        }
        
        
        $c_apply = new CashApply() ;        
        $transaction = $db->beginTransaction();
        try {
            $u_b->amt -= (int)$data["amt2transfer"] ; 
            $c_apply->amt2transfer += (int)$data["amt2transfer"] ; 
            $c_apply->target_bank_id = $u_b_acct->id ;            
            $u_b->save(false) ;$c_apply->save(false);            
            $transaction->commit();
            parent::rtn_json(['cd' => "1"]);
        } catch (Exception $e) {
            $transaction->rollBack() ;
            parent::rtn_json(['cd' => "-1","msg"=>"交易失败"]);
        }
        
    }
    
    public function actionAlipay2balance()
    {
        $uid = parent::getU()->getId();
        $data = parent::getJ();
            $ord = new ChargeOrd ();
            $ord->sn =  date('YmdHis') .  MyHelpers::gen_random_cd(6) ;
            $ord->uid = $uid ;
            $ord->wish_id =0 ;           
            $ord->chennel = ChargeOrd::CHENNEL_ALIPAY ;
            $ord->save(false);
            parent::rtn_json(['cd'=>1,'ord'=>$ord]) ;
    }
    public function actionTxDetail()
    {
        $uid = parent::getU()->getId();
        $sql_in =
       "SELECT".
        " inout_detail.id,".
        " inout_detail.d_type,".
        " inout_detail.uid,".
        " inout_detail.amount,".
        " inout_detail.created_at,".
        " inout_detail.updated_at".
        " FROM inout_detail".
        " where inout_detail.uid = " . $uid
        ;
        
     $d_tails = DBService::q_with_native_sql($sql_in, 1) ;  
     parent::rtn_json(['cd'=>1,'data'=>$d_tails]) ;
    }
    
    public function actionFeedback()
    {
        $data = parent::getJ() ;
        $uid = parent::getU()->getId();
//                  var_dump($data["feedback"]) ;
        
        $feedback = new FeedBack();
        $feedback->uid = $uid ;
        $feedback->content = $data["feedback"] ;
        $feedback->save(false) ;
        parent::rtn_json(['cd'=>1]) ;
    
    }
     
    
}
