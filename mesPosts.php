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
        <?php
        if (isset($_SESSION['jwt'])) {
            echo '<h1>Tokyon \ ' . $_SESSION["username"] . '</h1>';
        } else {
            echo '<h1>Tokyon \ anonymous</h1>';
        }
        ?>
        <nav>
            <ul>
                <li><a href="API.php">Accueil</a></li>
                <li><a href="mesPosts.php">Mes posts</a></li>
                <li><a href="deconnexion.php">Déconnexion</a></li>
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
            if (isset($_SESSION['IdUser'])) {
                listArticles($_SESSION['IdUser']);
            }
            ?>
        </section>
    </main>
    <footer>
        <p>&copy; 2023 My Chat App</p>
    </footer>
</body>

</html>