<?php

class RestoreForm extends CFormModel
{
    public $inn;
    public $verifyCode;

    public function rules()
    {
        return array(
            array('inn, verifyCode', 'required'),
            array('verifyCode', 'captcha'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'inn' => 'ИНН/УНП или Email',
            'verifyCode' => 'Код проверки',
        );
    }
}
