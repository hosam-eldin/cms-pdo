<?php
require_once 'init.php';

if(isPostRequest()){

  $count = getPostData('article_count');
  
    $article = new Article();

      if($article->generateDummyData($count)){
        redirect('admin.php');
      }
}