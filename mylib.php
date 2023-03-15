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
    if ($login == "user" && $mdp == "pass") {
        $headers = array('alg' => 'HS256', 'typ' => 'jwt');
        $payload = array('username' => $login, 'exp' => (time() + 60));
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
    $req = $linkpdo->prepare('SELECT Auteur,Contenu,Date_Modif,Date_Publication FROM article where id = :id');
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
// A PRIORI PAS UTILE
//function getBySignalement()
//{
//    $linkpdo = connexionBd();
    // preparation de la Requête sql
  //  $req = $linkpdo->prepare('select * from chuckn_facts where signalement > 0');
    //if ($req == false) {
        die('Erreur ! GetAll');
    //}
    // execution de la Requête sql
    //$req->execute();
    //if ($req == false) {
     //   die('Erreur ! GetAll');
    //}
    //return $req->fetchAll();
//}

/*function getByVote()
//{
    $linkpdo = connexionBd();
     preparation de la Requête sql
    $req = $linkpdo->prepare('select * from chuckn_facts where vote > 3 order by vote desc');
    if ($req == false) {
        die('Erreur ! GetAll');
    }
     execution de la Requête sql
    $req->execute();
    if ($req == false) {
        die('Erreur ! GetAll');
    }
    return $req->fetchAll();
}*/

/*function getByLast10()
{
    $linkpdo = connexionBd();
    // preparation de la Requête sql
    $req = $linkpdo->prepare('select * from chuckn_facts order by id desc limit 10');
    if ($req == false) {
        die('Erreur ! GetAll');
    }
    // execution de la Requête sql
    $req->execute();
    if ($req == false) {
        die('Erreur ! GetAll');
    }
    return $req->fetchAll();
}*/

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

function post($phrase)
{
    $linkpdo = connexionBd();
    // preparation de la Requête sql
    $req = $linkpdo->prepare('insert into article (Contenu,Date_Publication,date_Modif,Id_Utilisateur) value(:contenu,NOW(),NOW(),:idUtilisateur)');
    if ($req == false) {
        die('Erreur ! Post');
    }
    // execution de la Requête sql
    $req->execute(array(':phrase' => $phrase));
    if ($req == false) {
        die('Erreur ! Post');
    }
    // recuperation du dernier id
    $lastId = $linkpdo->lastInsertId();
    return getId($lastId);
}

function put($id, $phrase)
{
    $linkpdo = connexionBd();
    // preparation de la Requête sql
    $req = $linkpdo->prepare('update chuckn_facts set phrase = :phrase, date_modif = NOW() where id = :id');
    if ($req == false) {
        die('Erreur ! Put');
    }
    // execution de la Requête sql
    $req->execute(array('id' => $id, ':phrase' => $phrase));
    if ($req == false) {
        die('Erreur ! Put');
    }
    // recuperation du dernier id
    return getId($id);
}

function putVotePlus1($id)
{
    $linkpdo = connexionBd();
    // preparation de la Requête sql
    $req = $linkpdo->prepare('update chuckn_facts set vote = vote + 1 where id = :id');
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

function putVoteMoins1($id)
{
    $linkpdo = connexionBd();
    // preparation de la Requête sql
    $req = $linkpdo->prepare('update chuckn_facts set vote = vote - 1 where id = :id');
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
