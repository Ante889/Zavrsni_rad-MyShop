<?php 


class AdminController extends AuthorizationController
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

    public function index(array $parameters=[])
    {
        $this -> view -> render($this->path.'adminTemplate');
    }

    public function categories()
    {

        $categories = Categories::selectAll();
        $this -> view -> render($this->path.'adminCategories');
    }
}