<?php
require_once("API_fonctions.php");
require('jwt_utils.php');
session_start();

?>

<!DOCTYPE html>
<html>

<head>
    <title>Tokyon</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    <header>
        <h1>Tokyon \ <?php echo $_SESSION['username'] ?></h1>
        <nav>
            <ul>
                <li><a href="API.php">Accueil</a></li>
                <li><a href="mesPosts.php">Mes posts</a></li>
                <li><a href="#">Paramètres</a></li>
            </ul>
        </nav>
    </header>
    <?php

    if (isset($_POST['supprimer'])) {
        delete($_POST['supprimer']);
    }

    ?>
    <main>
        <h1>Mes posts</h1>
        <section class="post-list">
            <?php
            listArticles($_SESSION['IdUser']);
            ?>
        </section>
    </main>
    <footer>
        <p>&copy; 2023 My Chat App</p>
    </footer>
</body>

</html>