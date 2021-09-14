<?php 


class AdminProductsController extends Controller
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
        $productsClass = New Products;

        $products = $productsClass -> selectAll();
 
        $this -> view -> render($this->path.'adminProducts',[
            'products' => $products
        ]);
    }
}