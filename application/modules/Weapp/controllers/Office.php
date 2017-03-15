<?php
defined('BASE_PATH') OR exit('No direct script access allowed');

class OfficeController extends Yaf_Controller_Abstract{
    public function indexAction(){
        $data = array();
        $data['title'] = 'office处理';
        $data['class'] = 'office';

        $this->getView()->assign('data', $data);
    }
    
    public function parseExcelAction(){

        if(empty($_FILES)){
            echo '<a href="/weapp/office/index">请上传excel文件</a>';
            return false;
        }
        
        $config['upload_path']      = BASE_PATH .'/upload/excel/';
        $config['allowed_types']    = 'xls|xlsx';
        $config['max_size']     = 3072;

        $fileName = @md5_file($_FILES['file']['tmp_name']);
        $tmp = explode('.', basename($_FILES['file']['name']));
        $ext = array_pop($tmp);
        $config['file_name'] = $fileName . '.'. $ext;


        $upload = new Upload($config);
        if (!$upload->do_upload('file')){
            $data = array();
            $data['rtn'] = 999;
            $data['err_msg'] = $upload->display_errors('', '');
            
            $response = new Yaf_Response_Http();
            $response->setHeader('Content-Type', 'text/json');
            $response->setBody(json_encode($data));
            $response->response();
            return false;
        }
        
        try{
            $PHPExcel = PHPExcel_IOFactory::load(BASE_PATH .'/upload/excel/'. $config['file_name']); // 载入excel文件
        }catch(Exception $e){
            try{
                $reader = PHPExcel_IOFactory::createReader('Excel5'); //设置以Excel5格式(Excel97-2003工作簿)
                $PHPExcel = $reader->load(BASE_PATH .'/upload/excel/'. $config['file_name']); // 载入excel文件
            }catch(Exception $e){
                try{
                    $reader = PHPExcel_IOFactory::createReader('Excel2007'); //设置以Excel5格式(Excel97-2003工作簿)
                    $PHPExcel = $reader->load(BASE_PATH .'/upload/excel/'. $config['file_name']); // 载入excel文件
                }catch(Exception $e){
                    $response = new Yaf_Response_Http();
                    $response->setBody($e->getMessage());
                    $response->response();
                    return false;
                }
            }
        }
        
        $sheet = $PHPExcel->getSheet(0); // 读取第一個工作表
        $rowAndColumn = $sheet->getHighestRowAndColumn();
        $highestRow = $rowAndColumn['row'];// 取得总行数
        $highestColumm= PHPExcel_Cell::columnIndexFromString($rowAndColumn['column']);  // 取得总列数 字母列转换为数字列 如:AA变为27
            
        /** 循环读取每个单元格的数据 */
        $data = array('title'=>'解析excel', 'class'=>'office');
        $colname = array();
        $tbody = array();
        for ($row = 1; $row <= $highestRow; $row++){//行数是以第1行开始
            $tmp = array();
            for ($column = 0; $column < $highestColumm; $column++) {//列数是以第0列开始
                $cell = $sheet->getCellByColumnAndRow($column, $row);
                if($row === 1){
                    $value = $cell->getValue();
                    $colname[] = array('title'=>$value, 'field'=>$value, 'width'=>'50px');
                    continue;
                }
                
                if($cell->hasHyperlink()){
                    $tmp[$colname[$column]['field']] = '<a href="'. $cell->getHyperlink()->getUrl() .'" target="_blank">'.$cell->getValue().'</a>';
                }else{
                    $tmp[$colname[$column]['field']] = $cell->getValue();
                }
            }
            
            if($row === 1){
                $data['thead'] = $colname;
                continue;
            }
            $tbody[] = $tmp;
        }

        foreach ($sheet->getDrawingCollection() as $k => $drawing) {
            if(!method_exists($drawing, 'getMimeType')){//导入xlsx时，$drawing是PHPExcel_Worksheet_Drawing的实例，不支持上传图片；导入xls时，$drawing是PHPExcel_Worksheet_MemoryDrawing的实例，支持上传图片
                break;
            }
            
            list($column, $row) = PHPExcel_Cell::coordinateFromString($drawing->getCoordinates());
            $column = PHPExcel_Cell::columnIndexFromString($column)-1;//获取所在列号

            $data['thead'][$column] = array_merge($data['thead'][$column], array('template'=>'<a #if(data.'.$data['thead'][$column]['title'].'){# href="#: data.'.$data['thead'][$column]['title'].' #" data-lightbox="img" data-title="图片预览" #}# ><img #if(data.'.$data['thead'][$column]['title'].'){# src="#: data.'.$data['thead'][$column]['title'].' #" #}else{# src="http://placeholdit.imgix.net/~text?txtsize=14&txt=%E5%9B%BE%E8%A3%82%E4%BA%86&w=17&h=17&txttrack=0" #}# width="17" height="17" onerror="http://placeholdit.imgix.net/~text?txtsize=14&txt=%E5%9B%BE%E8%A3%82%E4%BA%86&w=17&h=17&txttrack=0"/></a>'));
            
            $headerCell = $sheet->getCellByColumnAndRow($column, 1);
            
            $fieldName = $headerCell->getValue();
            $filename = $drawing->getIndexedFilename(); //文件名
            $imageFilePath = BASE_PATH .'/upload/image/'.$row .'_'. $column .'_'. $filename;

            switch ($drawing->getMimeType()){//处理图片格式
                case 'image/jpp':
                case 'image/jpeg':
                    imagejpeg($drawing->getImageResource(), $imageFilePath);
                    break;
                case 'image/gif':
                    imagegif($drawing->getImageResource(), $imageFilePath);
                    break;
                case 'image/png':
                    imagepng($drawing->getImageResource(), $imageFilePath);
                    break;
            }
            
            $tbody[$row-2][$fieldName] = '/upload/image/'.$row .'_'. $column .'_'.$filename;
        }

        $data ['tbody'] = $tbody;
        $this->getView()->assign('data', $data);
    }
    
    public function parseWordAction(){
        
        return false;
    }
}