<?php
defined('BASE_PATH') OR exit('No direct script access allowed');

class JobController extends Yaf_Controller_Abstract {
    
    public function getPlwAction($expect=''){
        $jobModel = new JobModel();
        if($expect){
            $jobModel->keepPlw($expect);
        }else{
            $db = Database::getInstance();
            
            $query = $db->order_by('id', 'desc')->get('app_pl5');
            $row = $query->num_rows()>0 ? $query->row_array() : array();
            if(!$row){
                for($i=4; $i<=ltrim(date('Y'), '20'); $i++){
                    for($j=1; $j<=360; $j++){
                        $expect = str_pad($i, 2, 0, STR_PAD_LEFT).str_pad($j, 3, 0, STR_PAD_LEFT);
                        $jobModel->keepPlw($expect);
                    }
                }
            }else{
                $jobModel->keepPlw($row['expect']+1);
            }
        }
    }
    
    
    public function getPlsAction($expect=''){
        $jobModel = new JobModel();
        if($expect){
            $jobModel->keepPls($expect);
        }else{
            $db = Database::getInstance();
            
            $query = $db->order_by('id', 'desc')->get('app_pl3');
            $row = $query->num_rows()>0 ? $query->row_array() : array();
            if(!$row){
                for($i=4; $i<=ltrim(date('Y'), '20'); $i++){
                    for($j=1; $j<=360; $j++){
                        $expect = str_pad($i, 2, 0, STR_PAD_LEFT).str_pad($j, 3, 0, STR_PAD_LEFT);
                        $jobModel->keepPls($expect);
                    }
                }
            }else{
                $jobModel->keepPls($row['expect']+1);
            }
        }
    }
    
    
    public function getQxcAction($expect=''){
        $jobModel = new JobModel();
        if($expect){
            $jobModel->keepQxc($expect);
        }else{
            $db = Database::getInstance();
            
            $query = $db->order_by('id', 'desc')->get('app_qxc');
            $row = $query->num_rows()>0 ? $query->row_array() : array();
            if(!$row){
                for($i=9; $i<=ltrim(date('Y'), '20'); $i++){
                    for($j=1; $j<=200; $j++){
                        $expect = str_pad($i, 2, 0, STR_PAD_LEFT).str_pad($j, 3, 0, STR_PAD_LEFT);
                        $jobModel->keepQxc($expect);
                    }
                }
            }else{
                $jobModel->keepQxc(str_pad($row['expect']+1, 5, 0, STR_PAD_LEFT));
            }
        }
    }
    
    
    public function getDltAction($expect=''){
        $jobModel = new JobModel();
        if($expect){
            $jobModel->keepDlt($expect);
        }else{
            $db = Database::getInstance();
            
            $query = $db->order_by('id', 'desc')->get('app_dlt');
            $row = $query->num_rows()>0 ? $query->row_array() : array();
            if(!$row){
                for($i=7; $i<=ltrim(date('Y'), '20'); $i++){
                    for($j=1; $j<=160; $j++){
                        $expect = str_pad($i, 2, 0, STR_PAD_LEFT).str_pad($j, 3, 0, STR_PAD_LEFT);
                        $jobModel->keepDlt($expect);
                    }
                }
            }else{
                $jobModel->keepDlt(str_pad($row['expect']+1, 5, 0, STR_PAD_LEFT));
            }
        }
    }
    
    
    public function get3DAction($expect=''){
        $jobModel = new JobModel();
        if($expect){
            $jobModel->keep3D($expect);
        }else{
            $db = Database::getInstance();
            
            $query = $db->order_by('id', 'desc')->get('app_fc3d');
            $row = $query->num_rows()>0 ? $query->row_array() : array();
            if(!$row){
                for($i=2004; $i<=ltrim(date('Y')); $i++){
                    for($j=1; $j<=360; $j++){
                        $expect = str_pad($i, 4, 0, STR_PAD_LEFT).str_pad($j, 3, 0, STR_PAD_LEFT);
                        $jobModel->keep3D($expect);
                    }
                }
            }else{
                $jobModel->keep3D($row['expect']+1);
            }
        }
    }
    
    
    public function getSsqAction($expect=''){
        $jobModel = new JobModel();
        if($expect){
            $jobModel->keepSsq($expect);
        }else{
            $db = Database::getInstance();
            
            $query = $db->order_by('id', 'desc')->get('app_ssq');
            $row = $query->num_rows()>0 ? $query->row_array() : array();
            if(!$row){
                for($i=3; $i<=ltrim(date('Y'), '20'); $i++){
                    for($j=1; $j<=154; $j++){
                        $expect = str_pad($i, 2, 0, STR_PAD_LEFT).str_pad($j, 3, 0, STR_PAD_LEFT);
                        $jobModel->keepSsq($expect);
                    }
                }
            }else{
                $jobModel->keepSsq(str_pad($row['expect']+1, 5, 0, STR_PAD_LEFT));
            }
        }
    }
    
}
