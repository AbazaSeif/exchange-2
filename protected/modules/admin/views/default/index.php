<?php
$user = AuthUser::model()->findByPk(Yii::app()->user->_id);
?>

<h1>Добрый день, <?php echo $user->name;?></h1>
<div class="info">
    
    <ul class="info-list">
        <li>
            Перевозчики
        </li>
        <li>
            <span>Всего перевозчиков: <?php echo User::model()->count();?></span>
        </li>
        <li>
            <span>Активных: <?php echo User::model()->count('status='.User::USER_ACTIVE);?></span>
        </li>
        <li>
            <span>Не подтвержденных: <?php echo User::model()->count('status='.User::USER_NOT_CONFIRMED);?></span>
        </li>
        <li>
            <span>Предупрежденных: <?php echo User::model()->count('status='.User::USER_WARNING);?></span>
        </li>
        <li>
            <span>Временно заблокированных: <?php echo User::model()->count('status='.User::USER_TEMPORARY_BLOCKED);?></span>
        </li>
        <li>
            <span>Заблокированных: <?php echo User::model()->count('status='.User::USER_BLOCKED);?></span>
        </li>
    </ul>
    <ul class="info-list">
        <li>
            Перевозки
        </li>
        <li>
            <span>Всего перевозок: <?php echo Transport::model()->count();?></span>
        </li>
        <li>
            <span>Активных: <?php echo Transport::model()->count('status=1');?></span>
        </li>
        <li>
            <span>Архивных: <?php echo Transport::model()->count('status=0');?></span>
        </li>
    </ul>
</div>