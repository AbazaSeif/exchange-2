<?php
/* @var $this SiteController */
/* @var $error array */

$this->pageTitle=Yii::app()->name . ' - Error';
$this->breadcrumbs=array(
	'Error',
);

$allTranports = Transport::model()->findAllByAttributes(array('status'=>1));
?>
<div id="show-errors">
<h2>Error <?php echo $code . ' ' . CHtml::encode($message); ?></h2>
    <?php if(count($allTranports)): ?>
    <div>
        <div>Возможно Вас заинтересуют следующие заявки на перевозку:</div>
    <?php foreach($allTranports as $transport): ?>
        <p>
            <a class="t-header" href="/transport/description/id/<?php echo $transport->id ?>/" >
                <?php echo $transport->location_from . ' &mdash; ' . $transport->location_to ?>
            </a>
        </p>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
