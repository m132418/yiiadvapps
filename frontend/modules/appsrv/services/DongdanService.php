<?php
namespace frontend\modules\appsrv\services;

class DongdanService
{
    public static function q_dongdan_with_id($did) {
         $db =  \Yii::$app->db ;
        $sql =
        " SELECT              ".
            " dongdan.id,         ".
            " dongdan.wishid,     ".
            " dongdan.uid,        ".
            " dongdan.pics,       ".
            " dongdan.title,      ".
            " dongdan.content,    ".
            " dongdan.gps_x,      ".
            " dongdan.gps_y,      ".
            " dongdan.d_name,     ".
            " dongdan.created_at, ".
            " dongdan.updated_at,  ".            
            " wish.wish_name  ".
//             " wish.title  ".
            
            " FROM                ".
            " dongdan             ".
            " LEFT JOIN wish ON dongdan.wishid = wish.id ".
            " where dongdan.id = " . $did
                
        ;
//         var_dump($sql) ;exit() ;
        $model = $db->createCommand($sql);
        $row = $model->queryOne() ;
      
      return      $row ;
    }
    
    public static function q_dongdan_with_uid ($uid) {
      
        $db =  \Yii::$app->db ;
        $sql =
        " SELECT              ".
            " dongdan.id,         ".
            " dongdan.wishid,     ".
            " dongdan.uid,        ".
            " dongdan.pics,       ".
            " dongdan.title,      ".
            " dongdan.content,    ".
            " dongdan.gps_x,      ".
            " dongdan.gps_y,      ".
            " dongdan.d_name,     ".
            " dongdan.created_at, ".
            " dongdan.updated_at,  ".            
            " wish.wish_name  ".
//             " wish.title  ".
            
            " FROM                ".
            " dongdan             ".
            " INNER JOIN wish ON dongdan.wishid = wish.id ".
            " where uid = " . $uid
                
        ;
//         var_dump($sql) ;exit() ;
        $model = $db->createCommand($sql);
        $all = $model->queryAll() ;
      
      return      $all ;
      
    }
    
    public static function bangcun_count_with_did ($did) {
    
        $db =  \Yii::$app->db ;
        $sql =
        "SELECT wish.id AS wish_id,                      ".
        "deposit.id AS deposit_id,                       ".
        "deposit.uid FROM dongdan                        ".
        "INNER JOIN wish ON dongdan.wishid = wish.id     ".
        "INNER JOIN deposit ON deposit.wishid = wish.id  ".
        "where deposit.uid = 0 and   dongdan.id =" . $did
        ;
        $model = $db->createCommand($sql);
        $one = $model->queryOne() ;
//         var_dump($one) ; exit() ;
        if (!$one) {
             return      0 ;
        }
        
        $sql2 =
        "SELECT COUNT(1) result  ".
            " FROM charge_ord         ".
            " where deposit_id = ". $one["deposit_id"] .
            " and wish_id =". $one["wish_id"]
        ;
        $final_result = $db->createCommand($sql2)->queryScalar();
    
        return      $final_result ;
    
    }
   
  

}

?>