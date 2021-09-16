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
}