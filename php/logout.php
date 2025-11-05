<?php
session_start(); // Iniciar la sesión
session_unset(); // Eliminar todas las variables de sesión
session_destroy(); // Destruir la sesión
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cerrando sesión...</title>
    <script>
        // Código JavaScript para limpiar las cookies y redirigir
        document.cookie.split(";").forEach(function(cookie) {
            document.cookie = cookie.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/");
        });
        window.location.href = "signin.php";
    </script>
</head>
<body>
    <h1>Cerrando sesión...</h1>
    <p>Por favor, espera mientras te redirigimos a la página de inicio de sesión.</p>
</body>
</html>
