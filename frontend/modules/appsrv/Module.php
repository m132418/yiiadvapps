<?php

namespace frontend\modules\appsrv;

/**
 * appsrv module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'frontend\modules\appsrv\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    // initialize the module with the configuration loaded from config.php
    \Yii::configure($this, require(__DIR__ . '/config.php'));
    \Yii::$app->user->enableSession = false;
    \Yii::$app->response->format =  \yii\web\Response::FORMAT_JSON;
   
        // custom initialization code goes here
    }
}
