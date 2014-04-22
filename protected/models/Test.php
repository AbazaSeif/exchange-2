<?php

class Test extends CFormModel
{
    public $email;

    public function rules()
    {
        return array(
            array('email', 'safe'),
            array('email', 'required'),
            array('email', 'email', 'message'=>'Неправильный Email адрес'),
        );
    }
    
    public function attributeLabels()
    {
        return array(
            'email' => 'Email',
        );
    }
}
