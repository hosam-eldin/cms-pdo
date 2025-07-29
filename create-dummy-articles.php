<?php
require_once 'init.php';

if(isPostRequest()){

  
  
    $article = new Article();

      if($article->generateDummyData(getPostData('article_count'))){
        redirect('admin.php');
      }
}