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
  $req->execute(array('mail' => $login,
                      'mdp' => $mdp ));
  if ($req == false) {
    die('Erreur !');
  }
  if ($req->rowCount() > 0) {
      foreach ($data as $key => $value) {
          if ($key == 'Id_Utilisateur') {
             $idUser = $value;
          }elseif($key == 'Id_Role'){
            $idRole = $value;
          }}
    $headers = array('alg' => 'HS256', 'typ' => 'jwt');
    $payload = array('username' => $login,'IdUser'=> $idUser,'IdRole'=>$idRole,'exp' => (time() + 60));
    $jwt = generate_jwt($headers, $payload);
    return $jwt;
  } else {
    return "error";
  }
}

function if_jwt_correct()
{
  return is_jwt_valid(get_authorization_header());
}

function getArticleById($id)
{
  $linkpdo = connexionBd();
  // preparation de la Requête sql
  $req = $linkpdo->prepare('SELECT Auteur,Contenu,Date_Modif,Date_Publication FROM article where Id_Article = :id');
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
  $req = $linkpdo->prepare('SELECT Auteur,Contenu,Date_Modif,Date_Publication FROM article');
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

function post($contenu,$id)
{
    $linkpdo = connexionBd();
    // preparation de la Requête sql
    $req = $linkpdo->prepare('insert into article (Contenu,Date_Publication,Date_Modif,Id_Utilisateur) value(:contenu,NOW(),NOW(),:idUtilisateur)');
    if ($req == false) {
        die('Erreur ! Post');
    }
    // execution de la Requête sql
    $req->execute(array(':contenu' => $contenu,
                        ':idUtilisateur'=>$id));
    if ($req == false) {
        die('Erreur ! Post');
    }
    // recuperation du dernier id
    $lastId = $linkpdo->lastInsertId();
    return getArticleById($lastId);
}

function put($id, $contenu)
{
    $linkpdo = connexionBd();
    // preparation de la Requête sql
    $req = $linkpdo->prepare('update article set contenu = :contenu, Date_Modif = NOW() where Id_Article = :id');
    if ($req == false) {
        die('Erreur ! Put');
    }
    // execution de la Requête sql
    $req->execute(array('id' => $id, ':contenu' => $contenu));
    if ($req == false) {
        die('Erreur ! Put');
    }
    // recuperation du dernier id
    return getArticleById($id);
}

function putAvisPositif($idUtilisateur,$idArticle)
{
    $linkpdo = connexionBd();
    // preparation de la Requête sql
    $req = $linkpdo->prepare('REPLACE INTO reagis (Id_Utilisateur, Id_Article, avis) VALUES (:idUtilisateur,:idArticle, 1)');
    if ($req == false) {
        die('Erreur ! Put');
    }
    // execution de la Requête sql
    $req->execute(array('idUtilisateur' => $idUtilisateur,
                        'idArticle' => $idArticle));
    if ($req == false) {
        die('Erreur ! Put');
    }
    // recuperation du dernier id
    return getAllArticles($id);
}

function putAvisNegatif($idUtilisateur,$idArticle)
{
    $linkpdo = connexionBd();
    // preparation de la Requête sql
    $req = $linkpdo->prepare('REPLACE INTO reagis (Id_Utilisateur, Id_Article, avis) VALUES (:idUtilisateur,:idArticle, 2)');
    if ($req == false) {
        die('Erreur ! Put');
    }
    // execution de la Requête sql
    $req->execute(array('idUtilisateur' => $idUtilisateur,
                        'idArticle' => $idArticle));
    if ($req == false) {
        die('Erreur ! Put');
    }
    // recuperation du dernier id
    return getAllArticles($id);
}

function putSignalementPlus1($id)
{
  $linkpdo = connexionBd();
  // preparation de la Requête sql
  $req = $linkpdo->prepare('update chuckn_facts set signalement = signalement + 1 where id = :id');
  if ($req == false) {
    die('Erreur ! Put');
  }
  // execution de la Requête sql
  $req->execute(array('id' => $id));
  if ($req == false) {
    die('Erreur ! Put');
  }
  // recuperation du dernier id
  return getId($id);
}

function putSignalementMoins1($id)
{
  $linkpdo = connexionBd();
  // preparation de la Requête sql
  $req = $linkpdo->prepare('update chuckn_facts set signalement = signalement - 1 where id = :id');
  if ($req == false) {
    die('Erreur ! Put');
  }
  // execution de la Requête sql
  $req->execute(array('id' => $id));
  if ($req == false) {
    die('Erreur ! Put');
  }
  // recuperation du dernier id
  return getId($id);
}

function delete($id)
{
  $linkpdo = connexionBd();
  // preparation de la Requête sql
  $req = $linkpdo->prepare('delete from chuckn_facts where id = :id');
  if ($req == false) {
    die('Erreur ! Delete');
  }
  // execution de la Requête sql
  $req->execute(array('id' => $id));
  if ($req == false) {
    die('Erreur ! Delete');
  }
}
