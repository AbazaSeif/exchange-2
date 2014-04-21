<?php
class TEmail2{
    public $from_email;
    public $from_name;
    public $to_email;
    public $to_name;
    public $subject;
    public $data_charset='UTF-8';
    public $send_charset='windows-1251';
    public $body='';
    public $type='text/plain';
    function sendMail(){
        $dc = $this->data_charset;
        $sc = $this->send_charset;
        //Кодируем поля адресата, темы и отправителя
        $enc_to = $this->mimeHeaderEncode($this->to_name,$dc,$sc).' <'.$this->to_email.'>';
        $enc_subject = $this->mimeHeaderEncode($this->subject,$dc,$sc);
        $enc_from = $this->mimeHeaderEncode($this->from_name,$dc,$sc).' <'.$this->from_email.'>';
        /***************************************************************/
        /*$this->body = '
            <!DOCTYPE html>
            <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
                    <meta name="viewport" content="width=400, initial-scale=0.5" />
                    <meta content="telephone=no" name="format-detection" />
                    <title>Новые заявки на перевозку</title>
                    <style type="text/css" media="screen">
                          
                            body { padding:0 !important; margin:0 !important; display:block !important; background:#ededed; -webkit-text-size-adjust:none }
                            .footer a, .footer a:hover, .footer a:active { color: #ffffff; text-decoration: none; }
                            a, a:hover, a:active { color:#7b828b; text-decoration:underline }
                            p { padding:0 !important; margin:0 !important } 
                    </style>
            </head>
            <body class="body" style="padding:0 !important; margin:0 !important; display:block !important; background:#ededed; -webkit-text-size-adjust:none">
            <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ededed">
                <tr>
                    <td align="center" valign="top">
                        <table width="624" border="0" cellspacing="0" cellpadding="0">
                            <!-- Top -->
                            <tr>
                                <td class="top" style="color:#4a5461; font-family:Verdana; font-size:11px; line-height:15px; text-align:center">
                                    <img src="http://exchange.lbr.ru/images/mail/empty.gif" width="1" height="20" style="height:20px; float: left" alt="" />
                                    <img src="http://exchange.lbr.ru/images/mail/empty.gif" width="1" height="15" style="height:15px; float: left" alt="" />
                                </td>
                            </tr>
                            <!-- END Top -->
                            <!-- Header -->
                            <tr>
                                <td>
                                    <table style="margin: 0; padding: 0" width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td>
                                                <img src="http://exchange.lbr.ru/images/mail/header_top.jpg" alt="" border="0" width="624" height="29" style="float: left"/>
                                            </td>
                                        </tr>
                                    </table>
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td class="img" style="font-size:0pt; line-height:0pt; text-align:left;" width="1" bgcolor="#dfdfdf"></td>
                                            <td class="img" style="font-size:0pt; line-height:0pt; text-align:left;" width="1" bgcolor="#c1c1c1"></td>
                                            <td bgcolor="#00abab">
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                        <td class="img" style="font-size:0pt; line-height:0pt; text-align:left; padding-left: 17px; padding-bottom: 10px"><img src="http://exchange.lbr.ru/images/mail/header_logo.jpg" style="border: 0; float: left" alt="" border="0" width="300" height="23" /></td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="1" bgcolor="#c1c1c1"></td>
                                            <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="1" bgcolor="#dfdfdf"></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <!-- END Header -->' . 
                            $this->body .
                            '<!-- Footer -->
                            <tr class="footer">
                                <td>
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="1" bgcolor="#dfdfdf"></td>
                                            <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="1" bgcolor="#b1b1b1"></td>
                                            <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="1" bgcolor="#00abab"></td>
                                            <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="1" bgcolor="#00abab"></td>
                                            <td bgcolor="#00abab">
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                        <td style="padding-left: 20px; padding-top: 14px; padding-bottom: 3px;"><span style="font-family:Verdana; font-size:11px; text-align:left;"><a href="http://exchange.lbr.ru/" target="_blank" style="text-decoration: none; color: #ffffff !important;">www.exchange.lbr.ru</a></span></td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="1" bgcolor="#00abab"></td>
                                            <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="1" bgcolor="#00abab"></td>
                                            <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="1" bgcolor="#b1b1b1"></td>
                                            <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="1" bgcolor="#dfdfdf"></td>
                                        </tr>
                                    </table>
                                    <img src="http://exchange.lbr.ru/images/mail/footer_bottom.jpg" alt="" border="0" width="624" height="29" style="padding-top: 0; float: left" />
                                </td>
                            </tr>
                            <!-- END Footer -->
                            <!-- Bottom -->
                            <tr>
                                <td class="bottom" style="color:#7b828b; font-family:Verdana; font-size:11px; line-height:20px; text-align:center">
                                    <table width="624" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td style="text-align:center;">
                                                <span style="color:#7b828b; font-family:Verdana; font-size:11px; line-height:20px; text-align:center">support.ex@lbr.ru</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="text-align:center;">
                                                <span style="color:#7b828b; font-family:Verdana; font-size:11px; line-height:20px; text-align:center">Copyright &copy; 2014 ООО "ЛБР-Агромаркет"</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <img src="http://exchange.lbr.ru/images/mail/empty.gif" width="1" height="50" style="height:50px; float: left" alt="" />
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <!-- END Bottom -->
                        </table>
                    </td>
                </tr>
            </table>
            </body>
            </html>'
        ;*/
        //var_dump($this->body); exit;
        /***************************************************************/
        //Кодируем тело письма
        $enc_body = $dc==$sc?$this->body:iconv($dc,$sc.'//IGNORE',$this->body);
        //Оформляем заголовки письма
        $headers = '';
        $headers.="Mime-Version: 1.0\n";
        $headers.="Content-type: ".$this->type."; charset=".$sc."\n";
        $headers.="From: ".$enc_from."\n";
        //Отправляем
        return mail($enc_to,$enc_subject,$enc_body,$headers);
    }
    
    function mimeHeaderEncode($str, $data_charset, $send_charset){
        if($data_charset != $send_charset)
            $str=iconv($data_charset,$send_charset.'//IGNORE',$str);
        return ('=?'.$send_charset.'?B?'.base64_encode($str).'?=');
    }
}

