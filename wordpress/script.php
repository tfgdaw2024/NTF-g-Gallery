<?php
header('Content-Type: application/json');

// Parámetros de conexión a la base de datos
$host = 'mysql.tfgdaw.dreamhosters.com';  // o la IP del servidor de base de datos
$dbname = 'tfgdaw_dreamhosters_com_5';
$username = 'behgse7q';
$password = 'aT6QcsP^';

// Crear conexión
$conn = new mysqli($host, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consulta SQL para obtener los datos deseados
$query = "SELECT * FROM wp_a25dc9_users";
$result = $conn->query($query);

// Almacenar los datos en un array
$data = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// Cerrar la conexión
$conn->close();

// Devolver los datos en formato JSON
echo json_encode($data);
?>
