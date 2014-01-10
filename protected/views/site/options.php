<div class="office-menu">
	<ul class="menuMainTop">
		<li>
			<a href="/site/officeuser/">
				<span>Ставки</span>
			</a>
		</li>
		<li>
			<a href="/site/officeuseroption/">
				<span>Настройки</span>
			</a>
		</li>
	</ul>
</div>
<h2>User</h2>

<!--form id="user-options" method="GET" action="/options/">
    <div>jjjj</div>
	<button type="submit">Отправить</button>
</form-->

<?php
   /* $this->widget('zii.widgets.CListView', array(
        'dataProvider' => $data,
        'itemView'     => '_view',
        'ajaxUpdate'   => false, 
        'emptyText'    => 'Нет перевозок',
        'itemsTagName' => 'ul',
        'summaryText'  => 'Показано {start}&mdash;{end} из {count}',
        'template'     => '<div class="mainPagerContainer"><div class="sorting">{summary}{sorter}</div></div>{items}{pager}',
        'sortableAttributes'=>array(
            'date_published' => 'По дате публикации'
        ),
        'pager'      => array(
            'class'  => 'LinkPager',
            'header' => false,
            'firstPageLabel' => 'В начало',
            'prevPageLabel'  => 'Назад',
            'nextPageLabel'  => 'Вперёд',
            'lastPageLabel'  => 'В конец',
        )
    )); */
?>

<div class="form">
<?php echo CHtml::beginForm(); ?>
 
<?php //echo CHtml::errorSummary($model); ?>
 
<div class="row">
<?php //echo CHtml::activeLabel($model,'username'); ?>
<?php //echo CHtml::activeTextField($model,'username'); ?>
</div>
 
<div class="row">
<?php //echo CHtml::activeLabel($model,'password'); ?>
<?php //echo CHtml::activePasswordField($model,'password'); ?>
</div>
 
<div class="row rememberMe">
<?php //echo CHtml::activeCheckBox($model,'rememberMe'); ?>
<?php //echo CHtml::activeLabel($model,'rememberMe'); ?>
</div>
 
<div class="row submit">
<?php echo CHtml::submitButton('Войти'); ?>
</div>
 
<?php echo CHtml::endForm(); ?>
</div>
<!-- form -->


