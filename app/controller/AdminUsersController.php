<?php 


class AdminUsersController extends Controller
{
    private $path = 'admin'. DIRECTORY_SEPARATOR ;

    public function __construct()
    {
        parent::__construct();
        if(!Request::isAdmin())
        {
            $indexController = new IndexController;
            $indexController -> index();
            exit;
        }
    }

    public function index()
    {
        $usersClass = New Users;
        if(isset($_POST['submit']) && !empty($_POST['email']))
        {
            $users= $usersClass -> selectAllLike("%".trim($_POST['email'])."%",'email');
        }else
        {
            $users = $usersClass -> selectAll();
        }  
        $this -> view -> render($this->path.'adminUsers',[
            'users' => $users
        ]);
    }

    public function createUsers()
    {
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
                $UsersClass = new Users;
                $UsersClass -> name = $name;
                $UsersClass -> lastname = $lastname;
                $UsersClass -> password =  password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                $UsersClass -> email = strtolower($email);
                $UsersClass -> register_time = time();
                $UsersClass -> role = 'user';
                $UsersClass -> Create();
                $SuccessMsg= 'Account has been successfully created';
            }
        }
        $this -> view -> render($this->path.'adminUsersAdd',[
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


    public function updateUsers(array $parameters=[])
    {
        $SuccessMsg='';     
        $UsersClass = new Users;
        $UsersClass -> where= $parameters[0];
        $Fields = $UsersClass-> select('id')[0];
    
        if(isset($_POST['submit'])){

        isset($_POST['name']) ? $name = trim($_POST['name']) : $name = '';
        isset($_POST['role']) ? $role = trim($_POST['role']) : $role = '';
        isset($_POST['lastname']) ? $lastname = trim($_POST['lastname']) : $lastname = '';
        isset($_POST['email']) ?$email = strtolower(trim($_POST['email'])) : $email = '';
        isset($_POST['password']) ? $password = trim($_POST['password']) : $password = '';

        $UsersClass -> name = $name;
        $UsersClass -> lastname = $lastname;
        $UsersClass -> password =  password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $UsersClass -> email = strtolower($email);
        $UsersClass -> register_time = time();
        $UsersClass -> role = $role;
        $UsersClass -> where = $parameters[0];
        $UsersClass -> update('id');
        $SuccessMsg= 'Account has been successfully created';
        Request::redirect(App::config('url'). 'AdminUsers/updateUsers/'. $parameters[0]);
        }
        $this -> view -> render($this->path.'adminUsersUpdate',[
            'succesMsg' => $SuccessMsg,
            'returnField' => [
              'id' => $Fields -> id,
              'name' => $Fields -> name, 
              'lastname' => $Fields ->lastname,
              'role' => $Fields ->role,
              'email' => $Fields ->email, 
              'password' => $Fields ->password, 
            ]
        ]);
    }



    public function deleteUsers(array $parameters=[])
    {
        $usersClass = New users;
        $usersClass -> where = $parameters[0];
        $usersClass -> delete('id');
        Request::redirect(App::config('url'). 'AdminUsers');
    }

    public function setUser(array $parameters=[])
    {
        $usersClass = New Users;
        $usersClass -> role = 'user';
        $usersClass -> where = $parameters[0];
        $usersClass -> Update('id');
        Request::redirect(App::config('url'). 'AdminUsers');
    }

    public function setAdmin(array $parameters=[])
    {
        $usersClass = New Users;
        $usersClass -> role = 'admin';
        $usersClass -> where = $parameters[0];
        $usersClass -> Update('id');
        Request::redirect(App::config('url'). 'AdminUsers');
    }
}