<?php

/**
 * This is the model class for table "User".
 *
 * The followings are the available columns in table 'User':
 * @property integer $id
 * @property string $company
 * @property integer $inn
 * @property integer $status
 * @property string $country
 * @property string $region
 * @property string $city
 * @property string $district
 * @property string $name
 * @property string $secondname
 * @property string $surname
 * @property string $login
 * @property string $password
 * @property integer $phone
 * @property integer $phone2
 * @property integer $parent
 * @property integer $type_contact
 * @property string $email
 *
 * The followings are the available model relations:
 * @property Changes[] $changes
 * @property Message[] $messages
 * @property NfyMessages[] $nfyMessages
 * @property NfySubscriptions[] $nfySubscriptions
 * @property Rate[] $rates
 * @property UserEvent[] $userEvents
 * @property UserField[] $userFields
 */
class User extends CActiveRecord
{
        const USER_NOT_CONFIRMED = 0;
        const USER_ACTIVE = 1;
        const USER_WARNING = 2;
        const USER_TEMPORARY_BLOCKED = 3;
        const USER_BLOCKED = 4;
        const PARENT_BLOCKED = 5;
        public $password_confirm;
        
        public static $userStatus = array(
            User::USER_NOT_CONFIRMED => 'Не подтвежден',
            User::USER_ACTIVE => 'Активен',
            User::USER_WARNING => 'Предупрежден',
            User::USER_TEMPORARY_BLOCKED => 'Временно заблокирован',
            User::USER_BLOCKED => 'Заблокирован',
        );        

        /**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
            return 'user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
            // NOTE: you should only define rules for those attributes that
            // will receive user inputs.
            return array(
                array('company, block_date, reason, inn, name, surname, secondname, password, status, country, region, city, district, phone, phone2, type_contact, parent, email', 'safe'),
            );
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
            // NOTE: you may need to adjust the relation name and the related
            // class name for the relations automatically generated below.
            return array(
                'changes' => array(self::HAS_MANY, 'Changes', 'user_id'),
                'messages' => array(self::HAS_MANY, 'Message', 'user_id'),
                'nfyMessages' => array(self::HAS_MANY, 'NfyMessages', 'user_id'),
                'nfySubscriptions' => array(self::HAS_MANY, 'NfySubscriptions', 'user_id'),
                'rates' => array(self::HAS_MANY, 'Rate', 'user_id'),
                'userEvents' => array(self::HAS_MANY, 'UserEvent', 'user_id'),
                'userFields' => array(self::HAS_MANY, 'UserField', 'user_id'),
            );
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
            return array(
                'id' => 'ID',
                'company' => 'Комания',
                'inn' => 'ИНН/УНП ',
                'status' => 'Статус',
                'country' => 'Страна',
                'region' => 'Область',
                'city' => 'Город',
                'district' => 'Район',
                'name' => 'Имя',
                'secondname' => 'Отчество',
                'surname' => 'Фамилия',
                // 'login' => 'Логин',
                'password' => 'Пароль',
                'phone'  => 'Телефон',
                'phone2' => 'Телефон №2',
                'parent' => 'Родитель',
                'type_contact' => 'Тип',
                'email'  => 'Email',
                'reason' => 'Причина',
                'block_date' => 'Причина',
            );
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
            // @todo Please modify the following code to remove attributes that should not be searched.

            $criteria=new CDbCriteria;

            $criteria->compare('id',$this->id);
            $criteria->compare('company',$this->company,true);
            $criteria->compare('inn',$this->inn);
            $criteria->compare('status',$this->status);
            $criteria->compare('country',$this->country,true);
            $criteria->compare('region',$this->region,true);
            $criteria->compare('city',$this->city,true);
            $criteria->compare('district',$this->district,true);
            $criteria->compare('name',$this->name,true);
            $criteria->compare('secondname',$this->secondname,true);
            $criteria->compare('surname',$this->surname,true);
            $criteria->compare('login',$this->login,true);
            $criteria->compare('password',$this->password,true);
            $criteria->compare('phone',$this->phone);
            $criteria->compare('phone2',$this->phone2);
            $criteria->compare('parent',$this->parent);
            $criteria->compare('type_contact',$this->type_contact);
            $criteria->compare('email',$this->email,true);
            $criteria->compare('reason',$this->reason,true);
            $criteria->compare('block_date',$this->reason,true);

            return new CActiveDataProvider($this, array(
                    'criteria'=>$criteria,
            ));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return User the static model class
	 */
	public static function model($className=__CLASS__)
	{
            return parent::model($className);
	}
        
