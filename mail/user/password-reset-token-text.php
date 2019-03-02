<?php

use app\models\forms\PasswordResetRequestForm;

/* @var $this yii\web\View */
/* @var $user app\models\UserAccount */

$token = PasswordResetRequestForm::generateToken($model->password_reset_token, $model->user_id);
$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['password/reset', 'token' => $token]);

?>
Hello <?= $model->firstName .' ' .$model->lastName; ?>

Follow the link below to reset your password:

<?= $resetLink ?>
