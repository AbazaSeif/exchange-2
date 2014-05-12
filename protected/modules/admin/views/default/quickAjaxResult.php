<?php
if(!empty($changesFlag)) {
    if(!empty($data)){
        foreach ($data as $li)
        {
            $text = $li['surname'] . ' ' . $li['name'] . ' ' . $li['secondname'] . ' '  . $li['email'];
            echo '<li><a href="'.Yii::app()->getBaseUrl(true).'/admin/changes/showchanges/id/'.$li['id'].'/">'.$text.'</a></li>';
        }
    } else echo '<li>По Вашему запросу ничего не найдено ...</li>';
} else {
    if(!empty($data)){
        foreach ($data as $li)
        {
            $inn = (!empty($li['inn'])) ? ' (ИНН: ' . $li['inn'] . ') ': ' ';
            $text = $li['company'] . $inn . $li['email'];
            echo '<li><a href="'.Yii::app()->getBaseUrl(true).'/admin/user/edituser/id/'.$li['id'].'/">'.$text.'</a></li>';
        }
    } else echo '<li>По Вашему запросу ничего не найдено ...</li>';
}
