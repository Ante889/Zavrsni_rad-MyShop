<?php


class Products extends Model
{

    protected static $db_parameters = (['id','title', 'author', 'image', 'price', 'category', 'quantity', 'content', 'pdf','creation_time','where']);
    protected static $db_table ='products';
    public $id;
    public $title;
    public $author;
    public $image;
    public $price;
    public $category;
    public $quantity;
    public $content;
    public $pdf;
    public $creation_time;
    public $where;//Stavljaš samo kad radiš update/select;

}