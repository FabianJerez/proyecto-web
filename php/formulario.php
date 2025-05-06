<?php
$conexion = new mysqli("localhost", "root", "", "sca");

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $asunto = $_POST["asunto"] ?? '';
    $email = $_POST["email"] ?? '';
    $mensaje = $_POST["mensaje"] ?? '';
    $archivo_nombre = "";
    $estado = "Pendiente"; // Estado inicial, los otros pueden ser "en proceso" y "resuelto"

    if (isset($_FILES['archivoAdjunto']) && $_FILES['archivoAdjunto']['error'] == 0) {
        $archivo_nombre = $_FILES['archivoAdjunto']['name'];
        $archivo_temp = $_FILES['archivoAdjunto']['tmp_name'];
        $ruta_destino = "../uploads/" . basename($archivo_nombre);//me copia los archivos que me mandan normalmente documentos,comprobantes,facturas a mi carpeta upload
        move_uploaded_file($archivo_temp, $ruta_destino);
    }

    $stmt = $conexion->prepare("INSERT INTO mensajes (asunto, email, mensaje, archivo_nombre, estado) VALUES (?, ?, ?, ?, ?)");//evitar la inyeccion de sql
    $stmt->bind_param("sssss", $asunto, $email, $mensaje, $archivo_nombre, $estado);//evitar la inyeccion de sql

    if ($stmt->execute()) {
        echo "✅ Mensaje enviado correctamente.";
    } else {
        echo "❌ Error al guardar el mensaje: " . $stmt->error;
    }

    $stmt->close();
    $conexion->close();
}
?>
