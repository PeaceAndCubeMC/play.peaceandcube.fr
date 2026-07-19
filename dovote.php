<?php

require __DIR__ . "/vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use xPaw\MinecraftQuery;
use xPaw\MinecraftQueryException;

$response = [];

$shouldContinue = false;
if (check_site_1($response) && check_site_2($response) && check_site_3($response)) {
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
    } catch (MinecraftQueryException $e) {
        $Exception = $e;
        echo $e;
    }

    $timer = Number_Format(MicroTime(true) - $timer, 4, '.', '');

    if (($players = $query->GetPlayers()) !== false) {

        if (in_array($username, $players)) {

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
            } catch (Exception $e) {
                echo "Impossible d'accéder à la base de données SQLite : " . $e->getMessage();
                die();
            }

            $exist = $pdo->prepare("SELECT * from votes where pseudo = :val");
            $exist->execute(array(
                ':val' => $username
            ));
            $reception = empty($exist);
            $com = 0;
            $row = $exist->fetch(PDO::FETCH_ASSOC);

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
            } else {
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

function check_site_1(array &$response): bool
{
    $serverToken = $_ENV["SITE_1_TOKEN"];
    $userIp = $_SERVER['REMOTE_ADDR'];
    $result = file_get_contents("https://serveur-prive.net/api/v1/servers/$serverToken/votes/$userIp");
    $data = json_decode($result);

    if ($data->success) {
        // Vous pouvez utiliser les variables suivantes :
        $data->voted_at; // Correspond à la date du vote au format timestamp
        $data->next_vote_seconds; // Correspond au nombre de secondes restantes avant que l'utilisateur puisse à nouveau voter
        $data->username; // Pseudonyme de l'utilisateur (si il a spécifié son pseudo lors de son vote)

        return true;
    } else {
        $response["message"] = "Tu n'as pas voté sur serveur-prive.net !";
        return false;
    }
}

function check_site_2(array &$response): bool
{
    $serverId = $_ENV["SITE_2_SERVER_ID"];
    $userIp = $_SERVER['REMOTE_ADDR'];
    $result = file_get_contents("https://www.serveursminecraft.org/sm_api/peutVoter.php?id=$serverId&ip=$userIp");

    if ($result == "true") {
        $response["message"] = "Tu n'as pas voté sur serveursminecraft.org !";
        return false;
    } else {
        // return $result; // la variable donne le nombre de seconde restant.
        return true;
    }
}

function check_site_3(array &$response): bool
{
    $serverToken = $_ENV["SITE_3_TOKEN"];
    $userIp = $_SERVER['REMOTE_ADDR'];
    $result = file_get_contents("https://api.top-serveurs.net/v1/votes/check-ip?server_token=$serverToken&ip=$userIp");
    $data = json_decode($result);

    if ($data->success) {
        return true;
    } else {
        $response["message"] = "Tu n'as pas voté sur top-serveurs.net !";
        return false;
    }
}

?>