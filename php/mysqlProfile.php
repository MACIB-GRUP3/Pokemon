<?php
$name = "";
$pokeballs  = 20;
//establecemos la conexión con la base de datos
$link = mysqli_connect("localhost","root","","Pokewebapp");
//revisamos que se haya realizado la conexión
if($link == false){
	$message = "ERROR: Could not connect ".mysqli_connect_error();
}else{
//obtenemos los datos enviados por el post
$email = $_SESSION["currentEmail"];
//utilizando solo el correo, obtendremos los pokemons
//que el usuario tiene en su pokedek
$sql = "SELECT p.id, p.img_id,p.especie,p.nombre,p.peso,p.altura,p.baxp FROM pokemon p INNER JOIN Pokedek_pokemon pp ON p.id = pp.id_pokemon INNER JOIN Pokedek pk  ON pp.id_pokedek = pk.id INNER JOIN Usuario u ON pk.id_usuario = u.id WHERE u.correo ='$email' ORDER BY p.id DESC";
//revisamos que se haya ejecutado el query
//creamos una bandera para identificar si se encontraron pokemons o no
$flag = false; $totalPokes = 0;
$result = mysqli_query($link, $sql);
if (mysqli_num_rows($result) > 0){
    while ($row = $result->fetch_assoc())
    {
       // echo "img_id: ".$row["img_id"]." especie: ".$row["especie"]." //nombre: ".$row["nombre"]." peso: ".$row["peso"]." altura: //".$row["altura"]." baxp: ".$row["baxp"]; 
    	//adding data to the array
      $myArr[] = $row;
      $totalPokes++;

    }
$flag = true;
}else{
  $flag = false;
	$message = "Pokemons cannnot be found";
}
//comprobaremos que  exista el correo  en la base de datos
$sql = "SELECT nombre FROM usuario WHERE correo='$email'";
$result = mysqli_query($link, $sql);

if (mysqli_num_rows($result) > 0){
    while ($row = $result->fetch_assoc())
    {
        foreach($row as $value) $name = $value;
    }
}else{
	$message = "Credenciales incorrectas";
// Close connection
mysqli_close($link);}
//recuperamos pokeballs
$sql = "SELECT pokeballs FROM usuario WHERE correo='$email'";
$result = mysqli_query($link, $sql);

if (mysqli_num_rows($result) > 0){
    while ($row = $result->fetch_assoc())
    {
        foreach($row as $value) $pokeballs = $value;
    }
}else{
	$message = "Credenciales incorrectas";
// Close connection
mysqli_close($link);}
  }
?>