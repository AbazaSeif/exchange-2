<?php

class StatisticsController extends Controller
{
    public function actionIndex()
    {
        $model = new StatisticsForm;
        $model->date_from = $model->user_activity_date_from = date('01-m-Y');
        $model->date_to = $model->user_activity_date_to = date('d-m-Y');
        $this->render('statistics', array('model' => $model));
    }
    
    public function actionCheck() {
        $transports = Yii::app()->db->createCommand()
                ->select('*')
                ->from('transport')
                ->where('status=0')
                ->order('date_close desc, date_published')
                ->queryAll()
        ;

        foreach ($transports as $transport) {
            $row = '';
            $model = new Rate;
            $criteria = new CDbCriteria;
            $criteria->select = 'min(price) AS price, id, user_id';
            $criteria->condition = 'transport_id = :id';
            $criteria->params = array(':id' => $transport['id']);
            $minPrice = $model->model()->find($criteria);
            if (!empty($minPrice)) {
                $criteria->select = 'id, user_id';
                $criteria->order = 'date';
                $criteria->condition = 'transport_id = :id and price like :price';
                $criteria->params = array(':id' => $transport['id'], ':price' => $minPrice->price);
                $row = $model->model()->find($criteria);
            }
            $tr = Transport::model()->findByPk($transport['id']);
            if (!empty($row)) {
                if ($transport['rate_id'] != $row->id) {
                    echo '(' . $transport['t_id'] . ') ' . $tr->rate_id . ' => ' . $row->id . '<br>';
                    $tr->rate_id = $row->id;
                    $tr->save();
                }
            } else if (!empty($transport['rate_id'])) {
                echo '(' . $transport['t_id'] . ') ' . $tr->rate_id . ' => null<br>';
                $tr->rate_id = null;
                $tr->save();
            }
        }
    }

