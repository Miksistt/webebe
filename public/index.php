<?php

use App\Controllers\ArticleController;
use App\Core\Filemanager;
use App\Models\Article;
use App\Views\ArticleView;
require './vendor/autoload.php';
require './config/settings.php';
require './App/Core/Helper.php';

$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

$articles = new Article();
$articlesView = new ArticleView();
$articlesController = new ArticleController($articles, $articlesView);


$uri = $_SERVER['REQUEST_URI'];
switch ($uri) {
    case '/':
        include_once('./public/index.php');
        break;
    case '/calc':
        include_once ('./templates/calc.html');
        break;
    case '/articles':
        $articlesController->showArticleList();
        break;
    default:
        include_once('./templates/partials/404.php');
        break;
}
