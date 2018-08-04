<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once APPPATH."/third_party/excel/PHPExcel.php";
 
class Ems_email_excel extends PHPExcel {
    public $fileName;
    public $objPHPExcel;
    public $objExcel;
    private static $ci = null;
    public function __construct(){
        parent::__construct();
        self::$ci =& get_instance();
        $this->objExcel = new PHPExcel();
    }
    
    public function setFileObj($fileName = ''){
        $this->fileName = $fileName;
        $this->objPHPExcel = PHPExcel_IOFactory::load($this->fileName);
    }  
    
    public function read(){
        //get only the Cell Collection
        $cell_collection = $this->objPHPExcel->getActiveSheet()->getCellCollection();
 
        //extract to a PHP readable array format
        foreach ($cell_collection as $cell) {
            $column = $this->objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
            $row = $this->objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
            $data_value = $this->objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
         
            //The header will/should be in row 1 only. of course, this can be modified to suit your need.
            if ($row == 1) {
                $header[$row][$column] = $data_value;
            } else {
                //$arr_data[$row][$column] = $data_value;
                if($column == 'A'){
                    if($data_value != ''){
                        $arr_data[$row][$column] = date('m/d/Y', $this->_createDate($data_value));
                    }else{
                        $arr_data[$row][$column] = '';
                    }
                }else{
                    $arr_data[$row][$column] = $data_value;
                }
            }
        }
         
        //send the data in an array format
        $data['header'] = $header;
        $data['values'] = $arr_data;
        return $data;
    }

    public function readWithTimestamp(){
        //get only the Cell Collection
        $cell_collection = $this->objPHPExcel->getActiveSheet()->getCellCollection();
 
        //extract to a PHP readable array format
        foreach ($cell_collection as $cell) {
            $column = $this->objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
            $row = $this->objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
            $data_value = $this->objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
         
            //The header will/should be in row 1 only. of course, this can be modified to suit your need.
            if ($row == 1) {
                $header[$row][$column] = $data_value;
            } else {
                //$arr_data[$row][$column] = $data_value;
                if($column == 'A'){
                    if($data_value != ''){
                        $arr_data[$row][$column] = date('m/d/Y_H:i:s', $this->_createDate($data_value));
                    }else{
                        $arr_data[$row][$column] = '';
                    }
                }else{
                    $arr_data[$row][$column] = $data_value;
                }
            }
        }
         
        //send the data in an array format
        $data['header'] = $header;
        $data['values'] = $arr_data;
        return $data;
    }

    private function _createDate($val = ''){
        return PHPExcel_Shared_Date::ExcelToPHP($val);
    }


    public function createMultipleSheet($titles = array(), $sheetData = array(), $transdet = array()){
        $i = 0;
        foreach($titles as $key => $value){
            if($i > 0){
                $this->objExcel->createSheet();
            }
            $this->objExcel->setActiveSheetIndex($i)->setTitle("$value");
            $this->_getSheet($i, $key, $sheetData[$key], $transdet);
            $i++;
        }
        $this->objExcel->setActiveSheetIndex(0);  
    }

    private function _getSheet($sheetIndex = 0, $title = '', $sheetData = array(), $transDet = array()){
        if($title == 'table1'){
            $this->_createTable1($sheetIndex, $sheetData);
        }
        if($title == 'table2'){
            $this->_createTable2($sheetIndex, $sheetData);
        }
        if($title == 'table3'){
            $this->_createTable3($sheetIndex, $sheetData, $transDet);
        }
    }

    private function _createTable1($sheetIndex = 0, $sheetData = array()){
        $row = 1;
        $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(0, $row, ' ');
        $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(1, $row, 'KW');
        $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(2, $row, 'Amps');
        $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(3, $row, 'Voltage');
        $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(4, $row, 'HZ');
        $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(5, $row, 'PF');
        $this->_setColor('A'.$row.':F'.$row, '5ebe3d');
        $row = $row + 1;
        if(count($sheetData) > 0){
            foreach ($sheetData as $key => $value) {
                $this->objExcel->setActiveSheetIndex($sheetIndex)->mergeCells('A'.$row.':F'.$row)->setCellValueByColumnAndRow(0, $row, 'Date : '.date("F j, Y",strtotime($key)));
                $this->_setColor('A'.$row.':F'.$row, '8ec2e9');
                $row = $row + 1;
                if(count($value) > 0){
                    foreach ($value as $keyD => $valueD) {
                        $leftTitle = 'CPP';
                        if($keyD == 'emsData'){
                            $leftTitle = 'WIL-R';
                        }
                        $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(0, $row, $leftTitle);
                        $this->_getTable1DataValue($valueD, $row, $sheetIndex);
                        $row = $row + 1;
                    }
                }
            }
        }
    }

    private function _getTable1DataValue($valueD = array(), $row = 0, $sheetIndex = 0){
        if(count($valueD) > 0){
            foreach ($valueD as $keyDD => $valueDD) {
                if(count($valueDD) > 0){
                    $col = 1;
                    foreach ($valueDD as $keyDDD => $valueDDD) {
                        $datas = '';
                        if($valueDDD != ''){
                            $datas = $valueDDD;
                        }else{
                            $datas = 0;
                        }
                        $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow($col, $row, $datas);
                        $col++;
                    }
                }
            }
        }
    }

