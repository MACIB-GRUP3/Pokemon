<?php
$id_usuario = "";
$id_pokedek = "";
$id_pokemon = "";

$message = "";
// Establecemos la conexión con la base de datos
$link = mysqli_connect("localhost", "root", "", "Pokewebapp");

// Revisamos que se haya realizado la conexión
if ($link == false) {
    $message = "ERROR: Could not connect " . mysqli_connect_error();
} else {
    // Obtenemos el id del usuario de la sesión
    session_start();
    $userID = $_SESSION["currentId"];

    // Verificar si hay pokeballs disponibles
    $sql = "SELECT pokeballs FROM usuario WHERE id='$userID'";
    $result = mysqli_query($link, $sql);
    $row = mysqli_fetch_assoc($result);
    $pokeballs = $row["pokeballs"];

    if ($pokeballs > 0) {
        // Obtenemos el id del pokedek del usuario
        $sql = "SELECT id FROM pokedek WHERE id_usuario='$userID'";
        $result = mysqli_query($link, $sql);

        if (mysqli_num_rows($result) > 0) {
            while ($row = $result->fetch_assoc()) {
                foreach ($row as $value) $id_pokedek = $value;
            }

            // Verificación aleatoria del 33%
            $randomNumber = rand(1, 2);
            if ($randomNumber == 1) {
                // Insertar el pokemon normalmente
                $img_id = $_COOKIE["img_id"];
                $especie = $_COOKIE["especie"];
                $peso = $_COOKIE["peso"];
                $altura = $_COOKIE["altura"];
                $baxp = $_COOKIE["baxp"];

                // Creamos el query de insert para el pokemon
                $sql = "INSERT INTO pokemon (img_id,especie,nombre,peso,altura,baxp) VALUES ('$img_id','$especie','$especie','$peso','$altura','$baxp')";
                if (mysqli_query($link, $sql)) {
                    // Obtenemos el id del último pokemon agregado
                    $id_pokemon = mysqli_insert_id($link);

                    // Agregamos el pokemon al pokedek
                    $sql = "INSERT INTO pokedek_pokemon (id_pokedek,id_pokemon) VALUES ('$id_pokedek','$id_pokemon')";
                    if (mysqli_query($link, $sql)) {
                        $message = "Pokemon added to Pokedek";
                        header('Location: /successInsert.html');
                    } else {
                        $message = "Pokemon cannot be added to Pokedek";
                    }
                } else {
                    $message = "Error inserting Pokemon";
                }
            } else {
                // Restar una pokeball de la tabla usuario y redirigir a failInsert.html
                $sql = "UPDATE usuario SET pokeballs = pokeballs - 1 WHERE id='$userID'";
                if (mysqli_query($link, $sql)) {
                    $message = "Pokeball subtracted from user";
                    header('Location: /failInsert.html');
                } else {
                    $message = "Error subtracting pokeball from user";
                }
            }
        } else {
            $message = "Pokedek not found for user";
        }
    } else {
        // No hay pokeballs disponibles, redirigir a nopokeballs.html
        header('Location: /nopokeballs.html');
    }

    // Cerramos la conexión
    mysqli_close($link);
}
