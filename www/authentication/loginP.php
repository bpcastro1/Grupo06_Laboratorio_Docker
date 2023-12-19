<?php
session_start();

require_once '../constants/constantes.php';
require_once '../DB/coneccion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idUsuario = $_POST["usuario"];
    $password = $_POST["password"];

    $conexion = coneccion();

    $query = "SELECT * FROM veris.usuarios WHERE IdUsuario = '$idUsuario' AND Password = '$password'";
    $query2 = "SELECT * FROM veris.pacientes WHERE IdUsuario = '$idUsuario'" ;
    $result = $conexion->query($query);

    if ($result->num_rows > 0) {
        $_SESSION["idUsuario"] = $idUsuario;
        $_SESSION["rol"] = 3;
        $_SESSION["IdPaciente"] = $conexion->query($query2)->fetch_assoc()["IdPaciente"];
        header("Location: ../unido.php");
        exit();
    } else {
        header("Location: loginPacientes.php?error=Credenciales incorrectas. Por favor, inténtelo de nuevo.");
        exit();
    }

    $conexion->close();
}
?>