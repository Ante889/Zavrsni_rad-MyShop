<?php

class LoginController extends Controller
{

    public function index(array $parameters=[]){

        isLogin('email','index');
        $SuccessMsg='';
        $errors= [
            'email' => '',
            'password' => ''
        ];
        isset($_POST['email'])? $email = trim($_POST['email']) : $email= "";
        isset($_POST['password'])? $password = trim($_POST['password']) : $password= "";
        isset($_POST['checkbox'])? $checkbox = trim($_POST['checkbox']) : $checkbox= "";


        if(isset(LoginWithCookie()[0]->email)){
            $_SESSION['email'] = LoginWithCookie()[0]->email;
            $_SESSION['role'] = LoginWithCookie()[0]->role;
            setRemembermeToken(LoginWithCookie()[0]->email);
            header('Location:'. App::config('url').'index');
        }
   

        if(isset($_POST['submit']) || LoginWithCookie()){

            $errors = loginErrors($email,$password,$errors);
            
            if(empty($errors['email']) && empty($errors['password'])){
                if($checkbox == 1){
                    setRemembermeToken($email);
                }
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
    
    public function register(array $parameters=[]){

        isLogin('email','index');
    
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
        Login:: SetRemembermeToken([
            'rememberme_token' => $token,
            'email' => $_SESSION['email']
        ]);

        unset($_SESSION['email']);
        unset($_SESSION['role']);
        header('Location:'. App::config('url'). 'login');
        $this -> view -> render('login');

    }


    public function forgotPassword(array $parameters=[])
    {

        $Msg='';

        if(isset($_POST['submit']))
        {
            isset($_POST['email']) ?$email = strtolower(trim($_POST['email'])) : $email = '';
            $Msg= forgotPassword($email);
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
            $errors = passwordError($password, $confirmPassword);
            if(empty($errors)){
                Login::UpdatePassword([
                    'reset_password_token' => trim($parameters[0]),
                    'password' => password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]),
                    'reset_password_token2' => ''
                ]);
                $SuccessMsg='Password updated. Login now';
            }
        }
        }else{
            header('Location:'. App::config('url').'index');
        }
        $this -> view -> render('resetPassword',[

            'SuccessMsg' => $SuccessMsg,
            'Errors' => $errors,
            'token' => $parameters[0]

        ]);
    }

}
