<?php

use App\Database;
use App\shortcodes;

// Get the post ID from the URL
$post_id = $match['params']['id'];
$db = new Database();
$db_conx_rdj = $db->connect();
// Prepare and execute the SELECT query
$stmt = $db_conx_rdj->prepare("SELECT id, title, content, date_posted, posted_by
FROM z_posts WHERE " . PREFIX . "_posts.id = :post_id AND " . PREFIX . "_posts.post_type = 1");
$stmt->execute([':post_id' => $post_id]);

// Fetch the result
$post = $stmt->fetch();
?>
<section>
  <div class="container justify-content-center align-items-center h-100">
    <div class="row">
      <div class="col-lg-8 admin-post-content">
        <h1>Editer un article</h1>
        <hr>
        <form action="/post-update/<?= $post_id ?>" method="post" enctype="multipart/form-data">
          <div class="mb-3">
            <label for="title">Titre :</label><br>
            <input type="text" name="title" id="title" class="form-control" value="<?= $post['title']; ?>">
          </div>

          <div class="mb-3">
            Sélectionnez une image à télécharger pour la couverture de votre billet:
            <input type="file" name="fileToUpload" class="form-control" id="fileToUpload">
          </div>
          <div class="p-3 mt-3 mb-3" style="background-color: #eaeaea;">
            <label for="is_featured">Mettre ce contenu en avant :</label><br>
            <select name="is_featured" id="is_featured" class="form-control">
              <option value="1">Mettre ce contenu en avant</option>
              <option value="0">Ne pas mettre ce contenu en avant</option>
            </select>
          </div>
          <div class="mb-3">

            <label for="content" style="margin-top: 20px;">Contenu de l'article:</label><br>
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#imageModal">
              <i class="bi bi-card-image me-3"></i><small>ajouter une image</small>
            </button>
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#galleryModal">
              <i class="bi bi-images me-3"></i><small>ajouter une gallerie d'images</small>
            </button>

            <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#uploadModal">
              <i class="bi bi-images me-3"></i><small>Téléverser des images</small>
            </button>

            <textarea name="content" id="content" class="form-control" cols='10' rows="15" value="<?= $post['content']; ?>"></textarea>
            <div class="form-text">Pour voir la liste des shortcodes, <a href="#" data-bs-toggle="modal" data-bs-target="#shortCodeModal">c'est ici </a> </div>
          </div>
          <div class="p-3 mt-3 mb-3" style="background-color: #eaeaea;">
            <label for="post_type">Afficher ce contenu comme :</label><br>
            <select name="post_type" id="post_type" class="form-control">
              <option value="1">Un article</option>
              <option value="2">Une page</option>
            </select>
          </div>
          <button class="btn btn-dark" type="submit" value="Envoyer" name="submit">Envoyer</button>
        </form>
      </div>
    </div>
  </div>
</section>