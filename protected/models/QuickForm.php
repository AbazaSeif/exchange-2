<?php
class QuickForm extends CFormModel
{
    public $message;
    public $user;
    public $transport;
  
    public function rules()
    {
        return array(
            array('message', 'required'),
            array('user', 'safe'),
            array('transport', 'safe'),
        );
    }
  
    public function attributeLabels()
    {
        return array(
            'message' => 'Сообщение',
        );
    }
}