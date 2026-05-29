<?php
// Requerir el autoloader de Composer para usar la librería oficial de MongoDB
require 'vendor/autoload.php';

// 1. Validar que la petición sea estrictamente por el método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 2. Recibir datos usando la superglobal $_POST y eliminar espacios en blanco
    $nombres = trim($_POST['nombres'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $tipo_fallo = trim($_POST['tipo_fallo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');

    // 3. Validación de seguridad básica: Evitar registros vacíos
    if (empty($nombres) || empty($email) || empty($tipo_fallo) || empty($descripcion)) {
        die('Error: Todos los campos son obligatorios. Por favor, regresa y completa el formulario.');
    }

    try {
        // 4. Conexión a MongoDB Atlas
        // REEMPLAZA esta URI con tu cadena de conexión real de MongoDB Atlas
        $uri = "mongodb+srv://luzandfer26_db_user:aJ2e8MtSvTpta2Fv@servidor.3itggok.mongodb.net/?appName=SERVIDOR";
        
        $cliente = new MongoDB\Client($uri);

        // Seleccionar la base de datos 'sena' y la colección 'soporte_mensajeria'
        $coleccion = $cliente->sena->soporte_mensajeria;

        // 5. Preparar el documento a insertar
        // Sanitizamos los datos básicos para prevenir inyección de HTML (XSS)
        $documento = [
            'nombres' => htmlspecialchars($nombres),
            'email' => filter_var($email, FILTER_SANITIZE_EMAIL),
            'tipo_fallo' => htmlspecialchars($tipo_fallo),
            'descripcion' => htmlspecialchars($descripcion),
            // Utilizamos UTCDateTime para guardar la fecha actual estándar en MongoDB
            'fecha_registro' => new MongoDB\BSON\UTCDateTime() 
        ];

        // 6. Ejecutar la inserción
        $resultado = $coleccion->insertOne($documento);

        // 7. Verificar el resultado de la operación
        if ($resultado->getInsertedCount() > 0) {
            echo "<h2>¡Éxito!</h2>";
            echo "<p>Tu reporte de soporte se ha guardado correctamente.</p>";
            echo "<p>ID de seguimiento: " . $resultado->getInsertedId() . "</p>";
            echo "<a href='index.html'>Volver al inicio</a>";
        } else {
            echo "<p>Error: No se pudo guardar el reporte en la base de datos.</p>";
        }

    } catch (Exception $e) {
        // Manejo de errores de conexión o ejecución en la base de datos
        die("Error de conexión con MongoDB: " . $e->getMessage());
    }

} else {
    // Si alguien intenta acceder al archivo directamente por URL (GET)
    echo "Acceso denegado. Por favor, utiliza el formulario correspondiente.";
}
?>