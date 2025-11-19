<?php

namespace App\Controllers;

use App\Models\Article;
use App\Traits\Helper;
use App\Views\ArticleView;

class ArticleController
{
    public Article $article;
    public ArticleView $articleView;
    public function __construct(Article $article, ArticleView $articleView){
        $this->article = $article;
        $this->articleView = $articleView;
    }

    public function showArticleList()
    {
       $articles = $this->article->all();
       $path = TEMPLATES_DIR . '/articles';
       //$this->dd($path);
       $this->articleView->showArticleList($path, $articles);
    }
}