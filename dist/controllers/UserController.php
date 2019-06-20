<?php

namespace app\controllers;

use app\components\DeleteRecord;
use app\models\queries\Bitacora;
use app\models\queries\Common;
use app\models\search\UserSearch;
use app\models\User;
use Exception;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class UserController
 *
 * @category  Controller
 * @package   User
 * @author    Patricio Rojas Ortiz <patricio-rojaso@outlook.com>
 * @copyright 2019 Patricio Rojas Ortiz
 * @license   Private license
 * @release   1.0
 * @link      https://appwebd.github.io
 * @date      11/1/18 10:12 PM
 * @php       version 7.2
 */
class UserController extends BaseController
{
    const USER_ID = 'user_id';

    /**
     * Before action instructions for to do before call actions
     *
     * @param object $action action name
     *
     * @return mixed \yii\web\Response
     * @throws BadRequestHttpException
     */
    public function beforeAction($action)
    {
        if ($this->checkBadAccess($action->id)) {
            return $this->redirect(['/']);
        }

        $bitacora = New Bitacora();
        $bitacora->register(Yii::t('app', 'showing the view'), 'beforeAction', MSG_INFO);
        return parent::beforeAction($action);
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function behaviors()
    {
        return $this->behaviorsCommon();
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();

        if ($model->load(Yii::$app->request->post())) {
            $request = Yii::$app->request->post('User');
            $model->email_is_verified = false;
            $model->email_confirmation_token = null;
            $model->setPassword($request['password']);
            $model->generateAuthKey();
            $model->ipv4_address_last_login = Yii::$app->getRequest()->getUserIP();

            $model->generateEmailConfirmationToken(true);
            $this->saveRecord($model);
        }

        return $this->render(ACTION_CREATE, [MODEL => $model]);
    }

    /**
     * @param object $model
     * @return bool|Response
     */
    private function saveRecord($model)
    {
        try {
            $status = Common::transaction($model, 'save');
            $this->saveReport($status);
            if ($status) {
                $primaryKey = BaseController::stringEncode($model->user_id);
                return $this->redirect([ACTION_VIEW, 'id' => $primaryKey]);
            }
        } catch (Exception $exception) {
            $bitacora = New Bitacora();
            $bitacora->registerAndFlash($exception, 'saveRecord', MSG_ERROR);
        }
        return false;
    }

    /**
     * Deletes an existing row of User model. If deletion is successful,
     * the browser will be redirected to the 'index' page.
     *
     * @param integer $id primary key iof table user
     *
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $deleteRecord = New DeleteRecord();
        if (!$deleteRecord->isOkPermission(ACTION_DELETE)) {
            return $this->redirect([ACTION_INDEX]);
        }

        $model = $this->findModel($id);
        if ($this->fkCheck($model->user_id) > 0) {
            $deleteRecord->report(2);
            return $this->redirect([ACTION_INDEX]);
        }

        try {
            $status = Common::transaction($model, ACTION_DELETE);
            $deleteRecord->report($status);
        } catch (Exception $exception) {
            $bitacora = New Bitacora();
            $bitacora->registerAndFlash($exception, 'actionDelete', MSG_ERROR);
        }
        return $this->redirect([ACTION_INDEX]);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param string $primaryKey primary key of table user (encrypted value)
     *
     * @return object User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    private function findModel($primaryKey)
    {

        $primaryKey = BaseController::stringDecode($primaryKey);
        $model = User::findOne($primaryKey);
        if ($model !== null) {
            return $model;
        }

        $event = Yii::t(
            'app',
            'The requested page does not exist {id}',
            ['id' => $primaryKey]
        );

        $bitacora = New Bitacora();
        $bitacora->registerAndFlash($event, 'findModel', MSG_SECURITY_ISSUE);

        throw new NotFoundHttpException(
            Yii::t(
                'app',
                'The requested page does not exist.'
            )
        );
    }

    /**
     * Check nro. records found in other tables related.
     *
     * @param integer $userId int Primary Key of table User
     *
     * @return integer numbers of rows in other tables (integrity referential)
     */
    private function fkCheck($userId)
    {
        return Common::getNroRowsForeignkey(
            'logs',
            self::USER_ID,
            $userId
        );
    }

    /**
     * Lists all User models.
     *
     * @return mixed
     */
    public function actionIndex()
    {

        $searchmodel_user = new UserSearch();
        $dataprovide_user = $searchmodel_user->search(Yii::$app->request->queryParams);

        $page_size = $this->pageSize();
        $dataprovide_user->pagination->pageSize = $page_size;

        return $this->render(
            ACTION_INDEX,
            [
                'searchModelUser' => $searchmodel_user,
                'dataProviderUser' => $dataprovide_user,
                'pageSize' => $page_size
            ]
        );
    }

    /**
     * Delete many records of this table User
     *
     * @return mixed
     */
    public function actionRemove()
    {
        $result = Yii::$app->request->post('selection');
        $deleteRecord = new DeleteRecord();

        if (!$deleteRecord->isOkPermission(ACTION_DELETE) || !$deleteRecord->isOkSeleccionItems($result)) {
            return $this->redirect([ACTION_INDEX]);
        }

        $nroSelections = sizeof($result);
        $status = [];
        // 0: OK was deleted,  1: KO Error deleting record,  2: Used in the system,  3: Not found record in the system

        for ($counter = 0; $counter < $nroSelections; $counter++) {
            try {
                $primaryKey = $result[$counter];
                $model = User::findOne($primaryKey);
                $fkCheck = $this->fkCheck($primaryKey);
                $item = $deleteRecord->remove($model, $fkCheck);
                $status[$item] = $status[$item] . $primaryKey . ',';
            } catch (Exception $exception) {
                $bitacora = New Bitacora();
                $bitacora->registerAndFlash($exception, 'actionRemove', MSG_ERROR);
            }
        }

        $deleteRecord->summaryDisplay($status);
        return $this->redirect([ACTION_INDEX]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id primary key of table user
     *
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $this->saveRecord($model);
        }

        return $this->render(ACTION_UPDATE, [MODEL => $model]);
    }

    /**
     * Displays a single User model.
     *
     * @param integer $id primary key of table user
     *
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $event = Yii::t('app', 'view record {id}', ['id' => $model->user_id]);
        $bitacora = New Bitacora();
        $bitacora->register($event, 'actionView', MSG_INFO);

        return $this->render(ACTION_VIEW, [MODEL => $model]);
    }
}