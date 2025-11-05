<?php 
session_start();

if ($_SESSION["currentEmail"] !== "admin@stucom.com") {
  header("Location: /signin.php");
  exit();
}

$link = mysqli_connect("localhost", "root", "", "Pokewebapp");

if (isset($_POST['userId']) && isset($_POST['pokeballs'])) {
  $userId = $_POST['userId'];
  $pokeballs = $_POST['pokeballs'];

  $query = "UPDATE usuario SET pokeballs = pokeballs + $pokeballs WHERE id = $userId";
  $result = mysqli_query($link, $query);

  if (!$result) {
    echo mysqli_error($link);
    exit();
  }
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
    <title>Admin</title>
  </head>
  <body>
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
      </div>
    </div>
    <div class="container mt-5 d-flex justify-content-between">
      <div>
        <h1 class="display-4">Dev Panel</h1>
        <img class="img-fluid mt-3" src="/img/pokedex.png" alt="Pokedex" />
      </div>
      <div>
        <form method="POST" action="dev.php">
          <div class="form-group">
            <label for="userId">ID de Usuario:</label>
            <input type="text" class="form-control" id="userId" name="userId" required>
          </div>
          <div class="form-group">
            <label for="pokeballs">Pokeballs:</label>
            <input type="number" class="form-control" id="pokeballs" name="pokeballs" required>
          </div>
          <button type="submit" class="btn btn-primary">Agregar Pokeballs</button>
        </form>
      </div>
    </div>
    <!-- Footer -->
    <div class="footer mt-5">
      <p class="text-center p-4">
        ©2023 All rights reserved
        developers
      </p>
    </div>
  </body>
</html>
