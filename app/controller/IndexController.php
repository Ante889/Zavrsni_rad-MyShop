<?php 


class IndexController extends Controller
{

    private $path = 'public'. DIRECTORY_SEPARATOR ;


    public function index(array $parameters=[])
    {

        $categoriesClass = new Categories;
        $categories = $categoriesClass -> selectAll();
        $ProductsClass = new Products;
        foreach($categories as $key){
            $ProductsClass->where = $key->id;
            $ProductsInCategory[$key->name] = count($ProductsClass->select('category'));
        }
        $ProductsClass = new Products;
        if(isset($parameters[0]))
        {
            $ProductsClass->where = $parameters[0];
            $Products = $ProductsClass-> select('category');
        }else{
            $Products = $ProductsClass-> selectAll();
            
        }

        $this -> view -> render($this->path.'index',[
            'ProductsInCategory' => $ProductsInCategory,
            'categories' => $categories,
            'products' => $Products
        ]);
    }
    

    public function error(array $parameters=[])
    {
        $this -> view -> render($this->path.'error');
    }
}