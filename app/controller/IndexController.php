<?php 


class IndexController extends Controller
{

    private $path = 'public'. DIRECTORY_SEPARATOR ;
    private $error ="";


    public function index(array $parameters=[])
    {   
        $limit = 6;
        $offset = 0;
        $bigTitle = 'BOOKS';
        $ProductsInCategory=[];
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
        
        //dodaj rating u products array
        foreach ($Products as $key) {
            $rating =$this->getRating($key -> id);
            $key-> rating = $rating;  
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
        if(!empty($_GET['page'])){
            $offset = (10 * $_GET['page']) - 10;
            $page = $_GET['page'];
        }else{
            $page = 1;
        }
        $commentsClass= new Comments;
        $commentsClass->where = $parameters[0];
        $commentsClass = count($commentsClass -> select('product'));
        $pathForPager = 'index/productpage/'.$parameters[0].'?page=';

        if(isset($_POST['submit']))
        {
            $this->insertComment($parameters[0]);
        }
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
            $comments = $this->getComments($parameters[0]);

        }else{
            Request::redirect(App::config('url'));
            return;
        }

        $this -> view -> render($this->path.'productpage',[
            'rating' => $this-> getRating($parameters[0]),
            'checkrating' => $this-> checkRating($parameters[0]),
            'commentError' => $this->error,
            'product' => $product,
            'availability' => $Availability,
            'comments' => $comments,
            'pagination' =>[
                'itemsNumber' => ceil($commentsClass/10),
                'maxPage' => ceil($commentsClass/10) - (ceil($commentsClass/10)-$page) + 2,
                'path' => $pathForPager,
                'page' => $page
            ]
        ]
    );
    }

    public function insertComment($parameters)
    {
        $commentsClass= new Comments;
        $this -> error= commenthelper::commentError(trim($_POST['content']));
        if(empty($this -> error)){
        $commentsClass -> user = trim($_SESSION['User'] -> id);
        $commentsClass -> product = trim($parameters[0]);
        $commentsClass -> comment = trim($_POST['content']);
        $commentsClass -> comment_date= date("d-m-y"); 
        $commentsClass -> approved = 1;
        $commentsClass -> create();
        }
    }

    public function getComments($id)
    {
        $limit= 10;
        $offset = 0;
        $commentsClass = new Comments;
        $commentsInner =  $commentsClass -> innerSelectLimit([
            'comments1' => 'id',
            'comments2' => 'product',
            'users' => 'name',
            'comments3' => 'comment',
            'comments4' => 'comment_date',
            'comments5' => 'approved'
            ],
            'comments',
            ['comments-users'],
            [
            'comments.product' => $id
            ],$limit,$offset
        );
        $comments=[];
        foreach ($commentsInner as $key => $value)
        {
            if($value-> approved == 1)
            {
                $comments[$key] = $value;
            }
        }
        return $comments;

    }

    public function getRating($product)
    {
        $ratingClass= new Ratings;
        $ratingClass -> where = $product;
        $result= $ratingClass -> select('product');
        for ($i=0; $i < count($result); $i++) { 
            $allRatings[$i] = $result[$i] ->rating;
        }
        if(isset($allRatings)){
        $result = array_sum($allRatings) / count($result);
        return $result;
        }else{
            return 'no rating';
        }
    }

    public function setRating($param=[])// prvi parametar product drugi rating
    {
        if($this -> checkRating($param[0]) === true)
        {
            $ratingClass= new Ratings;
            $ratingClass -> user = $_SESSION['User'] -> id;
            $ratingClass -> product = $param[0];
            $ratingClass -> rating = $param[1];
            $ratingClass -> create();
        }
        Request::redirect(App::config('url').'index/productpage/'. $param[0]);
    }

    public function checkRating($product)
    {
        $ratingClass= new Ratings;
        $ratingClass -> where = $product;
        $result= $ratingClass -> select('product');
        foreach ($result as $key) {
            if($key -> user == $_SESSION['User']->id)
            {
                return $key -> rating;
            }
        }
        return true;
    }
    

    public function error(array $parameters=[])
    {
        $this -> view -> render($this->path.'error');
    }

}