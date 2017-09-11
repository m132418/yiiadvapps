<?php
namespace frontend\modules\appsrv\controllers;
use common\components\Easemob;
use common\components\MyHelpers;
use common\models\SmsLog;
use common\models\EasemobExt;
use common\models\User;
use yii\helpers\Json;
use frontend\modules\appsrv\services\UserProfileService ;
include_once    \Yii::getAlias('@vendor') . '/alidayu/TopSdk.php';
use AlibabaAliqinFcSmsNumSendRequest;
use TopClient;
use frontend\modules\appsrv\services\DBService;
// require_once '@vendor'; 
/**
 * Default controller for the `appsrv` module
 */
class UserController extends AppSrvBase1Controller
{
    public function actionLogin()
    {  
        $data = parent::getJ() ;
        $mobi = $data['mobi'];
        $pwd = $data['pwd'];
        // check mobi
        $result = User::findOne([
            'mobi' => $mobi
        ]);
        
            // validate password
         if ($result && $result->validatePassword($pwd))  
         { 
//              $access_token = $result->genAccessToken($pwd) ;      
//          $result->access_token = $access_token ;
//          $result->save() ;
         
//          $easemob = [
//              "username"=>$result->easemob_u ,  "passowrd"=>$result->easemob_p ,
//          ];
//       $row =   UserProfileService::q_profile_from_uid($result->id) ;
// var_dump($row) ;exit() ;
       
//              parent::rtn_json(['cd'=>"1", "uid"=>$result->id,"access_token"=>$result->access_token,'nickname'=> is_null($result->nickname) ? "无昵称": $result->nickname  
//                  , "portrait"=>   isset($row) ?$row["portrait"] : ""  ,
//                  "signature"=>   isset($row) ?$row["signature"] : "",
//                  "baseurl" =>\Yii::$app->params["qiniu"]['portraits']  ,"easemob"=>$easemob]) ;
             
                 parent::rtn_json(['cd'=>"1"]) ;
         }
         parent::rtn_json(['cd'=>"-1"]) ;        
       
    }
    /*
     * sent 注册验证
     */
    public function actionRegcd()
    {
//         $mobino = $_POST['mobino'];
        
        $data = parent::getJ() ;
        //         $uid = parent::getU()->getId();
        $mobino = ($data["mobino"]) ;

        date_default_timezone_set('Asia/Shanghai');
        $c = new TopClient;
        $c ->appkey = \Yii::$app->params["sms"] ["appk"] ;
        $c ->secretKey = \Yii::$app->params["sms"] ["secretk"] ;
        $req = new AlibabaAliqinFcSmsNumSendRequest;
        $req ->setExtend( "123456" );
        $req ->setSmsType( "normal" );
        $req ->setSmsFreeSignName( "注册验证" );
        $vcode  = rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);
        $req ->setSmsParam( "{\"code\":\"". $vcode. "\",\"product\":\"粒粒米\"}" );
        $req ->setRecNum( $mobino );
        $req ->setSmsTemplateCode( "SMS_1125036" );
        $resp = $c ->execute( $req );
        
     
//         echo $resp->result->err_code;
//         echo $resp->request_id;
        
        if ($resp->result->err_code == 0) {
//             echo $resp->request_id;
            
            $model = new SmsLog ;
            
            $model->mobino =$mobino;
            $model->request_id = $resp->request_id;
            $model->vcode = $vcode ;
            $model->template = "SMS_1125036" ;
            $model->save(false);
//             header("Content-type: application/json;charset=utf-8");
//              echo( Json::encode(['cd'=>1]));
//              return  ;
             parent::rtn_json(['cd'=>"1"]) ;
        }
//         header("Content-type: application/json;charset=utf-8");
//         echo( Json::encode(['cd'=>-1]));
        parent::rtn_json(['cd'=>"-1"]) ;
    }
    
    public function actionChkcd1()
    {
        $data = parent::getJ() ;
        $mobino = $data['mobino'];
       
    
        date_default_timezone_set('Asia/Shanghai');
        $c = new TopClient;
        $c ->appkey = \Yii::$app->params["sms"] ["appk"] ;
        $c ->secretKey = \Yii::$app->params["sms"] ["secretk"] ;
        $req = new AlibabaAliqinFcSmsNumSendRequest;
        $req ->setExtend( "123456" );
        $req ->setSmsType( "normal" );
        $req ->setSmsFreeSignName( "身份验证" );
        $vcode  = rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);
        $req ->setSmsParam( "{\"code\":\"". $vcode. "\",\"product\":\"粒粒米\"}" );
        $req ->setRecNum( $mobino );
        $req ->setSmsTemplateCode( "SMS_1125039" );
        $resp = $c ->execute( $req );
    

    
        if ($resp->result->err_code == 0) {
            //             echo $resp->request_id;
    
            $model = new SmsLog ;
    
            $model->mobino =$mobino;
            $model->request_id = $resp->request_id;
            $model->template = "SMS_1125039" ;
            $model->vcode = $vcode ;
            $model->save(false);
          
//             header("Content-type: application/json;charset=utf-8");
//             echo( Json::encode(['cd'=>1]));
//             return  ;
            parent::rtn_json(['cd'=>"1"]) ;
        }
