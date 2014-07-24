<?php
$users = '';
$duplicateUsers = Yii::app()->db->createCommand()
    ->select('company')
    ->from('user')
    ->where('email like "'.$data['email'].'"')
    ->queryAll()
;
foreach($duplicateUsers as $user){
    if(!empty($users)) $users .= ', ';
    $users .= $user['company'];
}

?>
<div><?php echo $data['count'].' повторения - '.$data['email'].' ('.$users.')'?></div>

