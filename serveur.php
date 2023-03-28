<?php
/// Librairies éventuelles (pour la connexion à la BDD, etc.)
include('mylib.php');

/// Paramétrage de l'entête HTTP (pour la réponse au Client)
header("Content-Type:application/json");

// Récupération token et création varaibles
if (get_bearer_token() != null) {
  $IdRole = json_decode(jwt_decode(get_bearer_token()), true)['IdRole'];
  $exp = json_decode(jwt_decode(get_bearer_token()), true)['exp'];
}

/// Identification du type de méthode HTTP envoyée par le client
$http_method = $_SERVER['REQUEST_METHOD'];
switch ($http_method) {

    // Cas de la méthode GET
  case "GET":
    try {

      //! //////////////
      //! Traitement ///
      //! //////////////

      // Exception
      if (isset($_GET['id'])) {
        if (empty($_GET['id'])) {
          throw new Exception("L'entité fournie (id) avec la requête est incompréhensible ou incomplète", 422);
        }
        $matchingData = getArticleById($_GET['id']);
      } else {
        $matchingData = getAllArticles();
      }
      $RETURN_CODE = 200;
      $STATUS_MESSAGE = "Requête traitée avec succès";
    } catch (\Throwable $th) {
      $RETURN_CODE = $th->getCode();
      $STATUS_MESSAGE = $th->getMessage();
    } finally {
      // Envoi de la réponse au Client
      deliver_response($RETURN_CODE, $STATUS_MESSAGE, $matchingData);
    }
    break;

    //! Cas de la méthode POST
  case "POST":
    try {

      //! //////////////
      //! Traitement ///
      //! //////////////

      // vérification token + droit utilisateur
      if (is_jwt_valid(get_bearer_token()) == true && $IdRole == 2) {
        // Récupération des données envoyées par le Client
        $postedData = file_get_contents('php://input');
        $postedData = json_decode($postedData, true);
        // Exception
        if (empty($postedData['contenu'])) {
          throw new Exception("L'entité fournie (contenu) avec la requête est incompréhensible ou incomplète", 422);
        }
        // Traitement
        $matchingData = post($postedData['contenu'], 1);
        $RETURN_CODE = 200;
        $STATUS_MESSAGE = "Requête traitée avec succès";
      } else {
        $matchingData = null;
        $RETURN_CODE = 403;
        $STATUS_MESSAGE = "Permission non accordée";
      }
    } catch (\Throwable $th) {
      $RETURN_CODE = $th->getCode();
      $STATUS_MESSAGE = $th->getMessage();
    } finally {
      // Envoi de la réponse au Client
      deliver_response($RETURN_CODE, $STATUS_MESSAGE, $matchingData);
    }
    break;

    //! Cas de la méthode PUT
    //TODO: PATCH au lieu de PUT car pas toute la ressource
  case "PUT":
    break;
  case "PATCH":
    break;
    // Cas de la méthode DELETE
  case "DELETE":
    try {

      //! //////////////
      //! Traitement ///
      //! //////////////

      //Exception
      if (isset($_GET['id']) && empty($_GET['id'])) {
        throw new Exception("L'entité fournie (id) avec la requête est incompréhensible ou incomplète", 422);
      }
      if (is_jwt_valid(get_bearer_token())) {
        /// Traitement
        $matchingData = delete($_GET['id']);
        $RETURN_CODE = 200;
        $STATUS_MESSAGE = "Requête traitée avec succès";
      } else {
        $RETURN_CODE = 401;
        $STATUS_MESSAGE = "Tokken invalide !";
      }
    } catch (\Throwable $th) {
      $RETURN_CODE = $th->getCode();
      $STATUS_MESSAGE = $th->getMessage();
    } finally {
      //! Envoi de la réponse au Client
      deliver_response($RETURN_CODE, $STATUS_MESSAGE, $matchingData);
    }
    break;
  default:
    $matchingData = null;
    $RETURN_CODE = 405;
    $STATUS_MESSAGE = "Méthode introuvable";
    deliver_response($RETURN_CODE, $STATUS_MESSAGE, $matchingData);
    break;
}
/// Envoi de la réponse au Client
function deliver_response($status, $status_message, $data)
{
  /// Paramétrage de l'entête HTTP, suite
  header("HTTP/1.1 $status $status_message");
  /// Paramétrage de la réponse retournée
  $response['status'] = $status;
  $response['status_message'] = $status_message;
  $response['data'] = $data;
  /// Mapping de la réponse au format JSON
  $json_response = json_encode($response);
  echo $json_response;
}
