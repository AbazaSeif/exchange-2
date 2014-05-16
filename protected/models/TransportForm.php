<?php

class TransportForm extends CFormModel
{
    public $id;
    public $t_id;
    public $new_transport;
    public $rate_id;
    public $start_rate;
    public $status;
    public $type;
    public $user_id;
    public $currency;
    public $location_from;
    public $location_to;
    public $customs_clearance_RF; // Russian Federation
    public $customs_clearance_EU; // Euro Union
    public $auto_info;
    public $description;
    public $date_close;
    //public $date_close_new;
    public $date_from;
    public $date_to;
    public $date_to_customs_clearance_RF;
    public $date_published;

    public function rules()
    {
        return array(
            array('location_from, location_to, description, date_close, date_from, start_rate', 'required', 'message'=>'Заполните поле "{attribute}"'),
            array('start_rate', 'numerical', 'integerOnly'=>true, 'min'=>0, 'message'=>'Поле "{attribute}" должно содержать число', 'tooSmall'=>'Значение поля "{attribute}" не может быть меньше нуля !'),
            array('new_transport, rate_id, start_rate, status, type, currency', 'numerical', 'integerOnly'=>true),
            array('t_id, user_id', 'length', 'max'=>64),
            array('id, t_id, location_from, new_transport, rate_id, start_rate, status, type, user_id, currency, customs_clearance_RF, customs_clearance_EU, location_to, auto_info, description, date_to_customs_clearance_RF, date_close, date_from, date_to, date_published', 'safe')
            //array('id, date_close_new, t_id, location_from, new_transport, rate_id, start_rate, status, type, user_id, currency, customs_clearance_RF, customs_clearance_EU, location_to, auto_info, description, date_to_customs_clearance_RF, date_close, date_from, date_to, date_published', 'safe')
            //array('location_from, location_to, auto_info, description, date_close, date_from, date_to, date_published', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            //array('id, t_id, new_transport, rate_id, start_rate, status, type, user_id, currency, location_from, location_to, auto_info, description, date_close, date_from, date_to, date_published', 'safe', 'on'=>'search'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            't_id' => 'Id 1c',
            'rate_id' => 'ID Ставки',
            'start_rate' => 'Ставка',
            'status' => 'Статус',
            'type' => 'Тип перевозки',
            'user_id' => 'ID пользователя',
            'location_from' => 'Место загрузки',
            'location_to' => 'Место разгрузки',
            'customs_clearance_RF' => 'Место таможенной очистки в РФ',
            'customs_clearance_EU' => 'Место таможенного оформления в ЕС',
            'auto_info' => 'Транспорт',
            'description' => 'Описание',
            'date_close' => 'Время закрытия заявки',
            //'date_close_new' => 'Время закрытия заявки',
            'date_from' => 'Дата загрузки',
            'date_to' => 'Дата разгрузки',
            'date_to_customs_clearance_RF' => 'Дата доставки в пункт таможенной очистки в РФ',
            'date_published' => 'Дата публикации',
            'currency' => 'Валюта',
        );
    }
}
