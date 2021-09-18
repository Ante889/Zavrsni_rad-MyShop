<?php 


class IndexController extends Controller
{

    private $path = 'public'. DIRECTORY_SEPARATOR ;


    public function index(array $parameters=[])
    {   
        $bigTitle = 'BOOKS';
        $categoriesClass = new Categories;
        $Categories = $categoriesClass -> selectAll();
        $ProductsClass = new Products;
        $displayCategory=null;
        foreach($Categories as $key){
            if(isset($parameters[0]) && $key -> id === $parameters[0]){
                $bigTitle=$key->name;
            }
            $ProductsClass->where = $key->id;
            $ProductsInCategory[$key->name] = count($ProductsClass->select('category'));
        }
        $ProductsClass = new Products;
        if(isset($parameters[0]))
        {
            $ProductsClass->where = $parameters[0];
            $Products = $ProductsClass-> select('category');
        }elseif(!empty($_GET['search'])){
            $bigTitle = 'Search result - ' . $_GET['search'];
            $Products = $ProductsClass-> selectAllLike('%'.trim($_GET['search']).'%','title');
            if(count($Products) == 0){
                $bigTitle = 'No result';
            }
        }else{
            $Products = $ProductsClass-> selectAll();
        }



        $this -> view -> render($this->path.'index',[
            'ProductsInCategory' => $ProductsInCategory,
            'categories' => $Categories,
            'products' => $Products,
            'displayCategory' => $displayCategory,
            'bigTitle' => $bigTitle
        ]);
    }

    public function productpage(array $parameters=[])
    {
        if(!empty($parameters[0]))
        {   $Availability=null;
            $productClass= new Products;
            $productClass -> where = $parameters[0];
            $product=$productClass -> select('id')[0];
            if($product -> quantity > 10){
                $Availability = 'Available';
            }elseif($product -> quantity < 10){
                $Availability = 'less than 10';
            }elseif($product -> quantity == 0){
                $Availability = 'Not available';
            }

        }else{
            $this -> index();
            return;
        }

        $this -> view -> render($this->path.'productpage',[
            'product' => $product,
            'Availability' => $Availability 
        ]
            );
    }
    

    public function error(array $parameters=[])
    {
        $this -> view -> render($this->path.'error');
    }
}