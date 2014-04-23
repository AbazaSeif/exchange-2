<?php
class TEmailTemplate
{
    public static function sendMailToNewContact($user, $curUser, $password)
    {
        $name = '';
        if(!empty($user->name)) $name = $user->name;
        if(!empty($user->secondname)){
            if(!empty($name)) $name .= ' ';
            $name .= $user->secondname;
        }
        if(!empty($name)) $name .= ',';

        $email = new TEmail2;
        $email->from_email = Yii::app()->params['adminEmail'];
        $email->from_name  = 'Биржа перевозок ЛБР АгроМаркет';
        $email->to_email   = $user->email;
        $email->to_name    = '';
        $email->subject    = "Приглашение";
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
                                                                    <td class="text" style="margin: 0; color:#666666; font-family:Verdana; font-size:12px; line-height:18px; text-align:left" valign="top">
                                                                        <table width="100%" border="0" cellspacing="0" cellpadding="0" >
                                                                            <tr>
                                                                                <td style="color:#000000; font-family:Verdana; font-size:20px; line-height:24px; text-align:left; font-weight:normal">
                                                                                    '.$name.'
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <img src="http://exchange.lbr.ru/images/test/empty.gif" width="1" height="5" style="height:5px; float: left" alt="" />
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="width: 100%; padding-top: 10px; padding-bottom: 10px; color:#666666; font-family:Verdana; font-size:12px; line-height:18px; text-align:left; font-weight:normal">
                                                                                    Вы были зарегистрированы на бирже перевозок ЛБР-Агромаркет как контактное лицо '.$curUser->company.'.
                                                                                    <br /><br />
                                                                                    Логин: '.$user->email.'
                                                                                    <br />
                                                                                    Пароль: '.$password.'
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
                                                        <td class="text" style="color:#666666; font-family:Verdana; font-size:12px; line-height:14px; text-align:left; padding-top: 10px; padding-bottom: 5px" valign="top">
                                                            Изменить текущий пароль Вы можете в кабинете пользователя в меню "Настройки".
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text" style="color:#666666; font-family:Verdana; font-size:10px; line-height:10px; text-align:left; padding-top: 10px; padding-bottom: 5px" valign="top">
                                                            Это сообщение является автоматическим, на него не следует отвечать.
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
                <!-- END Content -->'
        ;
        $email->sendMail();
    }
    
    public static function sendAboutChangeStatus($model, $changes)
    {
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

