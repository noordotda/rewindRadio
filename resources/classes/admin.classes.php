<?php
namespace App;

use App\Text;

class ManagePosts {
// Function to handle the upload of post's featured image
public static function imageUpload($featured_image, $target_dir) {
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo((string) $featured_image, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    if (isset($_POST["submit"])) {
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if ($check !== false) {
            echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }

        // Check if file already exists
        if (file_exists($featured_image)) {
            echo "Sorry, file already exists.";
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES["fileToUpload"]["size"] > 8_000_000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if ($imageFileType != ['jpg','png','jpeg','gif']
    ) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        } else {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $featured_image)) {
                echo "The file " . basename((string)$_FILES["fileToUpload"]["name"]) . " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }
}

// Function to handle the update of a post
public static function handlePostUpdate($id) {
    try {
        // Connection to the database
        $db = new Database;
        $db_conx_rdj = $db->connect();
    } catch (\PDOException $e) {
        echo "Error connecting to the database: " . $e->getMessage();
        exit;
    }

    // Data processing form
    $title = htmlspecialchars((string)$_POST['title']);
    $content = $_POST['content'];
    $slug = strtolower(str_replace(' ', '-', $title));
    $date_posted = date("Y-m-d");
    $featured_image = $_FILES["fileToUpload"]["name"];

    // SQL insert query
    $query = "UPDATE " . PREFIX . "_posts SET title = :title, content = :content, slug = :slug, date_posted = :date_posted, featured_image = :featured_image WHERE id=$id";
    $stmt = $db_conx_rdj->prepare($query);
    $stmt->bindParam(':title', $title);
$stmt->bindParam(':content', $content);
$stmt->bindParam(':slug', $slug);
$stmt->bindParam(':date_posted', $date_posted);
$stmt->bindParam(':featured_image', $featured_image);

if ($stmt->execute()) {
  return true;
} else {
  return false;
}

    if ($stmt->execute()) {
  return true;
} else {
  return false;
} } 
public static function listNews() {
    global $router;
    include(RESOURCES_PATH . 'lang/lang-' . LANG . '.php');
    $db = new Database();
    $db_conx_rdj = $db->connect();
    $query = "SELECT " . PREFIX . "_posts.id, " . PREFIX . "_posts.featured_image," . PREFIX . "_posts.posted_by, " . PREFIX . "_posts.date_posted, " . PREFIX . "_posts.title, " . PREFIX . "_posts.content, " . PREFIX . "_posts.post_type, " . PREFIX . "_users.username, " . PREFIX . "_users.nice_nickname," . PREFIX . "_categories.name as category_name, " . PREFIX . "_tags.name as tag_name,
    DATE_FORMAT(DATE(date_posted), '%d/%m/%Y') AS clean_date FROM " . PREFIX . "_posts
    LEFT JOIN " . PREFIX . "_users ON " . PREFIX . "_posts.posted_by = " . PREFIX . "_users.id 
    LEFT JOIN " . PREFIX . "_categories ON " . PREFIX . "_posts.category_id = " . PREFIX . "_categories.id 
    LEFT JOIN " . PREFIX . "_tags ON " . PREFIX . "_posts.tag_id = " . PREFIX . "_tags.id ORDER BY " . PREFIX . "_posts.date_posted DESC";
    $result = $db_conx_rdj->query($query);
    if ($result->rowCount() > 0) {
      while ($row = $result->fetch()) {
        $id = $row['id'];
        $posted_by = $row['posted_by'];
?>

        <!-- Display the articles -->
 <tr>
    <td><?php if($row['post_type'] == 2) { echo "Page";} elseif($row['post_type'] == 1)  {echo "Articles";}; ?></td>
    
    <td>    <a href="<?= $router->generate('single_post', ['id' => $id]); ?>">
              <?= Text::cutText($row['title'], 80) ?></a></td>
    <td>
    <a href="<?= $router->generate('profile', ['id' => $posted_by]); ?>">
                <?php if (isset($row['nice_nickname'])) {
                  echo $row['nice_nickname'];
                } else {
                  echo $row['username'];
                } ?></a>
    </td>
    <td></td>
    <td></td>
    <td></td>
 </tr>       
      <?php }
    } else {
      echo '<div id="widget" style="padding: 20px;">
<div class="bd-callout bd-callout-info">
 <p>Pas d\'articles.</p>
</div>
</div>';
    }
  }
}
class User {
 public static function getAvatar() {
  global $router;
  if (isset($_SESSION['user_id'])) {
    // Retrieve user information from the database
    $db = new Database;
    $db_conx_rdj = $db->connect();
    $query = "SELECT * FROM ".PREFIX."_users WHERE id = :id";
    $statement = $db_conx_rdj->prepare($query);
    $statement->execute([':id' => $_SESSION['user_id']]);
    $user = $statement->fetch(\PDO::FETCH_ASSOC);
    echo '
    <div class="dropdown dropstart">
    <div class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
    <img src="uploads/profile/'.$user['avatar'].'" alt="'.$user['username'].'" class="avatar rounded-circle mx-3" width="32" height="32">
    </div>
    
    
    
    
  <ul class="dropdown-menu text-small">
    <li><a class="dropdown-item" href="'. $router->generate('post-list').'">Liste des articles</a></li>
    <li><a class="dropdown-item" href="'. $router->generate('post-add').'">Ajouter un article</a></li>
    <li><a class="dropdown-item" href="'. $router->generate('add-draft').'">Ajouter une proposition d\'article</a></li>
    <li><a class="dropdown-item" href="'. $router->generate('view-drafts').'">Voir mes propositions d\'article</a></li>
    <li><hr class="dropdown-divider"></li>
    <li><a class="dropdown-item" href="'. $router->generate('user-add').'">Ajouter un user</a></li>
    <li><a class="dropdown-item" href="'. $router->generate('user-list').'">Voir les utilisateurs</a></li>
    <li><hr class="dropdown-divider"></li>
    <li><a class="dropdown-item" href="'. $router->generate('profile').'">Voir mon profil</a></li>
    <li><a class="dropdown-item" href="'. $router->generate('settings').'">Paramètres</a></li>
    <li><a class="dropdown-item" href="'. $router->generate('logout').'">Se déconnecter</a></li>
  </ul>
</div>';
  }
}
}
