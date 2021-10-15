<?php


class Users extends Model
{

    protected static $db_parameters = (['id','name', 'lastname', 'password', 'role', 'email', 'register_time', 'confirm_email_token', 'reset_password_token', 'rememberme_token','where']);
    protected static $db_table ='users';
    public $id;
    public $name;
    public $lastname;
    public $password;
    public $role;
    public $email;
    public $register_time;
    public $confirm_email_token;
    public $reset_password_token;
    public $rememberme_token;
    public $where;//Stavljaš samo kad radiš update/select;

    public static function readLimit($limit, $offset)
    {

        $connection= DB::getInstance();
        $sql = 
        '
        select a.id,a.name, a.lastname, a.role , a.email , count(b.status) as bought
        from 
        users a
        left join orders b on b.user = a.id 
        group by a.name limit :limit offset :offset;
        ';
        $result = $connection -> prepare($sql);
        $result->bindValue('offset', $offset, PDO::PARAM_INT);
        $result->bindValue('limit', $limit, PDO::PARAM_INT);
        $result -> execute();
        return $result -> fetchAll();
    }

    public static function readLikeLimit($like,$limit, $offset)
    {

        $connection= DB::getInstance();
        $sql = 
        '
        select a.id,a.name, a.lastname, a.role , a.email , count(b.status) as bought
        from 
        users a
        left join orders b on b.user = a.id 
        where 
        a.email like :likeparm
        group by a.name
        limit :offset,:limit
        ';
        $result = $connection -> prepare($sql);
        $result->bindValue('offset', $offset, PDO::PARAM_INT);
        $result->bindValue('limit', $limit, PDO::PARAM_INT);
        $result->bindValue('likeparm', '%'. $like .'%');
        $result -> execute();
        return $result -> fetchAll();
    }

}