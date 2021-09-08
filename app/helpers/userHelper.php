<?php


class userhelper{

    public static function nameError(string $string){
        if(empty($string)){
            return 'Field cannot be empty';
        }else if(strlen($string) < 2 ){
            return 'name must contain at least 2 characters';
        }else if(preg_match('~[0-9]+~',$string)){
            return  'Only letters are allowed';
        }
        return '';
    }

    public static function emailError(string $email){
        $Selectresult = static::shortSelect('Users','email',$email);
        if(!empty($Selectresult)){
            return 'Email exists';
        }else if(empty($email)){
            return 'Field cannot be empty';
        }
        return '';
    }

    public static function passwordError(string $password, string $confirmPassword){

        if(empty($password) || empty($confirmPassword)){
            return 'Field cannot be empty';
        }else if($password != $confirmPassword){
            return 'Passwords not same';
        }else if(strlen($password) < 6){
            return 'Minimum 6 symbols';
        }else if(!preg_match('~[0-9]+~',$password)){
            return 'One symbol must be a number';
        }
        $passes=false;
        for ($i=0; $i < strlen($password) ; $i++) { 
            if(ctype_upper(substr($password, $i,1))){
                $passes=true;
            }
        }
        if($passes == false ){
            return 'One symbol must be a capital letter';
        }
        return '';
    }

    public static function loginErrors(string $email,string $password, array $errors){
        $Selectresult = static::shortSelect('Users','email',$email);
        isset($Selectresult-> password) ? $checkPassword = $Selectresult-> password : $checkPassword = '';
        if(empty($email)){
            $errors['email'] = 'Field cannot be empty';
        }else if(empty($Selectresult -> email)){
            $errors['email'] = 'Email does not exist';
        }else if(empty($password)){
            $errors['password'] = 'Field cannot be empty';
        }else if (!password_verify($password,$checkPassword)){
            $errors['password'] = 'Wrong password';
        }

        return $errors;
    }

    public static function setRemembermeToken($email){

        $token = bin2hex(random_bytes(52));
        setcookie('CookieT/', $token, time() + 3600 * 30);
        $RememberUser=new Users;
        $RememberUser -> rememberme_token = $token;
        $RememberUser -> where = $email;
        $RememberUser -> Update('email');

    }

    public static function LoginWithCookie(){

        if(isset($_COOKIE['CookieT/'])){
            $result = static::shortSelect('Users','rememberme_token',$_COOKIE['CookieT/']);
            unset($result -> password);
            unset($result -> confirm_email_token);
            unset($result -> reset_password_token);
            unset($result -> rememberme_token);
            return $result;
        }    
    }

    public static function forgotPassword($email)
    {
        $Selectresult = static::shortSelect('Users','email',$email);
        if(count($Selectresult) > 0){
            $token=bin2hex(random_bytes(52));
            $forgotPassword = new Users;
            $forgotPassword -> reset_password_token = $token;
            $forgotPassword -> where = $email;
            $forgotPassword -> update('email');

            //Test //// za sad ide u spam

            $to      = $email;
            $subject = 'Reset password'; 
            $message = 'reset password on link'. App::config('url') . 'Login/resetPassword/'. $token;
            $headers = 'From: webmaster@example.com' . "\r\n" .
                'Reply-To: webmaster@example.com' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

            mail($to, $subject, $message, $headers);
            return 'Password updated. Login now';

        }else{
            return "Email does not exists";
        }

    }

    public static function shortSelect($class,$where,$whereParam)
    {
        $instance = new $class;
        $instance -> where = $whereParam;
        if(!empty($instance -> select($where))){
            return $instance -> select($where)[0];
        }
    }
}