<?php
require_once("API_fonctions.php");
require('jwt_utils.php');
session_start();

// récuperer les informations de l'utilisateur depuis le token
if (isset($_SESSION['jwt'])) {
  $_SESSION['username'] = json_decode(jwt_decode($_SESSION['jwt']), true)['username'];
  $_SESSION['IdUser'] = json_decode(jwt_decode($_SESSION['jwt']), true)['IdUser'];
  $_SESSION['IdRole'] = json_decode(jwt_decode($_SESSION['jwt']), true)['IdRole'];
  $_SESSION['exp'] = json_decode(jwt_decode($_SESSION['jwt']), true)['exp'];
}
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

  if (isset($_POST['add']) && isset($_POST['contenu'])) {
    methodePost("contenu");
  }

  if (isset($_POST['supprimer'])) {
    delete($_POST['supprimer']);
  }

  if (isset($_POST['modifier'])) {
    modification();
  }

  if (isset($_POST['modification'])) {
    methodePut($_POST['modification'], $_POST['contenu']);
  }

  if (isset($_POST['like']) && isset($_SESSION['IdUser'])) {
    methodePostAvis($_POST['like'], $_POST['hidden'], $_SESSION['IdUser']);
  }
  if (isset($_POST['dislike']) && isset($_SESSION['IdUser'])) {
    methodePostAvis($_POST['dislike'], $_POST['hidden'], $_SESSION['IdUser']);
  }

  ?>
  <main>
    <section class="post-form">
      <form method="POST">
        <label for="contenu">Ajouter un nouvel article:</label>
        <textarea id="post" name="contenu" rows="3"></textarea>
        <button type="submit" name="add">Poster</button>
      </form>
    </section>
    <section class="post-list">
      <?php
      listArticles("");
      ?>
    </section>
  </main>
  <footer>
    <p>&copy; 2023 My Chat App</p>
  </footer>
</body>

</html>