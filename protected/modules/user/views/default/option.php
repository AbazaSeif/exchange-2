<?php if(Yii::app()->user->hasFlash('success')):?>
    <div class="info">
        <?php echo Yii::app()->user->getFlash('success'); ?>
    </div>
<?php endif; ?>
<?php if(Yii::app()->user->hasFlash('error')):?>
    <div class="info error">
        <?php echo Yii::app()->user->getFlash('error'); ?>
    </div>
<?php endif; ?>

<div class="o-left settings">
    <!-- Настройки для оповещения по почте и Параметры отображения -->
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
            <div class="title"><img src="/images/mail2.jpg"><span>Настройки для оповещения по почте</span></div>    
            <?php //echo $form->errorSummary($model); ?>
            <div class="row">
                <?php echo $form->checkBox($model, 'mail_transport_create_1'); ?>
                <?php echo $form->labelEx($model, 'mail_transport_create_1'); ?>
            </div>
            <div class="row">
                <?php echo $form->checkBox($model, 'mail_transport_create_2'); ?>
                <?php echo $form->labelEx($model, 'mail_transport_create_2'); ?>
            </div>
            <!--div class="row">
                <?php //echo $form->checkBox($model, 'mail_kill_rate'); ?>
                <?php //echo $form->labelEx($model, 'mail_kill_rate'); ?>             
            </div-->
            <div class="row">
                <?php echo $form->checkBox($model, 'mail_deadline'); ?>
                <?php echo $form->labelEx($model, 'mail_deadline'); ?>              
            </div>
            <div class="row">
                <?php echo $form->checkBox($model, 'mail_before_deadline'); ?>
                <?php echo CHtml::label('За ' . Yii::app()->params['minNotify'] . ' минут до закрытия перевозки', 'mail_before_deadline'); ?>                
            </div>
        </div>
        <div>
            <div class="title"><img src="/images/nds.jpg"><span>Параметры отображения</span></div>
            <div class="row">
                <?php $accountStatus = array('regl'=>'Отображать только региональные заявки на перевозку', 'intl'=>'Отображать только международные заявки на перевозку', 'all'=>'Отображать все заявки на перевозку'); ?>
                <?php echo $form->radioButtonList($model,'show',$accountStatus); ?>
            </div>
            <div class="row nds">
                <?php echo $form->checkBox($model, 'with_nds'); ?>
                <?php echo $form->label($model, 'with_nds'); ?>
            </div>
        </div>
        <div class="row submit">
            <?php 
                echo CHtml::submitButton('Сохранить', array('class' => 'r-submit')); 
            ?>
        </div>
        <?php $this->endWidget();?> 
    </div>
    
    <!-- Изменить пароль -->
    
    <div class="form">
        <?php $form = $this->beginWidget('CActiveForm', array('id'=>'password',
            'action'=>'/user/option/',
            'enableClientValidation'=>true,
            'clientOptions'=>array(
                'validateOnSubmit'=>true,
                'afterValidate'=>'js:function( form, data, hasError ) {
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
            <div class="title"><img src="/images/pass.png"><span>Изменить пароль</span></div>
            
            <?php echo $form->errorSummary($pass); ?>
            
            <div class="row password">
            <?php  
                echo $form->labelEx($pass, 'password');
                echo $form->passwordField($pass, 'password', array(
                    'value' => ''
                ));
                echo $form->error($pass, 'password'); 
                //echo CHtml::passwordField('PasswordForm[password]','');
            ?>
            </div>
            <div class="row password">
            <?php
                echo $form->labelEx($pass, 'new_password');
                echo $form->passwordField($pass, 'new_password');
                echo $form->error($pass, 'new_password'); 
            ?>    
            </div>                
            <div class="row password">
            <?php  
                echo $form->labelEx($pass, 'new_confirm');
                echo $form->passwordField($pass, 'new_confirm');
                echo $form->error($pass, 'new_confirm'); 
            ?>    
            </div>
        </div>
        <div class="row submit">
        <?php 
            echo CHtml::submitButton('Сохранить', array('class' => 'r-submit')); 
        ?>
        </div>
        <?php $this->endWidget();?> 
    </div>
</div>
<div class="o-right">
    <div class="form">
        <?php $form = $this->beginWidget('CActiveForm', array('id'=>'mail',
            'action'=>'/user/option/',
            'enableClientValidation'=>true,
            'clientOptions'=>array(
                'validateOnSubmit'=>true,
                'afterValidate'=>'js:function( form, data, hasError ) {     
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
            <div class="title"><img src="/images/mail.jpg"><span>Изменить email: </span><span><?php echo (!empty($curEmail)) ? $curEmail : 'Не указан email'; ?></span></div>
            <?php echo $form->errorSummary($mail); ?>
            <div class="row">
                <?php
                    echo $form->labelEx($mail, 'new_email');
                    echo $form->textField($mail, 'new_email', array(
                        //'value' => ' '
                    ));
                    echo $form->error($mail, 'new_email'); 
                ?>    
            </div>
            <div class="row password">
                <?php
                    echo $form->labelEx($mail, 'password');
                    echo $form->passwordField($mail, 'password', array(
                        //'value' => ' '
                    ));
                    echo $form->error($mail, 'password');
                ?>
            </div>
        </div>
        <div class="row submit">
            <?php 
                echo CHtml::submitButton('Сохранить', array('class' => 'r-submit')); 
            ?>
        </div>
        <?php $this->endWidget();?> 
    </div>
    <?php if(!Yii::app()->user->isContactUser): ?>
    <div>
        <?php 
        if ($mess = Yii::app()->user->getFlash('message')) {
            echo '<div class="message success">'.$mess.'</div>';
        }
        ?>
        <div class="title"><img src="/images/contacts.jpg">
             <span>Контактные лица</span>
             <span><?php echo CHtml::link('Создать', '/user/default/createcontact/', array('class' => 'btn-create-subuser')); ?></span>
        </div>
        <div class="o-contacts">
            <?php
                $this->widget('zii.widgets.CListView', array(
                    'dataProvider' => $dataContacts,
                    'itemView' => '_item', // представление для одной записи
                    'ajaxUpdate'=>false, // отключаем ajax поведение
                    'emptyText'=>'Нет контактных лиц',
                    'template'=>'{sorter} {items} {pager}',
                    'sorterHeader'=>'',
                    'itemsTagName'=>'ul',
                    'sortableAttributes'=>array('surname'),
                    'pager'=>array(
                        'class'=>'CLinkPager',
                        'header'=>false,
                        'prevPageLabel'=>'<',
                        'nextPageLabel'=>'>',
                        'lastPageLabel'=>'>>',
                        'firstPageLabel'=>'<<'
                    ),
                ));
            ?>
        </div>
    </div>
    <?php endif; ?>
</div>







