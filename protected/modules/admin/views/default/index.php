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
            <span>Всего перевозчиков: <?php echo User::model()->count('type_contact = 0');?></span>
        </li>
        <li>
            <span>Активных: <?php echo User::model()->count('status='.User::USER_ACTIVE.' and type_contact = 0');?></span>
        </li>
        <li>
            <span>Не подтвержденных: <?php echo User::model()->count('status='.User::USER_NOT_CONFIRMED.' and type_contact = 0');?></span>
        </li>
        <li>
            <span>Предупрежденных: <?php echo User::model()->count('status='.User::USER_WARNING.' and type_contact = 0');?></span>
        </li>
        <li>
            <span>Временно заблокированных: <?php echo User::model()->count('status='.User::USER_TEMPORARY_BLOCKED.' and type_contact = 0');?></span>
        </li>
        <li>
            <span>Заблокированных: <?php echo User::model()->count('status='.User::USER_BLOCKED.' and type_contact = 0');?></span>
        </li>
    </ul>
    <ul class="info-list">
        <li>
            Контактные лица
        </li>
        <li>
            <span>Всего контактных лиц: <?php echo User::model()->count('type_contact = 1');?></span>
        </li>
        <li>
            <span>Активных: <?php echo User::model()->count('status='.User::USER_ACTIVE.' and type_contact = 1');?></span>
        </li>
        <li>
            <span>Не подтвержденных: <?php echo User::model()->count('status='.User::USER_NOT_CONFIRMED.' and type_contact = 1');?></span>
        </li>
        <li>
            <span>Предупрежденных: <?php echo User::model()->count('status='.User::USER_WARNING.' and type_contact = 1');?></span>
        </li>
        <li>
            <span>Временно заблокированных: <?php echo User::model()->count('status='.User::USER_TEMPORARY_BLOCKED.' and type_contact = 1');?></span>
        </li>
        <li>
            <span>Заблокированных: <?php echo User::model()->count('status='.User::USER_BLOCKED.' and type_contact = 1');?></span>
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
        <li>
            <span>Черновиков: <?php echo Transport::model()->count('status=2');?></span>
        </li>
        <li>
            <span>Удаленных: <?php echo Transport::model()->count('status=3');?></span>
        </li>
        <li>
            <span>Всего международных: <?php echo Transport::model()->count('type = 0');?>, из них активных: <?php echo Transport::model()->count('type = 0 and status=1');?></span>
        </li>
        <li>
            <span>Всего региональных: <?php echo Transport::model()->count('type = 1');?>, из них активных: <?php echo Transport::model()->count('type = 1 and status=1');?></span>
        </li>
    </ul>
</div>