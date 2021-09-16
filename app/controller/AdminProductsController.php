<?php 


class AdminProductsController extends Controller
{
    private $path = 'admin'. DIRECTORY_SEPARATOR . 'products'. DIRECTORY_SEPARATOR ;

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
        if(isset($_POST['submit']) && !empty($_POST['title']))
        {
            $products= $productsClass -> selectAllLike("%".trim($_POST['title'])."%",'title');
        }else
        {
            $products = $productsClass -> selectAll();;
        }  
        $this -> view -> render($this->path.'adminProducts',[
            'products' => $products
        ]);
    }

    public function createProducts()
    {
        $categories= new Categories;
        $categories= $categories -> selectAll();
        $SuccessMsg='';
        $errors= [
            'title' => '',
            'author' => '', 
            'image' => '',
            'price' => '',
            'category' => '', 
            'quantity' => '',
            'content' => '', 
            'pdf' => ''
        ];
        $title = Request::issetTrim('title');
        $author = Request::issetTrim('author');
        $image = isset($_FILES['image']) ? $_FILES['image'] : '';
        $price = Request::issetTrim('price');
        $category = Request::issetTrim('category');
        $quantity = Request::issetTrim('quantity');
        $content = Request::issetTrim('content');
        $pdf = Request::issetTrim('pdf');
        if($image != ''){
            $imageName = uniqid().basename($image['name']);
        }else 
        {
            $imageName ='';
        }
        if(isset($_POST['submit'])){
            
            $errors['title'] = producthelper::basicError($title);
            $errors['author'] = producthelper::basicError($author);
            $errors['image'] = producthelper::photoError($image);
            $errors['price'] = producthelper::numbersError($price);
            $errors['category'] = producthelper::categoryError($category);
            $errors['quantity'] = producthelper::numbersError($quantity);
            $errors['content'] = producthelper::basicError($content);
            $errors['pdf'] = producthelper::basicError($pdf);       
           
            //Create product
    
            if(empty($errors['title']) && empty($errors['author']) && empty($errors['image']) && empty($errors['price'])&& empty($errors['category'])&& empty($errors['quantity'])&& empty($errors['content'])&& empty($errors['pdf'])){
                $ProductsClass = new Products;
                $ProductsClass -> title = $title;
                $ProductsClass -> author = $author;
                $ProductsClass -> image = $imageName;
                $ProductsClass -> price = $price;
                $ProductsClass -> category = $category;
                $ProductsClass -> quantity = $quantity;
                $ProductsClass -> content = $content;
                $ProductsClass -> pdf = $pdf;
                $ProductsClass -> Create();
                $SuccessMsg= 'Product has been successfully created';
                move_uploaded_file($image['tmp_name'], IMAGE_PATH .$imageName);
            }
        }
        $this -> view -> render($this->path.'adminProductsAdd',[
            'categories' => $categories,
            'errors' => $errors,
            'succesMsg' => $SuccessMsg,
            'returnField' => [
                'title' => $title,
                'author' => $author, 
                'image' => $imageName,
                'price' => $price,
                'category' => $category, 
                'quantity' => $quantity,
                'content' => $content, 
                'pdf' => $pdf
            ]
        ]);
    }

    public function updateProducts(array $parameters=[])
    {
        $categories= new Categories;
        $categories= $categories -> selectAll();
        $errors= [
            'title' => '',
            'author' => '', 
            'image' => '',
            'price' => '',
            'category' => '', 
            'quantity' => '',
            'content' => '', 
            'pdf' => ''
        ];

        $ProductsClass = new Products;
        $ProductsClass -> where= $parameters[0];
        $Fields = $ProductsClass-> select('id')[0];
        if(empty($Fields)){
            Request::redirect(App::config('url'). 'AdminProducts');
            return;
        }
        $image = isset($_FILES['image']) ? $_FILES['image'] : '';

        if(isset($_POST['submit'])){

        $errors['title'] = producthelper::basicError(Request::issetTrim('title'));
        $errors['author'] = producthelper::basicError(Request::issetTrim('author'));
        $errors['image'] = producthelper::photoError($image);
        $errors['price'] = producthelper::numbersError(Request::issetTrim('price'));
        $errors['category'] = producthelper::numbersError(Request::issetTrim('category'));
        $errors['quantity'] = producthelper::numbersError(Request::issetTrim('quantity'));
        $errors['content'] = producthelper::basicError(Request::issetTrim('content'));
        $errors['pdf'] = producthelper::basicError(Request::issetTrim('pdf'));
        if(empty($image['name'])){
            unset($errors['image']);
        }

        if(empty($errors['title']) && empty($errors['author']) && empty($errors['image']) && empty($errors['price'])&& empty($errors['category'])&& empty($errors['quantity'])&& empty($errors['content'])&& empty($errors['pdf'])){
        
            if(!empty($image['name'])){
                $imageName = uniqid().basename($image['name']);
                unlink(IMAGE_PATH . $Fields-> image);
                move_uploaded_file($image['tmp_name'], IMAGE_PATH .$imageName);
            }else 
            {
                $imageName =$Fields-> image;
            }

            $ProductsClass -> title = Request::issetTrim('title');
            $ProductsClass -> author = Request::issetTrim('author');
            $ProductsClass -> image =  $imageName;
            $ProductsClass -> price = Request::issetTrim('price');
            $ProductsClass -> category = Request::issetTrim('category');
            $ProductsClass -> quantity = Request::issetTrim('quantity');
            $ProductsClass -> content = Request::issetTrim('content');
            $ProductsClass -> pdf = Request::issetTrim('pdf');
            $ProductsClass -> where = $parameters[0];
            $ProductsClass -> update('id');
            Request::redirect(App::config('url'). 'AdminProducts/updateProducts/'. $parameters[0]);
        }
    }
        $this -> view -> render($this->path.'adminProductsUpdate',[
            'categories' => $categories,
            'errors' => $errors,
            'returnField' => [
              'id' => $parameters[0],
              'title' => $Fields -> title,
              'author' => $Fields -> author, 
              'image' => $Fields ->image,
              'price' => $Fields ->price,
              'category' => $Fields ->category, 
              'quantity' => $Fields ->quantity,
              'content' => $Fields ->content, 
              'pdf' => $Fields ->pdf
            ]
        ]);
    }

    public function deleteProducts(array $parameters=[])
    {
        $products = userhelper::shortSelect('Products','id',$parameters[0]);
        unlink(IMAGE_PATH . $products-> image);
        $productsClass = New Products;
        $productsClass -> where = $parameters[0];
        $productsClass -> delete('id');
        Request::redirect(App::config('url'). 'AdminProducts');
    }
}