<?php 
$email = $_SESSION["currentEmail"];
if (isset($_POST['email'])) {
	$user_mail = $_POST['email'];
}

//establecemos la conexión con la base de datos
$link = mysqli_connect("localhost","root","","Pokewebapp");
//revisamos que se haya realizado la conexión
if($link == false){
	echo "cannot connect";
// Close connection

}else{
	$sql = "SELECT nombre FROM usuario WHERE correo='$email'";
	$result = mysqli_query($link, $sql);
	if (mysqli_num_rows($result) > 0){
		 while ($row = $result->fetch_assoc())
    {
        foreach($row as $value) $name = $value;
    }
		//Lo primero que necesitamos, será obtener el nombre del usuario
	//que se ha encontrado
	$sql = "SELECT nombre FROM usuario WHERE correo ='$user_mail'";
	$result = mysqli_query($link, $sql);
	$name="";
	if (mysqli_num_rows($result) > 0){
            while ($row = $result->fetch_assoc())
    {
       foreach($row as $value) $nombre = $value;
    } 
    //Ahora, procederemos a obtener los specs de los pokemons encontrados.
    $sql = "SELECT p.id, p.img_id,p.especie,p.nombre,p.peso,p.altura,p.baxp FROM pokemon p INNER JOIN Pokedek_pokemon pp ON p.id = pp.id_pokemon INNER JOIN Pokedek pk  ON pp.id_pokedek = pk.id INNER JOIN Usuario u ON pk.id_usuario = u.id WHERE u.correo ='$user_mail' ORDER BY p.id DESC";
    $result = mysqli_query($link, $sql);
    if (mysqli_num_rows($result) > 0){
    	 while ($row = $result->fetch_assoc())
    		{
      			$poks[] = $row;
    		}
    }else{
    	$poks = Array();
    	$message = "Could not find pokemons";
    	// Close connection
	 	
    }
    	}	else{
    	$message = "Couldnot find username";
    	// Close connection
	 	
    		}
    	}else{
    		$message = "Couldnot find current user";
    	// Close connection
	 	
    	}
	
}
mysqli_close($link);
?>