<?php

class AdminStatisticsController extends Controller
{
    private $path = 'admin'. DIRECTORY_SEPARATOR ;

    public function __construct()
    {
        parent::__construct();
        if(!Request::isAdmin())
        {
            Request::redirect(App::config('url'));
        }
    }

    public function index()
    {

        $productsClass= new Products;
        $allProducts = $productsClass -> selectAll();
        $productTotalprice=[];
        if(!empty($allProducts))
        {
            foreach ($allProducts as $key ) {
                $productsCount[$key->title]=$productsClass-> innerSelectLimit(
                    ['bought' => 'price',],
                    'products',
                    ['products-bought'],
                    ['bought.product' => $key->id
                    ],999999,0
                );
            }
            $productNames = '';
            foreach ($productsCount as $key => $values) {
                if(!empty($values)){
                    $productNames =$productNames . $key . ' ';
                }
                foreach($values as $value => $result){
                    $productsPrice[$key][$value] = $result->price;
                }
            }
            $productNames =explode(' ',$productNames);
            for ($i=0; $i < count($productsPrice) ; $i++) { 
                $productTotalprice[$i] = [
                    'label' => $productNames[$i],
                    'y' => array_sum($productsPrice[$productNames[$i]])
                ];
            }
        }

        $this -> view -> render($this->path.'statistics',[
            'productTotalprice' => $productTotalprice
        ]);
    }
}