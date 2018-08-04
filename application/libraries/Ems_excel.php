<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once APPPATH."/third_party/excel/PHPExcel.php";
 
class Ems_excel extends PHPExcel {
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

    public function createMultipleSheet($titles = array(), $prepData = array()){
        foreach($titles as $key => $value){
            if($key > 0){
                $this->objExcel->createSheet();
                $this->objExcel->setActiveSheetIndex($key)->setTitle("$value");
                if($key == 1){
                    $this->_getKwhConsumptionSheet($key, $prepData[$key]);
                }elseif ($key == 2) {
                    if(isset($prepData[$key])){
                        $this->_getKwAvgSheet($key, $prepData[$key]);
                    }
                }elseif ($key == 3) {
                    if(isset($prepData[$key])){
                        $this->_getKwhCompressorReadingSheet($key, $prepData[$key]);
                    }
                }elseif ($key == 4) {
                    if(isset($prepData[$key])){
                        $this->_getKwhCompressorConsumedSheet($key, $prepData[$key]);
                    }
                }
            }else{
                $this->objExcel->setActiveSheetIndex(0)->setTitle("$value");
                $this->_getKwhReadingSheet(0, $prepData[0]);
            }
        }
        $this->objExcel->setActiveSheetIndex(0);  
    }

    private function _getKwhReadingSheet($sheetIndex = 0, $prepData = array()){
        $this->_getMergeCellKwhReading($sheetIndex, $prepData['merge']);
        $this->_getHeaderKwhReading($sheetIndex, 0, 2, $prepData['header']);
        $this->_getSheetKwhReading($sheetIndex, $prepData['data'], $prepData['header']);
    }

    private function _getKwhConsumptionSheet($sheetIndex = 1, $data = array()){
        $this->_getMergeCellKwhReading($sheetIndex, $data['merge']);
        $this->_getHeaderKwhConsumption($sheetIndex, 2, $data['header']);
        $this->_getSheetKwhConsumption($sheetIndex, $data['data']);
    }

    private function _getKwAvgSheet($sheetIndex = 2, $data = array()){
        $this->_getMergeCellKwhReading($sheetIndex, $data['merge']);
        $this->_getHeaderKwhConsumption($sheetIndex, 2, $data['header']);
        $this->_getSheetKwAvg($sheetIndex, $data['data']);
    }

    private function _getKwhCompressorReadingSheet($sheetIndex = 3, $data = array()){
        $this->_getMergeCellKwhReading($sheetIndex, $data['merge']);
        $this->_getHeaderKwhReading($sheetIndex, 0, 2, $data['header']);
        $this->_getSheetKwhCompressorReading($sheetIndex, $data['data']);
    }

    private function _getKwhCompressorConsumedSheet($sheetIndex = 4, $data = array()){
        $this->_getMergeCellKwhReading($sheetIndex, $data['merge']);
        $this->_getHeaderKwhReading($sheetIndex, 0, 2, $data['header']);
        $this->_getSheetKwhCompressorConsumed($sheetIndex, $data['data']);
    }

    private function _getMergeCellKwhReading($sheetIndex = 0,$data = array()){
        if(count($data) > 0){
            $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(0, 1, '');
            foreach ($data as $key => $value) {
                $this->objExcel->getActiveSheet()->mergeCells($value[0]);
                $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow($value[2], 1, $value[1]);
                $this->_setColor($value[0], $value[3], true, 14);
            }
        }
    }

    private function _getHeaderKwhReading($sheetIndex = 0, $column = 0,$row = 2, $data = array()){
        //$this->objExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0, $row, 'Date');
        $col = $column;
        foreach ($data as $key => $value) {
            $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow($col, $row, $value);
            $col = $col + 1;
        }
        $char = $this->_getExcelCellChar('A', $col - 1);
        $cell = "A".$row.":".$char.$row;
        $this->_setColor($cell, '8ec2e9', true, 12);
    }

    private function _getSheetKwhReading($sheetIndex = 0, $data = array(), $header = array()){
        if(count($data) > 0){
            $col = 0;
            $row = 3;
            foreach ($data as $keyD => $valueD) {
                $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow($col, $row, $keyD);
                $col = $col + 1;
                if(count($valueD) > 0){
                    foreach ($valueD as $keyT => $valueT) {
                        if(count($valueT) > 0){
                            foreach ($valueT as $keyTD => $valueTD) {
                                $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow($col, $row, $valueTD);
                                $col = $col + 1;
                            }
                        }
                    }
                }
                $row = $row + 1;
                $col = 0;
            }
        }
    }

