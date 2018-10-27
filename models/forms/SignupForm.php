<?php
/**
  * Singup Form
  *
  * @package     Singup Form
  * @author      Patricio Rojas Ortiz <patricio-rojaso@outlook.com>
  * @copyright   (C) Copyright - Web Application development
  * @license     Private license
  * @link        https://appwebd.github.io
  * @date        2018-06-16 23:03:06
  * @version     1.0
*/

namespace app\models\forms;

use Yii;
use yii\base\Model;
use app\models\User;
use app\helpers\Mail;

/**
 * User signup form
 */
class SignupForm extends Model
{
    const EMAIL      = 'email';
    const FIRST_NAME = 'firstName';
    const LAST_NAME  = 'lastName';
    const STRING     = 'string';
    const USERNAME   = 'username';

    public $username;
    public $email;
    public $password;
    public $firstName;
    public $lastName;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[self::EMAIL, self::FIRST_NAME, self::LAST_NAME, self::USERNAME, 'password'], 'required'],
            [[self::EMAIL, self::FIRST_NAME, self::LAST_NAME, self::USERNAME], 'trim'],

            [self::USERNAME, 'unique', 'targetClass' => User::className()],
            [self::USERNAME, self::STRING, 'min' => 2, 'max' => 255],

            [self::EMAIL, 'email'],
            [self::EMAIL, 'unique', 'targetClass' => User::className()],

            [self::FIRST_NAME, STR_DEFAULT, VALUE => ''],
            [self::LAST_NAME, STR_DEFAULT, VALUE => ''],
            [self::FIRST_NAME, self::STRING, LENGTH => [1, 80]],
            [self::LAST_NAME, self::STRING, LENGTH => [1, 80]],

            ['password', self::STRING, LENGTH => [5, 254]],
        ];
    }

    /**
     * Signs up new user
     *
     * @return app\model\User|null the saved user model or null if saving fails
     */
    public function signup()
    {
        if (!$this->checkFromEmail() || !$this->validate()) {
            return null;
        }

        $user = new User();
        $user->username                 = $this->username;
        $user->email                    = $this->email;
        $user->email_is_verified        = 0;
        $user->email_confirmation_token = ' ';
        $user->firstName                = $this->firstName;
        $user->lastName                 = $this->lastName;
        $user->telephone                = '';
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->generateEmailConfirmationToken(true);
        $user->profile_id               = 20; // 20: Usuario comun
        $user->ipv4_address_last_login  = Yii::$app->getRequest()->getUserIP();
        $user->active                   = 1;
        $user->company_id               = 0;
        $user->generatePasswordResetToken(true);

        if ($user->validate() && $user->save()) {
            Yii::info("OK your account was saved.", __METHOD__);
            $subject = Yii::t('app', 'Signup email of confirmation');
            if (!Mail::sendEmail($user, $subject, 'user/confirm-email')) { // app/mail/user/confirm-email-html.php
                Yii::warning(Yii::t('app', 'Failed to send confirmation email to new user.'), __METHOD__);
            }
            return $user;
        }

        Yii::$app->ui->warning('Could not save new user:', $user->errors);
        return null;
    }

    /**
     * Check if was defined params adminEmail in the file @app/config/params.php variable adminEmail.
     *
     * @return true|false
     */
    private function checkFromEmail()
    {
        $from   = Yii::$app->params['adminEmail'];
        if ($from == "email@example.com") {
            Yii::warning(
                Yii::t(
                    'app',
                    'The file config/params.php is incomplete (the variable adminEmail is missing)'
                ),
                __METHOD__
            );
            return false;
        }

        return true;
    }
}
