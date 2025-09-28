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

// Generar Codigo_Resp: R + 2 primeras letras del cargo (sin espacios) + 4 dígitos.
// Busca el primer hueco numérico disponible para ese prefijo.
function generarCodigoResp($cargo, $conexionBD) {
    $cargoClean = preg_replace('/\s+/', '', strtoupper($cargo));
    $prefijo = "R" . substr($cargoClean, 0, 2); // R + 2 letras

    $prefijoEsc = $conexionBD->real_escape_string($prefijo);
    $sql = "SELECT Codigo_Resp FROM responsables WHERE Codigo_Resp LIKE '{$prefijoEsc}%' ORDER BY Codigo_Resp ASC";
    $result = $conexionBD->query($sql);

    $ocupados = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $numPart = substr($row["Codigo_Resp"], strlen($prefijo)); // obtiene la parte numérica
            $num = intval($numPart);
            if ($num > 0) $ocupados[$num] = true;
        }
    }

    $n = 1;
    while (isset($ocupados[$n])) $n++;

    return $prefijo . str_pad($n, 4, "0", STR_PAD_LEFT);
}

// Generar ID_Resp (autonumérico gestionado por la API). Devuelve un número (string).
function generarIDResp($conexionBD) {
    // Tomamos los ID_Resp existentes (ordenados numéricamente) y buscamos el primer hueco
    $sql = "SELECT ID_Resp FROM responsables ORDER BY CAST(ID_Resp AS UNSIGNED) ASC";
    $result = $conexionBD->query($sql);

    $ocupados = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $val = intval($row["ID_Resp"]);
            if ($val > 0) $ocupados[$val] = true;
        }
    }

    $n = 1;
    while (isset($ocupados[$n])) $n++;
    return strval($n);
}

// ---------------- ENDPOINTS -----------------

// CONSULTAR por Codigo_Resp
if (isset($_GET["consultar"])) {
    $codigo = $conexionBD->real_escape_string($_GET["consultar"]);
    $sql = mysqli_query($conexionBD, "SELECT * FROM responsables WHERE Codigo_Resp='$codigo'");
    if (mysqli_num_rows($sql) > 0) {
        echo json_encode(mysqli_fetch_assoc($sql));
    } else {
        echo json_encode(["success" => 0]);
    }
    exit();
}

// CONSULTAR por Cedula
if (isset($_GET["consultarCC"])) {
    $cedula = $conexionBD->real_escape_string($_GET["consultarCC"]);
    $sql = mysqli_query($conexionBD, "SELECT * FROM responsables WHERE Cedula='$cedula'");
    if (mysqli_num_rows($sql) > 0) {
        echo json_encode(mysqli_fetch_assoc($sql));
    } else {
        echo json_encode(["success" => 0]);
    }
    exit();
}

// BORRAR
if (isset($_GET["borrar"])) {
    $codigo = $conexionBD->real_escape_string($_GET["borrar"]);

    // Transacción para asegurar consistencia
    $conexionBD->begin_transaction();
    try {
        // Paso 1: Poner null en los hijos
        $sqlChild = "UPDATE equiposmedicos SET Codigo_Resp=NULL WHERE Codigo_Resp='$codigo'";
        if (!$conexionBD->query($sqlChild)) {
            throw new Exception("Error actualizando equiposmedicos: " . $conexionBD->error);
        }

        // Paso 2: Borrar el responsable
        $sqlParent = "DELETE FROM responsables WHERE Codigo_Resp='$codigo'";
        if (!$conexionBD->query($sqlParent)) {
            throw new Exception("Error borrando responsable: " . $conexionBD->error);
        }

        $conexionBD->commit();
        echo json_encode(["success" => 1]);
    } catch (Exception $e) {
        $conexionBD->rollback();
        echo json_encode(["success" => 0, "error" => $e->getMessage()]);
    }

    exit();
}

// INSERTAR RESPONSABLE
if (isset($_GET["insertar"])) {

    // Leer datos JSON enviados desde la solicitud
    $data = json_decode(file_get_contents("php://input"));

    // Asignar variables con validación básica
    $Cedula  = isset($data->Cedula)  ? trim($data->Cedula)  : '';
    $Nombre  = isset($data->Nombre)  ? trim($data->Nombre)  : '';
    $Apellido = isset($data->Apellido) ? trim($data->Apellido) : '';
    $Cargo   = isset($data->Cargo)   ? trim($data->Cargo)   : '';
    $Telefono = isset($data->Telefono) ? trim($data->Telefono) : '';

    // Validaciones obligatorias
    if ($Cedula === "" || $Nombre === "" || $Apellido === "") {
        echo json_encode([
            "success" => 0, 
            "error" => "Faltan campos obligatorios (Cedula, Nombre, Apellido)."
        ]);
        exit();
    }

    // Sanitizar entradas para evitar inyecciones SQL
    $Cedula   = $conexionBD->real_escape_string($Cedula);
    $Nombre   = $conexionBD->real_escape_string($Nombre);
    $Apellido = $conexionBD->real_escape_string($Apellido);
    $Cargo    = $conexionBD->real_escape_string($Cargo);
    $Telefono = $conexionBD->real_escape_string($Telefono);

    // Validar que la cédula no esté repetida
    $checkCedula = mysqli_query($conexionBD, "SELECT 1 FROM responsables WHERE Cedula='$Cedula' LIMIT 1");
    if (mysqli_num_rows($checkCedula) > 0) {
        echo json_encode([
            "success" => 0, 
            "error" => "La cédula ya está registrada"
        ]);
        exit();
    }

    // Generar ID y Código de responsable mediante funciones de la API
    $ID_Resp     = generarIDResp($conexionBD);          // devuelve string con número (1,2,...)
    $Codigo_Resp = generarCodigoResp($Cargo, $conexionBD);

    // Insertar en la base de datos
    $sql = mysqli_query(
        $conexionBD, 
        "INSERT INTO responsables (Codigo_Resp, ID_Resp, Cedula, Nombre, Apellido, Cargo, Telefono) 
        VALUES ('$Codigo_Resp','$ID_Resp','$Cedula','$Nombre','$Apellido','$Cargo','$Telefono')"
    );

    // Respuesta JSON
    if ($sql) {
        echo json_encode([
            "success" => 1, 
            "Codigo_Resp" => $Codigo_Resp, 
            "ID_Resp" => $ID_Resp
        ]);
    } else {
        echo json_encode([
            "success" => 0, 
            "error" => $conexionBD->error
        ]);
    }

    exit();
}

