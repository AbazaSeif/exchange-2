<?php

class RestoreForm extends CFormModel
{
    public $inn;
    public $verifyCode;

    public function rules()
    {
        return array(
            array('inn, verifyCode', 'required'),
            array('inn', 'numerical'),
            array('verifyCode', 'captcha'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'inn' => 'ИНН/УНП ',
            'verifyCode' => 'Код проверки',
        );
    }
}
