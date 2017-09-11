<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">

    <?php
    NavBar::begin([
        'brandLabel' => 'My Company',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
//            'class' => 'navbar navbar-default',
        ],
    ]);
    $menuItems = [
        ['label' => 'Home', 'url' => ['/site/index']],
    ];
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => 'Login', 'url' => ['/user/login']];
    } else {

        if (common\components\MenuHelper::isRolenameExists('appadmin')){
            $menuItems[] = [
                'label' => '权限管理', 'url' => ['#'],
                'items'=> [
                    [ 'label' => '权限管理', 'url' => ['/rbac/permission/index']],
                    [ 'label' => '角色管理', 'url' => ['/rbac/role/index']],
                    [ 'label' => '赋权管理', 'url' => ['/rbac/assignment/index']],
                    [ 'label' => '规则管理', 'url' => ['/rbac/rule/index']],
                ]
            ];
            $menuItems[] = [
                'label' => '商品类别', 'url' => ['#'],
                'items'=> [
                    [ 'label' => '列表', 'url' => ['/shop/category/index']],
                    [ 'label' => '增加', 'url' => ['/shop/category/create']],

                ]
            ];
            $menuItems[] = [
                'label' => '商品', 'url' => ['#'],
                'items'=> [
                    [ 'label' => '列表', 'url' => ['/shop/product/index']],
                    [ 'label' => '增加', 'url' => ['/shop/product/create']],

                ]
            ];
            $menuItems[] = [
                'label' => '内容管理', 'url' => ['#'],
                'items'=> [
                    [ 'label' => '类别', 'url' => ['/cms/cms-catalog/index']],
                    [ 'label' => '内容', 'url' => ['/cms/cms-show/index']],
                ]
            ];


        }


        $menuItems[] = '<li>'
            . Html::beginForm(['/user/logout'], 'post')
            . Html::submitButton(
                'Logout (' . Yii::$app->user->identity->username . ')',
                ['class' => 'btn btn-link']
            )
            . Html::endForm()
            . '</li>';
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>


    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
