<!DOCTYPE html>
<html lang="fr">
<head>

  <!-- <script>
    document.getElementById('submit').addEventListener('click', function() {
      $ajax({
        type: 'POST',
        url: 'dovote.php',
        data: {
          'username': document.getElementById('username').value,
        },
        success: function(data) {
          $('#vote').html(data);
        }
      });
    });
  </script> -->

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="viewport" content="width=420; user-scalable=0;">
  <title>Vote - PeaceAndCube</title>
  <link rel="stylesheet" href="./css/reset.css">
  <link rel="stylesheet" href="./css/styles.css">
  <link rel="icon" href="favicon.ico" type="image/x-icon">
  <script src="js/username.js"></script>
</head>

  <body>
    <div class="container">
      <label id="switch" class="switch">
              <input type="checkbox" onchange="toggleTheme()" id="slider"/>
              <span class="check-slider round"></span>
          </label>
    </div>
    <div class="header">
        <h1><img src="./img/logo.png" draggable="false" alt="Page de vote PAC"/></h1>
        <a class="back" href="https://peaceandcube.fr/"><p>Retourner sur <span>peaceandcube.fr</span></p></a>
      </div>

      

      <div class="main">
        <div class="wrap">
          <a href="https://serveur-prive.net/minecraft/peaceandcube-12137" target="_blank" class="link-vote">Voter sur serveur-prive.net</a>
          <a href="https://www.serveursminecraft.org/serveur/6335/" target="_blank" class="link-vote">Voter sur serveursminecraft.org</a>
          <a href="https://top-serveurs.net/minecraft/peaceandcube" target="_blank" class="link-vote">Voter sur top-serveurs.net</a>
        <div class="card">
          <h2>Vote quotidiennement pour le serveur, et reçois 5 coins en récompense !</h2>
          <p>Clique sur chacun des liens pour voter pour le serveur, et entre ton pseudo en étant connecté sur le serveur !</p>
          <form action="dovote.php">
            <div class="username">
              <input type="text" id="username" name="username" placeholder="" maxlength="16" required autocomplete="username" data-form-type="username" title="Pseudo">
              <label for="username">Entre ton pseudo</label>
            </div>
            <div id="checkbox" class="remember">
              <input type="checkbox" name="remember_me" id="remember_me">
              <label for="remember_me">Se souvenir de moi</label>
            </div>
            <label for="submit" class="hide_label">Submit</label>
            <button id="submit">Je vote !</button>
          </form>
        </div>
        </div>
        <p class="info">
          Le vote est de nouveau disponible <span>24 heures</span> après que tu as validé ton vote.
        </p>
        <div class="slider-wrap">
          <h2>Avec les coins, vous pouvez</h2>
        
        <div class="slider">
        
          <div class="slider-panel-set">
              
            <div class="slider-panel">
              <div class="img one"><p>accéder à des donjons</p></div>
              </div>
              
            <div class="slider-panel">
              <div class="img three"><p>tenter de doubler la mise,<br>grâce à la roulette du hippie</p></div>
            </div>
            
          </div>
          
            <div class="buttons">
              <div class="left">
                &lt;
              </div>
              <div class="right">
                &gt;
              </div>
            </div>
          
        </div>
        </div>
        
      </div>
      <footer>
        <p>Réalisé par <a href="http://mc.devlose.fr" target="bank">Devlose</a> avec l'aide de <a href="https://github.com/YanisBft" target="bank">YanisBft</a>, Foxkills et Vastidus - © 2013-2022 <a href="https://peaceandcube.fr/">PeaceAndCube</a></p>
      </footer>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js" type="text/javascript"></script>
      <script src="js/theme.js"></script>
      <script src="js/slider.js"></script>
  </body>

</html>
