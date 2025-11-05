<?php 
session_start(); // Iniciar la sesión
require(__DIR__.'/php/mysqlMain.php');

// Verificar si el usuario está autenticado
if (!isset($_SESSION["currentEmail"])) {
  header("Location: /signin.php");
  exit();
}

// Inicializar variables
$name_trainer = '';
$user_mail = '';
$poks = array(); // Inicializar $poks como un array vacío
$message = "";

// Verificar si se ha enviado un correo mediante POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user-mail'])) {
    $trainerEmail = $_POST['user-mail'];

    // Conectar a la base de datos
    $link = mysqli_connect("localhost", "root", "", "Pokewebapp");

    // Revisar si se ha realizado la conexión
    if ($link == false) {
        $message = "ERROR: Could not connect " . mysqli_connect_error();
    } else {
        // Obtener el nombre y correo del usuario actual
        $email = $_SESSION["currentEmail"];
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

        // Obtener el nombre y correo del entrenador
        $sql = "SELECT nombre, correo FROM usuario WHERE correo='$trainerEmail'";
        $result = mysqli_query($link, $sql);
        if (mysqli_num_rows($result) > 0) {
            while ($row = $result->fetch_assoc()) {
                $name_trainer = $row['nombre'];
                $user_mail = $row['correo'];
            }

            // Obtener los Pokémon del entrenador
            $sql = "SELECT p.id, p.img_id, p.especie, p.nombre, p.peso, p.altura, p.baxp 
                    FROM pokemon p 
                    INNER JOIN Pokedek_pokemon pp ON p.id = pp.id_pokemon 
                    INNER JOIN Pokedek pk ON pp.id_pokedek = pk.id 
                    INNER JOIN Usuario u ON pk.id_usuario = u.id 
                    WHERE u.correo = '$trainerEmail' 
                    ORDER BY p.id DESC";
            $result = mysqli_query($link, $sql);
            if (mysqli_num_rows($result) > 0) {
                while ($row = $result->fetch_assoc()) {
                    $poks[] = $row; // Añadir los Pokémon al array $poks
                }
            }

        } else {
            $message = "Could not find trainer";
        }

        // Cerrar la conexión
        mysqli_close($link);
    }
} else {
    header("Location: /social.php");
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
    <link rel="stylesheet" href="/css/Main.css" />
    <link rel="stylesheet" href="/css/Social.css" />
    <title>Trainer View</title>
    <style>
        .carousel__viewport {
            display: flex;
            overflow: hidden;
            scroll-snap-type: x mandatory;
            position: relative;
        }
        .carousel__slide {
            min-width: 100%;
            scroll-snap-align: start;
            position: relative;
        }
        .carousel__navigation {
            display: flex;
            justify-content: space-between;
            position: absolute;
            width: 100%;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
        }
        .carousel__navigation-button {
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            z-index: 10;
        }
        .carousel__navigation-button:focus {
            outline: none;
        }
        .carousel::before,
        .carousel::after,
        .carousel__prev,
        .carousel__next {
            position: absolute;
            top: 0;
            margin-top: 37.5%;
            width: 4rem;
            height: 4rem;
            transform: translateY(-50%);
            border-radius: 50%;
            font-size: 0;
            outline: 0;
        }
        .carousel::before,
        .carousel__prev {
            left: -1rem;
        }
        .carousel::after,
        .carousel__next {
            right: -1rem;
        }
        .carousel::before,
        .carousel::after {
            content: "";
            z-index: 1;
            background-color: #333;
            background-size: 1.5rem 1.5rem;
            background-repeat: no-repeat;
            background-position: center center;
            color: #fff;
            font-size: 2.5rem;
            line-height: 4rem;
            text-align: center;
            pointer-events: none;
        }
        .carousel::before {
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpolygon points='0,50 80,100 80,0' fill='%23fff'/%3E%3C/svg%3E");
        }
        .carousel::after {
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpolygon points='100,50 20,100 20,0' fill='%23fff'/%3E%3C/svg%3E");
        }
    </style>
</head>
<body>
    <div class="Navbar">
        <div class="container-fluid d-flex justify-content-between">
            <a class="Navbar__brand" href="javascript:window.location.assign('main.php')">
                <img class="Navbar__brand-logo" src="/img/social.svg" alt="Logo" />
                <span class="font-weight-light">Poké</span>
                <span class="font-weight-bold">dex</span>
            </a>
            <div class="header__menu">
                <div class="header__menu--profile">
                    <img src="/img/avatar.svg" alt="User" />
                    <p>
                      <?php echo htmlspecialchars($name); ?>
                    </p>
                </div>
                <ul>
                    <li><a class="font-weight-bold" href="javascript:window.location.assign('profile.php')">Pokédex</a></li>
                    <li><a class="font-weight-bold" href="javascript:window.location.assign('social.php')">Social</a></li>
                    <li><a class="font-weight-bold" href="javascript:window.location.assign('main.php')">Home</a></li>
                    <li><a onclick="logout()" class="font-weight-bold" href="#">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="ml-5 mr-5 mt-3 Social__title d-flex flex-column">
        <div class="d-flex justify-content-center align-items-center">
            <div class="d-flex flex-column">
                <h1 class="display-1"><?php echo htmlspecialchars($name_trainer); ?></h1>
                <div class="d-flex justify-content-center font-weight-bold">
                    <?php echo htmlspecialchars($user_mail); ?>
                </div>
            </div>
            <div class="d-flex flex-column">
                <button disabled class="ml-5 btn btn-success font-weight-bold" href="javascript:window.location.assign('profile.php')">
                    Add to Friends
                </button>
                <a class="ml-5 mt-3 btn btn-danger font-weight-bold" href="javascript:window.location.assign('social.php')">
                    Get back to Social
                </a>
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-center position-relative">
            <section class="carousel" aria-label="Gallery">
                <div class="carousel__viewport">
                    <?php
                    if (sizeof($poks) != 0) {
                        for ($i = 0; $i < sizeof($poks); $i++) {
                            echo "
                                <div id=\"carousel__slide" . $i . "\"
                                class=\"carousel__slide\">
                                <div class=\"d-flex justify-content-center align-items-center\">
                                    <img src=\"https://img.pokemondb.net/artwork/large/" . htmlspecialchars($poks[$i]["nombre"]) . ".jpg\" alt=\"\" width=\"400px\">
                                    <div class=\"ml-4 d-flex flex-column\">
                                        <h1>" . htmlspecialchars($poks[$i]["nombre"]) . "</h1>
                                        <h3>" . htmlspecialchars($poks[$i]["peso"]) . " lbs.</h3>
                                        <h3>" . htmlspecialchars($poks[$i]["altura"]) . " fts.</h3>
                                        <h3>BAXP: " . htmlspecialchars($poks[$i]["baxp"]) . " pts.</h3>
                                    </div>
                                </div>
                            </div>
                            ";
                        }
                    } else {
                        echo "
                            <div id=\"carousel__slide0\"
                            class=\"carousel__slide\">
                            <div class=\"d-flex justify-content-center align-items-center\">
                                <img src=\"/img/broke.svg\" alt=\"\" width=\"400px\">
                            </div>
                        </div>
                        ";
                    }
                    ?>
                </div>
                <button class="carousel__navigation-button carousel__prev">❮</button>
                <button class="carousel__navigation-button carousel__next">❯</button>
            </section>
        </div>
    </div>
    <script>
        const slides = document.querySelectorAll('.carousel__slide');
        let currentIndex = 0;

        document.querySelector('.carousel__prev').addEventListener('click', () => {
            currentIndex = (currentIndex > 0) ? currentIndex - 1 : slides.length - 1;
            slides[currentIndex].scrollIntoView({ behavior: 'smooth' });
        });

        document.querySelector('.carousel__next').addEventListener('click', () => {
            currentIndex = (currentIndex < slides.length - 1) ? currentIndex + 1 : 0;
            slides[currentIndex].scrollIntoView({ behavior: 'smooth' });
        });
    </script>
</body>
</html>
