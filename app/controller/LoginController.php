<?php

class LoginController extends Controller
{
    private $path = 'login'. DIRECTORY_SEPARATOR ;

    public function index(){
        userhelper::RedirectIfLogin();

        $SuccessMsg='';
        $errors= [
            'email' => '',
            'password' => '',
            'alert' => ''
        ];
        $email = strtolower(Request::issetTrim('email'));
        $password = Request::issetTrim('password');
        $checkbox = Request::issetTrim('checkbox');
    
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
                $UsersClass = New Users; 
                $UsersClass -> where = $email;
                $result = $UsersClass -> select('email')[0];
                unset($result -> password);
                unset($result -> confirm_email_token);
                unset($result -> reset_password_token);
                unset($result -> rememberme_token);
                $_SESSION['User'] = $result;
                $_SESSION['Cart'] = [];
                Request::redirect(App::config('url'));
                return;
            }
        }

        $this -> view -> render($this->path . '/login',[
            'errors' => $errors,
            'returnField' => [
                'email' => $email, 
                'password' => $password
              ]
        ]);
   
    }
    
    public function register(){

        userhelper::RedirectIfLogin();
        $SuccessMsg='';
        $errors= [
            'name' => '',
            'lastname'=> '',
            'email' => '',
            'password' => ''
        ];
        $name = Request::issetTrim('name');
        $lastname = Request::issetTrim('lastname');
        $email = strtolower(Request::issetTrim('email'));
        $password = Request::issetTrim('password');
        $confirmPassword = Request::issetTrim('confirmPassword');
    
        if(isset($_POST['submit'])){
            
            $errors['name'] = userhelper::nameError($name);
            $errors['lastname'] = userhelper::nameError($lastname);
            $errors['email'] = userhelper::emailError($email);            
            $errors['password'] = userhelper::passwordError($password,$confirmPassword);
    
            //Create user
    
            if(empty($errors['name']) && empty($errors['lastname']) && empty($errors['email']) && empty($errors['password'])){
                $UsersClass = new Users;
                $UsersClass -> name = $name;
                $UsersClass -> lastname = $lastname;
                $UsersClass -> password =  password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                $UsersClass -> email = strtolower($email);
                $UsersClass -> register_time = time();
                $UsersClass -> role = 'user';
                $UsersClass -> Create();
                $SuccessMsg= $name. ' your account has been successfully created';
            }
        }
    
        $this -> view -> render($this->path .'/register',[
            'errors' => $errors,
            'succesMsg' => $SuccessMsg,
            'returnField' => [
              'name' => $name, 
              'lastname' => $lastname,
              'email' => $email, 
              'password' => $password, 
              'confirmPassword' => $confirmPassword, 
              'alert' => ''
            ]
        ]);
    }
    public function logout()
    {
        if(!Request::isLogin())
        {
            $Index = new IndexController;
            $Index -> index();
            return;
        }
        $token = bin2hex(random_bytes(16));
        $UsersClass = new Users;
        $UsersClass -> rememberme_token= $token;
        $UsersClass -> where = $_SESSION['User']->id;
        $UsersClass -> update('id');
        unset($_SESSION['User']);
        unset($_SESSION['Cart']);
        session_destroy();
        $this->index();

    }
    public function forgotPassword()
    {
        userhelper::RedirectIfLogin();
        $Msg='';
        if(isset($_POST['submit']))
        {
            isset($_POST['email']) ?$email = strtolower(trim($_POST['email'])) : $email = '';
            $Msg= userhelper::forgotPassword($email);
        }
        $this -> view -> render($this->path .'/forgotPassword',[

            'Msg' => $Msg
        ]);
    }

    public function resetPassword(array $parameters=[])
    {
        userhelper::RedirectIfLogin();
        $SuccessMsg='';
        $errors='';
        $protection = explode(".",$parameters[0]);
        if(isset($parameters[0]) && strlen($parameters[0]) == 105 && count($protection) ==5){
            if(isset($_POST['submit'])){
                $password = Request::issetTrim('password');
                $confirmPassword = Request::issetTrim('confirmPassword');
            $errors = userhelper::passwordError($password, $confirmPassword);
            if(empty($errors)){

                $UsersClass = new Users;
                $UsersClass -> password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                $UsersClass -> reset_password_token = 'empty';
                $UsersClass -> where = trim($parameters[0]);
                $UsersClass -> update('reset_password_token');
                $SuccessMsg='Password updated. Login now';
            }else{
                $errors= "Token dose not exists";
            }
        }
        }else{
                $IndexController = new IndexController;
                $IndexController-> index();
                return;
        }
        $this -> view -> render($this->path .'/resetPassword',[

            'SuccessMsg' => $SuccessMsg,
            'Errors' => $errors,
            'token' => $parameters[0]

        ]);
    }
}