    public function actionGetExcel($from, $to, $type) {
        $sql = '';
        $label = 'Все перевозки';

        Yii::import('ext.phpexcel.XPHPExcel');
        $objPHPExcel = XPHPExcel::createPHPExcel();
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
                ->setLastModifiedBy("Maarten Balliauw")
                ->setTitle("Office 2007 XLSX Test Document")
                ->setSubject("Office 2007 XLSX Test Document")
                ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("Test result file")
        ;

        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();
        $sheet->setTitle('Статистика');

        if ($type) {
            if ($type == 1) {
                $label = 'Международные перевозки';
                $sql = ' and type=0';
            } else {
                $label = 'Региональные перевозки';
                $sql = ' and type=1';
            }
        }

        if (empty($from))
            $from = '2014-01-01';
        if (empty($to))
            $to = date('Y-m-d');

        if (strtotime($to) < strtotime($from)) {
            $temp = $to;
            $to = $from;
            $from = $temp;
        }

        $transports = Yii::app()->db->createCommand()
                ->select('*')
                ->from('transport')
                ->where('status=0' . $sql . ' and date_close between "' . date('Y-m-d', strtotime($from)) . '" and "' . date('Y-m-d', strtotime($to . ' +1 days')) . '"')
                ->order('date_close desc, date_published')
                ->queryAll()
        ;

        $sheet->setCellValue('A1', $label)
                ->setCellValue('A2', date('d.m.Y', strtotime($from)) . ' - ' . date('d.m.Y', strtotime($to)))
        ;
        $sheet->getStyle('A1:A2')->getFont()->setBold(true);
        $sheet->getStyle('A1:A2')->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'cccccc')
                    )
                )
        );

        if (!empty($transports)) {
            $sheet->setCellValue('A4', 'Id 1C')
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
            for ($col = 'A'; $col != 'F'; $col++)
                $sheet->getColumnDimension($col)->setWidth(30);/* setAutoSize(true); */
            $sheet->getColumnDimension('H')->setAutoSize(true);
            $sheet->getStyle('A4:J4')->applyFromArray(
                    array(
                        'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => 'cccccc')
                        )
                    )
            );
            $sheet->getStyle('H')
                    ->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)
            ;
            $sheet->getStyle('A4:J4')->getFont()->setBold(true);
            $index = 5;
            foreach ($transports as $transport) {
                $currency = ' €';
                $showRate = $withNds = '';
                if (!$transport['currency']) {
                    $currency = ' руб.';
                } else if ($transport['currency'] == 1) {
                    $currency = ' $';
                }

                $rateCount = Rate::model()->countByAttributes(array(
                    'transport_id' => $transport['id']
                ));

                $users = Yii::app()->db->createCommand(array(
                            'select' => 'user_id',
                            'distinct' => 'true',
                            'from' => 'rate',
                            'where' => 'transport_id = ' . $transport['id'],
                        ))->queryAll();

                $userCount = count($users);

                $rate = Rate::model()->findByPk($transport['rate_id']);
                $ferryman = User::model()->findByPk($rate['user_id']);
                $ferrymanField = UserField::model()->findByAttributes(array('user_id' => $rate['user_id']));
                if ($rate['price']) {
                    $showRate = floor($rate['price']);
                    if ($ferrymanField->with_nds && $transport['type'] == Transport::RUS_TRANSPORT) {
                        $price = ceil($rate['price'] + $rate['price'] * Yii::app()->params['nds']);
                        if ($price % 10 != 0)
                            $price -= $price % 10;
                        $withNds .= ' (c НДС: ' . $price . ')';
                    }
                }
                if ($withNds)
                    $showRate = $showRate . ' ' . $withNds;
                $ferrymanCompany = (!empty($ferryman->company)) ? $ferryman->company : 'Нет ставок';
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $index, $transport['t_id'])
                        ->setCellValue('B' . $index, date('Y-m-d H:i', strtotime($transport['date_close'])))
                        ->setCellValue('C' . $index, $transport['location_from'])
                        ->setCellValue('D' . $index, $transport['location_to'])
                        ->setCellValue('E' . $index, $ferrymanCompany)
                        ->setCellValue('F' . $index, $rateCount)
                        ->setCellValue('G' . $index, $userCount)
                        ->setCellValue('H' . $index, $showRate)
                        ->setCellValue('I' . $index, $transport['start_rate'])
                        ->setCellValue('J' . $index, $currency)
                ;
                $index++;
            }
        } else {
            $sheet->setCellValue('A4', 'Перевозок, удолвлетворяющих условиям отбора, не найдено.');
        }

        // Redirect output to a clientâ€™s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Статистика биржи перевозок на ' . date('Y-m-d H-i') . '.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        Yii::app()->end();
    }

    public function actionUserActivity($from, $to) {
        set_time_limit(0);

        $resultOneTime = array();
        $resultMultipleTimes = array();

        $weeks = $this->separatePeriodIntoWeeks($from, $to);
        if (!empty($weeks)) {
            foreach ($weeks as $key => $week) {
                $rates = Yii::app()->db->createCommand()
                    ->select('*')
                    ->from('rate')
                    ->where('date between "' . date('Y-m-d', strtotime($week[0])) . '" and "' . date('Y-m-d', strtotime($week[1] . ' +1 days')) . '"')
                    ->group('user_id, transport_id')
                    ->queryAll()
                ;
                $temp = $tempMultiple = array();
                foreach ($rates as $rate) {
                    if (!in_array($rate['user_id'], $temp)) {
                        $resultOneTime[$key][] = $temp[] = $rate['user_id'];
                    } else if (!in_array($rate['user_id'], $tempMultiple)) {
                        if (($id = array_search($rate['user_id'], $resultOneTime[$key])) !== false) {
                            unset($resultOneTime[$key][$id]);
                        }
                        $resultMultipleTimes[$key][] = $tempMultiple[] = $rate['user_id'];
                    }
                }
            }
        }

        $this->actionGetActivity($from, $weeks, $to, $resultOneTime, $resultMultipleTimes);
    }

//    public function separatePeriodIntoWeeks($from, $to) {
//        $weeks = [];
//        $from = strtotime($from);
//        $to = strtotime($to);
//
//        if ($from == $to) {
//            $weeks[] = [date('d.m.Y', $from), date('d.m.Y', $to)];
//        } else {
//            while ($from < $to) {
//                $fromDay = date("N", $from); // a weekday number
//                if ($fromDay < 7) {
//                    $daysToSun = 7 - $fromDay;
//                    $end = strtotime("+ $daysToSun day", $from); // end of a week 
//                    if ($end > $to)
//                        $end = $to;
//
//                    if (date("n", $from) != date("n", $end)) { // if it's a new month
//                        $end = strtotime("last day of this month", $from);
//                    }
//
//                    $weeks[] = [date('d.m.Y', $from), date('d.m.Y', $end)];
//                    $from = $end;
//                } else {
//                    $weeks[] = [date('d.m.Y', $from), date('d.m.Y', $from)];
//                }
//
//                $from = strtotime("+1 day", $from);
//            }
//        }
//
//        return $weeks;
//    }

