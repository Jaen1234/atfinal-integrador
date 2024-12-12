<?php
session_start();
include('conexion.php'); // Asegúrate de incluir la conexión a la base de datos

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Verificar si se enviaron los datos del formulario de pago
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Datos del usuario
    $user_id = $_SESSION['user_id'];  // Asumiendo que tienes el user_id en la sesión
    $order_type = $_POST['orderType']; // 'delivery' o 'pickup'
    $address = $_POST['address'] ?? null;
    $phone = $_POST['phone'] ?? null;
    $store = $_POST['store'] ?? null;
    $subtotal = $_POST['subtotal'];  // Subtotal enviado desde el formulario
    $shipping_cost = $_POST['costo_envio']; // Costo de envío
    $total = $_POST['total']; // Total de la compra

    // Validar que todos los campos estén completos (dependiendo del tipo de envío)
    if ($order_type == 'delivery' && (!$address || !$phone)) {
        echo "Por favor, complete todos los campos.";
        exit();
    }

    if ($order_type == 'pickup' && !$store) {
        echo "Por favor, seleccione una tienda.";
        exit();
    }

    // Insertar el pedido en la tabla 'pedidos'
    $stmt = $pdo->prepare("INSERT INTO pedidos (usuario_id, tipo_envio, direccion, telefono, tienda, subtotal, costo_envio, total, fecha) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([
        $user_id, 
        $order_type, 
        $address, 
        $phone, 
        $store, 
        $subtotal, 
        $shipping_cost, 
        $total
    ]);

    // Obtener el ID del pedido recién insertado
    $order_id = $pdo->lastInsertId();

    // Obtener el carrito de la sesión
    $cart = json_decode($_SESSION['cart'], true);

    // Insertar los detalles de cada producto en la tabla 'detalle_pedidos'
    foreach ($cart as $item) {
        $stmt = $pdo->prepare("INSERT INTO detalle_pedidos (pedido_id, pizza_id, cantidad, precio) 
                               VALUES (?, ?, ?, ?)");
        $stmt->execute([$order_id, $item['id'], $item['quantity'], $item['price']]);
    }

    // Vaciar el carrito después de realizar el pedido
    $_SESSION['cart'] = json_encode([]);

    // Ahora redirigimos al usuario a la página de PayPal para procesar el pago
    header("Location: paypal_payment.php?order_id=$order_id");
    exit();
}

