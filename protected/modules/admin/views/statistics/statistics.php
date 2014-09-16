<h1>Статистика перевозок</h1>

<!--div>Сейчас на бирже всего перевозок: <?php echo Transport::model()->count();?> (Активных: <?php echo Transport::model()->count('status=1');?>, Архивных: <?php echo Transport::model()->count('status=0');?>, Черновиков: <?php echo Transport::model()->count('status=2');?>)</div-->

<?php $form = $this->beginWidget('CActiveForm', array(
        'id'=>'statistics-form',
        'action'=>$action,
        'enableClientValidation' => true,        
        'clientOptions'=>array(
            'validateOnSubmit'=>true,
            'validateOnChange' => true,
            'afterValidate'=>'js:function( form, data, hasError ) 
            {     
                if( hasError ){
                    return false;
                }
                else{
                    return true;
                }
            }'
        ),
    ));
?>
<div class="statistics">
    <div class="info">
        <ul class="info-list">
            <li>Выберите поля</li>
            <li>
                <span>Международные перевозки</span>
                <span>1</span>
            </li>
            <li>
                <span>Региональные перевозки</span>
                <span>1</span>
            </li>
            <li>
                <span>Период с</span>
                <span>1</span>
            </li>
            <li>
                <span>Период до</span>
                <span>1</span>
            </li>
        </ul>
    </div>
    <?php echo CHtml::link('Скачать Excel', '/admin/statistics/', array('class'=>'btn-admin')); ?>
</div>
<?php $this->endWidget(); ?> 
