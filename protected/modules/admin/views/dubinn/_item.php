<?php
$duplicateUsers = Yii::app()->db->createCommand()
    ->select('company, status')
    ->from('user')
    ->where('inn like "'.$data['inn'].'"')
    ->queryAll()
;
?>
<div><?php echo 'ИНН: <span class="attention">'.$data['inn'].'</span> ('.$data['count'].' повторения)'?></div>
<div class="dublicate">
    <ol>
    <?php foreach($duplicateUsers as $user): ?>
    <li><?php echo $user['company'].' (Статус: "'.User::$userStatus[$user['status']].'")'; ?></li>
    <?php endforeach; ?>
    </ol>
</div>