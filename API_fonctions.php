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
          <button type="submit" class="supprimer" name="supprimer" value="' . $v['Id_Article'] . '">Supprimer</button>
          <button class="modifier" name="modifier" value="' . $v['Id_Article'] . '">Modifier</button>
          <p>' . $v['Date_Publication'] . '.</p>
          <footer>
            <button type="submit" class="like" name="like" value="1">Like</button>
            <button type="submit" class="dislike" name="dislike" value="0">Dislike</button>
            <input type="hidden" name="hidden" value="' . $v['Id_Article'] . '"></input>
            <span class="likes">0 likes</span>
          </footer>
        </article>';
  }
  echo '</form></section>';
}

function GetOrGetId($post)
{
  if ($post == "") {
    $result = file_get_contents(
      'http://localhost/Projet_API/serveur.php',
      false,
      stream_context_create(array('http' => array(
        'method' => 'GET'
      )))
    );
  } elseif (!isset($_POST[$post])) {
    $result = file_get_contents(
      'http://localhost/Projet_API/serveur.php?idUti=' . $post,
      false,
      stream_context_create(array('http' => array(
        'method' => 'GET'
      )))
    );
  } else {
    $result = file_get_contents(
      'http://localhost/Projet_API/serveur.php?id=' . $_POST[$post],
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
    'http://localhost/Projet_API/serveur.php?id=' . $_SESSION['IdUser'],
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
            <button type="submit" class="like" name="like" value="1">Like</button>
            <button type="submit" class="dislike" name="dislike" value="0">Dislike</button>
            <input type="hidden" name="hidden" value="' . $v['Id_Article'] . '"></input>
            <span class="likes">0 likes</span>
          </footer>';
    if ($v['Id_Utilisateur'] == $_SESSION['IdUser'] || $_SESSION['IdRole'] == 1) {
      echo '<button class="supprimer" name="supprimer" value="' . $v['Id_Article'] . '">Supprimer</button>';
    }
    if ($v['Id_Utilisateur'] == $_SESSION['IdUser'] && $_SESSION['IdRole'] == 2) {
      echo '<button class="modifier" name="modifier" value="' . $v['Id_Article'] . '">Modifier</button>';
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

function methodePut($put, $postArray)
{
  $data = array("contenu" => $postArray);
  $data_string = json_encode($data);
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

function methodePostAvis($avis, $article, $utilisateur)
{
  $data = array("avis" => $avis, "utilisateur" => $utilisateur);
  $data_string = json_encode($data);
  /// Envoi de la requête
  $result = file_get_contents(
    'http://localhost/Projet_API/serveur.php?id=' . $article,
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

function modification()
{
  $data = GetOrGetId("modifier");
  foreach ($data['data'] as $v) {
    echo '
      <label for="contenu">Modification:</label>
      <form method="POST">
        <article>
          <header>
            <textarea id="post" name="contenu" rows="3">' . $v['Contenu'] . '</textarea>
            <button type="submit" name="modification" value="' . $v['Id_Article'] . '">modifier</button>
          </header>
        </article>
      </form>';
  }
}
