<?php
$articles = $this->article->all();
foreach ($articles as $article):?>
    <div class="article">
        <p> <?php echo $article['title'] ?> </p>
        <p> <?php echo $article['content'] ?> </p>
    </div>
<?php endforeach;?>