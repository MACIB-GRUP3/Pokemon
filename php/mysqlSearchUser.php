<?php
$message="";
//establecemos la conexión con la base de datos
$link = mysqli_connect("localhost","root","","Pokewebapp");
//revisamos que se haya realizado la conexión
if($link == false){
	echo "cannot connect";
// Close connection
mysqli_close($link);
}else{
   $user_mail = "";
    //si el usuario hizo submit en la busqueda, se muestra el 
    //usuario que busca si existw
    if(isset($_POST["user-mail"])){
    	
    	$user_mail = $_POST["user-mail"];
    	$sql = "SELECT * FROM usuario WHERE correo = '$user_mail'";
    	$result = mysqli_query($link, $sql);
    	if (mysqli_num_rows($result) > 0){
            setcookie("user_mail", $user_mail, time() + (86400 * 1), "/");
    		header('Location: /trainerView.php');
    	}	else{
    	$message = "Could not find user";
    	// Close connection
	 	mysqli_close($link);
    		}
    }

}
?>