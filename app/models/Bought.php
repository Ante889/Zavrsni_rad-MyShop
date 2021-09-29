<?php


class Orders extends Model
{

    protected static $db_parameters = (['orders','product','price','where']);
    protected static $db_table ='bought';
    public $orders;
    public $product;
    public $price;
    public $where;//Stavljaš samo kad radiš update/select;

}