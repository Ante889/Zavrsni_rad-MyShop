<?php 


class IndexController extends Controller
{
    public function index(array $parameters=[])
    {

        $categoriesClass = new Categories;
        $categories = $categoriesClass -> selectAll();
        $ProductsClass = new Users;
        if(isset($parameters[0]))
        {
            $Products = userhelper::shortSelect('Products', 'category', $parameters[0]);

        }else{
            $Products = $ProductsClass-> selectAll();
        }

        $this -> view -> render('index',[
            'categories' => $categories,
            'products' => $Products
        ]);
    }
    

    public function error(array $parameters=[])
    {
        $this -> view -> render('error');
    }


}