// ACTUALIZAR
if (isset($_GET["actualizar"])) {
    $data = json_decode(file_get_contents("php://input"));
    $Codigo_Resp_old = $conexionBD->real_escape_string($_GET["actualizar"]);

    // Obtener registro actual
    $sqlOld = mysqli_query($conexionBD, "SELECT * FROM responsables WHERE Codigo_Resp='$Codigo_Resp_old'");
    if (mysqli_num_rows($sqlOld) === 0) {
        echo json_encode(["success" => 0, "error" => "Registro no encontrado"]);
        exit();
    }
    $oldRow = mysqli_fetch_assoc($sqlOld);

    // Nuevos valores (si vienen)
    $Cedula = isset($data->Cedula) ? trim($data->Cedula) : $oldRow['Cedula'];
    $Nombre = isset($data->Nombre) ? trim($data->Nombre) : $oldRow['Nombre'];
    $Apellido = isset($data->Apellido) ? trim($data->Apellido) : $oldRow['Apellido'];
    $Cargo = isset($data->Cargo) ? trim($data->Cargo) : $oldRow['Cargo'];
    $Telefono = isset($data->Telefono) ? trim($data->Telefono) : $oldRow['Telefono'];

    // sanitizar
    $Cedula = $conexionBD->real_escape_string($Cedula);
    $Nombre = $conexionBD->real_escape_string($Nombre);
    $Apellido = $conexionBD->real_escape_string($Apellido);
    $Cargo = $conexionBD->real_escape_string($Cargo);
    $Telefono = $conexionBD->real_escape_string($Telefono);

    // Verificar que la cédula no esté duplicada (excepto en el mismo registro)
    $checkCedula = mysqli_query($conexionBD, "SELECT 1 FROM responsables WHERE Cedula='$Cedula' AND Codigo_Resp!='$Codigo_Resp_old' LIMIT 1");
    if (mysqli_num_rows($checkCedula) > 0) {
        echo json_encode(["success" => 0, "error" => "La cédula ya está registrada en otro responsable"]);
        exit();
    }

    // Si cambió el cargo, generar nuevo Codigo_Resp
    $newCodigo = $Codigo_Resp_old;
    $cargoChanged = ($oldRow['Cargo'] !== $Cargo);
    if ($cargoChanged) {
        $newCodigo = generarCodigoResp($Cargo, $conexionBD);
    }

    // MANTENER ID_Resp actual (no se regenera en actualización)
    $ID_Resp_old = $oldRow['ID_Resp'];

    // Transacción: actualizar tabla padre y tabla hija (equiposmedicos) si aplica
    $conexionBD->begin_transaction();
    try {
        // actualizar responsable (código primario puede cambiar)
        $sqlUpdate = "UPDATE responsables 
            SET Codigo_Resp='$newCodigo', ID_Resp='$ID_Resp_old', Cedula='$Cedula',
                Nombre='$Nombre', Apellido='$Apellido', Cargo='$Cargo', Telefono='$Telefono'
            WHERE Codigo_Resp='$Codigo_Resp_old'";

        if (!$conexionBD->query($sqlUpdate)) {
            throw new Exception("Error actualizando responsables: " . $conexionBD->error);
        }

        // Si cambió el Codigo_Resp, actualizar la(s) fila(s) hijas en equiposmedicos
        if ($cargoChanged && $newCodigo !== $Codigo_Resp_old) {
            $sqlChild = "UPDATE equiposmedicos SET Codigo_Resp='$newCodigo' WHERE Codigo_Resp='$Codigo_Resp_old'";
            if (!$conexionBD->query($sqlChild)) {
                throw new Exception("Error actualizando equiposmedicos (cascade manual): " . $conexionBD->error);
            }
        }

        $conexionBD->commit();
        echo json_encode(["success" => 1, "Codigo_Resp" => $newCodigo]);
    } catch (Exception $e) {
        $conexionBD->rollback();
        echo json_encode(["success" => 0, "error" => $e->getMessage()]);
    }

    exit();
}

// LISTAR todos
$sql = mysqli_query($conexionBD, "SELECT * FROM responsables");
if (mysqli_num_rows($sql) > 0) {
    $responsables = mysqli_fetch_all($sql, MYSQLI_ASSOC);
    echo json_encode($responsables);
} else {
    echo json_encode([["success" => 0]]);
}
?>