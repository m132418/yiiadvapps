<?php
namespace frontend\modules\appsrv\controllers;

use common\models\UserProfile;
use common\models\User;
// require_once '@vendor'; 
/**
 * Default controller for the `appsrv` module
 */
class UserProfileController extends AppSrvBase2Controller
{
    public function actionUpdateSignature()
    {
        $data = parent::getJ() ;
//                         var_dump(  $data["uid"]) ;
//                         var_dump(  $data["signature"]) ;
                        
        $this->check_user_exists($data["uid"] );
        
  $u_f =      UserProfile::findOne(['uid'=> $data["uid"]]) ;
   
  if ($u_f) {
      $u_f->signature =  $data["signature"] ;
      $u_f->save(false) ;
      
  }else 
  {
   $u_f =  new UserProfile() ;
   $u_f->signature =  $data["signature"] ;
   $u_f->uid = $data["uid"] ;
   $u_f->save(false) ;
  }
  parent::rtn_json(['cd'=>"1"]) ;
    }
    public function actionUpdateNickname()
    {
        $data = parent::getJ() ;
//                 var_dump(  $data["uid"]) ;
//                 var_dump(  $data["new_nickname"]) ;
//         $this->check_user_exists($data["uid"] );
    $u =    User::findOne(['id'=>$data["uid"]]);
        
    if ($u) {
       $u->nickname = $data["new_nickname"] ;
       $u->save(false) ;
       parent::rtn_json(['cd'=>"1"]) ;
    }else 
    {
        parent::rtn_json(['cd'=>"-1","msg"=>'没这个用户']) ;
    }
        
        
    }
    public function actionSetRegion()
    {
        $data = parent::getJ() ;
//         var_dump(  $data["uid"]) ;
//         var_dump(  $data["prov"]) ;
//         var_dump(  $data["city"]) ;
        $this->check_user_exists($data["uid"] );
        $user_profile_is_exists = (new \yii\db\Query())
        ->select(['id'])
        ->from('user_profile')
        ->where([  'uid' => $data["uid"] ])
        ->one();
        $u_p  =null;
        if ($user_profile_is_exists) {
            $u_p =   UserProfile::findOne([ 'uid' => $data["uid"]]) ;
        }else
        {
            $u_p = new UserProfile() ;
        }
        $u_p->prov =(int)  $data["prov"] ;    $u_p->city =(int)  $data["city"] ;
        $u_p->save(false) ;
        parent::rtn_json(['cd'=>"1"]) ;
    }
  
    public function actionUploadPhoto()
    {
        $data = parent::getJ() ;
        $uid = parent::getU()->getId() ;
//       var_dump(  $data["pic_keyname"]) ;
//       var_dump(  $data["uid"]) ;

$u_p =   UserProfile::findOne([ 'uid' => $data["uid"]]) ;   


      if ($u_p) {
      ;
      }else
      {
         $u_p = new UserProfile() ;    
         $u_p->uid =  $data["uid"]  ;
      }      
      $u_p->portrait = $data["pic_keyname"] ;
     
      $u_p->save(false) ;
      parent::rtn_json(['cd'=>"1"]) ;
    }
    /**
     * 
     */private function check_user_exists($uid)
    {
        //         parent::rtn_json(['cd'=>"11"]) ;
          // check if this uid exists in user and user_profile
              $user = (new \yii\db\Query())
              ->select(['id'])
              ->from('user')
              ->where([  'id' => $uid])
              ->one();
              // 
              
              if (!$user) {
                 parent::rtn_json(['cd'=>"-1" ,"msg"=>"没这个id的账号"]) ;
              }
    }

    
    public function actionUdtPwd()
    {  $data = parent::getJ() ; $uid = parent::getU()->getId() ;
//     var_dump($data) ;

  $u =  User::findOne(["id"=>$uid]) ;
  
   if ($u->youmeng_openid) {
         parent::rtn_json(['cd'=>"-1" ,"msg"=>"第三方登录不用改密码"]) ;
   }
   
   $u->password = $data["new_pwd"] ; $u->save(false) ;
   parent::rtn_json(['cd'=>"1" ]) ;
    }
    public function actionIndex()
    {
        
    }
   
}
