<?php
session_start();

// Conexión a la base de datos
$conn = new mysqli('localhost', 'root', '12345', 'pizzeria');

// Verificar si la conexión fue exitosa
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Registrar un nuevo usuario
if (isset($_POST['register'])) {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo_registro'];
    $contraseña = password_hash($_POST['contraseña_registro'], PASSWORD_DEFAULT);

    // Verificar si el correo ya está registrado
    $sql = "SELECT * FROM usuarios WHERE correo = '$correo'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "Este correo ya está registrado.";
    } else {
        // Insertar el nuevo usuario en la base de datos
        $sql = "INSERT INTO usuarios (nombre, correo, contraseña) VALUES ('$nombre', '$correo', '$contraseña')";
        if ($conn->query($sql) === TRUE) {
            $_SESSION['user'] = $nombre;  // Guardamos el nombre del usuario en la sesión
            header("Location: index.php");  // Redirigimos al menú principal
            exit();
        } else {
            echo "Error al registrar usuario: " . $conn->error;
        }
    }
}

// Iniciar sesión con un usuario existente
if (isset($_POST['login'])) {
    $correo = $_POST['correo'];
    $contraseña = $_POST['contraseña'];

    // Buscar el usuario en la base de datos
    $sql = "SELECT * FROM usuarios WHERE correo = '$correo'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();

        // Verificar la contraseña
        if (password_verify($contraseña, $usuario['contraseña'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['user'] = $usuario['nombre'];  // Guardamos el nombre del usuario en la sesión
            header("Location: index.php");  // Redirigimos al menú principal
            exit();
        } else {
            echo "Contraseña incorrecta.";
        }
    } else {
        echo "No se encontró el usuario con este correo.";
    }
}

$conn->close();
?>
