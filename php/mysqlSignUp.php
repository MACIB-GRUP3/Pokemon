<?php
$message = "";

// Establecemos la conexión con la base de datos
if (!empty($_POST)) {
    $link = mysqli_connect("localhost","root","","Pokewebapp");
    // Revisamos que se haya realizado la conexión
    if ($link == false){
        die("ERROR: Could not connect ".mysqli_connect_error());
    } else {
        // Obtenemos los datos enviados por el post
        $email = mysqli_real_escape_string($link, $_POST["email"]);
        $name = $_POST["name"];
        $pwd = password_hash($_POST["pwd"], PASSWORD_BCRYPT);
        $pokeballs= 30;
        $date = date("Y/m/d h:i:s");
        
        // Preparamos el query para evitar inyecciones SQL
        $stmt = $link->prepare("SELECT * FROM usuario WHERE correo=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0){
            $message = "Error: Email already exists";
        } else {
            $stmt = $link->prepare("INSERT INTO usuario (nombre,correo,contrasena,pokeballs,fecha_creacion) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssis", $name, $email, $pwd, $pokeballs, $date);
            $stmt->execute();
            
            $stmt = $link->prepare("SELECT id FROM usuario WHERE correo=?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $id = "";
            while($row = $result->fetch_array()){
                $id = $row['id'];
            }
            $stmt = $link->prepare("INSERT INTO pokedek (id_usuario) VALUES (?)");
            $stmt->bind_param("s", $id);
            $stmt->execute();

            header('Location: /signin.php');
            exit();
        }
        $stmt->close();
        mysqli_close($link);
    }
}
?>
