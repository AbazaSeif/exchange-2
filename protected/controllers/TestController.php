<?php
class TestController extends Controller
{
    public function actionRestore()
    { 
        $model = new RestoreForm;
        if(isset($_POST['RestoreForm'])) {
            $model->attributes = $_POST['RestoreForm'];
            if($model->validate()) {
                $user = array();
                $input = $_POST['RestoreForm']['inn'];
                
                //if(!empty($_POST['RestoreForm']['inn'])) {
                    if(is_numeric($input)) {
                        $user = User::model()->find(array(
                            'condition'=>'inn=:inn',
                            'params'=>array(':inn'=>$input))
                        );
                    } else {
                        $user = User::model()->find(array(
                            'condition'=>'email=:email',
                            'params'=>array(':email'=>$input))
                        );
                    }
                    
                    if(!empty($user)) {
                        if($user->status != User::USER_NOT_CONFIRMED && $user->status != User::USER_TEMPORARY_BLOCKED && $user->status != User::USER_BLOCKED){
                            if($user->email) {
                                $password = User::randomPassword();
                                $user->password = crypt($password, User::model()->blowfishSalt());
                                if($user->save()) {
                                    // send mail to ferryman with new password
                                    $email = new TEmail2;
                                    $email->from_email = Yii::app()->params['adminEmail'];
                                    $email->from_name  = 'Биржа перевозок ЛБР АгроМаркет';
                                    $email->to_email   = $user->email;
                                    $email->to_name    = '';
                                    $email->subject    = 'Восстановление доступа';
                                    $email->type = 'text/html';
                                    $email->body = '<!-- Content -->
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
                                                                        <img src="http://exchange.lbr.ru/images/test/content_top789.jpg" alt="" border="0" width="620" height="12" style="float: left"/>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                <tr>
                                                                    <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="20"></td>
                                                                    <td>
                                                                        <img src="http://exchange.lbr.ru/images/test/empty.gif" width="1" height="15" style="height:15px; float: left" alt="" />
                                                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                            <tr>
                                                                                <td>
                                                                                    <table width="100%" border="0" cellspacing="0" cellpadding="0" >
                                                                                        <tr>
                                                                                            <td class="img" style="font-size:0pt; line-height:0pt; text-align:left; " valign="top" width="185">
                                                                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                                                    <tr>
                                                                                                        <td>
                                                                                                            <img src="http://exchange.lbr.ru/images/test/empty.gif" width="1" height="25" style="height:25px; float: left" alt="" />
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td>
                                                                                                            <a href="http://exchange.lbr.ru/" target="_blank">
                                                                                                                <img src="http://exchange.lbr.ru/images/logo.png" alt="" border="0" width="179" height="66" style="float: left"/>
                                                                                                            </a>
                                                                                                        </td>
                                                                                                        <td>
                                                                                                            <img src="http://exchange.lbr.ru/images/test/empty.gif" width="20" height="1" style="width:20px" alt="" style="float: left"/>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                </table>
                                                                                            </td>
                                                                                            <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" valign="top" width="20"><img src="http://exchange.lbr.ru/images/test/img_right_shadow.jpg" alt="" border="0" width="8" height="131" style="float: left"/></td>
                                                                                            <td class="text" style="margin: 0; color:#a1a1a1; font-family:Verdana; font-size:12px; line-height:18px; text-align:left" valign="top">
                                                                                                <table width="100%" border="0" cellspacing="0" cellpadding="0" >
                                                                                                    <tr>
                                                                                                        <td style="color:#666666; font-family:Verdana; font-size:20px; line-height:24px; text-align:left; font-weight:normal">
                                                                                                            Восстановление доступа
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td>
                                                                                                            <img src="http://exchange.lbr.ru/images/test/empty.gif" width="1" height="10" style="height:10px; float: left" alt="" />
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td style="width: 100%; padding-top: 10px; padding-bottom: 10px; color:#666666; font-family:Verdana; font-size:12px; line-height:18px; text-align:left; font-weight:normal">
                                                                                                            Ваш пароль на на портале "Онлайн биржа перевозок ЛБР-АгроМаркет" был изменен:
                                                                                                            <br/><br/>
                                                                                                            <span style="color:#000000; font-weight: bold">
                                                                                                                Новый пароль: '.$password.'
                                                                                                            </span>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                </table>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </table>
                                                                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                                        <tr>
                                                                                            <td>
                                                                                                <img src="http://exchange.lbr.ru/images/test/separator.jpg" alt="" border="0" width="581" height="1" style="border: 0; float: left"/>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                            <tr>
                                                                                <td class="text" style="color:#000000; font-family:Verdana; font-size:12px; line-height:14px; text-align:left; padding-top: 10px; padding-bottom: 5px" valign="top">
                                                                                    Изменить текущий пароль Вы можете в кабинете пользователя в меню "Настройки".
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                    <td class="img" style="font-size:0pt; line-height:0pt; text-align:left; float: left" width="20"></td>
                                                                </tr>
                                                            </table>
                                                            <img src="http://exchange.lbr.ru/images/test/content_bottom.jpg" alt="" border="0" width="620" height="20" style="float: left"/>
                                                            <!-- END Main Content -->
                                                        </td>
                                                        <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="1" bgcolor="#c1c1c1"></td>
                                                        <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="1" bgcolor="#dfdfdf"></td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <!-- END Content -->
                                    ';
                                    $email->sendMail();
                                    
                                    //Dialog::message('flash-success', 'Отправлено!', 'Новый пароль был выслан на Ваш почтовый ящик.');
                                    Yii::app()->user->setFlash('success', 'Новый пароль был выслан на Ваш email.');
                                } else Yii::log($user->getErrors(), 'error');
                            } else {
                                Yii::app()->user->setFlash('error', 'В вашей учетной записи отсутствует email, поэтому Вы не можете восстановить пароль. Вам требуется связаться с отделом логистики и попросить их внести email.');
                                //Dialog::message('flash-success', 'Внимание!', 'В вашей учетной записи отсутствует email, поэтому Вы не можете восстановить пароль. Вам требуется связаться с логистами и попросить их внести в вашу учетную запись email.');
                            }
                        } else {
                            if(User::USER_NOT_CONFIRMED == $user->status) $message = 'не подтверждена';
                            else if(User::USER_TEMPORARY_BLOCKED == $user->status) $message = 'временно заблокирована';
                            else if(User::USER_BLOCKED == $user->status) $message = 'заблокирована';
                            
                            Yii::app()->user->setFlash('error', 'Восстановление доступа невозможно, т.к. Ваша учетная запись ' . $message.'.');
                            //Dialog::message('flash-error', 'Внимание!', ' Восстановление доступа невозможно. Ваша учетная запись ' . $message.'.');
                        }
                    } else {
                        Yii::app()->user->setFlash('error', 'Пользователь не найден, свяжитесь с отделом логистики.');
                        //Dialog::message('flash-error', 'Внимание!', 'Пользователя с таким ИНН/УНП и Email не найдено, свяжитесь с отделом логистики.');
                    }
                //} else Dialog::message('flash-error', 'Внимание!', 'Заполнены не все обязательные поля');
            } else {
                Dialog::message('flash-success', 'Внимание!', 'Вы заполнили не все обязательные поля.');  
            }
            $this->redirect('/site/restore/');
        } else {
            $this->render('restore', array('model' => $model));
        }
    }
}