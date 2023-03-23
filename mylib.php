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
    $payload = array('username' => $nom, 'IdUser' => $idUser, 'IdRole' => $idRole, 'exp' => (time() + 60));
    $jwt = generate_jwt($headers, $payload);
    return $jwt;
  } else {
    return "error";
  }
}

function getArticleById($id)
{
  $linkpdo = connexionBd();
  // preparation de la Requête sql
  $req = $linkpdo->prepare('SELECT Nom,Contenu,Date_Modif,Date_Publication FROM article, utilisateur where article.Id_Utilisateur = utilisateur.Id_Utilisateur AND Id_Article = :id');
  if ($req == false) {
    die('Erreur !');
  }
  // execution de la Requête sql
  $req->execute(array('id' => $id));
  if ($req == false) {
    die('Erreur !');
  }
  return $req->fetchAll();
}
function getAllArticles()
{
  $linkpdo = connexionBd();
  // preparation de la Requête sql
  $req = $linkpdo->prepare('SELECT Nom,Contenu,Date_Modif,Date_Publication FROM article, utilisateur where article.Id_Utilisateur = utilisateur.Id_Utilisateur');
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
  return getArticleById($lastId);
}
function avis($avis,$idArticle,$idUser){
  $linkpdo = connexionBd();
  // preparation de la Requête sql
  $req = $linkpdo->prepare('REPLACE INTO `reagis` (`Id_Article`, `Id_Utilisateur`, `Avis`) VALUES (:idArticle,:idUser,:avis);');
  if ($req == false) {
    die('Erreur ! Avis');
  }
  // execution de la Requête sql
  $req->execute(array(
    ':idArticle' => $idArticle,
    ':idUser' => $id,
    ':avis' => $idUser
  ));
  if ($req == false) {
    die('Erreur ! Avis');
  }
  // recuperation du dernier id
  $lastId = $linkpdo->lastInsertId();
  return getArticleById($lastId);
}
  


