<?php


class User {
  
  private $conn;
  private $table = "users";

  public function __construct() {
    $database = new Database();
    $this->conn = $database->getConnection();
  }

  public function register($username, $email, $password) {
    $query = "INSERT INTO " . $this->table . " (username, email, password) VALUES (:username, :email, :password ) ";
    
    $stmt = $this->conn->prepare($query);
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    // Bind parameters
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashedPassword);
    
    if ($stmt->execute()) {
      return true;
    }
    
    return false;
  }

  public function login($email, $password) {
    $query = "SELECT * FROM " . $this->table . " WHERE email = :email";
    
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $User = $stmt->fetch(PDO::FETCH_OBJ);

      if ($User && password_verify($password, $User->password)) {
         $_SESSION['logged_in'] = true;
          $_SESSION['user_name'] = $User->username;
          $_SESSION['user_email'] = $User->email;
        return $User->id; // Return User ID or any other User data as needed
      }
    return false;
  }

  public function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
  }
  

}