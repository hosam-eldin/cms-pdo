<?php

require_once 'init.php';
checkUserLoggedIn();

if(isPostRequest()){
  
  $id = $_POST['id'];
  $article = new Article();

    if($article->deleteWithImage($id)){

        redirect('admin.php');
        exit;
    }else{
      echo "Failed to delete";
    }
  
}

  