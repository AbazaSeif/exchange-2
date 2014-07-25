<?php
$duplicateUsers = Yii::app()->db->createCommand()
    ->select('company, status')
    ->from('user')
    ->where('email like "'.$data['email'].'"')
    ->queryAll()
;
?>
<div><?php echo 'Email: <span class="attention">'.$data['email'].'</span> ('.$data['count'].' повторения)'?></div>
<div class="dublicate">
    <ol>
    <?php foreach($duplicateUsers as $user): ?>
    <li><?php echo $user['company'].' (Статус: "'.User::$userStatus[$user['status']].'")'; ?></li>
    <?php endforeach; ?>
    </ol>
</div>

