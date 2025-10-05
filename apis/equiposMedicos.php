<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Conexión BD
$servidor = "localhost";
$usuario = "root";
$passwd = "";
$nombreBaseDatos = "gestion";
$conexionBD = new mysqli($servidor, $usuario, $passwd, $nombreBaseDatos);
$conexionBD->set_charset("utf8");

// ---------------- FUNCIONES -----------------

// Generar ID_EM (autonumérico gestionado por la API). Devuelve un string numérico.
function generarIDEM($conexionBD) {
    $sql = "SELECT ID_EM FROM equiposmedicos ORDER BY CAST(ID_EM AS UNSIGNED) ASC";
    $result = $conexionBD->query($sql);

    $ocupados = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $val = intval($row["ID_EM"]);
            if ($val > 0) $ocupados[$val] = true;
        }
    }

    $n = 1;
    while (isset($ocupados[$n])) $n++;
    return strval($n);
}

// ---------------- ENDPOINTS -----------------

// CONSULTAR por ID_EM
if (isset($_GET["consultar"])) {
    $id = $conexionBD->real_escape_string($_GET["consultar"]);
    $sql = mysqli_query($conexionBD, "SELECT * FROM equiposmedicos WHERE ID_EM='$id'");
    if (mysqli_num_rows($sql) > 0) {
        echo json_encode(mysqli_fetch_assoc($sql));
    } else {
        echo json_encode(["success" => 0]);
    }
    exit();
}

// BORRAR
if (isset($_GET["borrar"])) {
    $id = $conexionBD->real_escape_string($_GET["borrar"]);
    $sql = "DELETE FROM equiposmedicos WHERE ID_EM='$id'";
    if ($conexionBD->query($sql)) {
        echo json_encode(["success" => 1]);
    } else {
        echo json_encode(["success" => 0, "error" => $conexionBD->error]);
    }
    exit();
}

// INSERTAR EQUIPO
if (isset($_GET["insertar"])) {
    $data = json_decode(file_get_contents("php://input"));

    $Num_activo  = isset($data->Num_activo)  ? trim($data->Num_activo)  : '';
    $Marca       = isset($data->Marca)       ? trim($data->Marca)       : '';
    $Modelo      = isset($data->Modelo)      ? trim($data->Modelo)      : '';
    $Codigo_ubi  = isset($data->Codigo_ubi)  ? trim($data->Codigo_ubi)  : null;
    $Codigo_Resp = isset($data->Codigo_Resp) ? trim($data->Codigo_Resp) : null;

    if ($Num_activo === "" || $Marca === "" || $Modelo === "") {
        echo json_encode(["success" => 0, "error" => "Faltan campos obligatorios (Num_activo, Marca, Modelo)."]);
        exit();
    }

    $Num_activo  = $conexionBD->real_escape_string($Num_activo);
    $Marca       = $conexionBD->real_escape_string($Marca);
    $Modelo      = $conexionBD->real_escape_string($Modelo);
    $Codigo_ubi  = $conexionBD->real_escape_string($Codigo_ubi);
    $Codigo_Resp = $conexionBD->real_escape_string($Codigo_Resp);

    $ID_EM = generarIDEM($conexionBD);

    $sql = "INSERT INTO equiposmedicos (ID_EM, Num_activo, Marca, Modelo, Codigo_ubi, Codigo_Resp) 
            VALUES ('$ID_EM','$Num_activo','$Marca','$Modelo',
            " . ($Codigo_ubi ? "'$Codigo_ubi'" : "NULL") . ",
            " . ($Codigo_Resp ? "'$Codigo_Resp'" : "NULL") . ")";

    if ($conexionBD->query($sql)) {
        echo json_encode(["success" => 1, "ID_EM" => $ID_EM]);
    } else {
        echo json_encode(["success" => 0, "error" => $conexionBD->error]);
    }
    exit();
}

// ACTUALIZAR
if (isset($_GET["actualizar"])) {
    $data = json_decode(file_get_contents("php://input"));
    $idOld = $conexionBD->real_escape_string($_GET["actualizar"]);

    $sqlOld = mysqli_query($conexionBD, "SELECT * FROM equiposmedicos WHERE ID_EM='$idOld'");
    if (mysqli_num_rows($sqlOld) === 0) {
        echo json_encode(["success" => 0, "error" => "Registro no encontrado"]);
        exit();
    }
    $oldRow = mysqli_fetch_assoc($sqlOld);

    $Num_activo  = isset($data->Num_activo)  ? trim($data->Num_activo)  : $oldRow['Num_activo'];
    $Marca       = isset($data->Marca)       ? trim($data->Marca)       : $oldRow['Marca'];
    $Modelo      = isset($data->Modelo)      ? trim($data->Modelo)      : $oldRow['Modelo'];
    $Codigo_ubi  = isset($data->Codigo_ubi)  ? trim($data->Codigo_ubi)  : $oldRow['Codigo_ubi'];
    $Codigo_Resp = isset($data->Codigo_Resp) ? trim($data->Codigo_Resp) : $oldRow['Codigo_Resp'];

    $Num_activo  = $conexionBD->real_escape_string($Num_activo);
    $Marca       = $conexionBD->real_escape_string($Marca);
    $Modelo      = $conexionBD->real_escape_string($Modelo);
    $Codigo_ubi  = $conexionBD->real_escape_string($Codigo_ubi);
    $Codigo_Resp = $conexionBD->real_escape_string($Codigo_Resp);

    $sqlUpdate = "UPDATE equiposmedicos 
        SET Num_activo='$Num_activo', Marca='$Marca', Modelo='$Modelo',
            Codigo_ubi=" . ($Codigo_ubi ? "'$Codigo_ubi'" : "NULL") . ",
            Codigo_Resp=" . ($Codigo_Resp ? "'$Codigo_Resp'" : "NULL") . "
        WHERE ID_EM='$idOld'";

    if ($conexionBD->query($sqlUpdate)) {
        echo json_encode(["success" => 1, "ID_EM" => $idOld]);
    } else {
        echo json_encode(["success" => 0, "error" => $conexionBD->error]);
    }
    exit();
}

// LISTAR todos
$sql = mysqli_query($conexionBD, "SELECT * FROM equiposmedicos");
if (mysqli_num_rows($sql) > 0) {
    $equipos = mysqli_fetch_all($sql, MYSQLI_ASSOC);
    echo json_encode($equipos);
} else {
    echo json_encode([["success" => 0]]);
}
?>
