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

            $errors = loginErrors($email,$password,$errors);
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
            
            $errors['name'] = nameError($name);
            $errors['lastname'] = nameError($lastname);
            $errors['email'] = emailError($email);            
            $errors['password'] = passwordError($password,$confirmPassword);
    
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

