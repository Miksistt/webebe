<?php

namespace App\Views;

use App\Traits\Helper;

class ArticleView
{
    public $path;
    public function showArticleList($path, array $articles)
    {
        $fullPath = $path . '/articles_list.php';
        include $fullPath;
    }
}