<?php
session_start();
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" type="text/css" href="styleConnexion.css">
  <title>Page d'authentification</title>
</head>

<?php

function methodeBody($postArray, $postArray2)
{
  $data = array("login" => $postArray, "mdp" => $postArray2);
  $data_string = json_encode($data);
  /// Envoi de la requête
  $result = file_get_contents(
    'http://localhost/Projet_API/auth.php',
    false,
    stream_context_create(array(
      'http' => array(
        'method' => 'POST', // ou PUT
        'content' => $data_string,
        'header' => array('Content-Type: application/json' . "\r\n"
          . 'Content-Length: ' . strlen($data_string) . "\r\n")
      )
    ))
  );
  $data = json_decode($result, true);
  if ($data['data'] == "error") {
    echo '<p class="error-message">Login ou mot de passe invalide !</p>';
  } else {
    $_SESSION['jwt'] = $data['data'];
    //header('location: API.php');
    echo $data['data'];
  }
}

?>

<body>
  <div class="login-container">
    <h1>Connexion</h1>
    <form method="post">
      <label for="username">Nom d'utilisateur</label>
      <input type="text" id="username" name="username" required>

      <label for="password">Mot de passe</label>
      <input type="password" id="password" name="password" required>

      <button type="submit">Se connecter</button>

      <div class="error-message">
        <?php
        if (isset($_POST['username']) && isset($_POST['password'])) {
          // vérifier les identifiants et afficher un message d'erreur si nécessaire
          methodeBody($_POST['username'],$_POST['password']);
        }
        ?>
      </div>
    </form>
  </div>
</body>

</html>