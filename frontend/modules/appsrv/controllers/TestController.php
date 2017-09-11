<?php
namespace frontend\modules\appsrv\controllers;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use common\models\SiteMessenger;
use common\models\User;
use frontend\modules\appsrv\services\UserService ;
use frontend\modules\appsrv\services\UserProfileService ;
use frontend\modules\appsrv\services\DongdanService ;
use frontend\modules\appsrv\services\CommentsService ;
use common\components\MyHelpers ;
use common\components\Easemob ;
/**
 * Default controller for the `appsrv` module
 */
class TestController extends AppSrvBase1Controller
{
    public function actionChatlog() {
           
        $easemob_options = \Yii::$app->params["easemob"] ;
        $e = new Easemob($easemob_options);
        
        
        
    }

    public function actionIndex()
    {
//        $data = $items = ['some', 'array', 'of', 'values' => ['associative', 'array']];
//         header('Content-Type: application/json; charset=utf-8');
//         echo( Json::encode($data));
     $db =  \Yii::$app->db ;      
      $sql = "SELECT".
            " relationship.id,".
            " relationship_user.uid,".
            " rtype".
            " FROM".
            " relationship_user".
            " INNER JOIN relationship ON".
            " relationship_user.rid = relationship.id".
            " where rtype = 3 AND uid = 2"
                  ;
            $model = $db->createCommand($sql);           
            $ifexist = $model->queryScalar();
            VarDumper::dump($ifexist) ;
    }
    public function actionT1()
    {
                  $coments = (new \yii\db\Query())->select([
                'dd_coment.id',
                'dd_coment.dongdanid',
                'dd_coment.content',
                'dd_coment.uid',
                      'user.nickname',
                      'user_profile.portrait',
                'dd_coment.created_at',
                'dd_coment.updated_at'
            ])
                ->from('dd_coment')
                ->leftJoin("user" , "dd_coment.uid = user.id")
                ->leftJoin("user_profile","user_profile.uid = dd_coment.uid")
                ->where([
                'dongdanid' => 1
            ])
                ->all();
                var_dump($coments) ;
    }
    public function actionT2(){
        $db =  \Yii::$app->db ;
        $sql = "SELECT".
                " lilimiapp.relationship_user.uid".
                " FROM".
                " lilimiapp.wish".
                " INNER JOIN lilimiapp.relationship ON lilimiapp.wish.rid = lilimiapp.relationship.id".
                " INNER JOIN lilimiapp.relationship_user ON lilimiapp.relationship_user.rid = lilimiapp.relationship.id".
                " WHERE lilimiapp.wish.id = 2" ;
        $model = $db->createCommand($sql);
      $data =  $model->queryColumn("uid");
      var_dump($data) ;
      
      $my_array =$data;
      $to_remove = [1] ;
      $result = array_diff($my_array, $to_remove);
//       var_dump($result) ;

//     $messeges =  SiteMessenger::find()->where('in' , 'to_id' ,$result)->andWhere(['frm_id'=>1])->andWhere(['mtype'=>12]);
    
    $indcondition  = "(" .  implode(",",$result). ")" ;
    
      
    $connection = \Yii::$app->db;
$connection->createCommand()->update('site_messenger', ['status' => 0], 'frm_id =' . 1 . " and " . 'mtype =' . 12 . " and " . $indcondition )->execute();
      
    }
    public function actionT7(){
        $db =  \Yii::$app->db ;
        $sql = "SELECT name,id,parent_id,grade FROM region where grade ="  ;
//         var_dump($sql);
      $prov =   $db->createCommand($sql. 2)->queryAll();
//       var_dump($prov) ;
     
      $city =   $db->createCommand($sql. 3 . " and  parent_id=" . 320000 )->queryAll();
      var_dump($city) ;
      
    }
  
