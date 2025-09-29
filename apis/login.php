<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: POST");

// Configuración de la base de datos
$servidor = "localhost";
$usuario = "root";
$passwd = "";
$nombreBaseDatos = "gestion";

$conexionBD = new mysqli($servidor, $usuario, $passwd, $nombreBaseDatos);
if ($conexionBD->connect_error) {
    die(json_encode(["success" => 0, "error" => "Error de conexión: " . $conexionBD->connect_error]));
}

// Obtener datos del POST
$data = json_decode(file_get_contents("php://input"), true);
$usuarioIngresado = isset($data['usuario']) ? trim($data['usuario']) : '';
$passwordIngresado = isset($data['password']) ? $data['password'] : '';

if (empty($usuarioIngresado) || empty($passwordIngresado)) {
    echo json_encode(["success" => 0, "error" => "Debe ingresar usuario y contraseña"]);
    exit();
}

// Preparar consulta segura
$stmt = $conexionBD->prepare("SELECT * FROM usuarios WHERE usuario=? LIMIT 1");
$stmt->bind_param("s", $usuarioIngresado);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    // Verificar contraseña encriptada
    if (password_verify($passwordIngresado, $user['password'])) {
        // Login exitoso
        echo json_encode([
            "success" => 1,
            "usuario" => $user['usuario'],
            "mensaje" => "Bienvenido " . $user['usuario']
        ]);
    } else {
        echo json_encode(["success" => 0, "error" => "Contraseña incorrecta"]);
    }
} else {
    echo json_encode(["success" => 0, "error" => "Usuario no encontrado"]);
}
?>

