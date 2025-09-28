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

// ---------- Función para generar ID_ubi ----------
function generarID($conexionBD) {
    $sql = "SELECT ID_ubi FROM ubicaciones ORDER BY CAST(ID_ubi AS UNSIGNED) ASC";
    $result = $conexionBD->query($sql);

    $ocupados = [];
    while($row = $result->fetch_assoc()) {
        $ocupados[intval($row["ID_ubi"])] = true;
    }

    $n = 1;
    while(isset($ocupados[$n])) {
        $n++;
    }

    return strval($n); // devolver como string
}

// ---------- Función para generar Codigo_ubi ----------
function generarCodigoUbi($nombre, $conexionBD) {
    $prefijo = "U_" . strtoupper(substr($nombre, 0, 3));

    $sql = "SELECT Codigo_ubi FROM ubicaciones WHERE Codigo_ubi LIKE '$prefijo%' ORDER BY Codigo_ubi ASC";
    $result = $conexionBD->query($sql);

    $ocupados = [];
    while($row = $result->fetch_assoc()) {
        $num = intval(substr($row["Codigo_ubi"], 5)); // después de U_XXX
        $ocupados[$num] = true;
    }

    $n = 1;
    while(isset($ocupados[$n])) {
        $n++;
    }

    return $prefijo . str_pad($n, 4, "0", STR_PAD_LEFT);
}

// ---------- Consultar por Codigo_ubi ----------
if (isset($_GET["consultar"])) {
    $codigo = $conexionBD->real_escape_string($_GET["consultar"]);
    $sql = mysqli_query($conexionBD,"SELECT * FROM ubicaciones WHERE Codigo_ubi='$codigo'");
    echo json_encode(mysqli_num_rows($sql) > 0 ? mysqli_fetch_assoc($sql) : ["success"=>0]);
    exit();
}

// ---------- Consultar por Nombre_ubi ----------
if (isset($_GET["consultarNombre"])) {
    $nombre = $conexionBD->real_escape_string($_GET["consultarNombre"]);
    $sql = mysqli_query($conexionBD,"SELECT * FROM ubicaciones WHERE Nombre_ubi='$nombre'");
    echo json_encode(mysqli_num_rows($sql) > 0 ? mysqli_fetch_assoc($sql) : ["success"=>0]);
    exit();
}

// ---------- Consultar por ID_ubi ----------
if (isset($_GET["consultarID"])) {
    $id = $conexionBD->real_escape_string($_GET["consultarID"]);
    $sql = mysqli_query($conexionBD,"SELECT * FROM ubicaciones WHERE ID_ubi='$id'");
    echo json_encode(mysqli_num_rows($sql) > 0 ? mysqli_fetch_assoc($sql) : ["success"=>0]);
    exit();
}

// ---------- Eliminar ----------
if (isset($_GET["borrar"])) {
    $codigo = $conexionBD->real_escape_string($_GET["borrar"]);

    // Iniciar transacción
    $conexionBD->begin_transaction();
    try {
        // Paso 1: Poner null en las filas hijas
        $sqlChild = "UPDATE equiposmedicos SET Codigo_ubi=NULL WHERE Codigo_ubi='$codigo'";
        if (!$conexionBD->query($sqlChild)) {
            throw new Exception("Error actualizando equiposmedicos: " . $conexionBD->error);
        }

        // Paso 2: Borrar la ubicación
        $sqlParent = "DELETE FROM ubicaciones WHERE Codigo_ubi='$codigo'";
        if (!$conexionBD->query($sqlParent)) {
            throw new Exception("Error borrando ubicación: " . $conexionBD->error);
        }

        // Confirmar transacción
        $conexionBD->commit();
        echo json_encode(["success" => 1]);
    } catch (Exception $e) {
        $conexionBD->rollback();
        echo json_encode(["success" => 0, "error" => $e->getMessage()]);
    }
    exit();
}


