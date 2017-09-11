<?php
namespace frontend\modules\appsrv\services;

class UserService
{
    public static function user_friendlist_ids ($uid) {
        $row = (new \yii\db\Query())
        ->select(['myid', 'outterid'])
        ->from('friend_list')
        ->where([  'myid' => $uid ])
        ->all();
        
      return      array_column($row, "outterid") ;
      
    }
    public static function nickname_only1 ($uid) {
        $row = (new \yii\db\Query())
        ->select(['id', 'nickname'])
        ->from('user')
        ->where([  'id' => $uid ])
        ->one();
        return $row ;
    }
    public static function translate_nickname ($uids_arr) {
       $rows = (new \yii\db\Query())
    ->select(['id', 'nickname'])
    ->from('user')
    ->where([  'id' => $uids_arr ]) 
    ->all();
       return $rows;
    }
    
    public static function fromuid_4easemob ($uids_arr) {
        $rows = (new \yii\db\Query())
        ->select(['id' , 'easemob_u' , 'nickname'])
        ->from('user')
        ->where([  'id' => $uids_arr ])
        ->all();
        return $rows ;
    }

    
    
    public static function fromuid_4easemob_assist ( $target_id,$target_arr) {
             $uidkey = array_search($target_id, array_column($target_arr, 'id'));
//        var_dump(  $target_arr[$uidkey] ) ;
        return $target_arr[$uidkey]  ;
    }
}

?>