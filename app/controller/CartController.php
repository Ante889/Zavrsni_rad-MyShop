<?php 


class CartController extends AuthorizationController
{

    private $path = 'public'. DIRECTORY_SEPARATOR ;
    private $error ="";



    public function index(array $parameters=[])
    { 
        $this -> view -> render($this->path.'cart');
    }
}