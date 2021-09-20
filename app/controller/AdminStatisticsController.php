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
        $this -> view -> render($this->path.'adminTemplate');
    }
}