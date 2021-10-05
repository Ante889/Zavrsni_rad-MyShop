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
        //dodaj rating u products array i provjeri jesu li visible
        $Product=[];
        foreach ($Products as $key) {
            $rating =$this->getRating($key -> id);
            $key-> rating = $rating; 
            if($key->visible == 'visible')
            {
                $Product[$key->id] = $key;
            }
        }
        //Slideshow
        $slideshowClass = new Slideshow;
        $slideshowClass -> where = '1';
        $visible = $slideshowClass -> select('visible'); 

        $this -> view -> render($this->path.'index',[
            'ProductsInCategory' => $ProductsInCategory,
            'categories' => $Categories,
            'products' => $Product,
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
        {
            $productClass= new Products;
            $productClass -> where = $parameters[0];
            $product=$productClass -> select('id')[0];
            $comments = $this->getComments($parameters[0]);
        }
        if(empty($parameters[0]) || empty($product) || $product->visible != 'visible'){
            Request::redirect(App::config('url'));
            return;
        }

        $this -> view -> render($this->path.'productpage',[
            'checkrating' => $this-> checkRating($parameters[0]),
            'countrating' => $this-> countRating($parameters[0]),
            'rating' => $this-> getRating($parameters[0]),
            'commentError' => $this->error,
            'product' => $product,
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
    public function mybooks($parameters=[])
    {
        if(isset($parameters[0]))
        {
            $local_file = PDF_PATH . $parameters[0];
            $download_file = $parameters[0];

            // set the download rate limit (=> 20,5 kb/s)
            $download_rate = 20.5;
            if(file_exists($local_file) && is_file($local_file))
            {
                header('Cache-control: private');
                header('Content-Type: application/octet-stream');
                header('Content-Length: '.filesize($local_file));
                header('Content-Disposition: filename='.$download_file);

                flush();
                $file = fopen($local_file, "r");
                while(!feof($file))
                {
                    print fread($file, round($download_rate * 1024));
                    flush();
                    sleep(1);
                }
                fclose($file);}
            else {
                die('Error: The file '.$local_file.' does not exist!');
            }
        }

        if(request::isLogin()){
            $ordersClass = New Orders;
            $ordersClass -> where = $_SESSION['User'] -> id;
            $products = $ordersClass -> innerSelectLimit(
                [
                    'orders' => 'id',
                    'products' => 'pdf',
                    'products1' => 'image',
                    'products2' => 'title',
                    'products3' => 'author'
                ],
                'orders',
                [
                    'orders-bought',
                    'bought-products'
                ],
                [
                    'orders.status' => 'success'
                ],999,0
            );
            $this -> view -> render($this->path.'mybooks',[
                'products' => $products
            ]);
            unset($_SESSION['thankyou']);
        }else{
            Request::redirect(App::config('url'));
        }
        
    }

    public function insertComment($parameters)
    {
        $commentsClass= new Comments;
        $this -> error= commenthelper::commentError(trim($_POST['content']));
        if(empty($this -> error)){
        $commentsClass -> user = trim($_SESSION['User'] -> id);
        $commentsClass -> product = trim($parameters);
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
        if(!empty($_GET['page'])){
            $offset = (10 * $_GET['page']) - 10;
        }
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
        if(isset($_SESSION['User']) && $this -> checkRating($param[0]) === true)
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
        if(isset($_SESSION['User']))
        {
            foreach ($result as $key) {
                if($key -> user == $_SESSION['User']->id)
                {
                    return $key -> rating;
                }
            }
        }
        return true;
    }

    public function countRating($product)
    {
        $ratingClass= new Ratings;
        $ratingClass -> where = $product;
        $result= $ratingClass -> select('product');
        return count($result);
    }
    
    public function contact(array $parameters=[])
    {
        $this -> view -> render($this->path.'contact');
    }

    public function error(array $parameters=[])
    {
        $this -> view -> render($this->path.'error');
    }

}