    private function all_r_ids($wishid)
    {
        $db =  \Yii::$app->db ;
        $sql = "SELECT".
            " lilimiapp.relationship_user.uid".
            " FROM".
            " lilimiapp.wish".
            " INNER JOIN lilimiapp.relationship ON lilimiapp.wish.rid = lilimiapp.relationship.id".
            " INNER JOIN lilimiapp.relationship_user ON lilimiapp.relationship_user.rid = lilimiapp.relationship.id".
            " WHERE lilimiapp.wish.id = " . $wishid ;
        var_dump($sql);
        $model = $db->createCommand($sql);
        $data =  $model->queryColumn("uid");
         
    
        $my_array =$data;
        return $my_array;
    }
    public function actionT3(){
//         \Yii::trace('start calculating average revenue');
//        echo  date('YmdHis') ;
        $easemob_options = \Yii::$app->params["easemob"] ;
        VarDumper::dump($easemob_options);
    }
    public function actionT4(){
   $u =   User::findIdentityByAccessToken("43cf72e2cdde8519b9d07b8d67cf053e") ;
   var_dump($u) ;
    }
    public  function actionT5() {
      $redis = \Yii::$app->redis;
//       $result = $redis->hmset('test_collection', 'key1', 'val1', 'key2', 'val2');
//     $var =  $redis->hmget('test_collection') ;
//       $redis->set('k1',"hello"); $var =$redis->get("k1");
//     var_dump($var) ;
    $redis->hmset("wish:1:2" ,"3" , "Y" ,"4", "Y" ,"5" ,"N");
   $obj = $redis->hgetall("wish:1:2") ;
    var_dump($obj) ;
    
 $arr =   array_diff($obj, ["Y" , "N"]);
 var_dump($arr) ;
    
    }
    public function actionT6()
    {
//  $data = parent::getJ() ;
//  var_dump($data) ;die();
// echo "送达方式的" ;
       $obj = ["cid"=>"432" ,"catid"=>"20" ,"username"=>"admin","title"=>"这是个题目1"];
       $obj2 = ["cid"=>"430" ,"catid"=>"20" ,"username"=>"admin2","title"=>"这是个题目12"];
//        $arr = array_merge($arr,$obj ) ;
//        var_dump($arr) ;
        
 parent::rtn_json([[$obj,$obj2]]) ;
 
    }
    public function actionT8() {
        $coments_count = (new \yii\db\Query())
        ->select([
            'COUNT(1) coments_count'
        ])
        ->from('dd_coment')
        ->where([  'dongdanid' => 2 ])
        ->count();
        
        var_dump($coments_count) ;
        
        $zan_count = (new \yii\db\Query())
        ->select([
            'COUNT(1) zan_coount'
        ])
        ->from('dd_zan')
        ->where([  'did' => 2 ])
        ->count();
        var_dump($zan_count) ;
    }
    
    public function actionT9() {
//      $row =   UserProfileService::q_profile_from_uid(1) ;
//      var_dump($row) ;
    $t = time() ;
    var_dump($t) ;
    
$date1 = date('Y-m-d',$t) ;
var_dump($date1) ;
// echo date('Y-m-d',strtotime("$date1 +5 day"));
//     var_dump(date('Y-m-d',$t)) ;
  echo  MyHelpers::future_time_point(5, 1, $t) ;
    }
    /**
     * @param r
     */
     private function tr_nickname($r)
    {
        //         var_dump($r) ;
                $key = array_search(4, array_column($r, 'id'));
                return ( is_null ($r[$key]["nickname"] ) ? "未命名" :  $r[$key]["nickname"]  ) ;
    }
    public function actionT10()
    {
        $pub_token = "shduifsuydgygsyad1231"  ;
//         print_r("stop00") ;exit() ;
         $data = parent::getJ() ;
//          print_r("stop1") ;exit() ;
//          var_dump($data) ;exit() ;
         
        $uid =   base64_decode($data["ud"]) ;  
        $pwd =   base64_decode($data["ucp"]) ;
        $rec_verification =   ($data["vcd"]) ;
        
        
       $compute_verification =  sha1($uid . $pwd . $pub_token)     ;
       $rtn_str = null ;
       if (strcmp($rec_verification, $compute_verification)== 0) {
          $rtn_str = "welcome" ;
       }else
           $rtn_str = "sorry" ;
       
//         print_r("stop") ;exit() ;
            parent::rtn_json(['cd'=>"1","result"=>$rtn_str]) ;
    }

}
