<?php

namespace App\Models;

class Article
{
    protected array $articles;

    public function __construct()
    {
        $this->articles = [
            ['title'=>'title1', 'slug'=>'slug1', 'content'=>'content1'],
            ['title'=>'title2', 'slug'=>'slug2', 'content'=>'content2'],
            ['title'=>'title3', 'slug'=>'slug3', 'content'=>'content3'],
            ['title'=>'title4', 'slug'=>'slug4', 'content'=>'content4'],
            ['title'=>'title5', 'slug'=>'slug5', 'content'=>'content5'],
            ['title'=>'title6', 'slug'=>'slug6', 'content'=>'content6'],
            ['title'=>'title7', 'slug'=>'slug7', 'content'=>'content7'],
        ];
    }

    public function all()
    {
        return $this->articles;
    }
}