//         header("Content-type: application/json;charset=utf-8");
//         echo( Json::encode(['cd'=>-1]));
        parent::rtn_json(['cd'=>"-1"]) ;
    
    }
    
    public function actionChkcd2()
    {  $data = parent::getJ() ;
        $mobino = $data['mobino'];
        $vcode = $data['vcode'];
    
        $onedata =   SmsLog::findOne(['vcode'=>$vcode ,'mobino'=>$mobino ]) ;
        //      var_dump($onedata) ;exit();
        if (!$onedata) {
//             header("Content-type: application/json;charset=utf-8");
//             echo( Json::encode(['cd'=>-1]));
            parent::rtn_json(['cd'=>"-1"]) ;
        }else  
        {

       $u =    User::findOne(['mobi'=>$mobino ]) ;
           
            parent::rtn_json(['cd'=>"1" ,"access_toekn"=>$u->access_token]) ;
        }
        
    
    }
    public function actionYoumengMobi()
    {

        $post = file_get_contents("php://input");
        //decode json post input as php array:
        $data = JSON::decode($post, true);
        
        $mobi = $data["mobi"] ;
        $openid = $data["openid"] ;
        
        
        $onedata =   User::findOne(['youmeng_openid'=>$openid]) ;
        if (!$onedata) {
//             header("Content-type: application/json;charset=utf-8");
//             echo( Json::encode(['cd'=>-1]));
//             return  ;
            parent::rtn_json(['cd'=>"-1"]) ;
        }
        $onedata->mobi =$mobi ;
        $onedata->save(false) ;
//         header("Content-type: application/json;charset=utf-8");
//         echo( Json::encode(['cd'=>1]));
        parent::rtn_json(['cd'=>"11"]) ;
    }
    
    public function actionRestpwd()
    {
        $data = parent::getJ() ;
        $mobi = $data['mobi'];
        $pwd = $data['pwd'];
        
        // check vcode
        $onedata =   User::findOne(['mobi'=>$mobi]) ;
        if (!$onedata) {
//             header("Content-type: application/json;charset=utf-8");
//             echo( Json::encode(['cd'=>-1]));
//             return  ;
            parent::rtn_json(['cd'=>"-1"]) ;
        }
             
        $onedata->setPassword($pwd);
        $onedata->save(false) ;
        $newtoken = $onedata->genAccessToken($pwd) ;
//         header("Content-type: application/json;charset=utf-8");
//         echo( Json::encode(['cd'=>1,'access_token'=>$newtoken]));
        parent::rtn_json(['cd'=>"1",'access_token'=>$newtoken]) ;
    }
    
    public function actionRestpwd2()
    {
        $data = parent::getJ() ;
        $mobi = $data['mobi'];
        $pwd1 = $data['pwd1'];
        $pwd2 = $data['pwd2'];
    
        // check vcode
        $onedata =   User::findOne(['mobi'=>$mobi]) ;
        if (!$onedata) {
           parent::rtn_json(['cd'=>"-1" , "msg"=>"没这个账户"]) ;
        }
        
        
        if (!$onedata->validatePassword($pwd1)) {
             parent::rtn_json(['cd'=>"-1" , "msg"=>"原来密码没过"]) ;
        }
        
         
        $onedata->setPassword($pwd2);
        $onedata->save(false) ;
        $newtoken = $onedata->genAccessToken($pwd) ;
        //         header("Content-type: application/json;charset=utf-8");
        //         echo( Json::encode(['cd'=>1,'access_token'=>$newtoken]));
        parent::rtn_json(['cd'=>"1",'access_token'=>$newtoken]) ;
    }

    /*
     * mobino:15321678020
vcode:895687
pwd:123456
     */
    public function actionSignup()
    {
//         var_dump($_POST["mobino"]) ;

        $data = parent::getJ() ;
//         var_dump($data) ;exit();
        
        
        $check_mobi =   User::findOne(['mobi'=>$data["mobino"] ]) ;
        if ($check_mobi) {
            parent::rtn_json(['cd'=>"-1",'msg'=>"电话重复了"]) ;
        }
        // check vcode
//      $onedata =   SmsLog::find()->where(['mobino'=>$data["mobino"] ,'vcode'=>$data["vcode"] ])->one() ;
     
     $sql_in =
     "SELECT".
        " id".
        " FROM".
        " sms_log".
        " where mobino = '".$data["mobino"]."'".
        " and vcode ='" .$data["vcode"]."'"
         ;
   $onedata =  DBService::q_with_native_sql($sql_in, 3) ;
     
     
//      var_dump($onedata) ;exit();
      if (!$onedata) {
          parent::rtn_json(['cd'=>"-1","msg"=>"验证码没过"]) ;
      }  
      
      
      $user = new User();
    
          $user->mobi = $data["mobino"];
          $user->setPassword($data["pwd"]);
          $user->nickname = $data["nickname"] ;
          $user->generateAuthKey();           
          $access_token = $user->genAccessToken($data["pwd"]) ;
          $user->access_token = $access_token ;
          $user->save(false) ;
      
      $easemob_options = \Yii::$app->params["easemob"] ;
      $easemob = new Easemob($easemob_options);
      
      $username="llm_" . $user->getId() 
//       . '_' . MyHelpers::gen_random_cd(3) 
      ;
      $password= MyHelpers::gen_random_cd(6) ;
   $easemob_user =    $easemob->createUser($username, $password) ;
      
//    print_r($easemob_user) ;exit() ;
   
   $user->easemob_u = $username ;
   $user->easemob_p = $password ;
   $user->save(false) ;
   
   
//    ---------发环信 存 扩展部分--------
   $easemob_options = \Yii::$app->params["easemob"] ;
   $e = new Easemob($easemob_options);
   $easemob_result=  $e->sendText( 'sys-message',
       "users",
       [$user->easemob_u],
        "亲爱的粒粒米用户您好,还有未完成资料需要填写.",
       [
           //                 "fromNickname"=> $u_profile ["nickname"] ,"fromPortrait"=> $u_profile["portrait"] ,"dd-title"=>$dd["title"],"sent-date"=>date("Y-m-d")
           "key"=>"sys-message"
       ]
       ) ;
   $e_ext = new EasemobExt();
   $e_ext->frm_id = $user->id ;
   $e_ext->to_id = $user->id ;
   $e_ext->content =   "亲爱的粒粒米用户您好,还有未完成资料需要填写.";
   $e_ext->ext = json_encode(
       [
           "fromNickname"=> "粒粒米" ,"fromPortrait"=> "common.png" ,"sent-date"=>date("Y-m-d")
   
       ]
       );
   $e_ext->etype = EasemobExt::E_TYPE_SYS ;
   $e_ext->save(false) ;
   
//    ----------------

      parent::rtn_json(['cd'=>"1" ,"uid"=>$user->id, "nickname"=>$data["nickname"],"access_token"=>$access_token, "nickname"=> $data["nickname"] ,'easemob'=>[ "username"=>$username ,  "passowrd"=>$password ]]) ;

      
      
      
    }
    public function actionLinkYoumeng()
    {
        $post = file_get_contents("php://input");
        //decode json post input as php array:
        $data = JSON::decode($post, true);
        
        
       $openid = $data["openid"] ;
       
     $user =  User::findOne(['youmeng_openid'=>$openid]) ;
     
     if ($user) {// 有这个 openid 么？ 没有注一个 ,有就返回一个 token
         $access_token = $user->genYMAccessToken($openid) ;
         $user->access_token = $access_token ;
         $user->save(false) ;
//          echo( Json::encode(['cd'=>1,"access_token"=>$access_token]));
         
         
         $easemob = [
             "username"=>$user->easemob_u ,  "passowrd"=>$user->easemob_p ,
         ];
//          header("Content-type: application/json;charset=utf-8");
//          echo( Json::encode(['cd'=>1,"access_token"=>$access_token,"easemob"=>$easemob]));
         parent::rtn_json(['cd'=>"1","access_token"=>$access_token,"easemob"=>$easemob]);
         
     }else
     {
           $user = new User();
//       $user->username = $_POST["mobino"];
     
      $user->generateAuthKey();
      $user->youmeng_openid = $openid ;
      $access_token = $user->genYMAccessToken($openid) ;
      $user->access_token = $access_token ;
      
      $user->save(false) ;      
//       echo( Json::encode(['cd'=>1,"access_token"=>$access_token]));
      
      $easemob_options = \Yii::$app->params["easemob"] ;
      $easemob = new Easemob($easemob_options);
      
      $username="llm_" . $user->getId() . MyHelpers::gen_random_cd(3) ;
      $password= MyHelpers::gen_random_cd(6) ;
      $easemob_user =    $easemob->createUser($username, $password) ;
      
      $user->easemob_u = $username ;
      $user->easemob_p = $password ;
      $user->save(false) ;
//       header("Content-type: application/json;charset=utf-8");
       parent::rtn_json( ['cd'=>1,"access_token"=>$access_token,'easemob'=>[ "username"=>$username ,  "passowrd"=>$password ]]);
      
      
      
      
     }
            
    }


}
