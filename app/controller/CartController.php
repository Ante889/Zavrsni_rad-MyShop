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
                'discount' => $product-> discount,
                'price' => $product->price * (1-producthelper::floatDiscount($product->discount))
            ];
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

    public function thankyou()
    {
        if(isset($_GET['tx']))
        {
            $amount = $_GET['amt'];
            $transaction = $_GET['tx'];
            $status = $_GET['st'];
            $ordersController = new AdminOrdersController;
            $ordersController -> create($status,$transaction,$amount); 
            $order = userhelper::shortSelect('Orders','transaction_id',$transaction);
            $orderClass = New Orders;
            $orderInner =  $orderClass -> innerSelectLimit([
                'orders1' => 'id',
                'products1' => 'title',
                'products2' => 'author',
                'products3' => 'image',
                'bought' => 'price',
                'orders4' => 'amount',
                'orders5' => 'transaction_id',
                ],
                'products',
                [
                'products-bought',
                'bought-orders',
                'orders-users'
                ],
                [
                'bought.orders' => $order->id
                ],999,0
            );
            $msg = Util::ordersList($orderInner);
            mailerhelper::sendMail($_SESSION['User']->email,'Transaction: '.$transaction,'Transaction: '.$transaction,$msg);
            Request::redirect(App::config('url').'cart/thankyou');
        }else{
            $_SESSION['thankyou'] = 'thank you for buying book. Enjoy!';
            Request::redirect(App::config('url').'index/mybooks');
        }

    }
}