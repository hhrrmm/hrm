<?php  
    // file for download 
    $file = 'data/HR_input_file.xlsx';  
	
    header("Expires: 0");  
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");  
    header("Cache-Control: no-store, no-cache, must-revalidate");  
    header("Cache-Control: post-check=0, pre-check=0", false);  
    header("Pragma: no-cache");  
    header("Content-type: application/vnd.ms-excel;charset:UTF-8");  
    header('Content-length: '.strlen($file));      
    // tell file size  
    header('Content-length: '.filesize($file));  
    // set file name  
    header('Content-disposition: attachment; filename='.basename($file));  
	
    readfile($file);  
    // Exit script. So that no useless data is output-ed.  
    exit;  
?>  