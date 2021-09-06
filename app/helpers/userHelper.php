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
        if(count(Login::select('users',['email'],'email',['where' => $email])) > 0){
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

        $checkPassword = Login::select('users',['password'],'email',['where' => $email]);
        isset($checkPassword) ? $checkPassword = $checkPassword[0]->password : $checkPassword = '';
        if(empty($email)){
            $errors['email'] = 'Field cannot be empty';
        }else if(count(Login::select('users',['email'],'email',['where' => $email])) == 0){
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
        Login:: update('users','email',[
            'rememberme_token' => $token,
            'where' => $email
        ]);

    }

    public static function LoginWithCookie(){

        if(isset($_COOKIE['CookieT/'])){
            return Login::select('users',['id','name','lastname','role','email','register_time'],'rememberme_token',['where' => $_COOKIE['CookieT/']]);
        }    
    }

    public static function forgotPassword($email)
    {

        if(count(Login::select('users',['email'],'email',['where' => $email])) > 0){
            $token=bin2hex(random_bytes(52));
            Login::update('users','email',[
                'reset_password_token' => $token,
                'where' => $email
            ]);

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
}