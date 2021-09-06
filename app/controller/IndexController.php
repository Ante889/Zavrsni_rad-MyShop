<?php 


class IndexController extends Controller
{

    public function index(array $parameters=[])
    {
        $this -> view -> render('index');

        Login::update('users',[
            'name' => 'name2',
            'lastname' => 'lastname',
            'password' => 'password',
            'email' => 'email',
            'register_time' => time(),
            'role' => 'user',
            'id' => 5
        ]
    );
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