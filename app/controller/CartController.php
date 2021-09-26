<?php 


class CartController extends AuthorizationController
{

    private $path = 'public'. DIRECTORY_SEPARATOR ;
    private $error ="";

    public function index(array $parameters=[])
    {
        $this -> view -> render($this->path.'cart');
    }

    public function addProductToCart(array $parameters=[])
    {
        $productsClass = new Products;
        $productsClass -> where = $parameters[0];
        $product = $productsClass-> select('id')[0];
        if(empty($_SESSION['Cart'][$product-> id]))
        {
            $_SESSION['Cart'][$product-> id] = [
                'id' => $product-> id,
                'image' => $product-> image,
                'title' => $product-> title,
                'author' => $product-> author,
                'quantity' => $product-> quantity,
                'price' => $product-> price,
                'discount' => $product-> discount,
                'quantityInCart' => 1
            ];
        }else
        {
            $_SESSION['Cart'][$product-> id]['quantityInCart']++;
        }
        Request::redirect(App::config('url').'Cart/index');
    }

    public function destroyCart()
    {
        unset($_SESSION['Cart']);
        $_SESSION['Cart'] = [];
        Request::redirect(App::config('url').'Cart/index');
    }

    public function removeProduct($parameters=[])
    {
        foreach ($_SESSION['Cart'] as $key => $value) {
            if($value['id'] == $parameters[0])
            {
                unset($_SESSION['Cart'][$key]);
            }
        }
        Request::redirect(App::config('url').'Cart/index');
    }
}