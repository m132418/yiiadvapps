<?php
namespace frontend\modules\appsrv\controllers;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use common\models\SiteMessenger;
use common\models\User;
use frontend\modules\appsrv\services\DBService;
/**
 * Default controller for the `appsrv` module
 */
class IndexController extends AppSrvBase2Controller
{



    public function actionStart()
    {
        $u = parent::getU();         $db =  \Yii::$app->db ;
               $db =  \Yii::$app->db ;
        $sql ="SELECT".
                " wish.title,".
                " wish.content,".
                " wish.ytype,".
                " wish.pics,".
                " wish.classify,".
                " wish.deposit_type,".
                " wish.achieve_span,".
                " wish.target_amt,".
                " wish.kickoff_at,".
                " wish.id as wishid,".
                " SUM(deposit.deposit) as accomplished".
                " FROM   wish   INNER JOIN deposit".
                " ON deposit.wishid = wish.id".
                " where wishid in".
                " (SELECT deposit.wishid".
                " FROM deposit".
                " where uid = ".$u->getId() . " )".
                " GROUP BY wishid"
                ;
        
        $sql2 ="SELECT pic FROM carousel where status = 1" ;
//         var_dump($sql);
        $model = $db->createCommand($sql);
        $data =  $model->queryAll();
         
       $c = $db->createCommand($sql2)->queryAll() ;
//         header("Content-type: application/json;charset=utf-8");
//           echo (Json::encode([
//                      'cd' => 1,"data"=>$data
//                  ]));
//            
        
            parent::rtn_json ([
                'cd' => 1,'data' =>$data , "carousel"=>$c ,"baseurl" =>\Yii::$app->params["qiniu"]['carousel']
            ]);
     } 
     
     public function actionCms()
     {
         $data = parent::getJ() ; 
//          var_dump($data["cmsid"]) ;
         $sql_in =
         "SELECT content FROM ".
         "cms_content WHERE id =" .$data["cmsid"]
             ;
       $content =  DBService::q_with_native_sql($sql_in, 3) ;
       parent::rtn_json ([
           'cd' => 1,'data' =>$content 
       ]);

     }
     
    public function actionT1()
    {
      
        
//         VarDumper::dump($data) ;
    }
  
}
