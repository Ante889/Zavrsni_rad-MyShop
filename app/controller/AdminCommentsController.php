<?php 


class AdminCommentsController extends Controller
{
    private $path = 'admin'. DIRECTORY_SEPARATOR . 'comments'. DIRECTORY_SEPARATOR ;

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
        $commentsClass = new Comments;
        $commentsContent = $commentsClass -> selectAll();
        $commentsInner =  $commentsClass -> innerSelect([
            'products' => 'title',
            'users' => 'name',
            ],
            'products',
            ['products-comments',
             'comments-users'],
            [
            'comments.approved' => '1'
            ]
        );
        $this -> view -> render($this->path.'adminComments',[
            'commentsInner' => $commentsInner,
            'commentsContent' => $commentsContent
        ]);
    }

}