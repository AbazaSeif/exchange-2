<?php
class StatisticsController extends Controller
{
    public function actionIndex($transportType = 2)
    {
        $model = new StatisticsForm;
        $this->render('statistics', array('model'=>$model));
    }
    
    public function actionGetExcel($from, $to, $type)
    {        
        $sql = '';
        $label = 'Все перевозки';
        
        Yii::import('ext.phpexcel.XPHPExcel');    
        $objPHPExcel= XPHPExcel::createPHPExcel();
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
            ->setLastModifiedBy("Maarten Balliauw")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file")
        ;
        
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setTitle('Статистика');
        
        if($type) {
            if($type == 1){
                $label = 'Международные перевозки';
                $sql = ' and type=0';
            } else {
                $label = 'Региональные перевозки';
                $sql = ' and type=1';
            }
        }

        if(empty($from)) $from = '2014-01-01';
        if(empty($to)) $to = date('Y-m-d');

        if(strtotime($to)< strtotime($from)){
            $temp = $to;
            $to = $from;
            $from = $temp;
        }

        $transports = Yii::app()->db->createCommand()
            ->select('*')
            ->from('transport')
            ->where('status=0'.$sql.' and date_close between "'.date('Y-m-d', strtotime($from)).'" and "'.date('Y-m-d', strtotime($to.' +1 days')).'"')
            ->order('date_close desc, date_published')
            ->queryAll()
        ;
        
        $objPHPExcel->getActiveSheet()
            ->setCellValue('A1', $label)
            ->setCellValue('A2', date('d.m.Y', strtotime($from)).' - '.date('d.m.Y', strtotime($to)))
        ;
        $objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A1:A2')->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'cccccc')
                )
            )
        );

        if(!empty($transports)) {
            $objPHPExcel->getActiveSheet()
                ->setCellValue('A4', 'Id 1C')
                ->setCellValue('B4', 'Время закрытия заявки')
                ->setCellValue('C4', 'Место загрузки')
                ->setCellValue('D4', 'Место разгрузки')
                ->setCellValue('E4', 'Фирма победитель')
                ->setCellValue('F4', 'Кол-во ставок')
                ->setCellValue('G4', 'Кол-во фирм')
                ->setCellValue('H4', 'Лучшая ставка')
                ->setCellValue('I4', 'Начальная ставка')
                ->setCellValue('J4', 'Валюта')
            ;
            for($col = 'A'; $col != 'F'; $col++) $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth(30);/*setAutoSize(true);*/
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getStyle('A4:J4')->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'cccccc')
                    )
                )
            );
            $objPHPExcel->getActiveSheet()->getStyle('A4:J4')->getFont()->setBold(true);
            $index = 5;
            foreach($transports as $transport){
                $currency = ' €';
                $showRate = $withNds = '';
                if (!$transport['currency']) {
                   $currency = ' руб.';
                } else if($transport['currency'] == 1) {
                   $currency = ' $';
                } 
                
                $rateCount = Rate::model()->countByAttributes(array(
                    'transport_id'=> $transport['id']
                ));
                
                $users = Yii::app()->db->createCommand(array(
                    'select'   => 'user_id',
                    'distinct' => 'true',
                    'from'     => 'rate',
                    'where'    => 'transport_id = ' . $transport['id'],
                ))->queryAll();
                
                $userCount = count($users);

                $rate = Rate::model()->findByPk($transport['rate_id']);
                $ferryman = User::model()->findByPk($rate->user_id);
                $ferrymanField = UserField::model()->findByAttributes(array('user_id'=>$rate->user_id));
                if ($rate->price) {
                    $showRate = floor($rate->price);
                    if($ferrymanField->with_nds && $transport['type'] == Transport::RUS_TRANSPORT) {
                        $price = ceil($rate->price + $rate->price * Yii::app()->params['nds']);
                        if($price%10 != 0) $price -= $price%10;
                        $withNds .= ' (c НДС: '. $price.')';
                    }
                }
                if($withNds) $showRate = $showRate.' '.$withNds;
                $ferrymanCompany = ($ferryman->company) ? $ferryman->company : 'Нет ставок';
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$index, $transport['t_id'])
                    ->setCellValue('B'.$index, date('Y-m-d H:i', strtotime($transport['date_close'])))
                    ->setCellValue('C'.$index, $transport['location_from'])
                    ->setCellValue('D'.$index, $transport['location_to'])
                    ->setCellValue('E'.$index, $ferrymanCompany)
                    ->setCellValue('F'.$index, $rateCount)
                    ->setCellValue('G'.$index, $userCount)
                    ->setCellValue('H'.$index, $showRate)
                    ->setCellValue('I'.$index, $transport['start_rate'])
                    ->setCellValue('J'.$index,  $currency)
                ;
                $index++;
            }
        } else {
            $objPHPExcel->getActiveSheet()->setCellValue('A4', 'Перевозок, удолвлетворяющих условиям отбора, не найдено.');
        }

        // Redirect output to a clientâ€™s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Статистика биржи перевозок на '.date('Y-m-d H-i-s').'.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        Yii::app()->end();
    }
}

