<?php
$path = \Yii::getAlias("@vendor/hongshuo/yii2-v-player/VideoJsWidget.php");
require_once($path);
$path2 = \Yii::getAlias("@vendor/hongshuo/yii2-v-player/VideoJsAsset.php");
require_once($path2);
use hongshuo\vpalyer\VideoJsWidget ;
use hongshuo\vpalyer\VideoJsAsset ;
echo VideoJsWidget::widget([
    'options' => [
        'class' => 'video-js vjs-default-skin vjs-big-play-centered',
//        'poster' => "http://www.videojs.com/img/poster.jpg",
        'controls' => true,
//        'preload' => 'auto',
        'width' => '800',//970
//        'height' => '400',
    ],
    'tags' => [
        'source' => [
            ['src' => 'http://vjs.zencdn.net/v/oceans.mp4', 'type' => 'video/mp4'],
//            ['src' => 'http://vjs.zencdn.net/v/oceans.webm', 'type' => 'video/webm']
        ],
        'track' => [
            ['kind' => 'captions', 'src' => 'http://vjs.zencdn.net/vtt/captions.vtt', 'srclang' => 'zh-CN', 'label' => '语言']
        ]
    ]
]);
?>


<?php
echo VideoJsWidget::widget([
    'options' => [
        'class' => 'video-js vjs-default-skin vjs-big-play-centered',
        'controls' => true,
        'preload' => 'auto',
        'width' => '800',
//        'height' => '315',
        'data' => [
            'setup' => [
                'autoplay' => true,
                'techOrder' =>['flash', 'html5']
            ],
        ],
    ],
    'tags' => [
        'source' => [
            ['src' => 'rtmp://cp67126.edgefcs.net/ondemand/&mp4:mediapm/ovp/content/test/video/spacealonehd_sounas_640_300.mp4', 'type' => 'rtmp/mp4']
        ]
    ]
]);
?>
