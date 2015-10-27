<?php

class SearchComponent extends CApplicationComponent{
    
      public function init(){
           parent::init();
      }
    
      public function prepareSqlite()
      {
            if(!function_exists('lower')){
                function lower($str){
                    $return = str_replace(array(")", "(", "'", '"' ), "", $str);
                    return mb_strtolower(strip_tags($return), "UTF-8");
                }
            }
            
            Yii::app()->db->getPdoInstance()->sqliteCreateFunction('lower', 'lower', 1);
            Yii::app()->db_auth->getPdoInstance()->sqliteCreateFunction('lower', 'lower', 1);
            
            return true;
      }
}


