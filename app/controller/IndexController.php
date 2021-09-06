<?php 


class IndexController extends Controller
{

    public function index(array $parameters=[])
    {
        $this -> view -> render('index');
       Login::delete('users', 'id', '130');
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