<?php
require_once "init.php";

if(isPostRequest()){
   
  session_destroy(); // Destroy the session 
  redirect('index.php'); // Redirect to login page
  exit(); // Ensure no further code is executed after redirection
}else{
  redirect('index.php');
  exit;
}