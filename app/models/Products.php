<?php


class Products extends Model
{

    protected static $db_parameters = (['id','title', 'author', 'image', 'price', 'category','content','visible', 'pdf','creation_time','discount','where']);
    protected static $db_table ='products';
    public $id;
    public $title;
    public $author;
    public $image;
    public $price;
    public $category;
    public $content;
    public $visible;
    public $pdf;
    public $creation_time;
    public $discount;
    public $where;//Stavljaš samo kad radiš update/select;

}