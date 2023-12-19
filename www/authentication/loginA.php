<?php
session_start();

require_once '../constants/constantes.php';
require_once '../DB/coneccion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idUsuario = 1;
    $password = $_POST["password"];

    $conexion = coneccion();

    $query = "SELECT * FROM veris.usuarios WHERE IdUsuario = '$idUsuario' AND Password = '$password'";
    $result = $conexion->query($query);

    if ($result->num_rows > 0) {
        $_SESSION["idUsuario"] = $idUsuario;
        $_SESSION["rol"] = 1;
        header("Location: ../unido.php");
        exit();
    } else {
        header("Location: loginAdmin.php?error=Credenciales incorrectas. Por favor, inténtelo de nuevo.");
        exit();
    }

    $conexion->close();
}
?>