<?php
class Validator extends CValidator{
    // этот метод вызвается непосредственно при валидации
    protected function validateAttribute($object, $attribute) {
        
        Yii::log("------------------------------------",'info', 'application');
    }
}