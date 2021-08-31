<?php 

class View {

    public function render($site, $parameters=[])
    {
        include APP_PATH . 'view'. DIRECTORY_SEPARATOR . $site . '.phtml';
    }

}