<?php
include("jwt_utils.php");

function connexionBd()
{
  // informations de connection
  $SERVER = '127.0.0.1';
  $DB = 'gestion_articles';
  $LOGIN = 'root';
  $MDP = '';
  // tentative de connexion à la BD
  try {
    // connexion à la BD
    $linkpdo = new PDO("mysql:host=$SERVER;dbname=$DB", $LOGIN, $MDP);
  } catch (Exception $e) {
    die('Erreur ! Problème de connexion à la base de données : ' . $e->getMessage());
  }
  // retourne la connection
  return $linkpdo;
}

function is_connection_valid($login, $mdp)
{
  $linkpdo = connexionBd();
  // preparation de la Requête sql
  $req = $linkpdo->prepare('SELECT mail,Mdp,Nom,Id_Utilisateur,Id_Role FROM utilisateur where mail = :login and Mdp=:mdp');
  if ($req == false) {
    die('Erreur !');
  }
  // execution de la Requête sql
  $req->execute(array(
    'login' => $login,
    'mdp' => $mdp
  ));
  if ($req == false) {
    die('Erreur !');
  }
  if ($req->rowCount() > 0) {
    while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
      foreach ($data as $key => $value) {
        if ($key == 'Id_Utilisateur') {
          $idUser = $value;
        } elseif ($key == 'Id_Role') {
          $idRole = $value;
        } elseif ($key == 'Nom') {
          $nom = $value;
        }
      }
    }
    $headers = array('alg' => 'HS256', 'typ' => 'jwt');
    $payload = array('username' => $nom, 'IdUser' => $idUser, 'IdRole' => $idRole, 'exp' => (time() + 3600));
    $jwt = generate_jwt($headers, $payload);
    return $jwt;
  } else {
    return "error";
  }
}

function getArticleByIdArticle($id)
{
  $linkpdo = connexionBd();
  // preparation de la Requête sql
  $req = $linkpdo->prepare('SELECT Id_Article, Date_Publication, Contenu, Date_Modif, article.Id_Utilisateur, Nom FROM article, utilisateur where article.Id_Utilisateur = utilisateur.Id_Utilisateur AND Id_Article = :id');
  if ($req == false) {
    die('Erreur ! GetId');
  }
  // execution de la Requête sql
  $req->execute(array('id' => $id));
  if ($req == false) {
    die('Erreur ! GetId');
  }
  return $req->fetchAll();
}

function getReagisByIdArticle($id, $idUser)
{
  $linkpdo = connexionBd();
  // preparation de la Requête sql
  $req = $linkpdo->prepare('SELECT Id_Article, reagis.Id_Utilisateur, Avis, Nom FROM reagis, utilisateur where reagis.Id_Utilisateur = utilisateur.Id_Utilisateur AND Id_Article = :id AND reagis.Id_Utilisateur = :idUser');
  if ($req == false) {
    die('Erreur ! GetId');
  }
  // execution de la Requête sql
  $req->execute(array('id' => $id, 'idUser' => $idUser));
  if ($req == false) {
    die('Erreur ! GetId');
  }
  return $req->fetchAll();
}

function getArticleByIdUtilisateur($id)
{
  $linkpdo = connexionBd();
  // preparation de la Requête sql
  $req = $linkpdo->prepare('SELECT Id_Article, Date_Publication, Contenu, Date_Modif, article.Id_Utilisateur, Nom FROM article, utilisateur where article.Id_Utilisateur = utilisateur.Id_Utilisateur AND article.Id_Utilisateur = :id');
  if ($req == false) {
    die('Erreur ! GetId');
  }
  // execution de la Requête sql
  $req->execute(array('id' => $id));
  if ($req == false) {
    die('Erreur ! GetId');
  }
  return $req->fetchAll();
}

