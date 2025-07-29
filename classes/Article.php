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
      if($article->user_id == $_SESSION['user_id']){
        return $article;
      }else{
        redirect('admin.php');
      }
      
    }else{
      return false;
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

  public function update($title,$content,$author_id,$created_at, $imagePath){

    $query = " UPDATE " . $this->table . " SET title = :title, content = :content, created_at = :created_at";

      if($imagePath){
        $query .= ", image = :image";
      }

      $query .= " WHERE user_id = :author_id" ; 
     

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':title',$title);
    $stmt->bindParam(':content',$content);
    $stmt->bindParam(':author_id',$author_id);
    $stmt->bindParam(':created_at',$created_at);
    if($imagePath){$stmt->bindParam(':image',$imagePath,PDO::PARAM_STR);}
    
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
  
  public function uploadImage($file){

    
    $targetDir = 'uploads/';
    

    
    if(! is_dir($targetDir)){
      mkdir($targetDir, 0755 ,true );
    }
    
    if(isset($file) && $file['error'] === 0){
        $targetFile = $targetDir . basename($file['name']);
        $imageFileType = strtolower(pathinfo( $targetFile, PATHINFO_EXTENSION));

        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if(in_array($imageFileType,  $allowedTypes)){

          $uniqueFileName = uniqid() . '_' . time() . '.' . $imageFileType ;
          $targetFile = $targetFile . '_' . $uniqueFileName;
          
           if(move_uploaded_file($file['tmp_name'], $targetFile)){
           return $targetFile;
          }else{
            return " There was an error uploading the file";
          }
          
        }else{
          return " The image type is not allowed,only 'jpg', 'jpeg', 'png', 'gif' are allowed ";
        }
        return '';
      }

  }


  public function generateDummyData($num = 1){

    $query = " INSERT INTO " . $this->table . " (title, content, user_id, created_at, image)
               VALUES (:title, :content, :user_id, :created_at, :image)";

     $stmt = $this->conn->prepare($query);
     
      $dummy_titles = [
            'The Future of Technology', 'The Importance of Education',
            'How to Stay Productive', 'A Guide to Healthy Living',
            'Exploring the World of Science', 'Understanding Mental Health',
            'The Rise of AI', 'The Power of Positive Thinking',
            'Achieving Financial Freedom', 'The Benefits of Exercise'
        ];

        $dummy_content = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur sodales ligula in libero. Sed dignissim lacinia nunc.";
        $dummy_image = "https://via.placeholder.com/350x200";
        $user_id = 22;
        $created_at = date('Y-m-d');

        for($i = 0; $i < $num; $i++){
          $dummy_title = $dummy_titles[array_rand($dummy_titles)];

          $stmt->bindParam(':title', $dummy_title);
          $stmt->bindParam(':content', $dummy_content);
          $stmt->bindParam(':user_id', $user_id);
          $stmt->bindParam(':created_at', $created_at);
          $stmt->bindParam(':image', $dummy_image);

          $stmt->execute();
        }

        return true;

      
  }
  
    
  

  
}