<?php 


class Util
{ 

    public static function Pagination($pagination)
    {?>
        
        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-center">
            <li class="page-item">
                <a class="page-link" href="<?= App::config('url').$pagination['path']. '1' ?>" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
                <span class="sr-only">Previous</span>
                </a>
            </li>
            <?php for ($i=$pagination['page']-2; $i <= $pagination['maxPage']; $i++): ?>
                <?php if($i > 0 && $i <= $pagination['itemsNumber']): ?>
                    <?php if($pagination['page'] == $i): ?>
                    <li class='page-item active'><a class='page-link' href='<?= App::config('url').$pagination['path']. $i ?>'><?= $i ?></a></li>
                    <?php else:?>
                    <li class='page-item'><a class='page-link' href='<?= App::config('url').$pagination['path']. $i ?>'><?= $i ?></a></li>
                    <?php endif ?> 
                <?php endif ?>
            <?php endfor ?>

            <li class="page-item">
                <a class="page-link" href="<?= App::config('url').$pagination['path']. $pagination['itemsNumber'] ?>" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
                <span class="sr-only">Next</span>
                </a>
            </li>
            </ul>
        </nav>
<?php
    }
}
?>