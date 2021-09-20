<?php 


class IndexController extends Controller
{

    private $path = 'public'. DIRECTORY_SEPARATOR ;


    public function index(array $parameters=[])
    {   
        $limit = 6;
        $offset = 0;
        $bigTitle = 'BOOKS';
        $categoriesClass = new Categories;
        $Categories = $categoriesClass -> selectAll();
        $ProductsClass = new Products;
        $displayCategory=null;

        //Ako je postavljena stranice postavi limit
        if(!empty($_GET['page'])){
            $offset = ($limit * $_GET['page']) - $limit;
            $page = $_GET['page'];
        }else{
            $page = 1;
        }

        // Prikaži kategorije
        foreach($Categories as $key){
            if(isset($parameters[0]) && $key -> id === $parameters[0]){
                $bigTitle=$key->name;
            }
            $ProductsClass->where = $key->id;
            $ProductsInCategory[$key->name] = count($ProductsClass->select('category'));
        }
        //Prikaži proizvode 
        $ProductsClass = new Products;
        if(isset($parameters[0]))
        {
            $ProductsClass->where = $parameters[0];
            $ProductsNumber = count($ProductsClass -> select('category'));
            $Products = $ProductsClass-> selectLimit('category',$limit,$offset);
            $pathForPager = 'index/index/'.$parameters[0].'?page=';
        }elseif(!empty($_GET['search'])){
            $bigTitle = 'Search result - ' . $_GET['search'];
            $ProductsNumber = count($ProductsClass -> selectAllLike('%'.trim($_GET['search']).'%','title'));
            $Products = $ProductsClass-> selectAllLikeLimit('%'.trim($_GET['search']).'%','title',$limit,$offset);
            $pathForPager = 'index?search='.$_GET['search'].'&page=';
            if(count($Products) == 0){
                $bigTitle = 'No result';
            }
        }else{
            $ProductsNumber = count($ProductsClass -> selectAll());
            $Products = $ProductsClass-> selectAllLimit($limit,$offset);
            $pathForPager = 'index/index?page=';
        }

        //Slideshow
        $slideshowClass = new Slideshow;
        $slideshowClass -> where = '1';
        $visible = $slideshowClass -> select('visible'); 

        $this -> view -> render($this->path.'index',[
            'ProductsInCategory' => $ProductsInCategory,
            'categories' => $Categories,
            'products' => $Products,
            'displayCategory' => $displayCategory,
            'bigTitle' => $bigTitle,
            'visible' => $visible,
            'pagination' =>[
                'itemsNumber' => ceil($ProductsNumber/$limit),
                'maxPage' => ceil($ProductsNumber/$limit) - (ceil($ProductsNumber/$limit)-$page) + 2,
                'path' => $pathForPager,
                'page' => $page
            ]
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