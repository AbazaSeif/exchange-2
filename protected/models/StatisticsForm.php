<?php

class StatisticsForm extends CFormModel
{
    public $type;
    public $date_from;
    public $date_to;
    public $user_activity_date_from;
    public $user_activity_date_to;

    public function rules()
    {
        return array(
            
        );
    }
    
    public function attributeLabels()
    {
        return array(
            'type' => 'Перевозки',
            'date_from' => 'Период с',
            'date_to' => 'Период по',
            'user_activity_date_from' => 'Период с',
            'user_activity_date_to' => 'Период по',
        );
    }
}

