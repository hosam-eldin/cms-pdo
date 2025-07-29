<?php
require_once 'init.php';

if(isPostRequest()){


    if(isset($_POST['reorder-articles'])){

      try{
        $article = new Article();
        $article->reorderAndResetAutoIncrement();
      }catch(Exception $e){

        redirect('admin.php');
      }
        
  

    }
  
  
    
}