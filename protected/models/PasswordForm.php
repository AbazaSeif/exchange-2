<?php

class PasswordForm extends CFormModel
{
    //public $id; // для хранения идентификатора пользователя

    // дополнительные поля для новых данных
    public $password; // поле 'Введите текущий пароль'
    public $new_password; // поле 'Введите новый пароль'
    public $new_confirm;  // поле 'Подтвердите новый пароль'

    public function rules()
    {
        return array(
            array('password, new_password, new_confirm', 'required'),    
            //array('new_password', 'length', 'min'=>6, 'allowEmpty'=>false),
            array('password, new_password','match', 'pattern'=>'/^([a-zA-Zа-яА-ЯёЁ\d]+)$/i', 'message'=>'Пароль должен содержать только следующие символы: 0-9 a-z A-Z а-я А-Я'),
            array('new_password', 'match', 'pattern'=>'/([a-zA-Zа-яА-Я]+)/', 'message'=>'Пароль должен содержать минимум одну букву'),
            array('new_password', 'match', 'pattern'=>'/([0-9]+)/', 'message'=>'Пароль должен содержать минимум одну цифру'),
            array('new_confirm', 'compare', 'compareAttribute'=>'new_password', 'message'=>'Пароли не совпадают'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'password'=>'Текущий пароль',
            'new_password'=>'Новый пароль',
            'new_confirm'=>'Подтверждение пароля',
        );
    }
}
