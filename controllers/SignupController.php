<?php

namespace app\controllers;

use app\models\forms\SignupForm;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

/**
 * Class SingupController
 *
 * @package     Signup
 * @author      Patricio Rojas Ortiz <patricio-rojaso@outlook.com>
 * @copyright   (C) Copyright - Web Application development
 * @license     Private license
 * @link        https://appwebd.github.io
 * @date        11/1/18 10:33 PM
 * @version     1.0
 */
class SignupController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => [ACTION_INDEX],
                'rules' => [
                    [
                        ACTIONS => [ACTION_INDEX],
                        ALLOW => true,
                        ROLES => ['?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                ACTIONS => [
                    ACTION_INDEX => ['GET', 'POST'],
                ],
            ],
        ];
    }

    /**
     * @return string|Response the singup form, the singup message or
     * a redirect response
     */
    public function actionIndex()
    {

        $model = new SignupForm;
        if ($model->load(Yii::$app->request->post()) && $model->singup() !== null) {
            return $this->render('signed-up');
        }

        BaseController::bitacora(Yii::t('app', 'showing the view'), MSG_INFO);
        return $this->render(ACTION_INDEX, ['model' => $model]);
    }
}
