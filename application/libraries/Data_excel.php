<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once APPPATH."/third_party/excel/PHPExcel.php";
 
class Data_excel extends PHPExcel {
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
                $this->_setHeaderCell($key, $prepData['header']);
                $this->_setMeterData($key, $prepData['data'], $prepData['header']);
            }else{
                $this->objExcel->setActiveSheetIndex(0)->setTitle("$value");
                $this->_setHeaderCell(0, $prepData['header']);
                $this->_setMeterData(0, $prepData['data'], $prepData['header']);
            }
        }
        $this->objExcel->setActiveSheetIndex(0);  
    }

    private function _setHeaderCell($sheetIndex = 0, $header = array()){
        $row = 1;
        if(count($header) > 0){
            foreach ($header as $keyH => $valueH) {
                $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow($keyH, $row, $valueH[1]);
                $cell = $valueH[0]; $bckGrndColor = $valueH[3]; $textBold = true; $textSize = 12;
                $this->_setStyle($cell , $bckGrndColor, $textBold, $textSize);
            }
        }
    }

    private function _setMeterData($sheetIndex = 0, $data = array(), $header = array()){
        if(count($data[$sheetIndex]) > 0 && count($header) > 0){
            $row = 2;
            foreach ($data[$sheetIndex] as $keyD => $valueD) {
                $col = 0;
                foreach ($header as $keyH => $valueH) {
                    $this->objExcel->setActiveSheetIndex($sheetIndex)->setCellValueByColumnAndRow($col, $row, $valueD[$valueH[4]]);
                    if($valueD[$valueH[4]] < 0){
                        $column = PHPExcel_Cell::stringFromColumnIndex($col);
                        $cell = $column.$row; $bckGrndColor = 'FFFFFF'; $textBold = true; $textSize = 13;$textColor = 'FF0000';
                        $this->_setStyle($cell , $bckGrndColor, $textBold, $textSize, $textColor);
                    }
                    $col = $col + 1;
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
                                     ->setTitle("STEAM Report")
                                     ->setSubject("STEAM Report")
                                     ->setDescription("STEAM Report.")/*
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
