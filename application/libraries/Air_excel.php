<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once APPPATH."/third_party/excel/PHPExcel.php";
 
class Air_excel extends PHPExcel {
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
            }else{
                $this->objExcel->setActiveSheetIndex(0)->setTitle("$value");
                $this->_setHeaderCell(0, $prepData['header']);
                $this->_setMeterData(0, $prepData);
            }
        }
        $this->objExcel->setActiveSheetIndex(0);  
    }

    private function _setHeaderCell($sheetIndex = 0, $header = array()){
        //echo "<pre>";
        if(count($header) > 0){
            foreach ($header as $keyH => $valueH) {
                if(count($valueH) > 0){
                    $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow(0, $keyH, '');
                    foreach ($valueH as $k => $val) {
                        $this->objExcel->getActiveSheet()->mergeCells($val[0]);
                        if($keyH == 1 || $keyH == 2){
                            $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow($val[2], $keyH, $val[1]);
                        }else{
                            $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow($val[2] - 1, $keyH, $val[1]);
                        }
                        $cell = $val[0]; $bckGrndColor = $val[3]; $textBold = false;$textSize = 11;
                        if($keyH == 1 ){
                            $textBold = true; $textSize = 14;
                        }elseif($keyH == 2 ){
                            $textBold = true; $textSize = 11;
                        }elseif($keyH == 3 ){
                            $textBold = true; $textSize = 11;
                        }
                        $this->_setStyle($cell , $bckGrndColor, $textBold, $textSize);
                    }
                }
            }
        }
    }

    private function _getTotalTypeIds(){
        $data['totalGenIds'] = array(1,2,3,4);
        $data['totalConSpinnigIds'] = array();
        $data['totalConTowelIds'] = array(15,16,17,10,12,18,14);
        $data['totalConBedSheetIds'] = array(6,7,8,9);
        $data['totalConUtilityIds'] = array();
        return $data;
    }

    private function _setMeterData($sheetIndex = 0, $data = array()){
        $meterDet = $data['meter']['details'];
        unset($meterDet[0]);
        $genConsumArr = $this->_getTotalTypeIds();
        if(count($data['meter']['data']) > 0){
            $row = 5;
            foreach ($data['meter']['data'] as $keyD => $valueD) {
                $col = 0;
                $totalGen = 0;
                $totalConSpinnig = 0;
                $totalConTowel = 0;
                $totalConBedSheet = 0;
                $totalConUtility = 0;
                $totalConsumption = 0;
                $totalGenMinusCon = 0;
                $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow($col, $row, $keyD);
                foreach ($meterDet as $keyDe => $valueDe) {
                    if($keyDe != 0){
                        $cellVal = 0;
                        if($valueDe['meter_id'] == 0){
                            $cellVal = 0;
                        }elseif(isset($valueD[$valueDe['meter_id']])){
                            if($valueD[$valueDe['meter_id']] > 0){
                                $cellVal = $valueD[$valueDe['meter_id']];
                            }
                            if(in_array($valueDe['meter_id'], $genConsumArr['totalGenIds'])){
                                $totalGen = $totalGen + $cellVal;
                            }
                            if(in_array($valueDe['meter_id'], $genConsumArr['totalConSpinnigIds'])){
                                $totalConSpinnig = $totalConSpinnig + $cellVal;
                            }
                            if(in_array($valueDe['meter_id'], $genConsumArr['totalConTowelIds'])){
                                $totalConTowel = $totalConTowel + $cellVal;
                            }
                            if(in_array($valueDe['meter_id'], $genConsumArr['totalConBedSheetIds'])){
                                $totalConBedSheet = $totalConBedSheet + $cellVal;
                            }
                            if(in_array($valueDe['meter_id'], $genConsumArr['totalConUtilityIds'])){
                                $totalConUtility = $totalConUtility + $cellVal;
                            }
                        }
                        if($keyDe == 12){
                            $cellVal = $totalGen;
                        }
                        if($keyDe == 17){
                            $cellVal = $totalConSpinnig;
                        }
                        if($keyDe == 35){
                            $cellVal = $totalConTowel;
                        }
                        if($keyDe == 49){
                            $cellVal = $totalConBedSheet;
                        }
                        if($keyDe == 56){
                            $cellVal = $totalConUtility;
                        }
                        if($keyDe == 57){
                            $cellVal = $totalConsumption = $totalConSpinnig + $totalConTowel + $totalConBedSheet + $totalConUtility;
                        }
                        if($keyDe == 58){
                            $cellVal = $totalGenMinusCon = $totalGen - $totalConsumption;
                        }
                        $col++;
                        $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow($col, $row, $cellVal);
                    }
                    if($totalGenMinusCon  < 0){
                        $this->_setStyle('BG'.$row.':BG'.$row , 'FFFFFF', false, 11, 'FF0000');
                    }else{
                        $this->_setStyle('A'.$row.':BG'.$row , 'FFFFFF', false, 11);
                    }
                    
                }
                $row++;
            }
        }
    }
    

    private function _setStyle($cell = '', $backGrndColor = '', $textBold = false, $tetSize = '10', $textColor = '000000'){
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
                                            'color' => array('rgb' => $backGrndColor)
                                        ),
                                        'font'  => array(
                                            'bold'  => $textBold,
                                            'color' => array('rgb' => $textColor),
                                            'size'  => $tetSize,
                                            'name'  => 'Verdana'
                                        )
                            ));
    }

    private function _setColorWithBold($cell = '', $color = ''){
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
                                            'bold'  => true,
                                            /*'color' => array('rgb' => 'FF0000'),*/
                                            'size'  => 12,
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
                                     ->setTitle("AIR Report")
                                     ->setSubject("AIR Report")
                                     ->setDescription("AIR Report.")/*
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
