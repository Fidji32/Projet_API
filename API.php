<?php
require_once("functions.php");
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