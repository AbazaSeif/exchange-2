<?php

class passwordStrength extends CValidator
{
 
    public $strength;
 
    private $weak_pattern = '/^(?=.*[a-zA-Z0-9]).{5,}$/';
    private $strong_pattern = '/^(?=.*\d(?=.*\d))(?=.*[a-zA-Z](?=.*[a-zA-Z])).{5,}$/';


protected function validateAttribute($object,$attribute)
{
    // check the strength parameter used in the validation rule of our model
    if ($this->strength == 'weak')
      $pattern = $this->weak_pattern;
    elseif ($this->strength == 'strong')
      $pattern = $this->strong_pattern;
 
    // extract the attribute value from it's model object
    $value=$object->$attribute;
    if(!preg_match($pattern, $value))
    {
        $this->addError($object,$attribute,'your password is too weak!');
    }
}

public function clientValidateAttribute($object,$attribute)
{
 
    // check the strength parameter used in the validation rule of our model
    if ($this->strength == 'weak')
      $pattern = $this->weak_pattern;
    elseif ($this->strength == 'strong')
      $pattern = $this->strong_pattern;     
 
    $condition="!value.match({$pattern})";
 
    return "
if(".$condition.") {
    messages.push(".CJSON::encode('your password is too weak, you fool!').");
}
";
}}