<?php


class Categories extends Model
{

    protected static $db_parameters = (['id','name','where']);
    protected static $db_table ='categories';
    public $id;
    public $name;
    public $where;//Stavljaš samo kad radiš update/select;
}