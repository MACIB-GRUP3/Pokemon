<?php 
session_start(); // Iniciar la sesión
require(__DIR__.'/php/mysqlMain.php');
require(__DIR__.'/php/mysqlSearchUser.php');

if (!isset($_SESSION["currentEmail"])) {
  header("Location: /signin.php");
  exit();
}

$usersToUse = array();
$counters = array();
$message = "";
$name = "";
$user_mail = "";

// Obtener el correo electrónico del usuario desde la sesión
$email = $_SESSION["currentEmail"];

// Conectar a la base de datos
$link = mysqli_connect("localhost", "root", "", "Pokewebapp");

// Revisar si se ha realizado la conexión
if ($link == false) {
    $message = "ERROR: Could not connect " . mysqli_connect_error();
} else {
    // Obtener el nombre del usuario actual
    $sql = "SELECT nombre, correo FROM usuario WHERE correo='$email'";
    $result = mysqli_query($link, $sql);
    if (mysqli_num_rows($result) > 0) {
        while ($row = $result->fetch_assoc()) {
            $name = $row['nombre'];
            $user_mail = $row['correo'];
        }
    } else {
        $message = "Could not find user";
    }

  
    // Filtrar usuarios si se envió un correo electrónico en el formulario
    if (isset($_POST['user-mail']) && !empty($_POST['user-mail'])) {
        $searchEmail = $_POST['user-mail']; // No se usa mysqli_real_escape_string

        // Debugging input
        echo "<pre>Input Email: " . htmlspecialchars($searchEmail) . "</pre>";

        $sql2 = "SELECT * FROM usuario WHERE correo LIKE '%" . $searchEmail . "%'";
    } else {
        $sql2 = "SELECT * FROM usuario WHERE correo != 'admin@stucom.com'";
    }

    // Obtener los usuarios
    $result2 = mysqli_query($link, $sql2);
    if (mysqli_num_rows($result2) > 0) {
        $users = array();
        while ($row = $result2->fetch_assoc()) {
            $users[] = $row;
        }

        $usersToUse = $users;
        $counters = array();

        foreach ($usersToUse as $user) {
            $id_usuario = $user["id"];
            $sql3 = "SELECT COUNT(p.id) AS 'n' FROM pokemon p
                    INNER JOIN Pokedek_pokemon pp ON p.id = pp.id_pokemon 
                    INNER JOIN Pokedek pk ON pp.id_pokedek = pk.id
                    INNER JOIN Usuario u ON pk.id_usuario = u.id WHERE u.id ='$id_usuario'";
            $result3 = mysqli_query($link, $sql3);

            if (mysqli_num_rows($result3) > 0) {
                while ($row = $result3->fetch_assoc()) {
                    array_push($counters, $row);
                }
            } else {
                $message = "Count pokemons query error";
            }
        }
    } else {
        $message = "No users found.";
    }

    // Cerrar la conexión
    mysqli_close($link);
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
    <link rel="stylesheet" href="/css/Main.css" />
    <link rel="stylesheet" href="/css/Social.css" />
    <link rel="stylesheet" href="/css/Badge.css" />
    <title>Social</title>
</head>
<body>
    <div class="Navbar">
        <div class="container-fluid d-flex justify-content-between">
            <a class="Navbar__brand" href="javascript:window.location.assign('main.php')">
                <img
                  class="Navbar__brand-logo"
                  src="/img/social.svg"
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
                      echo htmlspecialchars($name);
                      ?>
                    </p>
                </div>
                <ul>
                    <li><a class="font-weight-bold" href="javascript:window.location.assign('profile.php')">Pokédex</a></li>
                    <li><a class="font-weight-bold" href="javascript:window.location.assign('main.php')">Home</a></li>
                    <li><a onclick="logout()" class="font-weight-bold" href="#">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="ml-5 mr-5 mt-3 Social__title d-flex justify-content-center flex-column">
        <div class="row">
            <div class="col-6 ml-5">
                <h1 class="display-3">Find more Pokémon trainers here!</h1>
                <form action="social.php" method="post" id="search-form">
                    <div class="input-group mt-4 mb-1">
                        <div class="input-group-prepend">
                            <button class="btn btn-primary" type="submit">Search</button>  
                        </div>
                        <input type="text" class="form-control" placeholder="by email" aria-label="" aria-describedby="basic-addon1" id="user-mail" name="user-mail">
                    </div>
                    <small class="font-weight-bold text-danger mb-5">
                      <?php 
                      echo htmlspecialchars($message);
                      ?>
                    </small>
                </form>             
                <div class="container">
                    <?php
                    for ($i = 0; $i < count($usersToUse); $i++) {
                        echo "<form action=\"trainerView.php\" method=\"post\" class=\"Social__users\">
                          <input type=\"hidden\" name=\"user-mail\" value=\"".$usersToUse[$i]["correo"]."\" />
                          <button type=\"submit\" class=\"btn btn-link p-0\">
                            <li class=\"BadgesListItem\">
                                <img
                                  src=\"/img/avatar.svg\"
                                  alt=\"avatar\"
                                  class=\"BadgesListItem__avatar\"
                                />
                                <div>
                                  <div>
                                    <span>".
                                      $usersToUse[$i]["nombre"]
                                    ."</span>
                                  </div>
                                  <div class=\"font-weight-light\">".
                                    htmlspecialchars($usersToUse[$i]["correo"])
                                  ."</div>
                                  <div class=\"font-weight-light\">".
                                    htmlspecialchars($counters[$i]["n"])." Pokémons on his own
                                  </div>
                                </div>
                              </li>
                          </button>
                        </form>";
                    }
                    ?>
                </div>
            </div>
            <div class="col-5 d-flex justify-content-center flex-column">
                <div class="d-flex justify-content-center">
                     <img class="Social__img" src="/img/pika.png" alt="" width="440px">
                </div>
            </div>
        </div>
    </div>
    <script src="/js/social.js"></script>
</body>
</html>