// Comprobamos si se recibió una acción de pago
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action == 'register_payment') {
        // Obtener los datos enviados desde el frontend
        $orderId = $_POST['orderId'];
        $totalAmount = $_POST['totalAmount'];
        $paymentMethod = $_POST['paymentMethod'];
        $paymentStatus = $_POST['paymentStatus'];
        $transactionId = $_POST['transactionId'];

        // Insertar el pago en la base de datos
        $stmt = $pdo->prepare("INSERT INTO pagos (pedido_id, monto, metodo_pago, estado, transaccion_id) 
                               VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$orderId, $totalAmount, $paymentMethod, $paymentStatus, $transactionId]);

        // Responder con éxito
        echo json_encode(['success' => true]);
        exit();
    }

    if ($action == 'update_order_status') {
        // Obtener el ID del pedido y el nuevo estado
        $orderId = $_POST['orderId'];
        $status = $_POST['status'];

        // Actualizar el estado del pedido
        $stmt = $pdo->prepare("UPDATE pedidos SET status = ? WHERE id = ?");
        $stmt->execute([$status, $orderId]);

        // Responder con éxito
        echo json_encode(['success' => true]);
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procesar Pago</title>
    <link rel="stylesheet" href="css/procesarpago.css">
    <link rel="stylesheet" href="css/metodopago.css">
    <link rel="stylesheet" href="css/carcompleto.css">
    <link rel="stylesheet" href="css/navegacion.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://www.paypal.com/sdk/js?client-id=AXSAsLgwHTjWyY9w3Ayqez3t-LA7zMbZtHlL-L_wXEOY-XEH4SjGVAH1TYkBP5UyJ3_O-hpuuzhUAOE7"></script>
</head>
<body class="lol">
  <!-- Barra de navegación -->
  <header class="navbar-top">
        <nav class="navbar-container">
            
            <!-- Logo a la izquierda -->
            <div class="logo">
                <a href="#" class="logo" onclick="reloadPage();">
                    <img src="img/margoniwhite.png" alt="Logo" width="150">
                </a>
               
            </div>

            <!-- Enlaces de navegación centrados -->
            <ul class="nav-links">
                <li><a href="index.php">Menú</a></li>
                <li><a href="mispedidos.html">Pedidos</a></li>
                <li><a href="#">Locales</a></li>
            </ul>

            <!-- Barra de navegación derecha con íconos -->
            <ul class="nav-links-right">
                <li><a href="tel:+123456789"><i class="fas fa-phone-alt"></i> Llámanos<br>902442742</br></a></li>
                <li><a href="https://wa.me/123456789"><i class="fab fa-whatsapp"></i> Pide por <br>WhatsApp</br></a></li>
                <?php
             
            
            if (!isset($_SESSION['user'])) {
                echo '<li><a href="login.php"><i class="fas fa-user"></i>Hola, <br>Iniciar sesión</br></a></li>';
            } else {
                // Si está logueado, muestra su nombre
                echo '<li>Hola, ' . $_SESSION['user'] . '</li>';
                echo '<li><a href="logout.php">Cerrar sesión</a></li>';
            }
            ?>
        </nav>
    </header>


    
        
    
    

    <section class="payment-container">
        <form method="POST" id="payment-form">
            <input type="hidden" name="cart" id="cartData">
            <input type="hidden" name="total" id="total-input">

            <!-- Formulario de pago (izquierda) -->
            <div class="form-container">
                <h2>Selecciona el tipo de envío</h2>
                <div class="shipping-options">
                    <label>
                        <input type="radio" name="orderType" value="delivery" checked> Delivery
                    </label>
                    <label>
                        <input type="radio" name="orderType" value="pickup"> Recoger en tienda
                    </label>
                </div>

                <!-- Información de Delivery -->
                <div id="delivery-info" class="delivery-info">
                    <label for="address">Dirección de entrega:</label>
                    <input type="text" id="address" name="address" placeholder="Dirección" required>
                    <label for="phone">Número de teléfono:</label>
                    <input type="text" id="phone" name="phone" placeholder="Número de teléfono" required>
                </div>

                <!-- Información de Pickup -->
                <div id="pickup-info" class="pickup-info" style="display: none;">
                    <label for="store">Selecciona la tienda:</label>
                    <select id="store" name="store" required>
                        <option value="los_olivos">Los Olivos</option>
                        <option value="san_martin">San Martín</option>
                    </select>
                </div>
            </div>
            <!-- Resumen de la compra (derecha) -->
<!-- Resumen de la compra (derecha) -->
<div class="summary-container">
    <h2>Resumen</h2>
    <p><strong>Subtotal:</strong> $<span id="subtotal">0.00</span></p>
    <p><strong>Envío:</strong> $<span id="shipping">10.00</span></p>
    <p><strong>Total:</strong> $<span id="total">0.00</span></p>

    <!-- Aquí van las opciones de método de pago (inicialmente ocultas) -->
    <div id="paypal-button-container" style="display: none;">
        <h3>Selecciona el método de pago</h3>

</div>
<button id="proceed-to-payment" type="submit" disabled>Continuar</button>



        </form>
       

    </section>

    <script>
        function loadCart() {
            let cart = JSON.parse(sessionStorage.getItem('cart')) || [];
            let subtotal = 0;
            cart.forEach(item => {
                subtotal += item.price * item.quantity;
            });

            document.getElementById('subtotal').textContent = subtotal.toFixed(2);
            let shippingCost = 10.00;
            document.getElementById('shipping').textContent = shippingCost.toFixed(2);
            let total = subtotal + shippingCost;
            document.getElementById('total').textContent = total.toFixed(2);
            document.getElementById('cartData').value = JSON.stringify(cart);
            document.getElementById('total-input').value = total.toFixed(2);
        }

        // Mostrar u ocultar información dependiendo del tipo de envío seleccionado
        document.querySelectorAll('input[name="orderType"]').forEach(function(input) {
            input.addEventListener('change', function() {
                if (this.value === 'delivery') {
                    document.getElementById('delivery-info').style.display = 'block';
                    document.getElementById('pickup-info').style.display = 'none';
                } else {
                    document.getElementById('delivery-info').style.display = 'none';
                    document.getElementById('pickup-info').style.display = 'block';
                }
                validateForm(); // Validar después de cambiar el tipo de envío
            });
        });

// Validar el formulario
function validateForm() {
    let isValid = true;
    let orderType = document.querySelector('input[name="orderType"]:checked').value;

    // Validación para Delivery
    if (orderType === 'delivery') {
        if (!document.getElementById('address').value || !document.getElementById('phone').value) {
            isValid = false;
        }
    } 
    // Validación para Pickup
    else if (orderType === 'pickup') {
        if (!document.getElementById('store').value) {
            isValid = false;
        }
    }

    // Si el formulario es válido, habilitamos el botón y mostramos el contenedor de PayPal
    if (isValid) {
        document.getElementById('proceed-to-payment').disabled = false;  // Habilitar el botón "Continuar"
        document.getElementById('paypal-button-container').style.display = 'block'; // Mostrar el contenedor de PayPal
    } else {
        document.getElementById('proceed-to-payment').disabled = true;  // Deshabilitar el botón "Continuar"
        document.getElementById('paypal-button-container').style.display = 'none'; // Ocultar el contenedor de PayPal
    }
}

// Habilitar la validación al escribir en los campos
document.querySelectorAll('#payment-form input, #payment-form select').forEach(function(input) {
    input.addEventListener('input', validateForm);
});

// Cargar el carrito cuando se cargue la página
document.addEventListener('DOMContentLoaded', function() {
    loadCart();
});


    </script>

    <script>
        // Mostrar detalles del pago según el método seleccionado
document.querySelectorAll('input[name="paymentMethod"]').forEach(function(input) {
    input.addEventListener('change', function() {
        // Ocultar todos los formularios de pago
        document.getElementById('credit-card-info').style.display = 'none';
        document.getElementById('paypal-info').style.display = 'none';

        // Mostrar el formulario correspondiente
        if (this.value === 'credit_card') {
            document.getElementById('credit-card-info').style.display = 'block';
        } else if (this.value === 'paypal') {
            document.getElementById('paypal-info').style.display = 'block';
        }
    });
});

// Función para validar los campos del formulario de pago
function validatePaymentForm() {
    let isValid = true;
    let paymentMethod = document.querySelector('input[name="paymentMethod"]:checked');

    // Verificar si se seleccionó un método de pago
    if (!paymentMethod) {
        isValid = false;
    }

    // Si es tarjeta de crédito, verificar los campos de tarjeta
    if (paymentMethod && paymentMethod.value === 'credit_card') {
        let cardNumber = document.getElementById('card-number').value;
        let expirationDate = document.getElementById('expiration-date').value;
        let cvv = document.getElementById('cvv').value;
        if (!cardNumber || !expirationDate || !cvv) {
            isValid = false;
        }
    }

    // Habilitar el botón continuar solo si es válido
    document.getElementById('proceed-to-payment').disabled = !isValid;
}

// Validar formulario de pago al cambiar el método de pago
document.querySelectorAll('input[name="paymentMethod"]').forEach(function(input) {
    input.addEventListener('change', validatePaymentForm);
});

// Habilitar validación del formulario de detalles de pago
document.querySelectorAll('.payment-info input').forEach(function(input) {
    input.addEventListener('input', validatePaymentForm);
});


    </script>


    <script>
    paypal.Buttons({
    style: {
        color: 'blue',
        shape: 'pill',
        label: 'pay'
    },
    createOrder: function(data, actions) {
        let totalAmount = document.getElementById('total').textContent;  // Obtiene el total de la compra
        return actions.order.create({
            purchase_units: [{
                amount: {
                    value: totalAmount  // Usamos el total calculado en el resumen
                }
            }]
        });
    },
    onApprove: function(data, actions) {
        actions.order.capture().then(function(details) {
            console.log(details);  // Aquí puedes ver los detalles del pago
            // Realizar el registro del pago en la base de datos
        });
    },
    onCancel: function(data) {
        alert("Pago cancelado");
        console.log(data);
    }
}).render('#paypal-button-container');


    </script>

</body>
</html>
