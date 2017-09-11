<?php
namespace frontend\modules\appsrv\services;

class CommentsService
{
    public static function retrieve_re_comments ($pid) {
        $row = (new \yii\db\Query())
        ->select([
            "dd_coment.id",
            "dd_coment.content",
            "dd_coment.uid",
            "dd_coment.cm_type",
            "dd_coment.created_at",
            "user.nickname",
            "user_profile.portrait"
        ])
        ->from('dd_coment_child')
        ->innerJoin("dd_coment" , "dd_coment_child.cid = dd_coment.id")
        ->leftJoin("user_profile" , "user_profile.uid = dd_coment.uid")
        ->innerJoin("user" , "user.id = dd_coment.uid")
        ->where([  'dd_coment_child.pid' => $pid ])
        ->all();
        
      return      $row ;
      
    }
   
   
    
    
//     public static function fromuid_4easemob_assist ( $target_id,$target_arr) {
//              $uidkey = array_search($target_id, array_column($target_arr, 'id'));
//        var_dump(  $target_arr[$uidkey] ) ;
//         return $target_arr[$uidkey]  ;
//     }
}

?>