function getAllArticles()
{
  // Préparation de la requête
  $sql = "SELECT 
            article.Id_Article,
            article.Date_Publication,
            article.Contenu,
            article.Date_Modif,
            article.Id_Utilisateur,
            utilisateur.Nom,
            CONCAT('Likes (', COUNT(CASE WHEN reagis.Avis = 1 THEN 1 END), ')') AS Nb_likes,
            GROUP_CONCAT(DISTINCT utilisateur_likes.Nom ORDER BY utilisateur_likes.Nom SEPARATOR ', ') AS Utilisateurs_likes,
            CONCAT('Dislikes (', COUNT(CASE WHEN reagis.Avis = 2 THEN 1 END), ')') AS Nb_dislikes,
            GROUP_CONCAT(DISTINCT utilisateur_dislikes.Nom ORDER BY utilisateur_dislikes.Nom SEPARATOR ', ') AS Utilisateurs_dislikes
        FROM 
            article
            JOIN utilisateur ON article.Id_Utilisateur = utilisateur.Id_Utilisateur
            LEFT JOIN reagis ON article.Id_Article = reagis.Id_article
            LEFT JOIN utilisateur utilisateur_likes ON reagis.Id_Utilisateur = utilisateur_likes.Id_Utilisateur AND reagis.Avis = 1
            LEFT JOIN utilisateur utilisateur_dislikes ON reagis.Id_Utilisateur = utilisateur_dislikes.Id_Utilisateur AND reagis.Avis = 2
        GROUP BY 
            article.Id_Article";
  $linkpdo = connexionBd();
  // preparation de la Requête sql
  $req = $linkpdo->prepare($sql);
  if ($req == false) {
    die('Erreur ! GetAll');
  }
  // execution de la Requête sql
  $req->execute();
  if ($req == false) {
    die('Erreur ! GetAll');
  }
  return $req->fetchAll();
}

function getAllArticlesPublisher()
{
  // Préparation de la requête
  $query = 'SELECT 
            article.Id_Article,
            article.Date_Publication,
            article.Contenu,
            article.Date_Modif,
            article.Id_Utilisateur,
            utilisateur.Nom,
            CONCAT("Likes (", COUNT(CASE WHEN reagis.Avis = 1 THEN 1 END), ")") AS Nb_likes,
            GROUP_CONCAT(DISTINCT IF(reagis.Avis = 1, NULL, utilisateur_likes.Nom) ORDER BY utilisateur_likes.Nom SEPARATOR ", ") AS Utilisateurs_likes,
            CONCAT("Dislikes (", COUNT(CASE WHEN reagis.Avis = 2 THEN 1 END), ")") AS Nb_dislikes,
            GROUP_CONCAT(DISTINCT IF(reagis.Avis = 2, NULL, utilisateur_dislikes.Nom) ORDER BY utilisateur_dislikes.Nom SEPARATOR ", ") AS Utilisateurs_dislikes
        FROM 
            article
            JOIN utilisateur ON article.Id_Utilisateur = utilisateur.Id_Utilisateur
            LEFT JOIN reagis ON article.Id_Article = reagis.Id_article
            LEFT JOIN utilisateur utilisateur_likes ON reagis.Id_Utilisateur = utilisateur_likes.Id_Utilisateur AND reagis.Avis = 1
            LEFT JOIN utilisateur utilisateur_dislikes ON reagis.Id_Utilisateur = utilisateur_dislikes.Id_Utilisateur AND reagis.Avis = 2
        GROUP BY 
            article.Id_Article';
  $linkpdo = connexionBd();
  // preparation de la Requête sql
  $req = $linkpdo->prepare($query);
  if ($req == false) {
    die('Erreur ! GetAll');
  }
  // execution de la Requête sql
  $req->execute();
  if ($req == false) {
    die('Erreur ! GetAll');
  }
  return $req->fetchAll();
}

