<?php
$user = User::model()->findByPk(Yii::app()->user->_id);
?>

<h1>Добрый день, <?php $user->name;?></h1>
<div class="info">
    <ul class="info-list">
        <li>
            <span>Всего перевозок: <?php echo Transport::model()->count();?></span>
        </li>
        <li>
            <span>Активных перевозок: <?php echo Transport::model()->count('status=1');?></span>
        </li>
        <li>
            <span>Активных перевозок: <?php echo Transport::model()->count('status=0');?></span>
        </li>
    </ul>
</div>