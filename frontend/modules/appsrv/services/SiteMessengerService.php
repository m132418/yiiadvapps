<?php
namespace frontend\modules\appsrv\services;
use common\models\SiteMessenger ;
class SiteMessengerService
{
    public function send_msg($frm_id,$to_id,$mtype = 0,$content) {
           $smsg =new SiteMessenger() ;
            $smsg->frm_id =$frm_id;
            $smsg->to_id = $to_id ;
            $smsg->mtype = $mtype ;
            $smsg->content = $content ;
            $smsg->save(false) ;
    }
    
    public function mark_as_read ($mid) {
        $m = SiteMessenger::findOne(["id" => $mid]);
        $m->is_read = 1 ;
        $m->save(false) ;
    }
}

?>