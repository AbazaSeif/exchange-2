<?php
$users = '';
$duplicateUsers = Yii::app()->db->createCommand()
    ->select('company')
    ->from('user')
    ->where('inn like "'.$data['inn'].'"')
    ->queryAll()
;
foreach($duplicateUsers as $user){
    if(!empty($users)) $users .= ', ';
    $users .= $user['company'];
}

?>
<div><?php echo 'ИНН: <span class="attention">'.$data['inn'].'</span> ('.$data['count'].' повторения)'?></div>
<div class="dublicate">
    <ol>
    <?php foreach($duplicateUsers as $user): ?>
    <li><?php echo $user['company']; ?></li>
    <?php endforeach; ?>
    </ol>
</div>