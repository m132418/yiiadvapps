<?php
namespace frontend\modules\appsrv\services;

class UserProfileService
{
    public static function q_profile_from_uid ($uid) {
       $row = (new \yii\db\Query())
    ->select(['id', 'portrait' ,'signature'])
    ->from('user_profile')
    ->where([  'uid' => $uid ]) 
    ->one();
       return $row ;
    }
    public static function q_profile_from_uid_all ($uid) {
       
        $db =  \Yii::$app->db ;
        $sql = 
        "SELECT".
            " `user`.id uid,".            
            " IFNULL(`user`.nickname,'') nickname ,".
            " IFNULL(`user`.mobi,'') mobi ,".
            " IFNULL(user_profile.age,'') age ,".
            " IFNULL( user_profile.gender,'') gender,".
            " IFNULL (user_profile.prov,'') prov,".
            " IFNULL(user_profile.city,'') city,".
            " IFNULL(user_profile.portrait,'') portrait,".
            " IFNULL(user_profile.signature,'') signature,".
            " IFNULL(user_profile.qq,'') qq,".
            " IFNULL(user_profile.wechat,'') wechat,".
            " IFNULL(user_profile.weibo,'') weibo".
            " FROM `user`".
            " LEFT JOIN user_profile".
            " ON user_profile.uid = `user`.id".
            " where `user`.id = " .$uid
        ;
//         var_dump($sql);
        $model = $db->createCommand($sql);
        $data =  $model->queryOne();
        
        return $data ;
    }  
    
    public static function q_profile_from_uids_all ($uids =[]) {
         
        if ( empty($uids) ) {
            return [];
        }
        
        $db =  \Yii::$app->db ;
        $sql =
        "SELECT".
        " `user`.id ,".
        "IFNULL( `user`.youmeng_openid,'') youmeng_openid, ".
        " IFNULL(`user`.nickname,'') nickname ,".
        " IFNULL(`user`.mobi,'') mobi ,".
        " IFNULL(user_profile.age,'') age ,".
        " IFNULL( user_profile.gender,'') gender,".
        " IFNULL (user_profile.prov,'') prov,".
        " IFNULL(user_profile.city,'') city,".
        " IFNULL(user_profile.portrait,'') portrait,".
        " IFNULL(user_profile.signature,'') signature,".
        " IFNULL(user_profile.qq,'') qq,".
        " IFNULL(user_profile.wechat,'') wechat,".
        " IFNULL(user_profile.weibo,'') weibo".
        " FROM `user`".
        " LEFT JOIN user_profile".
        " ON user_profile.uid = `user`.id".
        " where `user`.id in (" .  implode(",", $uids ). ")"
        ;
        //         var_dump($sql);
        $model = $db->createCommand($sql);
        $data =  $model->queryAll();
    
        return $data ;
    }
   
}

?>