<?php
/**
 * Singup view
 *
 * @package     Singup View
 * @author      Patricio Rojas Ortiz <patricio-rojaso@outlook.com>
 * @copyright   (C) Copyright - Web Application development
 * @license     Private commercial license
 * @link        https://appwebd.github.io
 * @date        2018-06-16 23:03:06
 * @version     1.0
 */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use app\models\forms\SignupForm;

/* @var yii\web\View $this */
/* @var yii\bootstrap\ActiveForm $form */
/* @var \app\models\User $model */

$this->title = Yii::t('app', 'Signup view');
$this->params[BREADCRUMBS][] = $this->title;

echo '
<div class="container">
    <div class="row">
        <div class="col-sm-3 "> &nbsp; </div>        
        <div class="col-sm-6 box">

            <div class="webpage ">';

                echo Yii::$app->ui->header(
                    'user',
                    $this->title,
                    Yii::t(
                        'app',
                        'Please complete all requested information.'
                    ),
                    true,
                    false
                );

                $form = ActiveForm::begin(
                    ['id' => 'form-signup',
                        'method' => 'post',
                        'options' => [STR_CLASS => 'form-horizontal webpage'],
                    ]
                );

                echo $form->field($model, SignupForm::FIRST_NAME, [
                    INPUT_OPTIONS => [AUTOFOCUS => AUTOFOCUS,
                        TABINDEX => '1',
                        'title' => 'First name is required information!',
                        'x-moz-errormessage' => 'First name is required information!',
                        REQUIRED => REQUIRED,
                        PLACEHOLDER => 'First name'],
                        INPUT_TEMPLATE => '<div class="input-group">
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-credit-card"></span>
                                            </span>
                                            {input}
                                        </div>',
                ])->textInput()->label(false);

                echo $form->field($model, SignupForm::LAST_NAME, [
                    INPUT_OPTIONS => [AUTOFOCUS => AUTOFOCUS,
                        TABINDEX => '1',
                        'title' => 'Last name is required!',
                        'x-moz-errormessage' => 'Last name is required!',
                        REQUIRED => REQUIRED,
                        PLACEHOLDER => 'Last name'],
                    INPUT_TEMPLATE => '<div class="input-group">
                                                            <span class="input-group-addon">
                                                                <span class="glyphicon glyphicon-credit-card"></span>
                                                            </span>
                                                            {input}
                                                        </div>',
                ])->textInput()->label(false);


                echo $form->field($model, 'username', [
                    INPUT_OPTIONS => [AUTOFOCUS => AUTOFOCUS, TABINDEX => '2'],
                    INPUT_TEMPLATE => '<div class="input-group">
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-user"></span>
                                            </span>
                                            {input}
                                        </div>',
                ])->textInput([PLACEHOLDER => Yii::t('app', ' User account')])->label(false);


                echo  $form->field($model, 'password', [
                    INPUT_OPTIONS => [AUTOFOCUS => AUTOFOCUS, TABINDEX => '3'],
                    INPUT_TEMPLATE => '<div class="input-group">
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-lock"></span>
                                            </span>
                                            {input}
                                        </div>',
                ])->passwordInput(['autocomplete' => 'new-password',
                    PLACEHOLDER => Yii::t('app', ' Password')])->label(false);


                echo $form->field($model, 'email', [
                    INPUT_OPTIONS => [AUTOFOCUS => AUTOFOCUS, TABINDEX => '4'],
                    INPUT_TEMPLATE => '<div class="input-group">
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-envelope"></span>
                                            </span>
                                            {input}
                                        </div>',
                ])->textInput([
                    PLACEHOLDER => Yii::t('app', ' valid email account, Ex: account@domain.com'),
                ])->label(false);

                echo '<div class="form-group">',
                        Html::submitButton(Yii::t('app', 'Signup'), [STR_CLASS => 'btn btn-primary',
                            'name' => 'signup-button', AUTOFOCUS => AUTOFOCUS, TABINDEX => '5',
                        ]),
                        $form->errorSummary($model, array(STR_CLASS => "alert alert-danger")),
                    '</div>';
                ActiveForm::end();

                echo Yii::$app->view->render('@app/views/partials/_links_return_to');
                echo '
            </div>
        </div>
        <div class="col-sm-3 "> &nbsp; </div>
    </div>
</div>';
