<?php

include "partials/admin/header.php";
include "partials/admin/navbar.php"; 



$article = new Article();
$userId = $_SESSION['user_id'];
$userArticles = $article->getArticlesByUser($userId);

?>

<main class="container my-5">
  <h2 class="mb-4">Welcome <?php echo $_SESSION['user_name']; ?> to Admin Dashboard</h2>

  <!-- Articles Table -->
  <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Title</th>
          <th>Author</th>
          <th>Published Date</th>
          <th>Excerpt</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>



        <!-- Example Article Row -->
        <?php if(!empty($userArticles)): ?>
        <?php foreach($userArticles as $articleItem): ?>
        <tr>
          <td><?php echo $articleItem->id; ?></td>
          <td><?php echo $articleItem->title; ?> 1</td>
          <td><?php echo $_SESSION['user_name']; ?></td>
          <td><?php echo $article->formatCreatedAt($articleItem->created_at); ?></td>
          <td><?php echo $article->getExcerpt($articleItem->content); ?></td>
          <td>
            <a href="edit-article.php?id=<?php echo $articleItem->id; ?>" class="btn btn-sm btn-primary me-1">Edit</a>
            <button class="btn btn-sm btn-danger" onclick="confirmDelete(1)">Delete</button>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>


      </tbody>
    </table>
  </div>
</main>

<?php
include "partials/admin/footer.php";
?>