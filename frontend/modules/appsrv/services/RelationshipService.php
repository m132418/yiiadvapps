<?php
namespace frontend\modules\appsrv\services;

class RelationshipService
{
    public  function is_some_type_relationship_exists($rtype , $uid) {
          $db =  \Yii::$app->db ;      
      $sql = "SELECT".
            " relationship.id,".
            " relationship_user.uid,".
            " rtype".
            " FROM".
            " relationship_user".
            " INNER JOIN relationship ON".
            " relationship_user.rid = relationship.id".
            " where rtype = ".$rtype ." AND uid =" .$uid
                  ;
            $model = $db->createCommand($sql);           
            $ifexist = $model->queryScalar();
            return $ifexist ;
    }
    public  function is_already_friend($myid , $target_id) {
        $db =  \Yii::$app->db ;
        $sql = "SELECT".
                " friend_list.myid,".
                " friend_list.outterid,".
                " friend_list.created_at,".
                " friend_list.updated_at,".
                " friend_list.`status`".
                " FROM friend_list".
                " where myid = ".$myid .
                " and outterid = " . $target_id
            ;
            $model = $db->createCommand($sql);
            $ifexist = $model->queryOne();
            return $ifexist ;
    }
}

?>