<?php
namespace frontend\modules\appsrv\services;

class WishService
{
    public static function sum_deposit ($wid) {
  
        
        $db =  \Yii::$app->db ;
        $sql =
      "SELECT                        ".
"SUM(deposit.deposit) sum_val  ".
"FROM deposit                  ".
"GROUP BY wishid               ".
"HAVING wishid =  ".$wid
        ;
        //         var_dump($sql);
        $model = $db->createCommand($sql);
        $data =  $model->queryScalar();
        
        if ($data) {
            return $data ;
        }else 
        {
            return 0 ;
        }
        
       
        
   
      
    }
   
   

}

?>