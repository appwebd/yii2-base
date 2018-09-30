<?php
/**
  * Controllers
  *
  * @package     Model of Controllers
  * @author      Patricio Rojas Ortiz <patricio-rojaso@outlook.com>
  * @copyright   (C) Copyright - Web Application development
  * @license     Private comercial license
  * @link        https://appwebd.github.io
  * @date        2018-07-30 20:29:23
  * @version     1.0
*/

namespace app\models;

use Yii;
use yii\helpers\HtmlPurifier;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * Controllers
 * Controllers
 *
 * @property tinyint(1)      active                     Active
 * @property char(80)        controller_description     Description
 * @property int(11)         controller_id              Controller
 * @property char(100)       controller_name            Name
 * @property tinyint(1)      menu_boolean_private       Menu is private
 * @property tinyint(1)      menu_boolean_visible       Menu is visible
 *
 */
class Controllers extends \yii\db\ActiveRecord
{
    const ACTIVE                     = 'active';
    const CONTROLLER_DESCRIPTION     = 'controller_description';
    const CONTROLLER_ID              = 'controller_id';
    const CONTROLLER_NAME            = 'controller_name';
    const MENU_BOOLEAN_PRIVATE       = 'menu_boolean_private';
    const MENU_BOOLEAN_VISIBLE       = 'menu_boolean_visible';

    /**
    * @return array the validation rules.
    */
    public function rules()
    {
        return [
            [[self::ACTIVE,
              self::CONTROLLER_DESCRIPTION,
              self::CONTROLLER_NAME,
              self::MENU_BOOLEAN_PRIVATE,
              self::MENU_BOOLEAN_VISIBLE], 'required'],
            [self::CONTROLLER_DESCRIPTION, STRING, LENGTH => [1, 80]],
            [self::CONTROLLER_NAME, STRING, LENGTH => [1, 100]],
            [[self::CONTROLLER_ID], 'integer'],
            [[self::ACTIVE,
              self::MENU_BOOLEAN_PRIVATE,
              self::MENU_BOOLEAN_VISIBLE], 'boolean'],
            [[self::CONTROLLER_DESCRIPTION,
              self::CONTROLLER_NAME], 'trim'],
            [[self::CONTROLLER_DESCRIPTION,
              self::CONTROLLER_NAME], function ($attribute) {
                $this->$attribute = HtmlPurifier::process($this->$attribute);
              }
            ],
         ];
    }

    /**
     * Permits add a controller
     *
     * @param string $controllerName Controller Name
     * @param string $controllerDesc A description of controller
     * @param boolean $menuBooleanPrivate Indicates Menu is Private?
     * @param boolean $menuBooleanVisible Indicates Menu is Visible?
     * @param boolean $active Indicates records active
     * @return void
     */
    public static function addControllers(
        $controllerName,
        $controllerDesc,
        $menuBooleanPrivate,
        $menuBooleanVisible,
        $active
    ) {
        $model = new Controllers();
        $model->controller_name = $controllerName;
        $model->controller_description = $controllerDesc;
        $model->menu_boolean_private = $menuBooleanPrivate;
        $model->menu_boolean_visible = $menuBooleanVisible;
        $model->active = $active;

        if ($model->save()) {
            Yii::info(Yii::t('app', 'OK your Controller was saved.'), __METHOD__);
            return true;
        }

        Yii::$app->ui->warning('Could not save new Controller:', $model->errors);
        return false;
    }

    /**
    * @return array customized attribute labels (name=>label)
    */
    public function attributeLabels()
    {
        return [
            self::ACTIVE               => Yii::t('app', 'Active'),
            self::CONTROLLER_DESCRIPTION => Yii::t('app', 'Description'),
            self::CONTROLLER_ID        => Yii::t('app', 'Controller'),
            self::CONTROLLER_NAME      => Yii::t('app', 'Name'),
            self::MENU_BOOLEAN_PRIVATE => Yii::t('app', 'Menu is private'),
            self::MENU_BOOLEAN_VISIBLE => Yii::t('app', 'Menu is visible'),

        ];
    }
    /**
     * behaviors
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @return string the name of the table associated with this ActiveRecord class.
     */
    public static function tableName()
    {
        return 'controllers';
    }

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getAction()
    {
        return $this->hasMany(
            Action::className(),
            [self::CONTROLLER_ID => self::CONTROLLER_ID]
        );
    }

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getLogs()
    {
        return $this->hasMany(
            Logs::className(),
            [self::CONTROLLER_ID => self::CONTROLLER_ID]
        );
    }

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getPermission()
    {
        return $this->hasMany(
            Permission::className(),
            [self::CONTROLLER_ID => self::CONTROLLER_ID]
        );
    }

    /**
     * Get controller_name of Controllers table
     * @return string
     */
    public static function getControllers($controllerName)
    {
        return static::findOne([self::CONTROLLER_NAME => $controllerName]);
    }


    /**
     * Get array from Controllers
     * @return Arrayhelper::map
     */
    public static function getControllersList()
    {
        $droptions = Controllers::find([self::ACTIVE=>1])
               ->orderBy([self::CONTROLLER_NAME => SORT_ASC])
               ->asArray()->all();
        return ArrayHelper::map($droptions, self::CONTROLLER_ID, self::CONTROLLER_NAME);
    }

    /**
     * Get primary key id
     *
     * @return integer primary key
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }
}
