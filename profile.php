<?php 
session_start(); // Iniciar sesión

require(__DIR__.'/php/mysqlProfile.php');

// Verificar si no hay una sesión iniciada
if (!isset($_SESSION["currentEmail"])) {
  header("Location: /signin.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/svg" href="/img/pokedex.png" />
    <link
      rel="stylesheet"
      href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
      integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
      crossorigin="anonymous"
    />
    <link rel="stylesheet" href="/css/Navbar.css" />
    <link rel="stylesheet" href="/css/Pokedex.css" />
    <link rel="stylesheet" href="/css/Modal.css" />
    <title>Pokédex</title>
  </head>
  <body onload="verifyProfile()">
    <div class="Navbar">
      <div class="container-fluid d-flex justify-content-between">
        <a class="Navbar__brand" href="javascript:window.location.assign('main.php')">
          <img
            class="Navbar__brand-logo"
            src="/img/ultraball.svg"
            alt="Logo"
          />
          <span class="font-weight-light">Poké</span>
          <span class="font-weight-bold">dex</span>
        </a>
        <div class="header__menu">
          <div class="header__menu--profile">
            <img src="/img/avatar.svg" alt="User" />
            <p>
            <?php 
            echo $name
            ?>
            </p>
          </div>
        </div>
      </div>
    </div>
    <div class="Pokedex">
      <div class="row d-flex justify-content-between align-items-center">
        <h1 class="display-4 pl-4">
          <span class="font-weight-light ml-2">
            Welcome to your Pokédex
          </span>
          <span class="font-weight-bold">
            <?php 
            echo $name
            ?>!
          </span>
          <span class="font-weight-light ml-2">
            ---  Pokeballs:
          </span>
          <span class="font-weight-bold">
            <?php 
            echo $pokeballs
            ?>!
          </span>
        </h1>
        <div class="d-flex flex-direction-row">
          <div>
            <a
              href="javascript:window.location.assign('main.php')"
              class="btn btn-primary mr-3 font-weight-bold"
              >Search more</a
            >
          </div>

          <div>
            <a
              href="javascript:window.location.assign('social.php')"
              class="btn btn-warning mr-3 font-weight-bold"
              >Social</a
            >
          </div>

          <?php
            if($_SESSION["currentEmail"] === "admin@stucom.com") {
              echo "<div>
<a href=\"javascript:window.location.assign('admin.php?file=./img/pokedex.png')\"
class=\"btn btn-info mr-3 font-weight-bold\"
>Admin</a
>
</div>";
            }
          ?>
  
          <div>
            <a
              onclick="logout()"
              class="btn btn-danger mr-5 font-weight-bold"
              >Logout</a
            >
          </div>
        </div>
      </div>
        <?php
        if($flag){
        	echo "<div class=\"row d-flex justify-content-center mb-4 mt-3 border\">
        <div class=\"col-2 d-flex flex-column justify-content-center align-items-center border\">
        	<h1 class=\"mb-3 Pokemon__name\">".$myArr[0]["nombre"]."</h1>
          <h4 class=\"mb-3 Pokemon__id\">".$myArr[0]["id"]."</h4>
          <h4 class=\"font-weight-light Pokemon__weight\">".$myArr[0]["peso"]." lbs.</h4>
          <h4 class=\"font-weight-light Pokemon__height\">".$myArr[0]["altura"]." fts.</h4>
          <h4 class=\"font-weight-light Pokemon__baxp\">BAXP: ".$myArr[0]["baxp"]." pts.</h4>
          <!--           <span onclick='showEditModal()' class=\"btn btn-success mt-4 font-weight-bold\"
                     >Edit Name</span>-->
        <span onclick='showDeleteModal()' class=\"btn btn-danger mt-4 font-weight-bold\"
          >Sell Pokémon</span
        >
        </div>";
      } else {
        echo "<div class=\"row d-flex justify-content-center mb-4 mt-3 border\">
        <div class=\"col-2 d-flex flex-column justify-content-center align-items-center border\">
        </div>";
      }
        ?>
        <div class="col-6 d-flex justify-content-center border">
          <?php
          if($flag){
          	echo "<img class=\"Pokemon__img\" src=\"https://img.pokemondb.net/artwork/large/".$myArr[0]["nombre"].".jpg\" alt=\"\" />";
          } else {
            echo "
            <div class=\" d-flex flex-column justify-content-center\">
            <div class=\"mt-5 text-center d-flex flex-column\">
            <span class=\"display-2\">No Pokemons</span>
            <span class=\"font-weight-bold\">Get back to the homepage to search more Pokémon!</span>
            <div\">
            <img class=\"mt-4 mb-5\" src=\"/img/broke.svg\" alt=\"\" width=\"400\">
            </div>
            </div>
            </div>"; 
          }
          ?>
        </div>
      </div>
      <?php
      if($flag && $totalPokes>1){
      	echo "<div class=\"row\">
        <section class=\"carousel\">
          <h2>
            <span class=\"font-weight-light\"> Your </span>
            <span class=\"font-weight-bold\"> Pokémons: </span>
          </h2>
          <div class=\"carousel__container\">";
          for($i=0;$i<sizeof($myArr);$i++){
            echo "<div class=\"carousel-item\">
            <img id=\"pokemon-{$myArr[$i]['id']}\" class=\"carousel-item__img\" data-id=\"{$myArr[$i]['id']}\" src=\"https://img.pokemondb.net/artwork/large/{$myArr[$i]['nombre']}.jpg\" alt=\"Cat\" />
            <div class=\"carousel-item__details\" onclick=\"displayPokemon(this.dataset.id)\" data-id=\"{$myArr[$i]['id']}\">
              <p class=\"carousel-item__details--title\">{$myArr[$i]['nombre']}</p>
            </div>
          </div>";
          };
          
            echo"
          </div>
        </section>
      </div>";
      }
      ?>
    </div>
    <div class="overlay" id="overlay">
    </div>
    
      
      
    <div class="modal" id="delete-modal">
      <h2 class="font-weight-light" >Are you sure you want to sell your Pokémon?</h2>
      <h4 class="font-weight-light" >It will give you a random num of pokeballs</h4>
      <div class="d-flex justify-content-center mt-4"> 
      <img
            class="rounded"
            src="/img/sad.gif"
            alt="Gif"
            width="300px"
          />
      </div>
      <div class="modal-buttons d-flex flex-row justify-content-center">
        <button onclick="hideDeleteModal()" class="modal-btn primary font-weight-bold" id="hide-modal">Cancel</button>
        <form action="/php/mysqlDeleteProfile.php" id="delete-form" method="post">
        <input type="hidden" id="pokemonIdToDelete" name="pokemonIdToDelete" value="" />
          <button type="submit" class="modal-btn warning font-weight-bold" id="pokemon-add">Sell</button>
        </form>
      </div>
    </div>
    <script src="/js/profile.js"></script>
  </body>
</html>