    private function _getHeaderKwhConsumption($sheetIndex = 1, $row = 2, $data = array()){
        $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(0, $row, 'Date');
        $col = 1;
        foreach ($data as $key => $value) {
            if($key == 'G' || $key == 'D'){
                if(count($value) > 0){
                    foreach ($value as $keyL => $valueL) {
                        $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow($col, $row, $valueL);
                        $col = $col + 1;
                    }
                }
            }else{
                $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow($col, $row, $value);
                $col = $col + 1;
            }
        }
        $char = $this->_getExcelCellChar('A', $col - 1);
        $cell = "A".$row.":".$char.$row;
        $this->_setColor($cell, '8ec2e9', true, 12);
    }

    private function _getSheetKwhConsumption($sheetIndex = 1, $data = array()){
        if(count($data) > 0){
            $col = 0;
            $row = 3;
            foreach ($data as $keyD => $valueD) {
                $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow($col, $row, $keyD);
                $col = $col + 1;
                if(count($valueD) > 0){
                    foreach ($valueD as $keyT => $valueT) {
                        if($keyT == 'G' || $keyT == 'D'){
                            if(count($valueT) > 0){
                                foreach ($valueT as $keyD => $valueD) {
                                    $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow($col, $row, $valueD);
                                    $col = $col + 1;
                                }
                            }
                        }else{
                            $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow($col, $row, $valueT);
                            $col = $col + 1;
                        }
                    }
                }
                $row = $row + 1;
                $col = 0;
            }
        }
    }

    private function _getSheetKwAvg($sheetIndex = 2, $data = array()){
        if(count($data) > 0){
            $col = 0;
            $row = 3;
            foreach ($data as $keyD => $valueD) {
                $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow($col, $row, $keyD);
                $col = $col + 1;
                if(count($valueD) > 0){
                    foreach ($valueD as $keyT => $valueT) {
                        if($keyT == 'G' || $keyT == 'D'){
                            if(count($valueT) > 0){
                                foreach ($valueT as $keyD => $valueD) {
                                    $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow($col, $row, $valueD);
                                    $col = $col + 1;
                                }
                            }
                        }else{
                            $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow($col, $row, $valueT);
                            $col = $col + 1;
                        }
                    }
                }
                $row = $row + 1;
                $col = 0;
            }
        }
    }

    private function _getSheetKwhCompressorReading($sheetIndex = 3, $data = array()){
        if(count($data) > 0){
            $col = 0;
            $row = 3;
            foreach ($data as $keyD => $valueD) {
                $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow($col, $row, $keyD);
                $col = $col + 1;
                if(count($valueD) > 0){
                    foreach ($valueD as $keyT => $valueT) {
                        $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow($col, $row, $valueT);
                        $col = $col + 1;
                    }
                }
                $row = $row + 1;
                $col = 0;
            }
        }
    }

    private function _getSheetKwhCompressorConsumed($sheetIndex = 4, $data = array()){
        if(count($data) > 0){
            $col = 0;
            $row = 3;
            foreach ($data as $keyD => $valueD) {
                $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow($col, $row, $keyD);
                $col = $col + 1;
                if(count($valueD) > 0){
                    foreach ($valueD as $keyT => $valueT) {
                        $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow($col, $row, $valueT);
                        $col = $col + 1;
                    }
                }
                $row = $row + 1;
                $col = 0;
            }
        }
    }

    private function _setColor($cell = '', $color = '', $textBold = false, $tetSize = '11', $textColor = '000000'){
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
                                        ),
                                        'font'  => array(
                                            'bold'  => $textBold,
                                            'color' => array('rgb' => $textColor),
                                            'size'  => $tetSize,
                                            'name'  => 'Verdana'
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
        $this->objExcel->getProperties()->setCreator("WIL ETV")
                                     ->setLastModifiedBy("WIL ETV")
                                     ->setTitle("EMS report")
                                     ->setSubject("EMS report")
                                     ->setDescription("EMS report.")/*
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
