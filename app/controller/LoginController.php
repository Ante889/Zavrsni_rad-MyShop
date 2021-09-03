<?php

class LoginController extends Controller
{

    public function index(array $parraymeters=[]){

        $SuccessMsg='';
        $errors= [
            'email' => '',
            'password' => ''
        ];

        isset($_POST['email'])? $email = $_POST['email'] : $email= "";
        isset($_POST['password'])? $password = $_POST['password'] : $password= "";
        isset($_POST['checkbox'])? $checkbox = $_POST['checkbox'] : $checkbox= "";
        
        if(isset($_POST['submit'])){

            isset(Login::checkPassword(['email' => $email])[0]) ? $resultPassword = Login::checkPassword(['email' => $email])[0]->password : $resultPassword = '';

            //Email & password

            if(empty($email)){
                $errors['email'] = 'Field cannot be empty';
            }else if(count(Login::checkEmail(['email' => $email])) == 0){
                $errors['email'] = 'Email does not exist';
            }else if(empty($password)){
                $errors['password'] = 'Field cannot be empty';
            }else if (!password_verify($password,$resultPassword)){
                $errors['password'] = 'Wrong password';
            }

            //Login

            if(empty($errors['email']) && empty($errors['password'])){
            
                $_SESSION['email'] = $email;
                $_SESSION['role'] = Login::getRole(['email' => $email])[0]->role;
                header('Location:'. App::config('url').'index');

            }

        }

        $this -> view -> render('Login',[
            'errors' => $errors,
            'returnField' => [
                'email' => $email, 
                'password' => $password
              ]
        ]);
   
    }
    
    public function register(array $parraymeters=[]){
    
        $SuccessMsg='';
        $errors= [
            'name' => '',
            'lastname'=> '',
            'email' => '',
            'password' => ''
        ];

        isset($_POST['name']) ? $name = trim($_POST['name']) : $name = '';
        isset($_POST['lastname']) ? $lastname = trim($_POST['lastname']) : $lastname = '';
        isset($_POST['email']) ?$email = strtolower(trim($_POST['email'])) : $email = '';
        isset($_POST['password']) ? $password = trim($_POST['password']) : $password = '';
        isset($_POST['confirmPassword']) ? $confirmPassword = trim($_POST['confirmPassword']) : $confirmPassword = '';

    
        if(isset($_POST['submit'])){
            
            //name
            if(empty($name)){
                $errors['name'] = 'Field cannot be empty';
            }else if(strlen($name) < 2 ){
                $errors['name'] = 'name must contain at least 2 characters';
            }else if(preg_match('~[0-9]+~',$name)){
                $errors['name'] = 'Only letters are allowed';
            }
    
            //lastname
    
            if(empty($lastname)){
                $errors['lastname'] = 'Field cannot be empty';
            }else if(strlen($lastname) < 2 ){
                $errors['lastname'] = 'Lastname must contain at least 2 characters';
            }else if(preg_match('~[0-9]+~',$lastname)){
                $errors['lastname'] = 'Only letters are allowed';
            }
    
            //email
            if(count(Login::checkEmail(['email'=>$email])) > 0){
                $errors['email'] = 'Email exists';
            }else if(empty($email)){
                $errors['email'] = 'Field cannot be empty';
            }
    
            //password
            if(empty($password) || empty($confirmPassword)){
                $errors['password'] = 'Field cannot be empty';
            }else if($password != $confirmPassword){
                $errors['password'] = 'Passwords not same';
            }else if(strlen($password) < 6){
                $errors['password'] = 'Minimum 6 symbols';
            }else if(!preg_match('~[0-9]+~',$password)){
                $errors['password'] = 'One symbol must be a number';
            }
            $passes=false;
            for ($i=0; $i < strlen($password) ; $i++) { 
                if(ctype_upper(substr($password, $i,1))){
                    $passes=true;
                }
            }
            if($passes == false ){
                $errors['password'] = 'One symbol must be a capital letter';
            }
    
            //Create user
    
            if(empty($errors['name']) && empty($errors['lastname']) && empty($errors['email']) && empty($errors['password'])){
                Login::createUser([
                    'name' => $name,
                    'lastname' => $lastname,
                    'password' => password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]),
                    'email' => strtolower($email),
                    'register_time' => time()
                ]);
                $SuccessMsg= $name. ' your account has been successfully created';
            }
        }
    
        $this -> view -> render('register',[
            'errors' => $errors,
            'succesMsg' => $SuccessMsg,
            'returnField' => [
              'name' => $name, 
              'lastname' => $lastname,
              'email' => $email, 
              'password' => $password, 
              'confirmPassword' => $confirmPassword, 
            ]
        ]);
    
    }

}

