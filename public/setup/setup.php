<?php

use App\StaticContent;

include('../../config/constants.php');
include('../../resources/classes/static.class.php');
include('../../resources/lang/lang-fr.php');
?>
<html>

<head>
    <style>
        pre {
            line-height: 20px;
        }
    </style>
    <?= StaticContent::getStyleSheet(); ?>
    <title>noordotda RadioDJ Plugin Setup Script</title>
</head>

<body class="bg-dark">
    <div class='text-center' style="margin:50px 0 0 0;"> <img src="logo.png" width="150px" height="150px" alt="rewindRadio logo"></div>

    <div class="card mx-auto mt-3 card-dark" style="width: 50rem;">
        <div class="card-header">
            <h4><?= $lang['setupTitle']; ?> 1</h4>
        </div>
        <div class="card-body">
            <pre>
<?php

if (isset($_POST['prefix'])) {
    $prefix = $_POST['prefix'];
    session_start();
    $_SESSION['prefix'] = $prefix;

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        // Get form input
        $hostname = $_POST['hostname'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $database = $_POST['database'];
        $prefix = $_POST['prefix'];
        $addFakeData = isset($_POST['addFakeData']) ? true : false;
        $deleteFakeData = isset($_POST['deleteFakeData']) ? true : false;
        $firstUsername = $_POST['siteUsername'];
        $firstPassword = $_POST['sitePassword'];
        $firstUserEmail = $_POST['userEmail'];
        $site_name = $_POST['site_name'];

        // Connect to the database
        try {
            $conn = new PDO("mysql:host=$hostname", $username, $password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "Connected to the database.<br>";
        } catch (PDOException $e) {
            die("Error connecting to the database: " . $e->getMessage());
        }
        // Check if the database exists
        $stmt = $conn->prepare("SHOW DATABASES LIKE ?");
        $stmt->execute([$database]);
        // Check if the delete fake data option was selected       
        if (isset($_POST['deleteFakeData'])) {
            // Delete fake data from the tables
            $stmt = $conn->prepare("DELETE * FROM " . $prefix . "_subcategory_info");
            $stmt->execute();
            $stmt = $conn->prepare("DELETE * FROM " . $prefix . "_events_info");
            $stmt->execute();
            $stmt = $conn->prepare("DELETE * FROM events");
            $stmt->execute();
            $stmt = $conn->prepare("DELETE * FROM  " . $prefix . "_posts");
            $stmt->execute();
            echo "Fake data deleted from the database.<br>";
        }
        // Check if the database exists
        $stmt = $conn->prepare("SHOW DATABASES LIKE ?");
        $stmt->execute([$database]);
        if ($stmt->rowCount() == 0) {

            // Create the database
            $sql = "CREATE DATABASE $database";
            $conn->exec($sql);
            echo "Created the database.<br>";
        } else {
            echo "The database already exists.<br>";
        }

        // Connect to the database
        $conn = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Connected to the database.<br>";

        // Check if the tables and columns exist
        $stmt = $conn->prepare("SHOW TABLES LIKE ?");
        $stmt->execute(['subcategory']);
        if ($stmt->rowCount() == 0) {
            echo "The subcategory table doesn't exists. Please install RadioDJ before executing this setup.<br>";
            die();
        } else {
            echo "The subcategory table exists. Please proceed.<br>";
        }
        $stmt = $conn->prepare("SHOW TABLES LIKE ?");
        $stmt->execute(['events']);
        if ($stmt->rowCount() == 0) {
            echo "The events table doesn't exists. Please install RadioDJ before executing this setup.<br>";
            die();
        } else {
            echo "The events table exists. Please proceed.<br>";
        }

        // Check if the tables and columns exist
        $stmt = $conn->prepare("SHOW TABLES LIKE ?");
        $stmt->execute(['subcategory_info']);
        if ($stmt->rowCount() == 0) {

            // Create the subcategory_info table
            $sql = "CREATE TABLE " . $prefix . "_subcategory_info (
        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        subcategory_id INT NOT NULL,
        description VARCHAR(200) DEFAULT NULL,
        image VARCHAR(80) DEFAULT NULL,
        guests VARCHAR(20) DEFAULT NULL,
        curator VARCHAR(20) DEFAULT NULL,
        scheduleTime VARCHAR(20) DEFAULT NULL,
        scheduleDay VARCHAR(20) DEFAULT NULL,
        mxcloud VARCHAR(20) DEFAULT NULL,
        theme VARCHAR(80) DEFAULT NULL,
        tags VARCHAR(80) DEFAULT NULL,
        enabled INT(1) DEFAULT NULL,
        backtime DATE DEFAULT NULL,
        is_fake INT(1) DEFAULT 0,
        FOREIGN KEY (subcategory_id) REFERENCES subcategory(id)
        ) CHARACTER SET utf8 COLLATE utf8_general_ci;";
            $conn->exec($sql);
            echo "Created the subcategory_info table.<br>";
        } else {
            echo "The subcategory_info table already exists.<br>";
        }

        // Create the events_info table
        $sql = "CREATE TABLE " . $prefix . "_events_info (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
     events_id INT NOT NULL, 
    image VARCHAR(80) DEFAULT NULL,
    guests VARCHAR(20) DEFAULT NULL,
    curator VARCHAR(20) DEFAULT NULL,
    tags VARCHAR(80) DEFAULT NULL,
    is_fake INT(1) DEFAULT 0,
    FOREIGN KEY  (events_id) REFERENCES events(id)
    ) CHARACTER SET utf8 COLLATE utf8_general_ci;";
        $conn->exec($sql);
        echo "Created the events_info table.<br>";

        // Alter the events table
        $sql = "ALTER TABLE events
    ADD is_fake TINYINT(1) NOT NULL DEFAULT 0;";
        $conn->exec($sql);
        echo "Alter the events table for detecting the fake events created with this script.<br>We will remove it when you delete the fake content.<br>";

        // Create the likes table
        $sql = "CREATE TABLE likes (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT UNSIGNED NOT NULL,
        song_id INT UNSIGNED NOT NULL,
        type ENUM('like','dislike') NOT NULL,
        FOREIGN KEY (song_id) REFERENCES songs(id) ON DELETE CASCADE
    ) CHARACTER SET utf8 COLLATE utf8_general_ci;";
        $conn->exec($sql);
        echo "Created the likes table.<br>";

        // Create the PREFIX_users table
        $sql = "CREATE TABLE " . $prefix . "_users (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    avatar VARCHAR(255),
    last_login DATETIME,
    member_since DATETIME,
    bio VARCHAR(255),
    job_title VARCHAR(255),
    facebook VARCHAR(255),
    instagram VARCHAR(255),
    twitter VARCHAR(255),
    twitch VARCHAR(255),
    tiktok VARCHAR(255),
    snapchat VARCHAR(255),
    discord VARCHAR(255),
    linkedin VARCHAR(255),
    first_name VARCHAR(255),
    last_name VARCHAR(255),
    nice_nickname VARCHAR(255),
    email VARCHAR(255),
    background_image VARCHAR(255),
    fav_quote VARCHAR(255),
    is_fake INT(1) DEFAULT 0,
    shows_id INT,
    posts_id INT
    ) CHARACTER SET utf8 COLLATE utf8_general_ci;";
        $conn->exec($sql);
        echo "Created the new users table. We did a new table to not mess with radioDJ tables. <br>";

        $sql = "CREATE TABLE " . $prefix . "_user_level (
        id INT NOT NULL PRIMARY KEY,
        level_name VARCHAR(20) NOT NULL,
        nice_name VARCHAR(50) NOT NULL,
        id_caps VARCHAR(50) NULL,
        level_ids VARCHAR(50) NULL);";
        $conn->exec($sql);
        echo "Created the user_level table. <br>";

        $sql = "INSERT INTO " . $prefix . "_user_level (id, level_name, nice_name, level_ids) VALUES
        (1, 'superadmin', 'Administrateur Root',NULL),
        (2, 'administrator', 'Administrateur',NULL),
        (3, 'blogEditor', 'Writer Manager',NULL),
        (4, 'hosteditor', 'Hosts Manager',NULL),
        (5, 'author', 'Auteur.trice',NULL),
        (6, 'host', 'Host / DJ',NULL),
        (7, 'contributor', 'Contributeur.trice',NULL),
        (8, 'subscriber', 'Abonn??.e',NULL);";
        $conn->exec($sql);
        echo "Insert the user levels . <br>";

        $sql = "CREATE TABLE " . $prefix . "_capabilities (
        `id` int(11) NOT NULL,
        `capability` varchar(50) NOT NULL,
        `nice_capname` varchar(50) NOT NULL
      );";
        $conn->exec($sql);
        echo "Created the capabalities table. <br>";

        $sql = "INSERT INTO " . $prefix . "_capabilities (id, capability, nice_capname) VALUES
      (1, 'create_post', 'Cr??er un article'),
      (2, 'edit_post', '??diter un article'),
      (3, 'publish_post', 'Publier un article'),
      (4, 'propose_post', 'Proposer un article'),
      (5, 'delete_propose_post', 'Supprimer une proposition'),
      (6, 'edit_propose_post', '??diter une proposition'),
      (7, 'edit_all_post', 'Voir tous les articles'),
      (8, 'delete_post', 'Supprimer un article'),
      (9, 'manage_categories', 'G??rer les cat??gories'),
      (10, 'upload_avatar', 'T??l??verser un avatar'),
      (11, 'upload_background', 'T??l??verser une image de fond'),
      (12, 'create_profile', 'Cr??er un utilisateur'),
      (13, 'edit_profile', '??diter mon profil'),
      (14, 'delete_profile', 'Supprimer mon profil'),
      (15, 'publish_profile', 'Activer un profil'),
      (16, 'add_episode', 'Ajouter un ??pisode'),
      (17, 'delete_episode', 'Supprimer un ??pisode'),
      (18, 'overwrite_episode', '??craser un ??pisode'),
      (19, 'create_show_page', 'Cr??er une page d\'??mission'),
      (20, 'publish_show', 'Publier une page d\'??mission'),
      (21, 'edit_show_page', '??diter une page d\'??mission'),
      (22, 'delete_show_page', 'supprimer une page d\'??mission');";
        $conn->exec($sql);
        echo "Created the capabilities table. <br>";

        // Create the news categories tables
        $sql = "CREATE TABLE " . $prefix . "_categories (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    is_fake INT(1) DEFAULT 0,
    slug VARCHAR(255) NOT NULL
    ) CHARACTER SET utf8 COLLATE utf8_general_ci;";
        $conn->exec($sql);
        echo "Created the categories table.<br>";

        // Create the news tags tables
        $sql = "CREATE TABLE " . $prefix . "_tags (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    is_fake INT(1) DEFAULT 0,
    slug VARCHAR(255) NOT NULL
    ) CHARACTER SET utf8 COLLATE utf8_general_ci;";
        $conn->exec($sql);
        echo "Created the tags table.<br>";

        // Create the news posts tables
        $sql = "CREATE TABLE " . $prefix . "_posts (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    date_posted DATETIME,
    posted_by INTEGER,
    slug VARCHAR(255) NOT NULL,
    featured_image VARCHAR(255),
    post_type VARCHAR(255),
    category_id INT(11),
    tag_id INT(11),
    is_fake INT(1) DEFAULT 0,
    is_featured INTEGER,
    FOREIGN KEY (posted_by) REFERENCES users(id)
    ) CHARACTER SET utf8 COLLATE utf8_general_ci;";
        $conn->exec($sql);
        echo "Created the posts table.<br>";

        $sql = "SET FOREIGN_KEY_CHECKS = 0;   
    CREATE TABLE " . $prefix . "_post_categories (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    post_id INTEGER NOT NULL,
    category_id INTEGER NOT NULL,
    is_fake INT(1) DEFAULT 0,
    FOREIGN KEY (post_id) REFERENCES posts(id),
    FOREIGN KEY (category_id) REFERENCES categories(id)
    ) CHARACTER SET utf8 COLLATE utf8_general_ci;";
        $conn->exec($sql);
        echo "Created the post_categories table.<br>";

        $sql = "SET FOREIGN_KEY_CHECKS = 0;  
    CREATE TABLE " . $prefix . "_post_tags (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    post_id INTEGER NOT NULL,
    tag_id INTEGER NOT NULL,
    is_fake INT(1) DEFAULT 0,
    FOREIGN KEY (post_id) REFERENCES posts(id),
    FOREIGN KEY (tag_id) REFERENCES tags(id)
    ) CHARACTER SET utf8 COLLATE utf8_general_ci;";
        $conn->exec($sql);
        echo "Created the post_tags table.<br>";

        // Insert fake data (optional)
        if ($addFakeData) {
            $sql = "INSERT INTO " . $prefix . "_subcategory_info (subcategory_id, description, image, guests, curator, scheduleTime, scheduleDay, mxcloud, theme, tags, enabled, backtime)
    VALUES (18, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'fake-image-1.jpg', '2-4', 'John Smith', '20:00', 'Monday', 'mxcloud1', 'Nature', 'outdoor, adventure', 1, '2022-01-01')";
            $conn->exec($sql);

            $sql = "INSERT INTO " . $prefix . "_subcategory_info (subcategory_id, description, image, guests, curator, scheduleTime, scheduleDay, mxcloud, theme, tags, enabled, backtime)
    VALUES (19, 'Suspendisse potenti. In hac habitasse platea dictumst.', 'fake-image-2.jpg', '4-6', 'Jane Doe', '19:00', 'Wednesday', 'mxcloud2', 'Arts', 'painting, music', 1, '2022-02-01')";
            $conn->exec($sql);

            $sql = "INSERT INTO " . $prefix . "_subcategory_info (subcategory_id, description, image, guests, curator, scheduleTime, scheduleDay, mxcloud, theme, tags, enabled, backtime)
    VALUES (30, 'Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae.', 'fake-image-3.jpg', '6-8', 'Bob Johnson', '21:00', 'Friday', 'mxcloud3', 'Food', 'cooking, wine tasting', 1, '2022-03-01')";
            $conn->exec($sql);
            echo "Added fake data to the subcategory_info table.<br>";

            $sql = "INSERT INTO events_categories (ID, NAME) VALUES(50, 'Website Events')";
            $conn->exec($sql);
            echo "Added a category for the website events.<br>";

            $sql = "INSERT INTO events ( ID, TYPE, TIME, NAME, DATE, DAY, hours, DATA, enabled, catID, smart, is_fake ) VALUES(1, 0, '21:00:00', 'First Fake Event', '2023-06-18', '&6', '&20', 'Clear Playlist!', TRUE, 50, FALSE, 1 )";
            $conn->exec($sql);

            $sql = "INSERT INTO events ( ID, TYPE, TIME, NAME, DATE, DAY, hours, DATA, enabled, catID, smart, is_fake ) VALUES(2, 0, '15:00:00', 'Second Fake Event', '2023-01-27', '&3', '&18', 'Clear Playlist!', TRUE, 50, FALSE, 1 )";
            $conn->exec($sql);

            $sql = "INSERT INTO events ( ID, TYPE, TIME, NAME, DATE, DAY, hours, DATA, enabled, catID, smart, is_fake ) VALUES(3, 0, '15:00:00', 'Third Fake Event', '2023-01-25', '&4', '&19', 'Clear Playlist!', TRUE, 50, FALSE, 1 )";
            $conn->exec($sql);


            $sql = "INSERT INTO " . $prefix . "_events_info (events_id, image, guests, curator, tags) VALUES (1, 'fake-image-1.jpg', '', 'John Smith', 'Nature, outdoor, adventure')";
            $conn->exec($sql);
            $sql = "INSERT INTO " . $prefix . "_events_info (events_id, image, guests, curator, tags)
    VALUES (2, 'fake-image-2.jpg', '', 'Jane Doe', 'painting, music')";
            $conn->exec($sql);
            $sql = "INSERT INTO " . $prefix . "_events_info (events_id, image, guests, curator, tags)
    VALUES (3, 'fake-image-3.jpg', '', 'Bob Johnson', 'Food, cooking, wine tasting')";
            $conn->exec($sql);
            echo "Added fake data to the events_info table.<br>";

            $sql = "INSERT INTO " . $prefix . "_posts (title, content, date_posted, posted_by, slug, featured_image, post_type,category_id, tag_id, is_fake, is_featured)
    VALUES ('??v??nement ?? venir ?? la radio', 'Notre webradio pr??pare un ??v??nement passionnant pour les amateurs de musique. Rejoignez-nous pour un week-end de musique non-stop! La prochaine grande chose ?? laquelle nous travaillons est un festival de musique en direct qui aura lieu les [dates]. Ce sera un week-end de musique non-stop, avec des artistes talentueux qui viendront sur sc??ne pour vous offrir les meilleures performances. Cette ann??e, nous avons une ligne incroyable d\'artistes, y compris [nom d\'artistes]. Ne manquez pas l\'occasion d\'assister ?? ce festival en direct et de vibrer au son de la musique. Rejoignez-nous pour c??l??brer la musique et passer un week-end inoubliable!', NOW(), '1', 'event-to-our-radio', 'pexels-taryn-elliott-6829488.jpg',1,1,1,1,0),

    ('B??n??volat chez rewindRadio', 'Un billet sur un ??v??nement pass?? ?? la radio :
    Nous venons de vivre un ??v??nement incroyable ?? notre webradio. Jetez un coup d\oeil ?? quelques moments cl??s de cette soir??e m??morable.
    
    La semaine derni??re, nous avons eu le plaisir d\'accueillir [nom d\'artiste] en direct ?? notre studio pour un concert exceptionnel. Cette soir??e ??tait remplie d\'??nergie et de musique enivrante, et nous sommes ravis de partager quelques-uns de nos moments pr??f??r??s avec vous.
    
    caption: [Nom de l\'artiste] en train de donner un spectacle ??poustouflant sur sc??ne.
    
        
    Ce fut une soir??e incroyable et nous remercions [nom de l\'artiste] pour ce moment m??morable.', NOW(), '1', 'chez-rewindradio', 'pexels-nati-14642654.jpg',1,1,1,1,0),
    
    ('B??n??volat chez rewindRadio', '<p>Vous souhaitez vous investir dans votre communaut?? et faire une diff??rence dans votre ville? Rejoignez notre ??quipe de b??n??voles pour notre organisme de radio communautaire!
    </p><p>
    En tant que b??n??vole, vous aurez la chance de participer ?? la cr??ation de contenu passionnant et de contribuer ?? l\'animation de notre radio. Que vous soyez passionn?? de musique, de journalisme ou que vous cherchiez simplement ?? vous faire de nouveaux amis, nous avons une place pour vous.
    
    </p><p>Le b??n??volat pour notre radio communautaire est une exp??rience enrichissante qui vous permettra de d??velopper de nouvelles comp??tences, de rencontrer de nouvelles personnes et de contribuer ?? votre communaut??. Alors n\'h??sitez pas ?? nous contacter pour en savoir plus sur les opportunit??s de b??n??volat disponibles. Nous esp??rons vous voir bient??t!</p>', NOW(), '1', 'privacy-policy', 'pexels-nati-14642654.jpg',2,1,1,1,0),

    ('Politique de confidentialit??', '<p>Nous ne pouvons pas vous fournir de lien vers un mod??le de politique de confidentialit??, mais voici quelques ??l??ments ?? inclure dans une politique de confidentialit?? pour une webradio :</p>
<ul>
   <li>Pr??sentation de votre entreprise et de votre webradio, y compris les coordonn??es et les informations de contact.</li>
   <li>Description de la mani??re dont vous collectez, utilisez et partagez les donn??es de vos utilisateurs, y compris les donn??es collect??es par les cookies et les technologies de suivi.</li>
   <li>Description de vos pratiques de s??curit?? pour prot??ger les donn??es de vos utilisateurs.</li>
   <li> Informations sur vos politiques de confidentialit?? en mati??re de publicit?? en ligne et de ciblage des annonces.</li>
   <li> Informations sur vos pratiques en mati??re de notification des changements apport??s ?? votre politique de confidentialit??.</li>
   <li> Informations sur vos pratiques en mati??re de gestion des droits des utilisateurs en ce qui concerne leurs donn??es personnelles.</li>
   <li> Informations sur la mani??re dont vous g??rez les plaintes et les demandes de vos utilisateurs en mati??re de confidentialit??.</li>
   <li> Informations sur la loi applicable et sur la mani??re dont vous g??rez les diff??rends ??ventuels.</li></ul>
   <p> Il est important de se rappeler que chaque pays et chaque r??gion ont leurs propres lois et r??glementations en mati??re de confidentialit??, il est donc important de consulter un avocat pour vous assurer que votre politique de confidentialit?? est conforme aux lois en vigueur.', NOW(), '1', 'privacy-policy', 'pexels-nati-14642654.jpg',2,1,1,1,0);";
            $conn->exec($sql);
            echo "Added fake data to the post table.<br>";
            $sql = "INSERT INTO " . $prefix . "_users( username, PASSWORD, avatar, last_login, member_since, bio, job_title, sm_facebook, sm_twitter, sm_instagram, sm_twitch, sm_tiktok, sm_discord, first_name, last_name, nice_nickname, email, background_image, fav_quote, is_fake) 

VALUES ('$firstUsername', '$firstPassword', 'AvatarMaker03.png', NULL, NULL, 'My bio', 'Founder & CEO', 'rewindRadio', 'rewindRadio', 'rewindRadio', 'rewindRadio', 'rewindRadio', 'rewindRadio', 'Osakari', 'Yasuhiro', 'Osakari Yasuhiro', '$firstUserEmail' , 'background_image.jpg', 'Be yourself. Everyone else is already taken. ??? Oscar Wilde', 0),

('Gallo2002', 'Iethue9ohph', 'AvatarMaker02.png', '2016-02-09 21:12:40', '2016-01-10 14:52:54', 'Certified explorer. Beer scholar. Food expert. Bacon lover. Creator. Troublemaker. Music junkie.', 'Directrice de cr??ation', 'rewindRadio', 'rewindRadio', 'rewindRadio', 'rewindRadio', 'rewindRadio', 'rewindRadio', 'Jennifer', 'Galloway', 'Jennifer Galloway', 'gallo2002@example.com', 'background_image', 'Create with the heart; build with the mind.', 0),

('john_doe', 'password1', 'https://example.com/avatar1.jpg', '2022-12-01 10:00:00', '2022-10-01 10:00:00', 'John Doe is a software engineer with 5 years of experience.', 'Software Engineer', 'https://facebook.com/johndoe', 'https://twitter.com/johndoe', 'https://instagram.com/johndoe', 'https://twitch.com/johndoe', 'https://tiktok.com/johndoe', 'https://discord.com/johndoe', 'John', 'Doe', 'johndoe', 'john.doe@example.com', 'https://example.com/background1.jpg', 'Code is poetry.', 0),

('jane_doe', 'password2', 'https://example.com/avatar2.jpg', '2022-11-15 12:00:00', '2022-09-01 10:00:00', 'Jane Doe is a graphic designer with 7 years of experience.', 'Graphic Designer', 'https://facebook.com/janedoe', 'https://twitter.com/janedoe', 'https://instagram.com/janedoe', 'https://twitch.com/janedoe', 'https://tiktok.com/janedoe', 'https://discord.com/janedoe', 'Jane', 'Doe', 'janedoe', 'jane.doe@example.com', 'https://example.com/background2.jpg', 'Design is not just what it looks like and feels like. Design is how it works.', 0);";

            $conn->exec($sql);
            echo "Added fake data to the news_user table. Don't worry, it won't mess with rdj users table.<br>";

            $sql = "INSERT INTO " . $prefix . "_categories (name, slug) VALUES('News', 'news'), ('Tech', 'tech'), ('Entertainment', 'entertainment')";
            $conn->exec($sql);
            echo "Added fake data to the categories table.<br>";
        }

        // Create the insert_subcategory_info trigger
        $sql = "CREATE TRIGGER insert_subcategory_info
    AFTER INSERT ON subcategory
    FOR EACH ROW
    BEGIN
    INSERT INTO " . $prefix . "_subcategory_info (subcategory_id)
    VALUES (NEW.id);
    END";
        $conn->exec($sql);
        echo "Created the insert_subcategory_info trigger.<br>";

        // Create the insert_subcategory_info trigger
        $sql = "CREATE TRIGGER insert_event_info AFTER INSERT ON EVENTS FOR EACH ROW
    INSERT INTO noor_events_info(subcategory_id)
    VALUES(NEW.id);";
        $conn->exec($sql);
        echo "Created the insert_events_info trigger.<br>";

        // Append the database constants to the config.php file
        $language = $_POST["language"];
        $config = "<?php\n";
        $config .= "define('PREFIX', '{$prefix}');\n";
        $config .= "define('DBHOST', '{$hostname}');\n";
        $config .= "define('DBNAME', '{$database}');\n";
        $config .= "define('DBUSER', '{$username}');\n";
        $config .= "define('DBPASSWORD', '{$password}');\n\n";
        $config .= "?>";
        file_put_contents('../../config/config.php', $config, FILE_APPEND);

        // Append the site constants to the constants.php file
        $constants = "\ndefine('SITE_NAME', '{$site_name}');\n";
        $constants .= "define('LANG', '{$language}');\n";
        file_put_contents('../../config/constants.php', $constants, FILE_APPEND);
        echo "Setup completed successfully!</pre>
    <a href='/'><button class='btn btn-dark'>Aller sur votre site web</button></a>";
    }
} ?>
    </div>
    </div>
    </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>


</body>
</html>