<?php
require_once("functions.php");
require('jwt_utils.php');
session_start();

// récuperer les informations de l'utilisateur depuis le token
$username = json_decode(jwt_decode($_SESSION['jwt']), true)['username'];
$IdUser = json_decode(jwt_decode($_SESSION['jwt']), true)['IdUser'];
$IdRole = json_decode(jwt_decode($_SESSION['jwt']), true)['IdRole'];
$exp = json_decode(jwt_decode($_SESSION['jwt']), true)['exp'];

?>

<!DOCTYPE html>
<html>

<head>
  <title>Tokyon</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
  <header>
    <h1>Tokyon</h1>
    <nav>
      <ul>
        <li><a href="#">Accueil</a></li>
        <li><a href="#">Profil</a></li>
        <li><a href="#">Paramètres</a></li>
      </ul>
    </nav>
  </header>
  <?php

  if (isset($_POST['add']) && isset($_POST['contenu'])) {
    methodBody("contenu");
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