<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: POST");

// Configuración de la base de datos
$servidor = "localhost";
$usuarioBD = "root";
$passwdBD = "";
$nombreBaseDatos = "gestion";

// Conexión
$conexionBD = new mysqli($servidor, $usuarioBD, $passwdBD, $nombreBaseDatos);
if ($conexionBD->connect_error) {
    die(json_encode(["success" => false, "error" => "Error de conexión a la base de datos"]));
}

// Leer datos del cuerpo JSON
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["usuario"], $data["password"], $data["rol"])) {
    echo json_encode(["success" => false, "error" => "Faltan datos"]);
    exit;
}

$usuario = $conexionBD->real_escape_string($data["usuario"]);
$passwordPlano = $data["password"];
$rol = $conexionBD->real_escape_string($data["rol"]);

// Verificar si el usuario ya existe
$sqlCheck = "SELECT id FROM usuarios WHERE usuario = '$usuario'";
$resultCheck = $conexionBD->query($sqlCheck);

if ($resultCheck && $resultCheck->num_rows > 0) {
    echo json_encode(["success" => false, "error" => "El usuario ya existe"]);
    exit;
}

// Encriptar la contraseña
$passwordHash = password_hash($passwordPlano, PASSWORD_BCRYPT);

// Insertar nuevo usuario
$sqlInsert = "INSERT INTO usuarios (usuario, password, rol) VALUES ('$usuario', '$passwordHash', '$rol')";
if ($conexionBD->query($sqlInsert) === TRUE) {
    echo json_encode(["success" => true, "usuario" => $usuario, "rol" => $rol]);
} else {
    echo json_encode(["success" => false, "error" => "Error al registrar: " . $conexionBD->error]);
}

$conexionBD->close();
?>
