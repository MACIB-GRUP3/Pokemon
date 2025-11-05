<?php
session_start(); // Iniciar sesión

// Verificar si hay una sesión iniciada
if (isset($_SESSION["currentEmail"])) {
  // La sesión está iniciada
  $response = array("isLoggedIn" => true);
} else {
  // La sesión no está iniciada
  $response = array("isLoggedIn" => false);
}

// Devolver la respuesta como JSON
header("Content-Type: application/json");
echo json_encode($response);
?>
