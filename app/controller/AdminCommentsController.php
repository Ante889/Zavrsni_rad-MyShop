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
        $commentsInner =  $commentsClass -> innerSelect([
            'comments1' => 'id',
            'products' => 'title',
            'comments2' => 'product',
            'users' => 'name',
            'comments3' => 'comment',
            'comments4' => 'comment_date',
            'comments5' => 'approved'
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
        ]);
    }

    public function deleteComments(array $parameters=[])
    {
        $comment = userhelper::shortSelect('Comments','id',$parameters[0]);
        $commentsClass = New Comments;
        $commentsClass -> where = $parameters[0];
        $commentsClass -> delete('id');
        Request::redirect(App::config('url'). 'AdminComments');
    }

}