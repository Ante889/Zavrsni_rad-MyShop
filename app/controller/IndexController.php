<?php 


class IndexController extends Controller
{

    public function index(array $parameters=[])
    {
        $this -> view -> render('index',$parameters); 
    }


}