// ---------- Insertar ----------
if (isset($_GET["insertar"])) {
    $data = json_decode(file_get_contents("php://input"));

    $Nombre_ubi = isset($data->Nombre_ubi) ? trim($data->Nombre_ubi) : '';
    $Ubicacion = isset($data->Ubicacion) ? trim($data->Ubicacion) : '';
    $Telefono = isset($data->Telefono) ? trim($data->Telefono) : '';

    // Validar campos obligatorios
    if ($Nombre_ubi === '' || $Ubicacion === '') {
        echo json_encode(["success" => 0, "error" => "Nombre y Ubicación son obligatorios"]);
        exit();
    }

    // Generar ID_ubi y Codigo_ubi
    $ID_ubi = generarID($conexionBD);
    $Codigo_ubi = generarCodigoUbi($Nombre_ubi, $conexionBD);

    $sql = mysqli_query($conexionBD,
        "INSERT INTO ubicaciones (Codigo_ubi, ID_ubi, Nombre_ubi, Ubicacion, Telefono) 
        VALUES ('$Codigo_ubi','$ID_ubi','$Nombre_ubi','$Ubicacion','$Telefono')"
    );

    echo json_encode($sql ? ["success"=>1,"Codigo_ubi"=>$Codigo_ubi,"ID_ubi"=>$ID_ubi] : ["success"=>0,"error"=>"Error al insertar"]);
    exit();
}


if (isset($_GET["actualizar"])) {
    $data = json_decode(file_get_contents("php://input"));
    $Codigo_ubi = $conexionBD->real_escape_string($_GET["actualizar"]);

    // Leer datos actuales
    $sqlOld = mysqli_query($conexionBD,"SELECT * FROM ubicaciones WHERE Codigo_ubi='$Codigo_ubi'");
    $old = mysqli_fetch_assoc($sqlOld);
    if(!$old){
        echo json_encode(["success"=>0,"error"=>"Ubicación no encontrada"]);
        exit();
    }

    // Solo actualizar si vienen datos nuevos
    $Nombre_ubi = !empty($data->Nombre_ubi) ? $data->Nombre_ubi : $old['Nombre_ubi'];
    $Ubicacion = !empty($data->Ubicacion) ? $data->Ubicacion : $old['Ubicacion'];
    $Telefono  = !empty($data->Telefono) ? $data->Telefono : $old['Telefono'];

    // Verificar si cambió el Nombre_ubi -> regenerar Codigo_ubi
    if($old["Nombre_ubi"] != $Nombre_ubi){
        $nuevoCodigo = generarCodigoUbi($Nombre_ubi, $conexionBD);

        // actualizar ubicaciones
        mysqli_query($conexionBD,
            "UPDATE ubicaciones 
            SET Codigo_ubi='$nuevoCodigo', Nombre_ubi='$Nombre_ubi', Ubicacion='$Ubicacion', Telefono='$Telefono'
            WHERE Codigo_ubi='$Codigo_ubi'"
        );

        // actualizar también en equiposmedicos
        mysqli_query($conexionBD,"UPDATE equiposmedicos SET Codigo_ubi='$nuevoCodigo' WHERE Codigo_ubi='$Codigo_ubi'");

        $Codigo_ubi = $nuevoCodigo;
    } else {
        mysqli_query($conexionBD,
            "UPDATE ubicaciones 
            SET Nombre_ubi='$Nombre_ubi', Ubicacion='$Ubicacion', Telefono='$Telefono'
            WHERE Codigo_ubi='$Codigo_ubi'"
        );
    }

    echo json_encode(["success"=>1,"Codigo_ubi"=>$Codigo_ubi]);
    exit();
}


// ---------- Listar todos ----------
$sql = mysqli_query($conexionBD,"SELECT * FROM ubicaciones");
if(mysqli_num_rows($sql) > 0){
    echo json_encode(mysqli_fetch_all($sql,MYSQLI_ASSOC));
} else {
    echo json_encode([["success"=>0]]);
}
?>