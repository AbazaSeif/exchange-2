<?php

class RestoreForm extends CFormModel
{
    public $inn;

    public function rules()
    {
        return array(
            array('inn', 'required'),
            array('inn', 'numerical'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'inn' => 'ИНН/УНП ',
        );
    }
}
