<?php
namespace common\components;

class MyHelpers
{

    public static function re_sort_arr_key($array)
    {
        if (empty($array)) {
            return $array;
        }
        while ($value = current($array)) {
            $arr[] = $value;
            next($array);
        }
        return ($arr);
    }

public static function p($data){
        // 定义样式
        $str='<pre style="display: block;padding: 9.5px;margin: 44px 0 0 0;font-size: 13px;line-height: 1.42857;color: #333;word-break: break-all;word-wrap: break-word;background-color: #F5F5F5;border: 1px solid #CCC;border-radius: 4px;">';
        // 如果是boolean或者null直接显示文字；否则print
        if (is_bool($data)) {
            $show_data=$data ? 'true' : 'false';
        }elseif (is_null($data)) {
            $show_data='null';
        }else{
            $show_data=print_r($data,true);
        }
        $str.=$show_data;
        $str.='</pre>';
        echo $str;
    }
    
    
    public static function future_time_point($ttype,$tspan,$kickoff_at)
    {
        $date1 = date('Y-m-d',$kickoff_at) ;
        $target_date = null ;
        switch ($ttype) {
            case 1: // 86400); //60s*60min*24h 
            $target_date =   date('Y-m-d',strtotime("$date1 +$tspan day"));
            break;
            case 2:// 86400); //60s*60min*24h*7 
                $target_date =   date('Y-m-d',strtotime("$date1 +$tspan*7 day"));
                break;
                case 3:
                     $target_date =   date('Y-m-d',strtotime("$date1 +$tspan month"));
                    break;
            
            default:
                ;
            break;
        }
        return $target_date ;
    }
    
    
    public static function future_time_point_unixtime($ttype,$tspan,$kickoff_at)
    {
        $date1 = date('Y-m-d',$kickoff_at) ;
        $target_date = null ;
        switch ($ttype) {
            case 1: // 86400); //60s*60min*24h
                $target_date =  strtotime("$date1 +$tspan day");
                break;
            case 2:// 86400); //60s*60min*24h*7
                $target_date =  strtotime("$date1 +$tspan*7 day");
                break;
            case 3:
                $target_date =   strtotime("$date1 +$tspan month");
                break;
    
            default:
                ;
                break;
        }
        return $target_date ;
    }
    
    public static function deposit_time_progress($ttype,$tspan,$kickoff_at,$now_time)
    {
        
        $target_date = self::future_time_point_unixtime($ttype, $tspan, $kickoff_at) ;
        
        if (($now_time >= $kickoff_at) && ($target_date >= $kickoff_at)) 
        {
            $progress   = ($now_time - $kickoff_at ) / ($target_date - $kickoff_at) ;            
            return $progress ;
        }
        else 
            return -1 ;
        
      
    }
    
    
    //天数之间相减
    public static function timeDays($startTime,$endTime)
    {
        $startTimeDay = strtotime(date('Y-m-d',$startTime));
        $endTimeDay = strtotime(date('Y-m-d',$endTime));
        $days=ceil(($endTimeDay-$startTimeDay)/86400); //60s*60min*24h
        if($days < 0) $days = 0;
        return $days;
    }

    public static function gen_random_cd($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i ++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function isMobile() {
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])){
            return true;
        }
        //如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset ($_SERVER['HTTP_VIA'])) {
            //找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }

        //判断手机发送的客户端标志,兼容性有待提高
        if (isset ($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = array (
                'nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap',
                'mobile'
            );
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return true;
            }
        }
        //协议法，因为有可能不准确，放到最后判断
        if (isset ($_SERVER['HTTP_ACCEPT'])) {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                return true;
            }
        }
        return false;
    }

}