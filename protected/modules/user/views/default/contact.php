<div class="form">
<?php $form = $this->beginWidget('CActiveForm', array('id'=>'options',
    'action'=>'/user/option/',
    'enableClientValidation'=>true,
    'clientOptions'=>array(
        'validateOnSubmit'=>true,
        'afterValidate'=>'js:function( form, data, hasError ) 
            {     
                if( hasError ){
                    return false;
                }
                else{
                    return true;
                }
            }'
    ),));
?>
    <div>
        <div class="title"><img src="/images/add-contact.png"><span>Создание контактного лица</span></div>                
            <div class="row">
            <?php echo $form->labelEx($model, 'login'); ?>
            <?php echo $form->textField($model, 'login'); ?>
            </div>
        </div>
	<div class="row submit">
	<?php 
	    echo CHtml::submitButton('Сохранить', array('class' => 'r-submit')); 
	?>
	</div>
    <?php $this->endWidget();?> 
</div>