//    public function actionGetActivity($from, $weeks, $to, $resultOneTime, $resultMultipleTimes) 
//    {
//        Yii::import('ext.phpexcel.XPHPExcel');
//        $objPHPExcel = XPHPExcel::createPHPExcel();
//        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
//            ->setLastModifiedBy("Maarten Balliauw")
//            ->setTitle("Office 2007 XLSX Test Document")
//            ->setSubject("Office 2007 XLSX Test Document")
//            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
//            ->setKeywords("office 2007 openxml php")
//            ->setCategory("Test result file")
//        ;
//
//        // --- Start - "1 time a week"
//        $objPHPExcel->setActiveSheetIndex(0);
//        $sheet = $objPHPExcel->getActiveSheet();
//        $sheet->setTitle('Активность-1 раз в неделю');
//
//        $sheet->setCellValue('A1', 'Период')
//            ->setCellValue('A2', date('d.m.Y', strtotime($from)) . ' - ' . date('d.m.Y', strtotime($to)))
//        ;
//        
//        $sheet->getStyle('A1:A2')->getFont()->setBold(true);
//        $sheet->getStyle('A1:A2')->applyFromArray(
//            array(
//                'fill' => array(
//                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
//                    'color' => array('rgb' => 'cccccc')
//                )
//            )
//        );
//        
//        for ($col = 'A'; $col != 'F'; $col++)
//            $sheet->getColumnDimension($col)->setWidth(30); /* setAutoSize(true); */
//        
//        $index = 4;
//        if(!empty($resultOneTime)) {
//            foreach($resultOneTime as $key => $item) {
//                $periodName = $weeks[$key][0].' - '.$weeks[$key][1];
//                $sheet->getStyle('A'.$index.':B'.$index)->getFont()->setBold(true);
//                $sheet->getStyle('A'.$index.':B'.$index)->applyFromArray(
//                    array(
//                        'fill' => array(
//                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
//                            'color' => array('rgb' => 'cccccc')
//                        )
//                    )
//                );
//                $sheet->setCellValue('A'.$index, $periodName)
//                    ->setCellValue('B'.$index, count($item))
//                ;
//                $index++;
//                foreach($item as $element) {
//                    $user = User::model()->findByPk($element)->company;
//                    $sheet->setCellValue('A'.$index, $user);
//                    //$sheet->setCellValue('A'.$index, $element);
//                    
//                    $index++;
//                }
//            }
//        }
//        
//        // --- End - "1 time a week"
//        // --- Start - "2 and more times a week"
//        $sheet = $objPHPExcel->createSheet(1);
//        $sheet->setTitle('Активность-более 2 раз в неделю');
//
//        $sheet->setCellValue('A1', 'Период')
//            ->setCellValue('A2', date('d.m.Y', strtotime($from)) . ' - ' . date('d.m.Y', strtotime($to)))
//        ;
//        
//        $sheet->getStyle('A1:A2')->getFont()->setBold(true);
//        $sheet->getStyle('A1:A2')->applyFromArray(
//            array(
//                'fill' => array(
//                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
//                    'color' => array('rgb' => 'cccccc')
//                )
//            )
//        );
//        
//        for ($col = 'A'; $col != 'F'; $col++)
//            $sheet->getColumnDimension($col)->setWidth(30); // setAutoSize(true);
//        
//        /*
//        $index = 5;
//        foreach ($resultMultipleTimes as $user) {
//        
//        }
//        */      
//        $index = 4;
//        if(!empty($resultOneTime)) {
//            foreach($resultMultipleTimes as $key => $item) {
//                $periodName = $weeks[$key][0].' - '.$weeks[$key][1];
//                $sheet->getStyle('A'.$index.':B'.$index)->getFont()->setBold(true);
//                $sheet->getStyle('A'.$index.':B'.$index)->applyFromArray(
//                    array(
//                        'fill' => array(
//                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
//                            'color' => array('rgb' => 'cccccc')
//                        )
//                    )
//                );
//                $sheet->setCellValue('A'.$index, $periodName)
//                    ->setCellValue('B'.$index, count($item))
//                ;
//                $index++;
//                foreach($item as $element) {
//                    $user = User::model()->findByPk($element)->company;
//                    $sheet->setCellValue('A'.$index, $user);
//                    //$sheet->setCellValue('A'.$index, $element);
//                    
//                    $index++;
//                }
//            }
//        }
//        // --- End - "2 and more times a week"
//  
//        // Redirect output to a client's web browser (Excel5)
//        header('Content-Type: application/vnd.ms-excel');
//        header('Content-Disposition: attachment;filename="Активность пользователей в разных перевозках за неделю на ' . date('Y-m-d H-i') . '.xls"');
//        header('Cache-Control: max-age=0');
//        // If you're serving to IE 9, then the following may be needed
//        header('Cache-Control: max-age=1');
//        // If you're serving to IE over SSL, then the following may be needed
//        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
//        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
//        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
//        header('Pragma: public'); // HTTP/1.0
//
//        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
//        $objWriter->save('php://output');
//        Yii::app()->end();
//    }
}

