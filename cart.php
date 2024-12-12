<?php
session_start(); // Asegúrate de que la sesión esté iniciada

// Verificar si el usuario está logueado
if (!isset($_SESSION['user'])) {
    // Si no está logueado, redirigir al login
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <link rel="stylesheet" href="css/carcompleto.css">
    <link rel="stylesheet" href="css/navegacion.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

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

    <section class="cart-container">
        <div class="cart-items">
            <h2>Carrito de Compras</h2>
            <table id="cart-table">
                <thead>
                    <tr>
                        <th>Imagen</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="cart-items">
                    <!-- Aquí se cargarán los productos del carrito -->
                </tbody>
            </table>
        </div>
        
        <!-- Sección de Totales -->
        <div class="cart-totals">
            <h2>Totales</h2>
            <p><strong>Subtotal:</strong> $<span id="cart-subtotal">0.00</span></p>
            <p><strong>Costo de envío:</strong> $<span id="cart-shipping">10.00</span></p>
            <p><strong>Total:</strong> $<span id="cart-total-price">0.00</span></p>
            <button id="checkout-btn" onclick="checkout()">Procesar pago</button>
        </div>
    </section>

    <script>
        // Función para cargar y mostrar los productos en el carrito
        function loadCart() {
            let cart = JSON.parse(sessionStorage.getItem('cart')) || [];
            let cartItemsContainer = document.getElementById('cart-items');
            let cartTotal = 0;
            let cartSubtotal = 0;
            cartItemsContainer.innerHTML = ''; // Limpiar el carrito antes de cargar los nuevos elementos

            cart.forEach((item, index) => {
                // Crear una fila por cada producto del carrito
                let row = document.createElement('tr');
                row.innerHTML = `
                    <td><img src="${item.image}" alt="${item.name}" style="width: 50px; height: 50px;"></td> <!-- Mostrar la imagen -->
                    <td>${item.name}</td>
                    <td>
                        <button onclick="decreaseQuantity(${index})">-</button>
                        ${item.quantity}
                        <button onclick="increaseQuantity(${index})">+</button>
                    </td>
                    <td>$${item.price.toFixed(2)}</td>
                    <td>$${(item.price * item.quantity).toFixed(2)}</td>
                    <td>
                        <button onclick="removeFromCart(${index})">Eliminar</button>
                    </td>
                `;
                cartItemsContainer.appendChild(row);

                // Calcular el subtotal del carrito
                cartSubtotal += item.price * item.quantity;
            });

            // Mostrar el subtotal y total en la página
            const shippingCost = 10;  // Costo de envío fijo
            const total = cartSubtotal + shippingCost;

            document.getElementById('cart-subtotal').textContent = cartSubtotal.toFixed(2);
            document.getElementById('cart-shipping').textContent = shippingCost.toFixed(2);
            document.getElementById('cart-total-price').textContent = total.toFixed(2);
        }

        // Función para incrementar la cantidad de un producto en el carrito
        function increaseQuantity(index) {
            let cart = JSON.parse(sessionStorage.getItem('cart')) || [];
            cart[index].quantity += 1; // Incrementar la cantidad
            sessionStorage.setItem('cart', JSON.stringify(cart)); // Guardar cambios en sessionStorage
            loadCart(); // Recargar el carrito para actualizar la vista
        }

        // Función para disminuir la cantidad de un producto en el carrito
        function decreaseQuantity(index) {
            let cart = JSON.parse(sessionStorage.getItem('cart')) || [];
            if (cart[index].quantity > 1) {
                cart[index].quantity -= 1; // Disminuir la cantidad
                sessionStorage.setItem('cart', JSON.stringify(cart)); // Guardar cambios en sessionStorage
                loadCart(); // Recargar el carrito para actualizar la vista
            }
        }

        // Función para eliminar un producto del carrito
        function removeFromCart(index) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Este producto se eliminará del carrito.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    let cart = JSON.parse(sessionStorage.getItem('cart')) || [];
                    cart.splice(index, 1); // Eliminar el producto del carrito usando su índice
                    sessionStorage.setItem('cart', JSON.stringify(cart)); // Guardar los cambios en sessionStorage
                    loadCart(); // Recargar el carrito
                    Swal.fire('Eliminado', 'El producto ha sido eliminado del carrito.', 'success');
                }
            });
        }

        // Función para manejar el proceso de checkout (aquí solo mostramos un mensaje)
        function checkout() {
            let cart = JSON.parse(sessionStorage.getItem('cart')) || [];

            if (cart.length === 0) {
                Swal.fire({
                    title: 'Carrito vacío',
                    text: 'No has agregado ningún producto al carrito.',
                    icon: 'warning',
                    confirmButtonText: 'Volver',
                });
                return;
            }
            
            // Puedes redirigir a la página de pago aquí
            window.location.href = 'procesar_pago.php';  // O personaliza esta lógica según tu necesidad
        }

        // Cargar el carrito cuando se cargue la página
        document.addEventListener('DOMContentLoaded', loadCart);
    </script>

</body>
</html>
