<?php
header('Content-Type: application/json');

// Parámetros de conexión a la base de datos
$host = 'mysql.tfgdaw.dreamhosters.com';  // o la IP del servidor de base de datos
$bbddNombre = 'tfgdaw_dreamhosters_com_5';
$usuario = 'behgse7q';
$contraseña = 'aT6QcsP^';

// Crear conexión
$conexion = new mysqli($host, $usuario, $contraseña, $bbddNombre);

// Verificar conexión
if ($conexion->connect_error) {
    echo("Error de conexión: " . $conexion->connect_error);
}

// Consulta SQL para obtener los datos deseados
$query = "SELECT * FROM wp_a25dc9_users";
$resultado = $conexion->query($query);

// Almacenar los datos en un array
$datos = array();
if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $datos[] = $fila;
    }
}

// Cerrar la conexión
$conexion->close();

// Devolver los datos en formato JSON
echo json_encode($datos);
?>

