<?php
namespace common\components;

use common\models\AuthMenu;
use yii\helpers\VarDumper;
use yii\helpers\ArrayHelper;
/**
 *
 * @author Administrator
 *    echo Nav::widget([
 * 'options' => ['class' => 'navbar-nav navbar-right'],
 * 'items' => common\components\MenuHelper::getMenu(),
 */
class MenuHelper
{
    public static function isRolenameExists($inTest){
        $roles = \Yii::$app->authManager->getRolesByUser(\Yii::$app->user->id);
      return  ArrayHelper::keyExists($inTest,$roles) ;
    }

    public static function getMenu()
    {
        $roles = \Yii::$app->authManager->getRolesByUser(\Yii::$app->user->id);
        $res = array_keys($roles);
        $result = static::getMenuRecrusive(0, $res);
//        VarDumper::dump($result);
//        exit();
        return $result;
    }

    private static function getMenuRecrusive($parent, $roles = [])
    {

        $items = AuthMenu::find()
            ->where([ 'pid'=>$parent, 'authitem' => $roles])
            ->orderBy('sortval')
            ->asArray()
            ->all();

        $tot = []; $result = [];

        foreach ($items as $item) {

            $result = array_merge($result, ['label' => $item['label']]);
            $result = array_merge($result, ['url' => $item['url']]);
            $result = array_merge($result, ['items' => static::getMenuRecrusive($item['id'], $roles)]);
//         $result =    implode(",", $result) ;
            array_push($tot,$result) ;
        }
        return $tot;
    }

}