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


        if(isset(userhelper::LoginWithCookie()[0])){
            $_SESSION['User'] = userhelper::LoginWithCookie()[0];
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
                $_SESSION['User'] = Login::select('users',['id','name','lastname','role','email','register_time'],'email',['where' => $email])[0];
                $IndexController = new IndexController;
                $IndexController-> index();
                return;
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
                Login::create('users',[
                    'name' => $name,
                    'lastname' => $lastname,
                    'password' => password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]),
                    'email' => strtolower($email),
                    'register_time' => time(),
                    'role' => 'user'
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
    public function logout(array $parameters=[])
    {
        $token = bin2hex(random_bytes(16));
        Login:: update('users','email',[
            'rememberme_token' => $token,
            'where' => $_SESSION['User']->email
        ]);
        unset($_SESSION['email']);
        unset($_SESSION['role']);
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
        $this -> view -> render('forgotPassword',[

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
                Login::update('users','reset_password_token',[
                    'password' => password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]),
                    'reset_password_token' => '',
                    'where' => trim($parameters[0]),
                ]);
                $SuccessMsg='Password updated. Login now';
            }
        }
        }else{
                $IndexController = new IndexController;
                $IndexController-> index();
                return;
        }
        $this -> view -> render('resetPassword',[

            'SuccessMsg' => $SuccessMsg,
            'Errors' => $errors,
            'token' => $parameters[0]

        ]);
    }

}
