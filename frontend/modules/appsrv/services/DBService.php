<?php
namespace frontend\modules\appsrv\services;

class DBService
{

    public static function q_with_native_sql($sql_in, $intype,$clumns=[])
    {
        $db = \Yii::$app->db;
        $model = $db->createCommand($sql_in);
        $data = null ;
        switch ($intype) {
            case 1:
                {
                    $data= $model->queryAll();
                }
                break;
            
            case 2:
                {
                     $data= $model->queryColumn($clumns);
                }
                break;
            case 3:
                {
                     $data= $model->queryOne();
                }
                break;
            case 4:
                {
                     $data= $model->queryScalar();
                }
                break;
           case 5:
                {
                     $data= $model->execute();
                }
                 break;
            
            default:
                ;
                break;
        }
//         var_dump($data) ;exit() ;
        return  $data ;
    }
}

?>