function getAllArticlesAnonymous()
{
  // Préparation de la requête
  $requete = "SELECT 
  article.Id_Article,
  article.Date_Publication,
  article.Contenu,
  article.Date_Modif,
  article.Id_Utilisateur,
  utilisateur.Nom,
  null AS Nb_likes,
  null AS Utilisateurs_likes,
  null AS Nb_dislikes,
  null AS Utilisateurs_dislikes
FROM 
  article
  JOIN utilisateur ON article.Id_Utilisateur = utilisateur.Id_Utilisateur
  LEFT JOIN reagis ON article.Id_Article = reagis.Id_article
  LEFT JOIN utilisateur utilisateur_likes ON reagis.Id_Utilisateur = utilisateur_likes.Id_Utilisateur AND reagis.Avis = 1
  LEFT JOIN utilisateur utilisateur_dislikes ON reagis.Id_Utilisateur = utilisateur_dislikes.Id_Utilisateur AND reagis.Avis = 2
GROUP BY 
  article.Id_Article";
  $linkpdo = connexionBd();
  // preparation de la Requête sql
  $req = $linkpdo->prepare($requete);
  if ($req == false) {
    die('Erreur ! GetAll');
  }
  // execution de la Requête sql
  $req->execute();
  if ($req == false) {
    die('Erreur ! GetAll');
  }
  return $req->fetchAll();
}

function post($phrase, $id)
{
  $linkpdo = connexionBd();
  // preparation de la Requête sql
  $req = $linkpdo->prepare('insert into article (Contenu,Date_Publication,date_Modif,Id_Utilisateur) value(:contenu,NOW(),NOW(),:idUtilisateur)');
  if ($req == false) {
    die('Erreur ! Post');
  }
  // execution de la Requête sql
  $req->execute(array(
    ':contenu' => $phrase,
    ':idUtilisateur' => $id
  ));
  if ($req == false) {
    die('Erreur ! Post');
  }
  // recuperation du dernier id
  $lastId = $linkpdo->lastInsertId();
  return getArticleByIdArticle($lastId);
}

function put($id, $contenu)
{
  $linkpdo = connexionBd();
  // preparation de la Requête sql
  $req = $linkpdo->prepare('update article set Contenu = :contenu, Date_Modif = NOW() where Id_Article = :id');
  if ($req == false) {
    die('Erreur ! Put');
  }
  // execution de la Requête sql
  $req->execute(array(':id' => $id, ':contenu' => $contenu));
  if ($req == false) {
    die('Erreur ! Put');
  }
  // recuperation du dernier id
  return getArticleByIdArticle($id);
}

function avis($avis, $idArticle, $idUser)
{
  $linkpdo = connexionBd();
  // preparation de la Requête sql
  $req = $linkpdo->prepare('REPLACE INTO `reagis` (`Id_Article`, `Id_Utilisateur`, `Avis`) VALUES (:idArticle,:idUser,:avis);');
  if ($req == false) {
    die('Erreur ! Avis');
  }
  // execution de la Requête sql
  $req->execute(array(
    ':idArticle' => $idArticle,
    ':idUser' => $idUser,
    ':avis' => $avis
  ));
  if ($req == false) {
    die('Erreur ! Avis');
  }
  // recuperation du dernier id
  return getReagisByIdArticle($idArticle, $idUser);
}

function delete($id)
{
  $linkpdo = connexionBd();
  // preparation de la Requête sql
  $req = $linkpdo->prepare('delete from article where Id_Article = :idArticle');
  if ($req == false) {
    die('Erreur ! Delete');
  }
  // execution de la Requête sql
  $req->execute(array(
    ':idArticle' => $id
  ));
  if ($req == false) {
    die('Erreur ! Delete');
  }
}

function verificationUtilisateurArticle($id)
{
  $linkpdo = connexionBd();
  // preparation de la Requête sql
  $req = $linkpdo->prepare('select Id_Utilisateur from article where Id_Article = :id');
  if ($req == false) {
    die('Erreur ! Delete');
  }
  // execution de la Requête sql
  $req->execute(array(
    ':id' => $id
  ));
  if ($req == false) {
    die('Erreur ! Delete');
  }
  $res = $req->fetch();
  return $res[0];
}