        //  Метод проверяет изменен ли пароль
       /* protected function beforeSave() {
            parent::beforeSave();
            if (isset($_POST['User_password']) && $_POST['User_password']!='') {
                $this->password = crypt($_POST['User_password'], User::model()->blowfishSalt());
            }
            return true;
        }*/

        //  Метод возвращет $cost значное число для хэширования пароля, где: 
        //  $cost - количество возвращаемых знаков
        public function blowfishSalt($cost = 13)
        {
            if (!is_numeric($cost) || $cost < 4 || $cost > 31) {
                throw new Exception("cost parameter must be between 4 and 31");
            }
            $rand = array();
            for ($i = 0; $i < 8; $i += 1) {
                $rand[] = pack('S', mt_rand(0, 0xffff));
            }
            $rand[] = substr(microtime(), 2, 6);
            $rand = sha1(implode('', $rand), true);
            $salt = '$2a$' . sprintf('%02d', $cost) . '$';
            $salt .= strtr(substr(base64_encode($rand), 0, 22), array('+' => '.'));
            return $salt;
        }

        public static function randomPassword() {
            $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
            $pass = array(); //remember to declare $pass as an array
            $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
            for ($i = 0; $i < 16; $i++) {
                $n = rand(0, $alphaLength);
                $pass[] = $alphabet[$n];
            }
            return implode($pass); //turn the array into a string
        }
        public static function statusLabel($statusId) 
        {
                $status = 'Активный';
                if($statusId == User::USER_NOT_CONFIRMED) $status = 'Не подтвержден';
                else if($statusId == User::USER_WARNING) $status = 'Предупрежден';
                else if($statusId == User::USER_TEMPORARY_BLOCKED) $status = 'Временно заблокирован';
                else if($statusId == User::USER_BLOCKED) $status = 'Заблокирован';
                return $status;
        }
        
