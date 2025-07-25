<?php

class Article{

  private $conn;
  private $table = "articles";

  public function __construct() {
    $database = new Database();
    $this->conn = $database->getConnection();
  }
  public function getExcerpt($content, $length = 100) {
    if (strlen($content) > $length) {
      return substr($content, 0, $length) . '...';
    }
    return $content;
  }

  public function getAllArticles() {
    
    $query = "SELECT * FROM " . $this->table . " ORDER BY id DESC";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
     
    return $stmt->fetchAll(PDO::FETCH_OBJ);
  }

  public function getArticlesByUser($userId)  {
    $query = " SELECT * FROM " . $this->table . " WHERE user_id = :user_id ORDER BY created_at DESC";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':user_id', $userId,PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);

    
  }
  
  public function getArticleId($id){
    $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1 ";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id',$id,PDO::PARAM_INT);
    $stmt->execute();
    $article = $stmt->fetch(PDO::FETCH_OBJ);

    if($article){
      return $article;
    }else{
      false;
    }
  }

  public function getArticleWithOwnerById($id){
    $query = "SELECT 
          articles.id,
          articles.image,
          articles.title,
          articles.content,
          articles.created_at,
          users.username AS author,
          users.email AS author_email
          FROM " . $this->table . "
          JOIN users ON articles.user_id = users.id
          WHERE articles.id = :id LIMIT 1 ";

          
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id',$id,PDO::PARAM_INT);
    $stmt->execute();
    $article = $stmt->fetch(PDO::FETCH_OBJ);

    if($article){
      return $article;
    }else{
      false;
    }
  }

  public function formatCreatedAt($date){
      return date( 'F J, Y' , strtotime($date));
  }

  public function create($title,$content,$author_id,$created_at, $imagePath){

    $query = " INSERT INTO " . $this->table . " (title, content, user_id, created_at, image ) 
                VALUES (:title, :content, :user_id, :created_at, :image) ";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':title',$title);
    $stmt->bindParam(':content',$content);
    $stmt->bindParam(':user_id',$author_id,PDO::PARAM_INT);
    $stmt->bindParam(':created_at',$created_at);
    $stmt->bindParam('image',$imagePath);
    
    return $stmt->execute();

  }

  public function deleteWithImage($id){

      $article = $this->getArticleId($id);
      if(($article)){
        //check for user ownership
        if($article->user_id == $_SESSION['user_id']){
        
            if(!empty($article->image) && file_exists($article->image)){
              if(!unlink($article->image)){
                return false;
              }
            }
            $query = "DELETE FROM " .$this->table . " WHERE id= :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
          }
            
        }else{
            redirect('admin.php');
        } 

          return false;
      }   
  
    
  

  
}