<?php
session_start();
require 'conexion.php'; // Asegúrate de tener una conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $orderType = $_POST['orderType']; // 'delivery' o 'pickup'
    $address = isset($_POST['address']) ? $_POST['address'] : null;
    $phone = isset($_POST['phone']) ? $_POST['phone'] : null;
    $store = isset($_POST['store']) ? $_POST['store'] : null;
    $cartData = json_decode($_POST['cartData'], true); // Convertir los datos del carrito en un array
    $totalAmount = $_POST['totalAmount']; // El total de la compra

    // Obtener el usuario actual
    $userId = $_SESSION['user_id']; // Suponiendo que el ID del usuario está en la sesión

    // Insertar los datos en la base de datos (puedes ajustarlo según tu estructura)
    $query = "INSERT INTO orders (user_id, order_type, address, phone, store, total_amount) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('issssi', $userId, $orderType, $address, $phone, $store, $totalAmount);
    $stmt->execute();

    // Obtener el ID del pedido recién insertado
    $orderId = $stmt->insert_id;

    // Insertar los detalles de la compra (productos del carrito)
    foreach ($cartData as $item) {
        $productName = $item['name'];
        $quantity = $item['quantity'];
        $price = $item['price'];
        $query = "INSERT INTO order_items (order_id, product_name, quantity, price) 
                  VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('isii', $orderId, $productName, $quantity, $price);
        $stmt->execute();
    }

    // Redirigir al usuario a una página de confirmación o mostrar un mensaje
    header('Location: confirmacion_pago.php');
    exit();
} else {
    // Si no es una solicitud POST, redirigir al carrito
    header('Location: cart.php');
    exit();
}
?>