        public static function sendAboutChangeStatus($model, $changes)
        {
            // Send mail about changes in field "Status"
            /*if(array_key_exists('status', $changes)) {
                $reason = $name = '';
                if(!empty($model->name)) $name = $model->name;
                if(!empty($model->secondname)){
                    if(!empty($name)) $name .= ' ';
                    $name .= $model->secondname;
                }
                if($model->status != User::USER_NOT_CONFIRMED && $model->status != User::USER_ACTIVE){
                    $reason = '<p>Причина: '.$model->reason.'</p>';
                }

                $email = new TEmail;
                $email->from_email = Yii::app()->params['adminEmail'];
                $email->from_name  = 'Биржа перевозок ЛБР АгроМаркет';
                $email->to_email   = $model->email;
                $email->to_name    = '';
                $email->subject    = "Уведомление об изменении статуса";
                $email->type = 'text/html';
                $email->body = '<h1>'.$name.', </h1>' . 
                    '<p>Статус вашей учетной записи был изменен на "'.User::$userStatus[$model->status].'" </p>' .
                    $reason .
                    '</hr><h5>Это сообщение является автоматическим, на него не следует отвечать</h5>'
                ;
                $email->sendMail();
            }*/
            /*****************************************************/
            if(array_key_exists('status', $changes) || array_key_exists('block_date', $changes)) {
                if(!empty($model->email)) {
                    $reason = $name = '';
                    if(!empty($model->name)) $name = $model->name;
                    if(!empty($model->secondname)){
                        if(!empty($name)) $name .= ' ';
                        $name .= $model->secondname;
                    }
                    if(!empty($name))$name .= ',';
                    if($model->status != User::USER_NOT_CONFIRMED && $model->status != User::USER_ACTIVE){
                        $reason = 'Причина: '.$model->reason;
                    }

                    $email = new TEmail2;
                    $email->from_email = Yii::app()->params['adminEmail'];
                    $email->from_name  = 'Биржа перевозок ЛБР АгроМаркет';
                    $email->to_email   = $model->email;
                    $email->to_name    = '';
                    $email->subject    = 'Уведомление о смене статуса';
                    $email->type = 'text/html';
                    $message = '<!-- Content -->
                        <tr>
                            <td>
                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="1" bgcolor="#dfdfdf"></td>
                                        <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="1" bgcolor="#c1c1c1"></td>
                                        <td bgcolor="#ffffff">
                                            <!-- Main Content -->
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td>
                                                        <img src="http://exchange.lbr.ru/images/mail/content_top.jpg" alt="" border="0" width="620" height="12" style="float: left"/>
                                                    </td>
                                                </tr>
                                            </table>
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="20"></td>
                                                    <td>
                                                        <img src="http://exchange.lbr.ru/images/mail/empty.gif" width="1" height="15" style="height:15px; float: left" alt="" />
                                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                            <tr>
                                                                <td>
                                                                    <table width="100%" border="0" cellspacing="0" cellpadding="0" >
                                                                        <tr>
                                                                            <td class="img" style="font-size:0pt; line-height:0pt; text-align:left; " valign="top" width="185">
                                                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                                    <tr>
                                                                                        <td>
                                                                                            <img src="http://exchange.lbr.ru/images/mail/empty.gif" width="1" height="25" style="height:25px; float: left" alt="" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <a href="http://exchange.lbr.ru/" target="_blank">
                                                                                                <img src="http://exchange.lbr.ru/images/logo.png" alt="" border="0" width="179" height="66" style="float: left"/>
                                                                                            </a>
                                                                                        </td>
                                                                                        <td>
                                                                                            <img src="http://exchange.lbr.ru/images/mail/empty.gif" width="20" height="1" style="width:20px" alt="" style="float: left"/>
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </td>
                                                                            <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" valign="top" width="20"><img src="http://exchange.lbr.ru/images/mail/img_right_shadow.jpg" alt="" border="0" width="8" height="131" style="float: left"/></td>
                                                                            <td class="text" style="margin: 0; color:#a1a1a1; font-family:Verdana; font-size:12px; line-height:18px; text-align:left" valign="top">
                                                                                <table width="100%" border="0" cellspacing="0" cellpadding="0" >
                                                                                    <tr>
                                                                                        <td style="color:#000000; font-family:Verdana; font-size:20px; line-height:24px; text-align:left; font-weight:normal">
                                                                                            '.$name.'
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <img src="http://exchange.lbr.ru/images/mail/empty.gif" width="1" height="5" style="height:5px; float: left" alt="" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="width: 100%; padding-top: 10px; padding-bottom: 10px; color:#666666; font-family:Verdana; font-size:12px; line-height:18px; text-align:left; font-weight:normal">'
                   ;

                   if($model->status == User::USER_ACTIVE) $message .= 'Ваша учетная запись была активирована на бирже перевозок "ЛБР-Агромаркет".';
                   else if($model->status == User::USER_WARNING) $message .= 'Вам было вынесено предупреждение.';
                   else if($model->status == User::USER_BLOCKED) $message .= 'Ваша учетная запись была заблокирована.';
                   else if($model->status == User::USER_TEMPORARY_BLOCKED) $message .= 'Ваша учетная запись была заблокирована до '.date('d/m/Y', strtotime($model->block_date)).' года.';
                   else $message .= 'Статус вашей учетной записи был изменен на "'.User::statusLabel($model->status).'".';

                   if(!empty($reason)) 
                       $message .= '<br /><br />
                            <span style="color: #000; ">'.$reason.'</span>'
                       ;

                   $message .= '</td>
                                                                                    </tr>
                                                                                </table>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                        <tr>
                                                                            <td>
                                                                                <img src="http://exchange.lbr.ru/images/mail/separator.jpg" alt="" border="0" width="581" height="1" style="border: 0; float: left"/>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                            <tr>
                                                                <td class="text" style="color:#666666; font-family:Verdana; font-size:10px; line-height:10px; text-align:left; padding-top: 10px; padding-bottom: 5px" valign="top">
                                                                    Если Вы считаете, что статус был изменен ошибочно, просим связаться с нашим отделом логистики либо направить email на почтовый ящик support.ex@lbr.ru.
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                    <td class="img" style="font-size:0pt; line-height:0pt; text-align:left; float: left" width="20"></td>
                                                </tr>
                                            </table>
                                            <img src="http://exchange.lbr.ru/images/mail/content_bottom.jpg" alt="" border="0" width="620" height="20" style="float: left"/>
                                            <!-- END Main Content -->
                                        </td>
                                        <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="1" bgcolor="#c1c1c1"></td>
                                        <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="1" bgcolor="#dfdfdf"></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <!-- END Content -->'
                    ;
                    $email->body = $message;
                    $email->sendMail();
                }
            } 
        }
}