    private function _createTable2($sheetIndex = 0, $sheetData = array()){
        $row = 1;
        if(count($sheetData) > 0){
            foreach ($sheetData as $key => $value) {
                $this->objExcel->setActiveSheetIndex($sheetIndex)->mergeCells('A'.$row.':F'.$row)->setCellValueByColumnAndRow(0, $row, 'Date : '.date("F j, Y",strtotime($key)));
                $this->_setColor('A'.$row.':F'.$row, '8ec2e9');
                $row = $row + 1;
                $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(0, $row, 'Avg Line Loss % (All)');
                $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(1, $row, $value->avgLineLossAll);
                $row = $row + 1;
                $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(0, $row, 'Avg Transformer Loading % (All)');
                $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(1, $row, $value->avgTransLoadingPerAll);
                $row = $row + 1;
                $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(0, $row, 'Avg Transformer Loss% (Above Standard of 2%)');
                $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(1, $row, $value->avgTransLossPer);
                $row = $row + 1;
                $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(0, $row, 'Number of Transformers Connected');
                $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(1, $row, $value->numberOfTransConnected);
                $row = $row + 1;
                $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(0, $row, 'Number of Transformers Error');
                $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(1, $row, $value->numberOfTransError);
                $row = $row + 1;
            }
        }
    }

    private function _createTable3($sheetIndex = 0, $sheetData = array(), $transDet = array()){
        $row = 1;
        $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(0, $row, 'Location');
        $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(1, $row, 'Transformer Name');
        $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(2, $row, 'Lowest Loading %');
        $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(3, $row, 'Highest Loading %');
        $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(4, $row, 'Avg Loading %');
        $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(5, $row, 'Duration for which Loading % is below 40%(Hours)');
        $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(6, $row, 'Avg Loss % above Standard of 2%');
        $this->_setColor('A'.$row.':H'.$row, '5ebe3d');
        $row = $row + 1;
        if(count($sheetData) > 0){
            foreach ($sheetData as $key => $value) {
                $this->objExcel->setActiveSheetIndex($sheetIndex)->mergeCells('A'.$row.':H'.$row)->setCellValueByColumnAndRow(0, $row, 'Date : '.date("F j, Y",strtotime($key)));
                $this->_setColor('A'.$row.':H'.$row, '8ec2e9');
                $row = $row + 1;
                if(count($value) > 0){
                    foreach ($value as $keyD => $valueD) {
                        if(count($valueD) > 0){
                            foreach ($valueD as $keyDD => $valueDD) {
                                $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(0, $row, $transDet[$keyDD]['location']);
                                $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(1, $row, $transDet[$keyDD]['name']);
                                $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(2, $row, $valueDD->lowLoading);
                                $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(3, $row, $valueDD->highLoading);
                                $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(4, $row, $valueDD->avgLoading);
                                $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(5, $row, $valueDD->duration);
                                if($valueDD->loss == 'N/A'){
                                    $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(6, $row, 'N/A');
                                }else{
                                    $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(6, $row, $this->_getLoss($valueDD->loss));
                                }
                                $row = $row + 1;   
                            }
                        }
                    }
                }
            }
        }
    }

    public function createMultipleSheet_bck_1($titles = array(), $sheetData = array(), $transdet = array()){
        $i = 0;
        foreach($titles as $key => $value){
            if($i > 0){
                $this->objExcel->createSheet();
            }
            $this->objExcel->setActiveSheetIndex($i)->setTitle("$value");
            $this->_getSheet($i, $sheetData[$key], $transdet);
            $i++;
        }
        $this->objExcel->setActiveSheetIndex(0);  
    }

    private function _getSheet_bck_1($sheetIndex = 0, $sheetData = array(), $transDet = array()){
        $this->_createTable1($sheetIndex, $sheetData->table1);
        $this->_createTable2($sheetIndex, $sheetData->table2);
        $this->_createTable3($sheetIndex, $sheetData->table3->transDet, $transDet);
    }

    private function _createTable1_bck_1($sheetIndex = 0, $sheetData = array()){
        $row = 1;
        $this->objExcel->setActiveSheetIndex($sheetIndex)->mergeCells('A1:F1')->setCellValueByColumnAndRow(0, $row, 'Table 1 : CPP to WIL Receiving Stats');
        $this->_setColor('A1:F1', '8ec2e9');
        $row = $row + 1;
        $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(0, $row, ' ');
        $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(1, $row, 'KW');
        $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(2, $row, 'Amps');
        $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(3, $row, 'Voltage');
        $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(4, $row, 'HZ');
        $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(5, $row, 'PF');
    
        if(count($sheetData) > 0){
            $col = 0;
            foreach ($sheetData as $key => $value) {
                if($key == 'emsData'){
                    $row = 4;
                    $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(0, $row, 'WIL-R');
                }else{
                    $row = 3;
                    $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(0, $row, 'CPP');
                }
                if(count($value) > 0){
                    $col = 1;
                    foreach ($value as $keyM => $valueM) {
                        foreach ($valueM as $keyMM => $valueMM) {
                            $datas = '';
                            if($valueMM != ''){
                                $datas = $valueMM;
                            }else{
                                $datas = 0;
                            }
                            $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow($col, $row, $datas);
                            $col++;
                        }
                    }
                }
            }
        }
    }

