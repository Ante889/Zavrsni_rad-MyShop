<?php


class producthelper{

    public static function basicError(string $string){
        if(empty($string)){
            return 'Field cannot be empty';
        }else if(strlen($string) < 2 ){
            return 'Field must contain at least 2 characters';
        }
        return '';
    }

    public static function discountError(string $string){

        if($string === '%'){
            return '';
        }

        $int=(int)$string;
        if(empty($string)){
            return 'Field cannot be empty';
        }else if(!preg_match('~[0-9]+~',$string)){
            return  'Only numbers are allowed';
        }else if($int < 0){
            return  'You cannot enter negative numbers';
        }else if($int > 100){
            return  '100 percent is the maximum';
        }
        return '';
    }

    public static function categoryError($categoryName)
    {

        if(empty($categoryName) && $categoryName === ""){
            return 'Field cannot be empty';
        }
    }

    public static function numbersError(string $string){
        $int=(int)$string;
        if(empty($string)){
            return 'Field cannot be empty';
        }else if(!preg_match('~[0-9]+~',$string)){
            return  'Only numbers are allowed';
        }else if($int < 0){
            return  'You cannot enter negative numbers';
        }
        return '';
    }

    public static function photoError($file)
    {
        $Errors= '';
	    $Upload_error_array = [
		0 => "There is no error, the file uploaded with success.",
		1 => "The uploaded file exceeds the upload_max_filesize",
		2 => "The uploaded file exceeds the MAX_FILE_SIZE",
		3 => "The uploaded file was only partially uploaded.",
		4 => "No file was uploaded.",
		6 => "Missing a temporary folder.",
		7 => "Failed to write file to disk.",
		8 => "A PHP extension stopped the file upload." ];
	 	if(empty($file) || !is_array($file) || !$file ){
	 		$Errors = "There was no file uploaded here";
		}
		else if ($file['error'] !=0) {
	 		$Errors = $Upload_error_array[$file['error']];
	 	}
        return $Errors;
        
    }

    public static function floatDiscount($discount){
        if(strlen($discount) == 1){
            return '0.0'.$discount;
        }else{
            return '0.'.$discount;
        }
    }
}