<?php

/* @var $this View */

/* @var $content string */

use api\assets\AppAsset;
use yii\helpers\Html;
use yii\web\View;

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
<body style="overflow-x: hidden;">
<?php $this->beginBody() ?>

<div class="wrap">
    <div class="first-block-header">
        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <div class="holst">
                    <div class="triangle-block-header-1"></div>
                    <div class="triangle-block-header-2"></div>
                    <div class="triangle-block-header-3"></div>
                    <div class="triangle-block-header-4"></div>
                    <div class="triangle-block-header-5"></div>
                    <div class="triangle-block-header-6"></div>
                </div>
            </div>
            <div class="col-md-4"></div>
        </div>
    </div>
    <div class="two-block-header">
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">
                <div class="row text-center box-block">
                    <div class="col-md-4 box-block-header">
                        <a href="/">
                            <div class="layout-block-header">
                                <i class="glyphicon glyphicon-asterisk" aria-hidden="true"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-3"></div>
        </div>
    </div>
    <div class="container" style="padding: 0;">
        <?= $content ?>
    </div>
    <div class="last-block-footer">
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6 col-sm-6">
                    </div>
                    <div class="col-md-6 col-sm-6">
                    </div>
                </div>
            </div>
            <div class="col-md-3"></div>
        </div>
    </div>
</div>

<footer class="footer block-footer">
    <div class="container">
        <p class="pull-left" style="color:#fff;">&copy; API <?= date('Y') ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
