<?php

include "partials/admin/header.php";
include "partials/admin/navbar.php"; 



$article = new Article();
$userId = $_SESSION['user_id'];
$userArticles = $article->getArticlesByUser($userId);

?>

<main class="container my-5">
  <h2 class="mb-4">Welcome <?php echo $_SESSION['user_name']; ?> to Admin Dashboard</h2>

  <div class="d-flex justify-content-between align-items-center mb-4">

    <form class="d-flex align-items-center" action="create-dummy-articles.php" method="POST">
      <label class="form-label me-2" for="articleCount">Number Of Articles</label>
      <input min="1" style="width: 100px; " class="form-control me-2" name="article_count" type="number">
      <button id="articleCount" type="submit" class="btn btn-primary ">
        Generate Articles
      </button>
    </form>
    <form action="reorder-articles.php" method="POST">
      <button name="reorder-articles" type="submit" class="btn btn-warning ">
        Reorder Articles Id's
      </button>
    </form>
    <button id="deleteSelectedBtn" class="btn btn-danger">Delete Selected Articles</button>

  </div>
  <!-- Articles Table -->
  <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
      <thead class="table-dark">
        <tr>
          <th><input type="checkbox" id="selectAll"></th>
          <th>ID</th>
          <th>Title</th>
          <th>Author</th>
          <th>Published Date</th>
          <th>Excerpt</th>
          <th>EDIT</th>
          <th>DELETE</th>
          <th>AJAX DELETE</th>
        </tr>
      </thead>
      <tbody>



        <!-- Example Article Row -->
        <?php if(!empty($userArticles)): ?>
        <?php foreach($userArticles as $articleItem): ?>
        <tr>
          <td><input type="checkbox" class="articleCheckbox" value="<?php echo $articleItem->id; ?>"></td>
          <td><?php echo $articleItem->id; ?></td>
          <td><?php echo $articleItem->title; ?> 1</td>
          <td><?php echo $_SESSION['user_name']; ?></td>
          <td><?php echo $article->formatCreatedAt($articleItem->created_at); ?></td>
          <td><?php echo $article->getExcerpt($articleItem->content); ?></td>
          <td>
            <a href="edit-article.php?id=<?php echo $articleItem->id; ?>" class="btn btn-sm btn-primary me-1">Edit</a>
          </td>
          <td>
            <form onsubmit="confirmDelete(<?php echo $articleItem->id; ?>)"
              action="<?php echo base_url('delete-article.php'); ?>" method="post">
              <input name="id" value="<?php echo $articleItem->id; ?>" type="hidden">
              <button class="btn btn-sm btn-danger">Delete</button>
            </form>
          </td>
          <td>
            <button data-id="<?php echo $articleItem->id;?>" class="btn btn-sm btn-danger delete-single">ajax
              delete</button>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>


      </tbody>
    </table>
  </div>
</main>

<script>
// Select or Deselect all checkoxes
document.getElementById('selectAll').onclick = function() {
  let checkboxes = document.querySelectorAll('.articleCheckbox');
  for (let checkbox of checkboxes) {
    checkbox.checked = this.checked;
  }
};

document.getElementById('deleteSelectedBtn').onclick = function() {
  let selectedIds = [];
  let checkboxes = document.querySelectorAll('.articleCheckbox:checked');
  checkboxes.forEach((checkbox) => {
    selectedIds.push(checkbox.value)
  });

  if (selectedIds.length === 0) {
    alert("HEY SELECT 1 AT LEAST");
    return;
  }

  if (confirm("Are you sure you want to delete this article")) {
    sendDeleteRequest(selectedIds)
  }

}

document.querySelectorAll('.delete-single').forEach((button) => {

  button.onclick = function() {
    let articleId = this.getAttribute('data-id');
    if (confirm("Are you sure you want to delete this article " + articleId + ' ?')) {
      sendDeleteRequest([articleId])
    }

  }

})

function sendDeleteRequest(articleIds) {
  let xhr = new XMLHttpRequest();
  xhr.open('POST', "<?php echo base_url('delete_articles.php') ?>", true);
  xhr.setRequestHeader('Content-Type', 'application/json');
  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4 && xhr.status === 200) {
      let response = JSON.parse(xhr.responseText);
      if (response.success) {
        alert("WE DID IT and article got deleted");
        location.reload();
      } else {
        alert('FAILED TO DELETE: ' + response.message)
      }
    }
  };
  xhr.send(JSON.stringify({
    article_ids: articleIds
  }))
}
</script>


<?php
include "partials/admin/footer.php";
?>