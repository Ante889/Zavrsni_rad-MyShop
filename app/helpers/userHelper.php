<?php

function isLogin(string $string, string $true){

    if(isset($_SESSION['email'])){
        header('Location:'. App::config('url') . $true);
    }

}

function nameError(string $string){
    if(empty($string)){
        return 'Field cannot be empty';
    }else if(strlen($string) < 2 ){
        return 'name must contain at least 2 characters';
    }else if(preg_match('~[0-9]+~',$string)){
        return  'Only letters are allowed';
    }
    return '';
}

function emailError(string $email){
    if(count(Login::checkEmail(['email'=>$email])) > 0){
        return 'Email exists';
    }else if(empty($email)){
        return 'Field cannot be empty';
    }
    return '';
}

function passwordError(string $password, string $confirmPassword){

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

function loginErrors(string $email,string $password, array $errors){
    
    isset(Login::checkPassword(['email' => $email])[0]) ? $resultPassword = Login::checkPassword(['email' => $email])[0]->password : $resultPassword = '';
    if(empty($email)){
        $errors['email'] = 'Field cannot be empty';
    }else if(count(Login::checkEmail(['email' => $email])) == 0){
        $errors['email'] = 'Email does not exist';
    }else if(empty($password)){
        $errors['password'] = 'Field cannot be empty';
    }else if (!password_verify($password,$resultPassword)){
        $errors['password'] = 'Wrong password';
    }

    return $errors;
}

function setRemembermeToken($email){

    $token = bin2hex(random_bytes(52));
    setcookie('CookieT/', $token, time() + 3600 * 30);
    Login:: SetRemembermeToken([
        'rememberme_token' => $token,
        'email' => $email
    ]);

}

function LoginWithCookie(){

    if(isset($_COOKIE['CookieT/'])){
        return Login::GetRemembermeToken(['rememberme_token' => $_COOKIE['CookieT/']]);
    }    
}

function forgotPassword($email)
{

    if(count(Login::checkEmail(['email' => $email])) > 0){
        $token=bin2hex(random_bytes(52));
        Login::Setreset_password_token([
            'email' => $email, 
            'reset_password_token' => $token
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