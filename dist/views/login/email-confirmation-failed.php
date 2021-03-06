<?php
/**
 * email confirmation failed view
 *
 * @package     login
 * @author      Patricio Rojas Ortiz <patricio-rojaso@outlook.com>
 * @copyright   (C) Copyright - Web Application development
 * @license     Private license
 * @link        https://appwebd.github.io
 * @date        2018-11-02 07:30:41
 * @version     1.0
 */

use app\components\UiComponent;

$this->title = Yii::t('app', 'Email confirmation');
$this->params[BREADCRUMBS][] = $this->title;

echo '
<div class="container">
    <div class="row">
        <div class="col-sm-3 "> &nbsp; </div>
        <div class="col-sm-6 box">

            <div class="webpage">';

$uiComponent = new UiComponent();
$uiComponent->header(
    'envelope',
    $this->title,
    ''
);

echo '<div class="success text-center"><h4>',
Yii::t('app', 'Email confirmation failed'),
'<br/><br/><br/></h4></div>';

echo Yii::$app->view->render('@app/views/partials/_links_return_to');

echo '
        </div>
        <div class="col-sm-3 "> &nbsp; </div>
    </div>
</div>';
