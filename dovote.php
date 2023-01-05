<?php

require __DIR__ . "/vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use xPaw\MinecraftQuery;
use xPaw\MinecraftQueryException;

$response = [];

$shouldContinue = false;
if (check_site_1() && check_site_2() && check_site_3()) {
  $shouldContinue = true;
}

if ($shouldContinue) {
  $username = $_POST["username"];

  if (empty($username)) {
    $response["state"] = "USERNAME_EMPTY";
    $response["message"] = "Tu n'as pas renseigné ton pseudo !";
    echo json_encode($response);
    exit();
  }

  // Display everything in browser, because some people can't look in logs for errors
  Error_Reporting(E_ALL | E_STRICT);
  Ini_Set('display_errors', true);

  $query = new MinecraftQuery();
  $timer = MicroTime(true);

  try {
      $query->Connect($_ENV["MC_SERVER_IP"], $_ENV["MC_SERVER_PORT"], 1);
  }
  catch (MinecraftQueryException $e) {
      $Exception = $e;
      echo $e;
  }

  $timer = Number_Format(MicroTime(true) - $timer, 4, '.', '');

  if (($Players = $query->GetPlayers()) !== false) {

    if (in_array($username, $Players)) {

        try {
            $pdo = new PDO('sqlite:' . dirname(__FILE__) . '/votes.sqlite');
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // ERRMODE_WARNING | ERRMODE_EXCEPTION | ERRMODE_SILENT
            $pdo->query("CREATE TABLE IF NOT EXISTS votes (
              id INTEGER PRIMARY KEY AUTOINCREMENT,
              pseudo VARCHAR(20),
              created DATETIME,
              last DATETIME,
              totalvote INTEGER
            )");
        }
        catch (Exception $e) {
            echo "Impossible d'accéder à la base de données SQLite : " . $e->getMessage();
            die();
        }

        $exist = $pdo->prepare("SELECT * from votes where pseudo = :val");
        $exist->execute(array(
            ':val' => $username
        ));
        $reception = empty($exist);
        $com       = 0;
        $row       = $exist->fetch(PDO::FETCH_ASSOC);

        if (empty($row)) {
            // Nouveau joueur
            $req = $pdo->prepare("INSERT INTO votes (pseudo,created,last,totalvote) VALUES(:pseudo,DATETIME('NOW'),DATETIME('NOW'),1)");
            $req->bindValue(':pseudo', $username);
            $result = $req->execute();

            require 'MinecraftVotifier.php';
            $votifier = new MinecraftVotifier($_ENV["VOTIFIER_PUBLIC_KEY"], $_ENV["MC_SERVER_IP"], '8192', 'vote');
            $votifier->sendVote($username);

            $response["state"] = "SUCCESS";
            $response["message"] = "Merci d'avoir voté !";
        }
        else {
            $d1 = new DateTime(date("Y-m-d H:i:s"));
            $d2 = new DateTime(($row['last']));
            $di = date_diff($d2, $d1)->format('%d');
            if ($di != 0) {
                $req2 = $pdo->prepare("UPDATE votes set totalvote = totalvote+1, last = DATETIME('NOW') where pseudo=:pseudo");
                $req2->bindValue(':pseudo', $username);
                $result2 = $req2->execute();

                require 'MinecraftVotifier.php';
                $votifier = new MinecraftVotifier($_ENV["VOTIFIER_PUBLIC_KEY"], $_ENV["MC_SERVER_IP"], '8192', 'vote');
                $votifier->sendVote($username);

                $response["state"] = "SUCCESS";
                $response["message"] = "Merci d'avoir voté !";
            } else {
                $date_expire = $d2->modify('+1 day');
                $response["state"] = "ALREADY_VOTED";
                $response["message"] = "Tu as déjà voté ces dernières 24h ! Encore " . date_diff($date_expire, $d1)->format('%H heures %i minutes %s secondes avant de voter.');
            }
        }
    } else {
      $response["state"] = "NOT_CONNECTED";
      $response["message"] = "Tu n'es pas connecté sur le serveur !";
    }
  } else {
    echo "Erreur";
  }
} else {
  $response["state"] = "NOT_VOTED";
}

echo json_encode($response);

function check_site_1() {
  global $response;
  $API_key = $_ENV["SITE_1_TOKEN"]; // Token de votre serveur
  $API_ip = $_SERVER['REMOTE_ADDR']; // Adresse IP de l'utilisateur
  $json = file_get_contents("https://serveur-prive.net/api/vote/json/$API_key/$API_ip");
  $json_data = json_decode($json);

  if($json_data->status == 1) {
    // Vous pouvez utiliser les variables suivantes :
    $json_data->vote; // Correspond à la date du vote au format timestamp
    $json_data->nextvote; // Correspond au nombre de secondes restantes avant que l'utilisateur puisse à nouveau voter
    $json_data->pseudo; // Pseudonyme de l'utilisateur (si il a spécifié son pseudo lors de son vote)
    
    return true;
  }
  else {
    $response["message"] = "Tu n'as pas voté sur serveur-prive.net !";
    return false;
  }
}

function check_site_2() {
  return true;
  // $api_adress = "https://www.serveursminecraft.org/sm_api/peutVoter.php?id=6335&ip=play.peaceandcube.fr:10098";
  // $api_result = @file_get_contents($api_adress);
  // if($api_result == "true")
  // {
  //     return true;
  // }
  // else
  // {
  //     // return $api_result; // la variable donne le nombre de seconde restant.
  //     return false;
  // }
}

function check_site_3() {
  return true;
  // $serverToken = $_ENV["SITE_3_TOKEN"]; // Token de votre serveur
  // $apiUrl = "https://api.top-serveurs.net/v1/votes/check-ip?server_token=$serverToken&ip=play.peaceandcube.fr:10098";
  // $apiResult = file_get_contents($apiUrl);
  // $json = json_decode($apiResult);
  // if ($json->success) {
  //   return true;
  // } else {
  //   return false;
  // }
}

?>