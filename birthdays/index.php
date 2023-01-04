<?php
    require __DIR__ . "/../vendor/autoload.php";

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    $array = yaml_parse_file($_ENV["BIRTHDAYS_PATH"]);
    $today = date("d-m");
    $today_uuids = [];

    foreach ($array as $uuid => $value) {
        $date = $value["date"];

        if ($date === $today) {
            $today_uuids[] = $uuid;
        }
    }

    $html = "";

    foreach ($today_uuids as $uuid) {
        $response = file_get_contents("https://sessionserver.mojang.com/session/minecraft/profile/" . $uuid);
        $json = json_decode($response);
        $name = $json->name;

        $data = file_get_contents("https://crafatar.com/avatars/" . $uuid . "?size=128");
        $img_src = 'data:image/x-png;base64,' . base64_encode($data);

        $html .= "<div class='player'>";
        $html .= "<img src='" . $img_src . "'>";
        $html .= "<p>" . $name . "</p>";
        $html .= "</div>";
    }
?>

<html>
    <head>
        <meta charset="UTF-8">
        <link rel="icon" type="image/ico" href="favicon.ico" />
        <link rel="stylesheet" href="styles.css" />
        <title>Anniversaires - PeaceAndCube</title>
    </head>
    <body>
        <h1>Anniversaires du jour</h1>
        <div id="birthdays">
            <?php echo $html ?>
        </div>
    </body>
</html>