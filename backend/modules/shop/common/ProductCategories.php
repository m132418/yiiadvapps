<?php
namespace backend\modules\shop\common;
use yii\db\Query ;
use yii\helpers\ArrayHelper;
use common\components\MyHelpers;
class ProductCategories
{
    public static function build_drop_down(){
        $rows = static::query_categories();
        $rows =    \common\components\Category::unlimitedForLevel1($rows) ;
        $rows =  static::build_drop_down_tree($rows);
//        MyHelpers::p($rows);
//
        $rows = array_merge(  [array('key'=>0,'val'=>'根节点')] ,$rows) ;
//        MyHelpers::p($rows);
//
//        die() ;
        $rows = ArrayHelper::map($rows,'key','val') ;
        return $rows ;
    }

    private static function query_categories()
    {
        $rows = (new Query())
            ->select(['id', 'pid', 'title as name'])
            ->from('shop_category')
            ->all();
        return $rows;
    }

    private static function build_drop_down_tree($rows){
        $rtn_array =array();
        foreach ($rows as $key => $value )
        {
            if ($value['level']>1)
                array_push($rtn_array,[   'key'=>$value['id']  , 'val'=>$value['html'] . $value['name']]);
            else
                array_push($rtn_array,[ 'key'=>$value['id'],'val' =>$value['name']]);

        }
        return $rtn_array;

    }

}