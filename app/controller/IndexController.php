<?php 


class IndexController extends Controller
{

    public function index(array $parameters=[])
    {
        $this -> view -> render('index');
    }

    public function login(array $parraymeters=[]){

        $this -> view -> render('login');
        if(isset($_POST['submit'])){
            echo $_POST['email'];
            echo $_POST['password'];
            echo $_POST['checkbox'];
        }

    }

    public function register(array $parraymeters=[]){

        $errors= [
            'name' => '',
            'lastname'=> '',
            'email' => '',
            'password' => ''
        ];

        if(isset($_POST['submit'])){

            $name = $_POST['name'];
            $lastname = $_POST['lastname'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirmPassword'];
            
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
            if(count(Register::checkEmail(['email'=>$email])) > 0){
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
                Register::createUser([
                    'name' => $name,
                    'lastname' => $lastname,
                    'password' => $password,
                    'email' => strtolower($email),
                    'register_time' => time()
                ]);
                $SuccessMsg= $name. ' your account has been successfully created';
            }else{
                $SuccessMsg='';
            }
        }

        $this -> view -> render('register',[
            'errors' => $errors,
            'succesMsg' => $SuccessMsg
        ]);

    }


    public function error(array $parameters=[])
    {
        $class = str_replace('Controller','',$parameters[0]);
        $this -> view -> render('error',[
            'class' => $class,
            'method'=> $parameters[1]
        ]);
    }


}