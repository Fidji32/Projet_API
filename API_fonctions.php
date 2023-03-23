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
        'method' => 'GET',
        'header' => array('Content-Type: application/json' . "\r\n"
          . 'Authorization: Bearer ' . $_SESSION['jwt'] . "\r\n")
      )))
    );
  } else {
    $result = file_get_contents(
      'http://localhost/Projet_API/serveur.php?id=' . $post,
      false,
      stream_context_create(array('http' => array(
        'method' => 'GET',
        'header' => array('Content-Type: application/json' . "\r\n"
          . 'Authorization: Bearer ' . $_SESSION['jwt'] . "\r\n")
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
    echo '<form action="">
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
          </footer>
        </article>
    </form>';
  }
}