//
//class StatisticsController extends Controller {
//
//    public function actionIndex($transportType = 2) 
//    {
//        echo 0; exit;
//        $model = new StatisticsForm;
//        $model->date_from = $model->user_activity_date_from = date('01-m-Y');
//        $model->date_to = $model->user_activity_date_to = date('d-m-Y');
//echo 1; exit;
//        $this->render('statistics', array('model' => $model));
//    }
//
//    public function actionCheck() {
//        $transports = Yii::app()->db->createCommand()
//                ->select('*')
//                ->from('transport')
//                ->where('status=0')
//                ->order('date_close desc, date_published')
//                ->queryAll()
//        ;
//
//        foreach ($transports as $transport) {
//            $row = '';
//            $model = new Rate;
//            $criteria = new CDbCriteria;
//            $criteria->select = 'min(price) AS price, id, user_id';
//            $criteria->condition = 'transport_id = :id';
//            $criteria->params = array(':id' => $transport['id']);
//            $minPrice = $model->model()->find($criteria);
//            if (!empty($minPrice)) {
//                $criteria->select = 'id, user_id';
//                $criteria->order = 'date';
//                $criteria->condition = 'transport_id = :id and price like :price';
//                $criteria->params = array(':id' => $transport['id'], ':price' => $minPrice->price);
//                $row = $model->model()->find($criteria);
//            }
//            $tr = Transport::model()->findByPk($transport['id']);
//            if (!empty($row)) {
//                if ($transport['rate_id'] != $row->id) {
//                    echo '(' . $transport['t_id'] . ') ' . $tr->rate_id . ' => ' . $row->id . '<br>';
//                    $tr->rate_id = $row->id;
//                    $tr->save();
//                }
//            } else if (!empty($transport['rate_id'])) {
//                echo '(' . $transport['t_id'] . ') ' . $tr->rate_id . ' => null<br>';
//                $tr->rate_id = null;
//                $tr->save();
//            }
//        }
//    }
//
//    public function actionGetExcel($from, $to, $type) {
//        $sql = '';
//        $label = 'Все перевозки';
//
//        Yii::import('ext.phpexcel.XPHPExcel');
//        $objPHPExcel = XPHPExcel::createPHPExcel();
//        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
//                ->setLastModifiedBy("Maarten Balliauw")
//                ->setTitle("Office 2007 XLSX Test Document")
//                ->setSubject("Office 2007 XLSX Test Document")
//                ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
//                ->setKeywords("office 2007 openxml php")
//                ->setCategory("Test result file")
//        ;
//
//        $objPHPExcel->setActiveSheetIndex(0);
//        $sheet = $objPHPExcel->getActiveSheet();
//        $sheet->setTitle('Статистика');
//
//        if ($type) {
//            if ($type == 1) {
//                $label = 'Международные перевозки';
//                $sql = ' and type=0';
//            } else {
//                $label = 'Региональные перевозки';
//                $sql = ' and type=1';
//            }
//        }
//
//        if (empty($from))
//            $from = '2014-01-01';
//        if (empty($to))
//            $to = date('Y-m-d');
//
//        if (strtotime($to) < strtotime($from)) {
//            $temp = $to;
//            $to = $from;
//            $from = $temp;
//        }
//
//        $transports = Yii::app()->db->createCommand()
//                ->select('*')
//                ->from('transport')
//                ->where('status=0' . $sql . ' and date_close between "' . date('Y-m-d', strtotime($from)) . '" and "' . date('Y-m-d', strtotime($to . ' +1 days')) . '"')
//                ->order('date_close desc, date_published')
//                ->queryAll()
//        ;
//
//        $sheet->setCellValue('A1', $label)
//                ->setCellValue('A2', date('d.m.Y', strtotime($from)) . ' - ' . date('d.m.Y', strtotime($to)))
//        ;
//        $sheet->getStyle('A1:A2')->getFont()->setBold(true);
//        $sheet->getStyle('A1:A2')->applyFromArray(
//                array(
//                    'fill' => array(
//                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
//                        'color' => array('rgb' => 'cccccc')
//                    )
//                )
//        );
//
//        if (!empty($transports)) {
//            $sheet->setCellValue('A4', 'Id 1C')
//                    ->setCellValue('B4', 'Время закрытия заявки')
//                    ->setCellValue('C4', 'Место загрузки')
//                    ->setCellValue('D4', 'Место разгрузки')
//                    ->setCellValue('E4', 'Фирма победитель')
//                    ->setCellValue('F4', 'Кол-во ставок')
//                    ->setCellValue('G4', 'Кол-во фирм')
//                    ->setCellValue('H4', 'Лучшая ставка')
//                    ->setCellValue('I4', 'Начальная ставка')
//                    ->setCellValue('J4', 'Валюта')
//            ;
//            for ($col = 'A'; $col != 'F'; $col++)
//                $sheet->getColumnDimension($col)->setWidth(30);/* setAutoSize(true); */
//            $sheet->getColumnDimension('H')->setAutoSize(true);
//            $sheet->getStyle('A4:J4')->applyFromArray(
//                    array(
//                        'fill' => array(
//                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
//                            'color' => array('rgb' => 'cccccc')
//                        )
//                    )
//            );
//            $sheet->getStyle('H')
//                    ->getAlignment()
//                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)
//            ;
//            $sheet->getStyle('A4:J4')->getFont()->setBold(true);
//            $index = 5;
//            foreach ($transports as $transport) {
//                $currency = ' €';
//                $showRate = $withNds = '';
//                if (!$transport['currency']) {
//                    $currency = ' руб.';
//                } else if ($transport['currency'] == 1) {
//                    $currency = ' $';
//                }
//
//                $rateCount = Rate::model()->countByAttributes(array(
//                    'transport_id' => $transport['id']
//                ));
//
//                $users = Yii::app()->db->createCommand(array(
//                            'select' => 'user_id',
//                            'distinct' => 'true',
//                            'from' => 'rate',
//                            'where' => 'transport_id = ' . $transport['id'],
//                        ))->queryAll();
//
//                $userCount = count($users);
//
//                $rate = Rate::model()->findByPk($transport['rate_id']);
//                $ferryman = User::model()->findByPk($rate['user_id']);
//                $ferrymanField = UserField::model()->findByAttributes(array('user_id' => $rate['user_id']));
//                if ($rate['price']) {
//                    $showRate = floor($rate['price']);
//                    if ($ferrymanField->with_nds && $transport['type'] == Transport::RUS_TRANSPORT) {
//                        $price = ceil($rate['price'] + $rate['price'] * Yii::app()->params['nds']);
//                        if ($price % 10 != 0)
//                            $price -= $price % 10;
//                        $withNds .= ' (c НДС: ' . $price . ')';
//                    }
//                }
//                if ($withNds)
//                    $showRate = $showRate . ' ' . $withNds;
//                $ferrymanCompany = (!empty($ferryman->company)) ? $ferryman->company : 'Нет ставок';
//                $objPHPExcel->setActiveSheetIndex(0)
//                        ->setCellValue('A' . $index, $transport['t_id'])
//                        ->setCellValue('B' . $index, date('Y-m-d H:i', strtotime($transport['date_close'])))
//                        ->setCellValue('C' . $index, $transport['location_from'])
//                        ->setCellValue('D' . $index, $transport['location_to'])
//                        ->setCellValue('E' . $index, $ferrymanCompany)
//                        ->setCellValue('F' . $index, $rateCount)
//                        ->setCellValue('G' . $index, $userCount)
//                        ->setCellValue('H' . $index, $showRate)
//                        ->setCellValue('I' . $index, $transport['start_rate'])
//                        ->setCellValue('J' . $index, $currency)
//                ;
//                $index++;
//            }
//        } else {
//            $sheet->setCellValue('A4', 'Перевозок, удолвлетворяющих условиям отбора, не найдено.');
//        }
//
//        // Redirect output to a clientâ€™s web browser (Excel5)
//        header('Content-Type: application/vnd.ms-excel');
//        header('Content-Disposition: attachment;filename="Статистика биржи перевозок на ' . date('Y-m-d H-i') . '.xls"');
//        header('Cache-Control: max-age=0');
//        // If you're serving to IE 9, then the following may be needed
//        header('Cache-Control: max-age=1');
//
//        // If you're serving to IE over SSL, then the following may be needed
//        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
//        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
//        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
//        header('Pragma: public'); // HTTP/1.0
//
//        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
//        $objWriter->save('php://output');
//        Yii::app()->end();
//    }
//
//    public function actionUserActivity($from, $to) {
//        set_time_limit(0);
//
//        //$from = '2015-01-01';
//        //$to = '2015-09-24';
//        
//        $resultOneTime = array();
//        $resultMultipleTimes = array();
//
//        $weeks = $this->separatePeriodIntoWeeks($from, $to);
//        if (!empty($weeks)) {
//            foreach ($weeks as $key => $week) {
//                $rates = Yii::app()->db->createCommand()
//                    ->select('*')
//                    ->from('rate')
//                    ->where('date between "' . date('Y-m-d', strtotime($week[0])) . '" and "' . date('Y-m-d', strtotime($week[1] . ' +1 days')) . '"')
//                    ->group('user_id, transport_id')
//                    ->queryAll()
//                ;
//                $temp = $tempMultiple = array();
//                foreach ($rates as $rate) {
//                    if (!in_array($rate['user_id'], $temp)) {
//                        $resultOneTime[$key][] = $temp[] = $rate['user_id'];
//                    } else if (!in_array($rate['user_id'], $tempMultiple)) {
//                        if (($id = array_search($rate['user_id'], $resultOneTime[$key])) !== false) {
//                            unset($resultOneTime[$key][$id]);
//                        }
//                        $resultMultipleTimes[$key][] = $tempMultiple[] = $rate['user_id'];
//                    }
//                }
//            }
//        }
//        /*
//        echo '<pre>';
//        var_dump($resultOneTime);
//        var_dump($resultMultipleTimes);
//        exit;
//        */
//        $this->actionGetActivity($from, $weeks, $to, $resultOneTime, $resultMultipleTimes);
//    }
//
//    public function separatePeriodIntoWeeks($from, $to) {
//        $weeks = [];
//        $from = strtotime($from);
//        $to = strtotime($to);
//
//        if ($from == $to) {
//            $weeks[] = [date('d.m.Y', $from), date('d.m.Y', $to)];
//        } else {
//            while ($from < $to) {
//                $fromDay = date("N", $from); // a weekday number
//                if ($fromDay < 7) {
//                    $daysToSun = 7 - $fromDay;
//                    $end = strtotime("+ $daysToSun day", $from); // end of a week 
//                    if ($end > $to)
//                        $end = $to;
//
//                    if (date("n", $from) != date("n", $end)) { // if it's a new month
//                        $end = strtotime("last day of this month", $from);
//                    }
//
//                    $weeks[] = [date('d.m.Y', $from), date('d.m.Y', $end)];
//                    $from = $end;
//                } else {
//                    $weeks[] = [date('d.m.Y', $from), date('d.m.Y', $from)];
//                }
//
//                $from = strtotime("+1 day", $from);
//            }
//        }
//
//        return $weeks;
//    }
//
//    public function actionGetActivity($from, $weeks, $to, $resultOneTime, $resultMultipleTimes) 
//    {
//        Yii::import('ext.phpexcel.XPHPExcel');
//        $objPHPExcel = XPHPExcel::createPHPExcel();
//        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
//            ->setLastModifiedBy("Maarten Balliauw")
//            ->setTitle("Office 2007 XLSX Test Document")
//            ->setSubject("Office 2007 XLSX Test Document")
//            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
//            ->setKeywords("office 2007 openxml php")
//            ->setCategory("Test result file")
//        ;
//
//        // --- Start - "1 time a week"
//        $objPHPExcel->setActiveSheetIndex(0);
//        $sheet = $objPHPExcel->getActiveSheet();
//        $sheet->setTitle('Активность-1 раз в неделю');
//
//        $sheet->setCellValue('A1', 'Период')
//            ->setCellValue('A2', date('d.m.Y', strtotime($from)) . ' - ' . date('d.m.Y', strtotime($to)))
//        ;
//        
//        $sheet->getStyle('A1:A2')->getFont()->setBold(true);
//        $sheet->getStyle('A1:A2')->applyFromArray(
//            array(
//                'fill' => array(
//                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
//                    'color' => array('rgb' => 'cccccc')
//                )
//            )
//        );
//        
//        for ($col = 'A'; $col != 'F'; $col++)
//            $sheet->getColumnDimension($col)->setWidth(30); /* setAutoSize(true); */
//        
//        $index = 4;
//        if(!empty($resultOneTime)) {
//            foreach($resultOneTime as $key => $item) {
//                $periodName = $weeks[$key][0].' - '.$weeks[$key][1];
//                $sheet->getStyle('A'.$index.':B'.$index)->getFont()->setBold(true);
//                $sheet->getStyle('A'.$index.':B'.$index)->applyFromArray(
//                    array(
//                        'fill' => array(
//                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
//                            'color' => array('rgb' => 'cccccc')
//                        )
//                    )
//                );
//                $sheet->setCellValue('A'.$index, $periodName)
//                    ->setCellValue('B'.$index, count($item))
//                ;
//                $index++;
//                foreach($item as $element) {
//                    $user = User::model()->findByPk($element)->company;
//                    $sheet->setCellValue('A'.$index, $user);
//                    //$sheet->setCellValue('A'.$index, $element);
//                    
//                    $index++;
//                }
//            }
//        }
//        
//        // --- End - "1 time a week"
//        // --- Start - "2 and more times a week"
//        $sheet = $objPHPExcel->createSheet(1);
//        $sheet->setTitle('Активность-более 2 раз в неделю');
//
//        $sheet->setCellValue('A1', 'Период')
//            ->setCellValue('A2', date('d.m.Y', strtotime($from)) . ' - ' . date('d.m.Y', strtotime($to)))
//        ;
//        
//        $sheet->getStyle('A1:A2')->getFont()->setBold(true);
//        $sheet->getStyle('A1:A2')->applyFromArray(
//            array(
//                'fill' => array(
//                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
//                    'color' => array('rgb' => 'cccccc')
//                )
//            )
//        );
//        
//        for ($col = 'A'; $col != 'F'; $col++)
//            $sheet->getColumnDimension($col)->setWidth(30); // setAutoSize(true);
//        
//        /*
//        $index = 5;
//        foreach ($resultMultipleTimes as $user) {
//        
//        }
//        */      
//        $index = 4;
//        if(!empty($resultOneTime)) {
//            foreach($resultMultipleTimes as $key => $item) {
//                $periodName = $weeks[$key][0].' - '.$weeks[$key][1];
//                $sheet->getStyle('A'.$index.':B'.$index)->getFont()->setBold(true);
//                $sheet->getStyle('A'.$index.':B'.$index)->applyFromArray(
//                    array(
//                        'fill' => array(
//                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
//                            'color' => array('rgb' => 'cccccc')
//                        )
//                    )
//                );
//                $sheet->setCellValue('A'.$index, $periodName)
//                    ->setCellValue('B'.$index, count($item))
//                ;
//                $index++;
//                foreach($item as $element) {
//                    $user = User::model()->findByPk($element)->company;
//                    $sheet->setCellValue('A'.$index, $user);
//                    //$sheet->setCellValue('A'.$index, $element);
//                    
//                    $index++;
//                }
//            }
//        }
//        // --- End - "2 and more times a week"
//  
//        // Redirect output to a client's web browser (Excel5)
//        header('Content-Type: application/vnd.ms-excel');
//        header('Content-Disposition: attachment;filename="Активность пользователей в разных перевозках за неделю на ' . date('Y-m-d H-i') . '.xls"');
//        header('Cache-Control: max-age=0');
//        // If you're serving to IE 9, then the following may be needed
//        header('Cache-Control: max-age=1');
//        // If you're serving to IE over SSL, then the following may be needed
//        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
//        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
//        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
//        header('Pragma: public'); // HTTP/1.0
//
//        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
//        $objWriter->save('php://output');
//        Yii::app()->end();
//    }
//}