    private function _createTable2_bck_1($sheetIndex = 0, $sheetData = array()){
        $row = 6;
        $this->objExcel->setActiveSheetIndex($sheetIndex)->mergeCells('A6:F6')->setCellValueByColumnAndRow(0, $row, 'Table 2 : Network Diagnostics');
        $this->_setColor('A6:F6', '8ec2e9');
        $row = $row + 1;
        $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(0, $row, 'Avg Line Loss % (All)');
        $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(1, $row, $sheetData->avgLineLossAll);
        $row = $row + 1;
        $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(0, $row, 'Avg Transformer Loading % (All)');
        $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(1, $row, $sheetData->avgTransLoadingPerAll);
        $row = $row + 1;
        $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(0, $row, 'Avg Transformer Loss% (Above Standard of 2%)');
        $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(1, $row, $sheetData->avgTransLossPer);
        $row = $row + 1;
        $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(0, $row, 'Number of Transformers Connected');
        $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(1, $row, $sheetData->numberOfTransConnected);
        $row = $row + 1;
        $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(0, $row, 'Number of Transformers Error');
        $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(1, $row, $sheetData->numberOfTransError);
    }

    private function _createTable3_bck_1($sheetIndex = 0, $sheetData = array(), $transDet = array()){
        $row = 13;
        $this->objExcel->setActiveSheetIndex($sheetIndex)->mergeCells('A13:K13')->setCellValueByColumnAndRow(0, $row, 'Table 3 : Details of Transformers');
        $this->_setColor('A13:F13', '8ec2e9');
        $row = $row + 1;
        $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(0, $row, 'Location');
        $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(1, $row, 'Transformer Name');
        $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(2, $row, 'Lowest Loading %');
        $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(3, $row, 'Highest Loading %');
        $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(4, $row, 'Avg Loading %');
        $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(5, $row, 'Duration for which Loading % is below 40%(Hours)');
        $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(6, $row, 'Avg Loss % above Standard of 2%');
        if(count($sheetData) > 0){
            foreach ($sheetData as $key => $value) {
                $row = $row + 1;
                $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(0, $row, $transDet[$key]['location']);
                $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(1, $row, $transDet[$key]['name']);
                $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(2, $row, $value->avgLoading);
                $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(3, $row, $value->lowLoading);
                $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(4, $row, $value->highLoading);
                $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(5, $row, $value->duration);
                if($value->loss == 'N/A'){
                    $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(6, $row, 'N/A');
                }else{
                    $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(6, $row, $this->_getLoss($value->loss));
                }
            }
        }

    }

    private function _getLoss($loss_per_avg = 0){
        if($loss_per_avg < 0){
              $lossHtml = number_format((float)round($loss_per_avg,2), 2, '.', '');
          }elseif($loss_per_avg >= 0 && $loss_per_avg <= 2){
              $lossHtml = 'With in range';
          }else{
            $loss = $loss_per_avg - 2;
            if($loss >= 0 && $loss <= 10){
              if($loss >= 0 && $loss <= 0.5){
                  $lossHtml = number_format((float)round($loss,2), 2, '.', '');
              }else{
                  $lossHtml = number_format((float)round($loss,2), 2, '.', '');
              }
            }else{
                $lossHtml = number_format((float)round($loss,2), 2, '.', '');
            }
          }
        return $lossHtml  ;
    }

    private function _setColor($cell = '', $color = ''){
        $this->objExcel->getActiveSheet()->getStyle($cell)
                            ->applyFromArray(array(
                                        'borders' => array(
                                            'allborders' => array(
                                                'style' => PHPExcel_Style_Border::BORDER_THIN,
                                                'color' => array('rgb' => '030303')
                                            )
                                        ),
                                        'fill' => array(
                                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                            'color' => array('rgb' => $color)
                                        )
                            ));
    }

    private function _getExcelCellChar($column = 'B', $step = 1){
        for($i = 0; $i < $step; $i++) {
            $column++;
        }
        return $column;
    }

    public function generate(){
        // Set document properties
        $this->objExcel->getProperties()->setCreator("ETV ADMIN")
                                     ->setLastModifiedBy("ETV ADMIN")
                                     ->setTitle("EMS Email Report")
                                     ->setSubject("EMS Email Report")
                                     ->setDescription("EMS Email Report.")/*
                                     ->setKeywords("office 2007 openxml php")
                                     ->setCategory("Test result file")*/;


        
    }

    public function download($fileName = 'test.xlsx'){
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$fileName.'"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($this->objExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    function p($data = array(), $prExit = true){
        echo "<pre>";
        print_r($data);
        if($prExit){
            exit;
        }
    }



}
