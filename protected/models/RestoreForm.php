<?php

class RestoreForm extends CFormModel
{
    public $inn;

    public function rules()
    {
        return array(
            array('inn', 'required'),
            array('inn', 'numerical'),
            /*array('inn', 'length', 'min' => 12, 'max'=>12, 
                'tooShort'=>Yii::t("translation", "{attribute} должен содержать 12 символов."),
                'tooLong'=>Yii::t("translation", "{attribute} должен содержать 12 символов.")
            )*/
        );
    }

    public function attributeLabels()
    {
        return array(
            'inn' => 'ИНН/УНП ',
        );
    }
}
