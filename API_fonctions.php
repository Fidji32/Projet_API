<?php

function retourDonnees($data)
{

  echo '<section class="post-form"><form method="POST"><label for="contenu">Dernière modification:</label>';
  foreach ($data['data'] as $v) {
    echo '
        <article>
          <header class="last">
            <p>' . $v['Contenu'] . '</p>
            <p>' . $v['Nom'] . '</p>
          </header>
          <button type="submit" class="delete" name="delete">Supprimer</button>
          <p>' . $v['Date_Publication'] . '.</p>
          <footer>
            <button class="like" name="like">Like</button>
            <button class="dislike" name="dislike">Dislike</button>
            <span class="likes">0 likes</span>
          </footer>
        </article>';
  }
  echo '</form></section>';
}

function GetOrGetId($post)
{
  if (!isset($_POST[$post])) {
    $result = file_get_contents(
      'http://localhost/Projet_API/serveur.php',
      false,
      stream_context_create(array('http' => array(
        'method' => 'GET'
      )))
    );
  } else {
    $result = file_get_contents(
      'http://localhost/Projet_API/serveur.php?id=' . $post,
      false,
      stream_context_create(array('http' => array(
        'method' => 'GET'
      )))
    );
  }
  return json_decode($result, true);
}

function methodePost($postArray)
{
  $data = array("contenu" => $_POST[$postArray]);
  $data_string = json_encode($data);
  /// Envoi de la requête
  $result = file_get_contents(
    'http://localhost/Projet_API/serveur.php',
    false,
    stream_context_create(array(
      'http' => array(
        'method' => 'POST',
        'content' => $data_string,
        'header' => array('Content-Type: application/json' . "\r\n"
          . 'Content-Length: ' . strlen($data_string) . "\r\n"
          . 'Authorization: Bearer ' . $_SESSION['jwt'] . "\r\n")
      )
    ))
  );
  $data = json_decode($result, true);
  retourDonnees($data);
}

function listArticles($post)
{
  $data = GetOrGetId($post);
  foreach ($data['data'] as $v) {
    echo '<form method="POST">
        <article>
          <header>
            <p>' . $v['Contenu'] . '</p>
            <p>' . $v['Nom'] . '</p>
          </header>
          <p>' . $v['Date_Publication'] . '.</p>
          <footer>
            <button class="like" name="like">Like</button>
            <button class="dislike" name="dislike">Dislike</button>
            <span class="likes">0 likes</span>
          </footer>';
    if ($v['Id_Utilisateur'] == $_SESSION['IdUser']) {
      echo '<button class="supprimer" name="supprimer" value="' . $v['Id_Article'] . '">Supprimer</button>';
    }
    echo '</article></form>';
  }
}

function delete()
{
  if (isset($_POST['supprimer'])) {
    $result = file_get_contents(
      'http://localhost/Projet_API/serveur.php?id=' . $_POST['supprimer'],
      false,
      stream_context_create(array('http' => array(
        'method' => 'DELETE',
        'header' => 'Authorization: Bearer ' . $_SESSION['jwt']
      )))
    );
  }
}
function methodePut($put){
  /// Envoi de la requête
  $result = file_get_contents(
    'http://localhost/Projet_API/serveur.php?id=' . $put,
    false,
    stream_context_create(array(
      'http' => array(
        'method' => 'PUT',
        'content' => $data_string,
        'header' => array('Content-Type: application/json' . "\r\n"
          . 'Content-Length: ' . strlen($data_string) . "\r\n"
          . 'Authorization: Bearer ' . $_SESSION['jwt'] . "\r\n")
      )
    ))
  );
  $data = json_decode($result, true);
  retourDonnees($data);
}

