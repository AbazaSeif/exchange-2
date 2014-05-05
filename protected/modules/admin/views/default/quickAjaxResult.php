<?php
//var_dump($data);
if(!empty($data)){
    foreach ($data as $li)
    {
        $inn = (!empty($li['inn'])) ? ' (ИНН: ' . $li['inn'] . ') ': ' ';
        $text = $li['company'] . $inn . $li['email'];
        echo '<li><a href="'.Yii::app()->getBaseUrl(true).'/admin/user/edituser/id/'.$li['id'].'/">'.$text.'</a></li>';
    }
} else echo '<li>По Вашему запросу ничего не найдено ...</li>';
