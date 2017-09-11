<?php
namespace frontend\modules\appsrv\controllers;
use common\models\ChargeOrd;
use common\models\Deposit;
use frontend\modules\appsrv\services\UserProfileService;
use frontend\modules\appsrv\services\UserService;
include_once \Yii::getAlias('@vendor') . '/ali-mobipay/alipay.config.php';
include_once \Yii::getAlias('@vendor') . '/ali-mobipay/lib/alipay_notify.class.php';
use AlipayNotify;
use common\models\InoutDetail;
use common\models\EasemobExt;
use common\models\Status;
use common\components\Easemob;
class ChargeordController extends AppSrvBase1Controller
{
    


    public function actionNotify()
    { 
       
        
        $alipay_config['partner'] = '2088221365172025';
        
        // 商户的私钥（后缀是.pen）文件相对路径
        $alipay_config['private_key_path'] = \Yii::getAlias('@vendor') . '/ali-mobipay/key/rsa_private_key.pem';
        
        // 支付宝公钥（后缀是.pen）文件相对路径
        $alipay_config['ali_public_key_path'] = \Yii::getAlias('@vendor') .'/ali-mobipay/key/alipay_public_key.pem';
        
        // ↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
        
        // 签名方式 不需修改
        $alipay_config['sign_type'] = strtoupper('RSA');
        
        // 字符编码格式 目前支持 gbk 或 utf-8
        $alipay_config['input_charset'] = strtolower('utf-8');
        
        // ca证书路径地址，用于curl中ssl校验
        // 请保证cacert.pem文件在当前文件夹目录中
        $alipay_config['cacert'] = getcwd() . '\\cacert.pem';
        
        // 访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
        $alipay_config['transport'] = 'http';
        
        $alipayNotify = new AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();

        if($verify_result) {//验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代
        
        
            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
        
            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
        
            //商户订单号
        
            $out_trade_no = $_POST['out_trade_no'];
        
            //支付宝交易号
        
            $trade_no = $_POST['trade_no'];
        
            //交易状态
            $trade_status = $_POST['trade_status'];
            $total_fee = $_POST['total_fee'];
        
            if($_POST['trade_status'] == 'TRADE_FINISHED') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序
        
                //注意：
                //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
                //请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的
        
                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            }
            else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                        // 判断该笔订单是否在商户网站中已经做过处理
                        // 如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                        // 如果有做过处理，不执行商户的业务程序
                        
                    // 注意：
                        // 付款完成后，支付宝系统发送该交易状态通知
                        // 请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的
                        
                    // 调试用，写文本函数记录程序运行情况是否正常
                        // logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
                        
                    // \Yii::trace('start calculating average revenue' . $out_trade_no);
                        // \Yii::trace('start calculating average revenue' . $trade_no );
                        // \Yii::trace('start calculating average revenue' . $trade_status );
                        // \Yii::trace('********************' . $total_fee );
                    $ord = ChargeOrd::findOne([
                        "sn" => $out_trade_no
                    ]);
                    $ord->ispayed = 1;
                    $ord->chennel_pay_id = $trade_no;
                    $ord->totfee = $total_fee;
                    $ord->save(false);
                    $detail = new InoutDetail() ;
                    if ($ord->wish_id > 0) {
                        $deposit = Deposit::findOne([
                            "id" => $ord->deposit_id
                        ]);
                        $deposit->deposit = $deposit->deposit + $total_fee;
                        $deposit->save(false);
                        
                        if ($ord->uid == 0) 
                        { // 帮存
                            $detail->d_type = InoutDetail::TYPE_HELP_CUN ;
                            $detail->amount = $total_fee ;
                            $detail->uid = $ord->uid ;
                            $detail->save(false) ;
                            
                            $easemobext = EasemobExt::find(["easemob_ext_id"=>$ord->easemob_ext_id])->one() ;
                           
                            if ($easemobext) {
                                $easemob_arr= UserService::fromuid_4easemob($easemobext->to_id) ;
                               $nickname_rtn= UserService::nickname_only1($easemobext->frm_id);
                                              $easemob_options = \Yii::$app->params["easemob"] ;
                                              $e = new Easemob($easemob_options);
                                              $easemob_result=  $e->sendText( 'interact-message',
                                                  "users",
                                                  [$easemob_arr [0]["easemob_u"]],
                                                  $nickname_rtn ["nickname"] . "帮你存了一笔",
                                                  [
                                                  //                 "fromNickname"=> $u_profile ["nickname"] ,"fromPortrait"=> $u_profile["portrait"] ,"dd-title"=>$dd["title"],"sent-date"=>date("Y-m-d")
                                                      "key"=>"interact-message"
                                                  ]
                                                  ) ;
                                
                            $easemobext->status = Status::STATUS_ACTIVE ;
                            $easemobext->save(false) ;
                            }
                            
                        }
                        else 
                        {
                            $detail->d_type = InoutDetail::TYPE_XWISH ;
                            $detail->amount = $total_fee ;
                            $detail->uid = $ord->uid ;
                            $detail->save(false) ;
                        }
                        

                    } elseif ($ord->wish_id == 0){ //充值
                        
                        $detail->d_type = InoutDetail::TYPE_CZ ;                        
                        $detail->amount = $total_fee ;
                        $detail->uid = $ord->uid ;
                        $detail->save(false) ;
                    }
                   
            
             
            }
        
            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
        
//             echo "success";		//请不要修改或删除
        
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        }
        else {
            //验证失败
//             echo "fail";
        
            //调试用，写文本函数记录程序运行情况是否正常
            //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        }
        

    }

    public function actionT1()
    {
//           \Yii::trace('****************'  );
    }
}
