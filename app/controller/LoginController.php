<?php

class LoginController extends Controller
{

    public function index(array $parameters=[]){

        $SuccessMsg='';
        $errors= [
            'email' => '',
            'password' => ''
        ];
        isset($_POST['email'])? $email = trim($_POST['email']) : $email= "";
        isset($_POST['password'])? $password = trim($_POST['password']) : $password= "";
        isset($_POST['checkbox'])? $checkbox = trim($_POST['checkbox']) : $checkbox= "";


        if(!empty(userhelper::LoginWithCookie())){
            $_SESSION['User'] = userhelper::LoginWithCookie();
            userhelper::setRemembermeToken($_SESSION['User']->email);
            $IndexController = new IndexController;
            $IndexController-> index();
            return;
        }
   

        if(isset($_POST['submit']) || userhelper::LoginWithCookie()){

            $errors = userhelper::loginErrors($email,$password,$errors);
            
            if(empty($errors['email']) && empty($errors['password'])){
                if($checkbox == 1){
                    userhelper::setRemembermeToken($email);
                }
                $createUser = New Users; 
                $createUser -> where = $email;
                $result = $createUser -> select('email')[0];
                unset($result -> password);
                unset($result -> confirm_email_token);
                unset($result -> reset_password_token);
                unset($result -> rememberme_token);
                $_SESSION['User'] = $result;
                $IndexController = new IndexController;
                $IndexController-> index();
                return;
            }
        }

        $this -> view -> render('login/login',[
            'errors' => $errors,
            'returnField' => [
                'email' => $email, 
                'password' => $password
              ]
        ]);
   
    }
    
    public function register(array $parameters=[]){
    
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
            
            $errors['name'] = userhelper::nameError($name);
            $errors['lastname'] = userhelper::nameError($lastname);
            $errors['email'] = userhelper::emailError($email);            
            $errors['password'] = userhelper::passwordError($password,$confirmPassword);
    
            //Create user
    
            if(empty($errors['name']) && empty($errors['lastname']) && empty($errors['email']) && empty($errors['password'])){
                $User = new Users;
                $User -> name = $name;
                $User -> lastname = $lastname;
                $User -> password =  password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                $User -> email = strtolower($email);
                $User -> register_time = time();
                $User -> role = 'user';
                $User -> Create();
                $SuccessMsg= $name. ' your account has been successfully created';
            }
        }
    
        $this -> view -> render('login/register',[
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
    public function logout(array $parameters=[])
    {
        $token = bin2hex(random_bytes(16));
        $UserLogout = new Users;
        $UserLogout -> rememberme_token= $token;
        $UserLogout -> where = $_SESSION['User']->id;
        $UserLogout -> update('id');
        unset($_SESSION['User']);
        $this->index();

    }
    public function forgotPassword(array $parameters=[])
    {

        $Msg='';
        if(isset($_POST['submit']))
        {
            isset($_POST['email']) ?$email = strtolower(trim($_POST['email'])) : $email = '';
            $Msg= userhelper::forgotPassword($email);
        }
        $this -> view -> render('login/forgotPassword',[

            'Msg' => $Msg
        ]);
    }

    public function resetPassword(array $parameters=[])
    {

        $SuccessMsg='';
        $errors='';

        if(isset($parameters[0])){
            if(isset($_POST['submit'])){
            isset($_POST['password']) ? $password = trim($_POST['password']) : $password = '';
            isset($_POST['confirmPassword']) ? $confirmPassword = trim($_POST['confirmPassword']) : $confirmPassword = '';
            $errors = userhelper::passwordError($password, $confirmPassword);
            if(empty($errors)){

                $resetPassword = new Users;
                $resetPassword -> password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                $resetPassword -> reset_password_token = 'empty';
                $resetPassword -> where = trim($parameters[0]);
                if($resetPassword -> update('reset_password_token')){
                    $SuccessMsg='Password updated. Login now';
                }else{
                    $errors= "Token dose not exists";
                    $IndexController = new IndexController;
                    $IndexController-> error();
                    return;
                    
                }
                
                
            }
        }
        }else{
                $IndexController = new IndexController;
                $IndexController-> index();
                return;
        }
        $this -> view -> render('login/resetPassword',[

            'SuccessMsg' => $SuccessMsg,
            'Errors' => $errors,
            'token' => $parameters[0]

        ]);
    }

}
